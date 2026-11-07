
<?php
session_start();

require_once __DIR__ . '/../config/conexion.php';

// VERIFICAR ACCESO
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$error = '';

// CERRAR SESIÓN
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../auth/login.php');
    exit;
}

// AGREGAR USUARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {

    $nombre = trim($_POST['nombre'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'user';

    if ($nombre === '' || $usuario === '' || $password === '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!in_array($rol, ['admin', 'user'], true)) {
        $error = 'El rol seleccionado no es válido.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE usuario = ?');
        $stmt->execute([$usuario]);

        if ($stmt->fetch()) {
            $error = 'Ese nombre de usuario ya está registrado.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'INSERT INTO usuarios (nombre, usuario, password, rol)
                 VALUES (?, ?, ?, ?)'
            );

            $stmt->execute([$nombre, $usuario, $hash, $rol]);

            header('Location: usuarios.php?msg=agregado');
            exit;
        }
    }
}

// EDITAR USUARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {

    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'user';

    if ($id <= 0 || $nombre === '' || $usuario === '') {
        $error = 'Completa correctamente los campos.';
    } elseif (!in_array($rol, ['admin', 'user'], true)) {
        $error = 'El rol seleccionado no es válido.';
    } elseif ($password !== '' && strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($id === (int)$_SESSION['user_id'] && $rol !== 'admin') {
        $error = 'No puedes quitarte el rol de administrador mientras estás conectado.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id FROM usuarios WHERE usuario = ? AND id != ?'
        );
        $stmt->execute([$usuario, $id]);

        if ($stmt->fetch()) {
            $error = 'Ese nombre de usuario ya pertenece a otra cuenta.';
        } else {
            if ($password === '') {
                $stmt = $pdo->prepare(
                    'UPDATE usuarios
                     SET nombre = ?, usuario = ?, rol = ?
                     WHERE id = ?'
                );

                $stmt->execute([$nombre, $usuario, $rol, $id]);

            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare(
                    'UPDATE usuarios
                     SET nombre = ?, usuario = ?, password = ?, rol = ?
                     WHERE id = ?'
                );

                $stmt->execute([$nombre, $usuario, $hash, $rol, $id]);
            }

            if ($id === (int)$_SESSION['user_id']) {
                $_SESSION['user_name'] = $nombre;
            }

            header('Location: usuarios.php?msg=editado');
            exit;
        }
    }
}

// ELIMINAR USUARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {

    $id = (int)($_POST['id'] ?? 0);

    if ($id === (int)$_SESSION['user_id']) {
        $error = 'No puedes eliminar tu propia cuenta.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);

        if ($stmt->fetch()) {
            $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
            $stmt->execute([$id]);

            header('Location: usuarios.php?msg=eliminado');
            exit;
        } else {
            $error = 'El usuario no existe.';
        }
    }
}

// CONSULTAR USUARIOS
$stmt = $pdo->query(
    'SELECT id, nombre, usuario, rol FROM usuarios ORDER BY id ASC'
);
$usuarios = $stmt->fetchAll();

// CONSULTAR USUARIO PARA EDITAR
$usuarioEditar = null;

if (isset($_GET['edit'])) {
    $idEditar = (int)$_GET['edit'];

    $stmt = $pdo->prepare(
        'SELECT id, nombre, usuario, rol FROM usuarios WHERE id = ?'
    );
    $stmt->execute([$idEditar]);

    $usuarioEditar = $stmt->fetch();

    if (!$usuarioEditar) {
        $error = 'El usuario que intentas editar no existe.';
    }
}

