
<?php
require_once __DIR__ . '/config/conexion.php';

$pageTitle = 'Solicitar una cita';

$servicios = $pdo->query(
    "SELECT id, nombre, precio FROM servicios ORDER BY nombre"
)->fetchAll();

$empleados = $pdo->query(
    "SELECT id, nombre FROM empleados ORDER BY nombre"
)->fetchAll();

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre_cliente'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $id_servicio = (int) ($_POST['id_servicio'] ?? 0);
    $id_empleado = (int) ($_POST['id_empleado'] ?? 0);
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';

    if (
        $nombre === '' || $telefono === '' ||
        $id_servicio <= 0 || $id_empleado <= 0 ||
        $fecha === '' || $hora === ''
    ) {
        $mensaje = 'Por favor, completa todos los campos.';
        $tipoMensaje = 'danger';

    } elseif ($fecha < date('Y-m-d')) {
        $mensaje = 'Selecciona una fecha válida.';
        $tipoMensaje = 'danger';

    } else {
        // Comprobar que el servicio y empleado existan
        $check = $pdo->prepare(
            "SELECT COUNT(*) FROM servicios WHERE id = ?"
        );
        $check->execute([$id_servicio]);
        $servicioExiste = $check->fetchColumn();

        $check = $pdo->prepare(
            "SELECT COUNT(*) FROM empleados WHERE id = ?"
        );
        $check->execute([$id_empleado]);
        $empleadoExiste = $check->fetchColumn();

        if (!$servicioExiste || !$empleadoExiste) {
            $mensaje = 'El servicio o empleado seleccionado no es válido.';
            $tipoMensaje = 'danger';
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO citas
                (nombre_cliente, telefono, id_servicio, id_empleado, fecha, hora, estado)
                VALUES (?, ?, ?, ?, ?, ?, 'pendiente')"
            );

            $stmt->execute([
                $nombre,
                $telefono,
                $id_servicio,
                $id_empleado,
                $fecha,
                $hora
            ]);

            $mensaje = '¡Tu solicitud de cita fue enviada correctamente! Nos pondremos en contacto contigo.';
            $tipoMensaje = 'success';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
body {
    background: #f4f7fc;
}

.reserva-banner {
    background: linear-gradient(135deg, #101b38, #1e3a8a, #4779c9);
    padding: 65px 20px;
    text-align: center;
    color: white;
    border-radius: 0 0 35px 35px;
}

.reserva-banner h1 {
    font-weight: 800;
    font-size: 2.5rem;
}

.reserva-banner p {
    color: #e2e8f0;
}

.reserva-card {
    background: white;
    max-width: 850px;
    margin: -25px auto 60px;
    position: relative;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 8px 30px rgba(15, 23, 42, .10);
}

.reserva-card h3 {
    color: #1e3a8a;
    font-weight: 750;
}

.reserva-card label {
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
}

.reserva-card .form-control,
.reserva-card .form-select {
    padding: 12px;
    border-radius: 9px;
    border-color: #dce3ef;
}

.reserva-card .form-control:focus,
.reserva-card .form-select:focus {
    border-color: #4779c9;
    box-shadow: 0 0 0 3px rgba(71, 121, 201, .15);
}

.btn-reserva {
    background: #1e3a8a;
    color: white;
    border: none;
    padding: 13px 28px;
    border-radius: 25px;
    font-weight: 700;
}

.btn-reserva:hover {
    background: #4779c9;
    color: white;
}

@media(max-width: 576px) {
    .reserva-card {
        margin: -20px 12px 40px;
        padding: 22px;
    }

    .reserva-banner h1 {
        font-size: 2rem;
    }
}
</style>

<section class="reserva-banner">
    <div class="container">
        <h1>Reserva tu cita</h1>
        <p>En Estilo &amp; Belleza queremos consentirte. ¡Agenda tu visita!</p>
    </div>
</section>

<div class="container">

    <div class="reserva-card">

        <h3 class="mb-2">Solicitar una cita</h3>
        <p class="text-muted mb-4">
            Completa tus datos y selecciona el servicio que deseas.
        </p>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= $tipoMensaje ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <?php if (count($servicios) === 0 || count($empleados) === 0): ?>

            <div class="alert alert-warning">
                En este momento no hay servicios o empleados disponibles para reservar.
            </div>

        <?php else: ?>

            <form method="POST" action="citas.php">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Nombre completo</label>
                        <input type="text"
                               name="nombre_cliente"
                               class="form-control"
                               placeholder="Escribe tu nombre"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Teléfono</label>
                        <input type="tel"
                               name="telefono"
                               class="form-control"
                               placeholder="Tu número de teléfono"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Servicio que deseas</label>
                        <select name="id_servicio" class="form-select" required>
                            <option value="">Seleccionar servicio</option>

                            <?php foreach ($servicios as $servicio): ?>
                                <option value="<?= (int) $servicio['id'] ?>">
                                    <?= htmlspecialchars($servicio['nombre']) ?>
                                    - C$ <?= htmlspecialchars($servicio['precio']) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Profesional</label>
                        <select name="id_empleado" class="form-select" required>
                            <option value="">Seleccionar profesional</option>

                            <?php foreach ($empleados as $empleado): ?>
                                <option value="<?= (int) $empleado['id'] ?>">
                                    <?= htmlspecialchars($empleado['nombre']) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Fecha de la cita</label>
                        <input type="date"
                               name="fecha"
                               class="form-control"
                               min="<?= date('Y-m-d') ?>"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Hora de la cita</label>
                        <input type="time"
                               name="hora"
                               class="form-control"
                               required>
                    </div>

                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-reserva">
                        <i class="bi bi-calendar-check me-2"></i>
                        Enviar solicitud
                    </button>
                </div>

            </form>

        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

