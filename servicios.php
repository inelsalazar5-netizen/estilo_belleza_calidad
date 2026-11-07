
<?php
$pageTitle = 'Nuestros Servicios';

// Conectar con la base de datos
require_once __DIR__ . '/config/conexion.php';

// Obtener los servicios registrados
$sql = "SELECT id, nombre, descripcion, precio, imagen
        FROM servicios
        ORDER BY id ASC";

$stmt = $pdo->query($sql);
$services = $stmt->fetchAll();

// Incluir el encabezado
require_once __DIR__ . '/includes/header.php';
?>

<main class="container mt-5 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold">Nuestros Servicios</h2>
            <p class="text-muted">
                Conoce los servicios que tenemos preparados para ti.
            </p>
        </div>

        <a href="citas.php" class="btn btn-primary">
            Solicitar Cita
        </a>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

        <?php if (count($services) > 0): ?>

            <?php foreach ($services as $service): ?>

                <div class="col">
                    <div class="card h-100 shadow-sm">

                        <?php if (!empty($service['imagen'])): ?>

                            <img
                                src="assets/img/<?= htmlspecialchars($service['imagen']) ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($service['nombre']) ?>"
                                style="height: 220px; object-fit: cover;"
                            >

                        <?php else: ?>

                            <div class="d-flex justify-content-center align-items-center bg-light"
                                 style="height: 220px;">
                                <span class="text-muted">
                                    Imagen no disponible
                                </span>
                            </div>

                        <?php endif; ?>

                        <div class="card-body">

                            <h5 class="card-title fw-bold">
                                <?= htmlspecialchars($service['nombre']) ?>
                            </h5>

                            <p class="card-text">
                                <?= nl2br(htmlspecialchars($service['descripcion'])) ?>
                            </p>

                            <p class="fw-bold text-primary">
                                Precio: C$ <?= htmlspecialchars($service['precio']) ?>
                            </p>

                        </div>

                    </div>
                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="col-12">
                <div class="alert alert-info text-center">
                    Actualmente no hay servicios registrados.
                </div>
            </div>

        <?php endif; ?>

    </div>

</main>

<?php
// Incluir el pie de página
require_once __DIR__ . '/includes/footer.php';
?>