$nombreAdmin = $_SESSION['user_name'] ?? 'Administrador';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de usuarios | Estilo & Belleza</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f5f9;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #263044;
        }

        /* MENÚ LATERAL */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #111827;
            position: fixed;
            top: 0;
            left: 0;
            padding: 25px 18px;
            color: white;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .brand {
            text-align: center;
            padding: 12px 5px 30px;
            border-bottom: 1px solid #374151;
        }

        .brand-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: #c5a46d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 25px;
            color: #111827;
        }

        .brand h4 {
            font-size: 19px;
            font-weight: 700;
            margin: 0;
        }

        .brand small {
            color: #c5a46d;
            letter-spacing: 2px;
            font-size: 10px;
        }

        .menu-title {
            color: #9ca3af;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 30px 12px 12px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 13px;
            color: #cbd5e1;
            text-decoration: none;
            padding: 13px 15px;
            border-radius: 10px;
            margin-bottom: 7px;
            transition: .3s;
            font-size: 14px;
        }

        .menu-link i {
            width: 20px;
            font-size: 16px;
        }

        .menu-link:hover,
        .menu-link.active {
            background: #c5a46d;
            color: #111827;
        }

        .sidebar-bottom {
            margin-top: auto;
            border-top: 1px solid #374151;
            padding-top: 18px;
        }

        .logout {
            color: #fca5a5;
        }

        .logout:hover {
            background: #7f1d1d;
            color: white;
        }

        /* CONTENIDO */
        .main-content {
            margin-left: 260px;
            padding: 30px 35px;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 15px;
        }

        .topbar h2 {
            font-size: 25px;
            font-weight: 700;
            margin: 0;
            color: #111827;
        }

        .topbar p {
            color: #8a93a5;
            margin: 5px 0 0;
            font-size: 14px;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            padding: 10px 16px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,.04);
        }

        .admin-avatar {
            width: 40px;
            height: 40px;
            background: #eee5d5;
            color: #987541;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .admin-info strong {
            font-size: 13px;
            display: block;
        }

        .admin-info small {
            color: #9ca3af;
            font-size: 11px;
        }

        /* TARJETAS */
        .panel {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,.035);
        }

        .panel-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 22px;
        }

        .panel-heading h4 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        .panel-heading p {
            color: #8a93a5;
            font-size: 13px;
            margin: 5px 0 0;
        }

        .btn-gold {
            background: #c5a46d;
            color: #111827;
            padding: 11px 18px;
            border-radius: 9px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            border: none;
            display: inline-block;
        }

        .btn-gold:hover {
            background: #b28e53;
            color: #111827;
        }

        /* TABLA */
        .table-responsive {
            border-radius: 10px;
        }

        .table {
            min-width: 650px;
            vertical-align: middle;
        }

        .table thead th {
            background: #1e293b;
            color: white;
            padding: 15px;
            font-size: 13px;
            font-weight: 600;
            border: none;
        }

        .table tbody td {
            padding: 15px;
            font-size: 13px;
            color: #475569;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #eee5d5;
            color: #987541;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            margin-right: 10px;
        }

        .role-admin {
            background: #f5ead8;
            color: #8a642c;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .role-user {
            background: #e2edff;
            color: #2563eb;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .btn-edit {
            background: #e2edff;
            color: #2563eb;
            border: none;
            padding: 8px 11px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 12px;
        }

        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            padding: 8px 11px;
            border-radius: 7px;
            font-size: 12px;
        }

        .btn-edit:hover {
            background: #2563eb;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: white;
        }

        /* FORMULARIO */
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #334155;
        }

        .form-control,
        .form-select {
            padding: 11px;
            border-radius: 9px;
            border: 1px solid #dbe0e8;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #c5a46d;
            box-shadow: 0 0 0 .2rem rgba(197,164,109,.15);
        }

        .btn-save {
            background: #1e293b;
            color: white;
            padding: 11px 20px;
            border: none;
            border-radius: 9px;
            font-weight: 600;
        }

        .btn-save:hover {
            background: #334155;
            color: white;
        }

        .btn-cancel {
            background: #e5e7eb;
            color: #374151;
            padding: 11px 20px;
            border-radius: 9px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }

        .footer {
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
            margin-top: 35px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 75px;
                padding: 20px 10px;
            }

            .brand h4,
            .brand small,
            .menu-title,
            .menu-link span,
            .sidebar-bottom span {
                display: none;
            }

            .brand-icon {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }

            .menu-link {
                justify-content: center;
                padding: 14px 5px;
            }

            .menu-link i {
                width: auto;
            }

            .main-content {
                margin-left: 75px;
                padding: 22px 15px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<!-- MENÚ LATERAL -->
<aside class="sidebar">

    <div class="brand">
        <div class="brand-icon">
            <i class="fa-solid fa-spa"></i>
        </div>

        <h4>Estilo & Belleza</h4>
        <small>ADMINISTRACIÓN</small>
    </div>

    <div class="menu-title">Menú principal</div>

    <a href="dashboard.php" class="menu-link">
        <i class="fa-solid fa-house"></i>
        <span>Inicio</span>
    </a>

    <a href="servicios.php" class="menu-link">
        <i class="fa-solid fa-scissors"></i>
        <span>Servicios</span>
    </a>

    <a href="empleados.php" class="menu-link">
        <i class="fa-solid fa-users"></i>
        <span>Empleados</span>
    </a>

    <a href="citas.php" class="menu-link">
        <i class="fa-solid fa-calendar-check"></i>
        <span>Gestión de citas</span>
    </a>

    <a href="usuarios.php" class="menu-link active">
        <i class="fa-solid fa-user-gear"></i>
        <span>Usuarios</span>
    </a>

    <div class="sidebar-bottom">
        <a href="usuarios.php?logout=1" class="menu-link logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Cerrar sesión</span>
        </a>
    </div>

</aside>


<!-- CONTENIDO PRINCIPAL -->
<main class="main-content">

    <div class="topbar">

        <div>
            <h2>Gestión de usuarios</h2>
            <p>Administra las cuentas y permisos del sistema.</p>
        </div>

        <div class="admin-info">
            <div class="admin-avatar">
                <i class="fa-solid fa-user"></i>
            </div>

            <div>
                <strong><?= htmlspecialchars($nombreAdmin) ?></strong>
                <small>Administrador</small>
            </div>
        </div>

    </div>


    <!-- MENSAJES -->
    <?php if (isset($_GET['msg'])): ?>

        <?php
        $mensajes = [
            'agregado' => 'Usuario agregado correctamente.',
            'editado' => 'Usuario actualizado correctamente.',
            'eliminado' => 'Usuario eliminado correctamente.'
        ];
        ?>

        <?php if (isset($mensajes[$_GET['msg']])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($mensajes[$_GET['msg']]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>


    <!-- LISTADO DE USUARIOS -->
    <section class="panel">

        <div class="panel-heading">

            <div>
                <h4>Usuarios registrados</h4>
                <p>
                    Total de cuentas:
                    <strong><?= count($usuarios) ?></strong>
                </p>
            </div>

            <a href="usuarios.php?add=1" class="btn-gold">
                <i class="fa-solid fa-plus me-2"></i>Agregar usuario
            </a>

        </div>

        <div class="table-responsive">

            <table class="table table-hover">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (count($usuarios) > 0): ?>

                    <?php foreach ($usuarios as $u): ?>

                        <tr>

                            <td><?= (int)$u['id'] ?></td>

                            <td>
                                <span class="user-avatar">
                                    <?= htmlspecialchars(strtoupper(substr($u['nombre'], 0, 1))) ?>
                                </span>

                                <strong>
                                    <?= htmlspecialchars($u['nombre']) ?>
                                </strong>
                            </td>

                            <td><?= htmlspecialchars($u['usuario']) ?></td>

                            <td>
                                <?php if ($u['rol'] === 'admin'): ?>
                                    <span class="role-admin">
                                        Administrador
                                    </span>
                                <?php else: ?>
                                    <span class="role-user">
                                        Usuario
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>

                                <div class="d-flex gap-2">

                                    <a href="usuarios.php?edit=<?= (int)$u['id'] ?>"
                                       class="btn-edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>

                                        <form method="POST"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">

                                            <input type="hidden" name="id"
                                                   value="<?= (int)$u['id'] ?>">

                                            <button type="submit" name="eliminar"
                                                    class="btn-delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                        </form>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="text-center py-4">
                            No hay usuarios registrados.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>


    <!-- FORMULARIO -->
    <?php if (isset($_GET['add']) || $usuarioEditar): ?>

        <?php
        $esEdicion = (bool)$usuarioEditar;

        $nombre = $usuarioEditar['nombre'] ?? '';
        $usuario = $usuarioEditar['usuario'] ?? '';
        $rol = $usuarioEditar['rol'] ?? 'user';
        ?>

        <section class="panel">

            <div class="panel-heading">

                <div>
                    <h4>
                        <?= $esEdicion ? 'Editar usuario' : 'Registrar nuevo usuario' ?>
                    </h4>
                    <p>Completa los datos de la cuenta.</p>
                </div>

            </div>

            <form method="POST">

                <?php if ($esEdicion): ?>
                    <input type="hidden" name="id"
                           value="<?= (int)$usuarioEditar['id'] ?>">
                <?php endif; ?>

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">Nombre completo</label>

                        <input type="text"
                               name="nombre"
                               class="form-control"
                               placeholder="Nombre completo"
                               value="<?= htmlspecialchars($nombre) ?>"
                               required>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">Nombre de usuario</label>

                        <input type="text"
                               name="usuario"
                               class="form-control"
                               placeholder="Nombre de usuario"
                               value="<?= htmlspecialchars($usuario) ?>"
                               required>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">Contraseña</label>

                        <input type="password"
                               name="password"
                               class="form-control"
                               minlength="6"
                               placeholder="<?= $esEdicion ? 'Dejar vacío para conservar la contraseña' : 'Mínimo 6 caracteres' ?>"
                               <?= $esEdicion ? '' : 'required' ?>>

                        <small class="text-muted">
                            <?= $esEdicion
                                ? 'Si no deseas cambiarla, deja el campo vacío.'
                                : 'La contraseña se guardará de forma segura.' ?>
                        </small>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">Rol del usuario</label>

                        <select name="rol" class="form-select" required>

                            <option value="admin"
                                <?= $rol === 'admin' ? 'selected' : '' ?>>
                                Administrador
                            </option>

                            <option value="user"
                                <?= $rol === 'user' ? 'selected' : '' ?>>
                                Usuario
                            </option>

                        </select>

                    </div>

                </div>

                <div class="mt-4 d-flex gap-2 flex-wrap">

                    <?php if ($esEdicion): ?>

                        <button type="submit" name="editar" class="btn-save">
                            <i class="fa-solid fa-floppy-disk me-2"></i>
                            Guardar cambios
                        </button>

                    <?php else: ?>

                        <button type="submit" name="agregar" class="btn-save">
                            <i class="fa-solid fa-user-plus me-2"></i>
                            Registrar usuario
                        </button>

                    <?php endif; ?>

                    <a href="usuarios.php" class="btn-cancel">
                        Cancelar
                    </a>

                </div>

            </form>

        </section>

    <?php endif; ?>


    <footer class="footer">
        © <?= date('Y') ?> Estilo & Belleza. Todos los derechos reservados.
    </footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
