
<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// VERIFICAR SESIÓN
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// CERRAR SESIÓN
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit;
}

// ELIMINAR CITA
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM citas WHERE id = ?');
    $stmt->execute([$id]);

    header('Location: citas.php?msg=eliminado');
    exit;
}

// AGREGAR O EDITAR CITA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $id_servicio = (int) ($_POST['id_servicio'] ?? 0);
    $id_empleado = (int) ($_POST['id_empleado'] ?? 0);
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $estado = $_POST['estado'] ?? 'pendiente';

    if (
        $nombre_cliente === '' || $telefono === '' ||
        $id_servicio <= 0 || $id_empleado <= 0 ||
        $fecha === '' || $hora === ''
    ) {
        header('Location: citas.php?msg=error');
        exit;
    }

    if ($id > 0) {
        $stmt = $pdo->prepare(
            'UPDATE citas SET nombre_cliente=?, telefono=?, id_servicio=?,
             id_empleado=?, fecha=?, hora=?, estado=? WHERE id=?'
        );

        $stmt->execute([
            $nombre_cliente, $telefono, $id_servicio,
            $id_empleado, $fecha, $hora, $estado, $id
        ]);

        header('Location: citas.php?msg=editado');
        exit;
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO citas
            (nombre_cliente, telefono, id_servicio, id_empleado, fecha, hora, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $nombre_cliente, $telefono, $id_servicio,
            $id_empleado, $fecha, $hora, $estado
        ]);

        header('Location: citas.php?msg=agregado');
        exit;
    }
}

// SERVICIOS Y EMPLEADOS
$services = $pdo->query(
    'SELECT id, nombre FROM servicios ORDER BY nombre'
)->fetchAll();

$employees = $pdo->query(
    'SELECT id, nombre FROM empleados ORDER BY nombre'
)->fetchAll();

// CITA PARA EDITAR
$id = (int) ($_GET['edit'] ?? 0);

$nombre_cliente = '';
$telefono = '';
$id_servicio = 0;
$id_empleado = 0;
$fecha = '';
$hora = '';
$estado = 'pendiente';

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM citas WHERE id = ?');
    $stmt->execute([$id]);
    $citaEditar = $stmt->fetch();

    if ($citaEditar) {
        $nombre_cliente = $citaEditar['nombre_cliente'];
        $telefono = $citaEditar['telefono'];
        $id_servicio = $citaEditar['id_servicio'];
        $id_empleado = $citaEditar['id_empleado'];
        $fecha = $citaEditar['fecha'];
        $hora = $citaEditar['hora'];
        $estado = $citaEditar['estado'];
    } else {
        $id = 0;
    }
}

// LISTADO DE CITAS
$citas = $pdo->query(
    'SELECT c.id, c.nombre_cliente, c.telefono, c.fecha, c.hora,
            c.estado, s.nombre AS servicio, e.nombre AS empleado
     FROM citas c
     LEFT JOIN servicios s ON c.id_servicio = s.id
     LEFT JOIN empleados e ON c.id_empleado = e.id
     ORDER BY c.fecha DESC, c.hora DESC'
)->fetchAll();

// CONTADORES
$total = count($citas);
$pendientes = 0;
$confirmadas = 0;
$completadas = 0;

foreach ($citas as $c) {
    if ($c['estado'] === 'pendiente') $pendientes++;
    if ($c['estado'] === 'confirmada') $confirmadas++;
    if ($c['estado'] === 'completada') $completadas++;
}

$mensajes = [
    'agregado' => 'La cita se agregó correctamente.',
    'editado' => 'La cita se actualizó correctamente.',
    'eliminado' => 'La cita se eliminó correctamente.',
    'error' => 'Completa todos los campos obligatorios.'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Administración de Citas | Estilo & Belleza</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --navy: #101b38;
    --blue: #3457a5;
    --accent: #6488d8;
    --background: #f4f6fb;
    --text: #475569;
}

* { box-sizing: border-box; }

body {
    margin: 0;
    background: var(--background);
    color: var(--text);
    font-family: "Segoe UI", Arial, sans-serif;
}

