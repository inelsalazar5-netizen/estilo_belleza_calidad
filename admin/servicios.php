
<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Cerrar sesión
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit;
}

// Eliminar servicio
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $pdo->prepare('DELETE FROM servicios WHERE id = ?');
    $stmt->execute([$id]);

    header('Location: servicios.php?msg=eliminado');
    exit;
}

// Registrar o actualizar servicio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = $_POST['precio'] ?? '';
    $imagen = trim($_POST['imagen'] ?? '');

    if ($nombre === '' || $descripcion === '' || $precio === '' || !is_numeric($precio)) {
        header('Location: servicios.php?msg=error');
        exit;
    }

    if ($id > 0) {
        $stmt = $pdo->prepare(
            'UPDATE servicios
             SET nombre = ?, descripcion = ?, precio = ?, imagen = ?
             WHERE id = ?'
        );

        $stmt->execute([$nombre, $descripcion, $precio, $imagen, $id]);

        header('Location: servicios.php?msg=editado');
        exit;
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO servicios (nombre, descripcion, precio, imagen)
             VALUES (?, ?, ?, ?)'
        );

        $stmt->execute([$nombre, $descripcion, $precio, $imagen]);

        header('Location: servicios.php?msg=agregado');
        exit;
    }
}

// Obtener servicio para editar
$idEditar = (int) ($_GET['edit'] ?? 0);

$nombre = '';
$descripcion = '';
$precio = '';
$imagen = '';

if ($idEditar > 0) {
    $stmt = $pdo->prepare('SELECT * FROM servicios WHERE id = ?');
    $stmt->execute([$idEditar]);
    $servicioEditar = $stmt->fetch();

    if ($servicioEditar) {
        $nombre = $servicioEditar['nombre'];
        $descripcion = $servicioEditar['descripcion'];
        $precio = $servicioEditar['precio'];
        $imagen = $servicioEditar['imagen'] ?? '';
    } else {
        $idEditar = 0;
    }
}

// Consultar todos los servicios
$stmt = $pdo->query('SELECT * FROM servicios ORDER BY id DESC');
$servicios = $stmt->fetchAll();

$totalServicios = count($servicios);

$mensajes = [
    'agregado' => 'El servicio se registró correctamente.',
    'editado' => 'El servicio se actualizó correctamente.',
    'eliminado' => 'El servicio se eliminó correctamente.',
    'error' => 'Verifica que todos los campos estén completos y que el precio sea válido.'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Administración de Servicios | Estilo & Belleza</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --navy: #101b38;
    --blue: #3457a5;
    --background: #f4f6fb;
}

body {
    margin: 0;
    background: var(--background);
    color: #475569;
    font-family: "Segoe UI", Arial, sans-serif;
}