.sidebar {
    width: 255px;
    min-height: 100vh;
    background: linear-gradient(180deg, #101b38, #1d3568);
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    padding: 28px 18px;
    z-index: 1000;
}

.brand {
    font-size: 21px;
    font-weight: 750;
    padding: 5px 10px 30px;
    border-bottom: 1px solid rgba(255,255,255,.12);
}

.brand span { color: #91b5ff; }

.menu-title {
    color: #a8b8d8;
    font-size: 11px;
    letter-spacing: 1.5px;
    margin: 30px 12px 12px;
    text-transform: uppercase;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 13px;
    color: #d9e3f7;
    text-decoration: none;
    padding: 13px 14px;
    margin-bottom: 7px;
    border-radius: 10px;
    transition: .25s;
}

.sidebar a:hover,
.sidebar a.active {
    background: rgba(255,255,255,.14);
    color: white;
}

.sidebar a.active {
    border-left: 3px solid #91b5ff;
}

.sidebar .logout {
    margin-top: 35px;
    color: #ffc3c3;
}

.main-content {
    margin-left: 255px;
    padding: 35px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.page-title {
    color: #152448;
    font-weight: 750;
    font-size: 28px;
    margin-bottom: 5px;
}

.welcome {
    font-size: 14px;
    color: #8994a8;
}

.profile {
    background: white;
    padding: 10px 16px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(20,40,80,.05);
    color: #263b68;
}

.summary-card {
    background: white;
    padding: 23px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(25,45,90,.05);
    border: 1px solid #edf0f6;
    height: 100%;
}

.summary-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 21px;
    margin-bottom: 15px;
}

.icon-blue { background: #e9efff; color: #3457a5; }
.icon-yellow { background: #fff5dc; color: #c38b16; }
.icon-green { background: #e3f7ed; color: #26935d; }
.icon-purple { background: #f0eaff; color: #8157c9; }

.summary-card h3 {
    font-size: 27px;
    color: #18294e;
    font-weight: 750;
    margin-bottom: 3px;
}

.summary-card p {
    margin: 0;
    color: #8994a8;
    font-size: 14px;
}

.content-card {
    background: white;
    border: 1px solid #edf0f6;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(25,45,90,.05);
    margin-top: 27px;
}

.section-heading {
    color: #18294e;
    font-weight: 700;
    font-size: 18px;
}

.table th {
    background: #f1f4fa;
    color: #506080;
    font-size: 12px;
    text-transform: uppercase;
    padding: 15px 12px;
    white-space: nowrap;
}

.table td {
    padding: 15px 12px;
    vertical-align: middle;
    font-size: 13px;
}

.table tbody tr:hover { background: #f7f9fd; }

.badge {
    padding: 8px 11px;
    border-radius: 20px;
}

.btn-primary {
    background: var(--blue);
    border-color: var(--blue);
}

.btn-primary:hover {
    background: #263f7c;
    border-color: #263f7c;
}

.form-control, .form-select {
    border-radius: 9px;
    padding: 11px;
    border-color: #dce3ef;
}

.form-control:focus, .form-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(100,136,216,.15);
}

.form-label {
    font-weight: 600;
    color: #354666;
}

@media (max-width: 900px) {
    .sidebar {
        width: 75px;
        padding: 20px 10px;
    }

    .brand {
        font-size: 0;
        text-align: center;
        padding: 10px 0 25px;
    }

    .brand span { font-size: 20px; }

    .menu-title, .link-text { display: none; }

    .sidebar a {
        justify-content: center;
        padding: 14px 5px;
        font-size: 19px;
    }

    .main-content {
        margin-left: 75px;
        padding: 22px 15px;
    }
}

@media (max-width: 576px) {
    .topbar {
        align-items: flex-start;
        gap: 12px;
        flex-direction: column;
    }
}
</style>
</head>

<body>

<aside class="sidebar">
    <div class="brand">ESTILO <span>&amp; BELLEZA</span></div>

    <div class="menu-title">Menú principal</div>

    <a href="dashboard.php">
        <i class="bi bi-grid"></i>
        <span class="link-text">Dashboard</span>
    </a>

    <a href="servicios.php">
        <i class="bi bi-scissors"></i>
        <span class="link-text">Servicios</span>
    </a>

    <a href="empleados.php">
        <i class="bi bi-people"></i>
        <span class="link-text">Empleados</span>
    </a>

    <a href="citas.php" class="active">
        <i class="bi bi-calendar-check"></i>
        <span class="link-text">Citas</span>
    </a>

    <a href="usuarios.php">
        <i class="bi bi-person-lock"></i>
        <span class="link-text">Usuarios</span>
    </a>

    <div class="menu-title">Cuenta</div>

    <a href="citas.php?logout=1" class="logout">
        <i class="bi bi-box-arrow-left"></i>
        <span class="link-text">Cerrar sesión</span>
    </a>
</aside>

<main class="main-content">

    <div class="topbar">
        <div>
            <h1 class="page-title">Administración de Citas</h1>
            <p class="welcome">Bienvenida al panel de gestión de Estilo & Belleza.</p>
        </div>

        <div class="profile">
            <i class="bi bi-person-circle me-2"></i>
            Administrador
        </div>
    </div>

    <?php if (isset($_GET['msg']) && isset($mensajes[$_GET['msg']])): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <?= htmlspecialchars($mensajes[$_GET['msg']]) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-icon icon-blue">
                    <i class="bi bi-calendar2-week"></i>
                </div>
                <h3><?= $total ?></h3>
                <p>Total de citas</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-icon icon-yellow">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h3><?= $pendientes ?></h3>
                <p>Citas pendientes</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-icon icon-green">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h3><?= $confirmadas ?></h3>
                <p>Citas confirmadas</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="summary-card">
                <div class="summary-icon icon-purple">
                    <i class="bi bi-stars"></i>
                </div>
                <h3><?= $completadas ?></h3>
                <p>Citas completadas</p>
            </div>
        </div>

    </div>

    <section class="content-card">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="section-heading mb-1">Listado de citas</h5>
                <p class="text-muted small mb-0">Control de las citas de tus clientes.</p>
            </div>

            <a href="citas.php?add=1" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Agregar cita
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Servicio</th>
                        <th>Empleado</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (count($citas) > 0): ?>

                    <?php foreach ($citas as $cita): ?>
                        <tr>
                            <td><?= (int) $cita['id'] ?></td>
                            <td><?= htmlspecialchars($cita['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($cita['telefono']) ?></td>
                            <td><?= htmlspecialchars($cita['servicio'] ?? 'No disponible') ?></td>
                            <td><?= htmlspecialchars($cita['empleado'] ?? 'No asignado') ?></td>
                            <td><?= htmlspecialchars($cita['fecha']) ?></td>
                            <td><?= htmlspecialchars(substr($cita['hora'], 0, 5)) ?></td>

                            <td>
                                <?php
                                $colores = [
                                    'pendiente' => 'warning text-dark',
                                    'confirmada' => 'success',
                                    'cancelada' => 'danger',
                                    'completada' => 'primary'
                                ];
                                $color = $colores[$cita['estado']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>">
                                    <?= htmlspecialchars(ucfirst($cita['estado'])) ?>
                                </span>
                            </td>

                            <td>
                                <div class="d-flex gap-2">
                                    <a href="citas.php?edit=<?= (int) $cita['id'] ?>"
                                       class="btn btn-sm btn-warning"
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <a href="citas.php?delete=<?= (int) $cita['id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       title="Eliminar"
                                       onclick="return confirm('¿Deseas eliminar esta cita?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                            Todavía no hay citas registradas.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php if (isset($_GET['add']) || $id > 0): ?>

        <section class="content-card" id="formulario">
            <h5 class="section-heading mb-4">
                <?= $id > 0 ? 'Editar cita' : 'Registrar nueva cita' ?>
            </h5>

            <form method="POST" action="citas.php">
                <input type="hidden" name="id" value="<?= $id ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre del cliente</label>
                        <input type="text" name="nombre_cliente"
                               class="form-control"
                               value="<?= htmlspecialchars($nombre_cliente) ?>"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono"
                               class="form-control"
                               value="<?= htmlspecialchars($telefono) ?>"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Servicio</label>
                        <select name="id_servicio" class="form-select" required>
                            <option value="">Seleccionar servicio</option>
                            <?php foreach ($services as $svc): ?>
                                <option value="<?= (int) $svc['id'] ?>"
                                    <?= $svc['id'] == $id_servicio ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($svc['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Empleado</label>
                        <select name="id_empleado" class="form-select" required>
                            <option value="">Seleccionar empleado</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= (int) $emp['id'] ?>"
                                    <?= $emp['id'] == $id_empleado ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($emp['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control"
                               value="<?= htmlspecialchars($fecha) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hora</label>
                        <input type="time" name="hora" class="form-control"
                               value="<?= htmlspecialchars($hora) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="pendiente" <?= $estado === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="confirmada" <?= $estado === 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
                            <option value="cancelada" <?= $estado === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                            <option value="completada" <?= $estado === 'completada' ? 'selected' : '' ?>>Completada</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        <?= $id > 0 ? 'Guardar cambios' : 'Registrar cita' ?>
                    </button>

                    <a href="citas.php" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </section>

    <?php endif; ?>

    <footer class="text-center text-muted small mt-5 mb-3">
        &copy; <?= date('Y') ?> Estilo & Belleza. Todos los derechos reservados.
    </footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