.sidebar {
    width: 255px;
    min-height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: linear-gradient(180deg, #101b38, #1d3568);
    color: white;
    padding: 28px 18px;
}

.brand {
    font-size: 20px;
    font-weight: 800;
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
    transition: .2s;
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

.page-title {
    color: #152448;
    font-weight: 750;
    font-size: 28px;
}

.welcome {
    color: #8994a8;
    font-size: 14px;
}

.profile {
    background: white;
    padding: 11px 17px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(20,40,80,.05);
    color: #263b68;
}

.summary-card,
.content-card {
    background: white;
    border: 1px solid #edf0f6;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(25,45,90,.05);
}

.summary-card {
    padding: 23px;
}

.summary-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: #e9efff;
    color: #3457a5;
    font-size: 21px;
    margin-bottom: 15px;
}

.summary-card h3 {
    color: #18294e;
    font-weight: 750;
    font-size: 27px;
    margin: 0;
}

.summary-card p {
    color: #8994a8;
    margin: 3px 0 0;
    font-size: 14px;
}

.content-card {
    padding: 25px;
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
    padding: 14px 12px;
    vertical-align: middle;
    font-size: 13px;
}

.table tbody tr:hover { background: #f7f9fd; }

.service-thumb {
    width: 55px;
    height: 45px;
    object-fit: cover;
    border-radius: 8px;
}

.form-control {
    border-radius: 9px;
    padding: 11px;
    border-color: #dce3ef;
}

.form-control:focus {
    border-color: #6488d8;
    box-shadow: 0 0 0 3px rgba(100,136,216,.15);
}

.form-label {
    font-weight: 600;
    color: #354666;
}

.btn-primary {
    background: var(--blue);
    border-color: var(--blue);
}

.btn-primary:hover {
    background: #263f7c;
    border-color: #263f7c;
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

    .brand span { font-size: 18px; }

    .menu-title,
    .link-text { display: none; }

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
    .main-content { padding: 18px 10px; }
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

    <a href="servicios.php" class="active">
        <i class="bi bi-scissors"></i>
        <span class="link-text">Servicios</span>
    </a>

    <a href="empleados.php">
        <i class="bi bi-people"></i>
        <span class="link-text">Empleados</span>
    </a>

    <a href="citas.php">
        <i class="bi bi-calendar-check"></i>
        <span class="link-text">Citas</span>
    </a>

    <a href="usuarios.php">
        <i class="bi bi-person-lock"></i>
        <span class="link-text">Usuarios</span>
    </a>

    <div class="menu-title">Cuenta</div>

    <a href="servicios.php?logout=1" class="logout">
        <i class="bi bi-box-arrow-left"></i>
        <span class="link-text">Cerrar sesión</span>
    </a>

</aside>

<main class="main-content">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Administración de Servicios</h1>
            <p class="welcome mb-0">Gestiona los servicios de Estilo & Belleza.</p>
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
        <div class="col-md-6 col-xl-4">
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="bi bi-scissors"></i>
                </div>
                <h3><?= $totalServicios ?></h3>
                <p>Servicios registrados</p>
            </div>
        </div>
    </div>

    <section class="content-card">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="section-heading mb-1">Listado de servicios</h5>
                <p class="text-muted small mb-0">Administra los servicios disponibles para tus clientes.</p>
            </div>

            <a href="servicios.php?add=1" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Agregar servicio
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($totalServicios > 0): ?>

                    <?php foreach ($servicios as $servicio): ?>
                        <tr>
                            <td><?= (int) $servicio['id'] ?></td>

                            <td>
                                <?php if (!empty($servicio['imagen'])): ?>
                                    <img
                                        class="service-thumb"
                                        src="../assets/img/<?= htmlspecialchars($servicio['imagen']) ?>"
                                        alt="Imagen del servicio">
                                <?php else: ?>
                                    <i class="bi bi-image text-muted fs-3"></i>
                                <?php endif; ?>
                            </td>

                            <td class="fw-semibold">
                                <?= htmlspecialchars($servicio['nombre']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($servicio['descripcion']) ?>
                            </td>

                            <td class="fw-bold text-primary">
                                C$ <?= htmlspecialchars($servicio['precio']) ?>
                            </td>

                            <td>
                                <div class="d-flex gap-2">
                                    <a href="servicios.php?edit=<?= (int) $servicio['id'] ?>"
                                       class="btn btn-sm btn-warning"
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <a href="servicios.php?delete=<?= (int) $servicio['id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       title="Eliminar"
                                       onclick="return confirm('¿Seguro que deseas eliminar este servicio?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-scissors fs-2 d-block mb-2"></i>
                            No hay servicios registrados.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php if (isset($_GET['add']) || $idEditar > 0): ?>

        <section class="content-card" id="formulario">

            <h5 class="section-heading mb-4">
                <?= $idEditar > 0 ? 'Editar servicio' : 'Registrar nuevo servicio' ?>
            </h5>

            <form method="POST" action="servicios.php">

                <input type="hidden" name="id" value="<?= $idEditar ?>">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre del servicio</label>
                        <input type="text" name="nombre"
                               class="form-control"
                               value="<?= htmlspecialchars($nombre) ?>"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Precio (C$)</label>
                        <input type="number" name="precio"
                               class="form-control"
                               step="0.01" min="0"
                               value="<?= htmlspecialchars((string) $precio) ?>"
                               required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  class="form-control"
                                  rows="4" required><?= htmlspecialchars($descripcion) ?></textarea>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Nombre del archivo de imagen</label>
                        <input type="text" name="imagen"
                               class="form-control"
                               value="<?= htmlspecialchars($imagen) ?>"
                               placeholder="Ejemplo: corte.jpg">
                        <small class="text-muted">
                            Escribe el nombre del archivo que está guardado en assets/img.
                        </small>
                    </div>

                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        <?= $idEditar > 0 ? 'Guardar cambios' : 'Registrar servicio' ?>
                    </button>

                    <a href="servicios.php" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>

            </form>
        </section>

    <?php endif; ?>

    <footer class="text-center text-muted small mt-5 mb-3">
        &copy; <?= date('Y') ?> Estilo &amp; Belleza. Todos los derechos reservados.
    </footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
