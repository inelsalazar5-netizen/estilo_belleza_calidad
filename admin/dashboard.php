
<?php
session_start();

require_once __DIR__ . '/../config/conexion.php';

// Verificar sesión
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Cerrar sesión
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../auth/login.php');
    exit;
}

// Obtener estadísticas
$totalServicios = $pdo->query("SELECT COUNT(*) FROM servicios")->fetchColumn();
$totalEmpleados = $pdo->query("SELECT COUNT(*) FROM empleados")->fetchColumn();
$totalCitas = $pdo->query("SELECT COUNT(*) FROM citas")->fetchColumn();
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

$nombreAdmin = $_SESSION['user_name'] ?? 'Administrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel Administrativo | Estilo & Belleza</title>

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

        .main-content {
            margin-left: 260px;
            padding: 30px 35px;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
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
            font-size: 17px;
        }

        .admin-info strong {
            font-size: 13px;
            display: block;
        }

        .admin-info small {
            color: #9ca3af;
            font-size: 11px;
        }

        .welcome-box {
            background: linear-gradient(110deg, #1e293b, #334155);
            color: white;
            padding: 30px;
            border-radius: 18px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .welcome-box h3 {
            font-weight: 700;
            font-size: 23px;
            margin-bottom: 9px;
        }

        .welcome-box p {
            color: #d1d5db;
            margin: 0;
            font-size: 14px;
        }

        .welcome-box .welcome-icon {
            position: absolute;
            right: 35px;
            top: 10px;
            font-size: 95px;
            color: rgba(197,164,109,.25);
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #263044;
        }

        .stat-card {
            background: white;
            border: none;
            border-radius: 16px;
            padding: 23px;
            height: 100%;
            box-shadow: 0 4px 20px rgba(0,0,0,.035);
            transition: .3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,.08);
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            font-size: 20px;
        }

        .icon-gold {
            background: #f5ead8;
            color: #a47b3f;
        }

        .icon-blue {
            background: #e2edff;
            color: #3975c9;
        }

        .icon-green {
            background: #dcf5e8;
            color: #26965b;
        }

        .icon-purple {
            background: #eee5ff;
            color: #8053c7;
        }

        .stat-card h3 {
            font-size: 30px;
            font-weight: 700;
            margin: 0;
            color: #111827;
        }

        .stat-card p {
            color: #8a93a5;
            margin: 5px 0 0;
            font-size: 13px;
        }

        .quick-card {
            background: white;
            border-radius: 15px;
            padding: 22px;
            text-decoration: none;
            color: #263044;
            display: block;
            height: 100%;
            box-shadow: 0 4px 20px rgba(0,0,0,.035);
            transition: .3s;
        }

        .quick-card:hover {
            transform: translateY(-4px);
            color: #a47b3f;
        }

        .quick-card i {
            font-size: 23px;
            color: #c5a46d;
            margin-bottom: 14px;
        }

        .quick-card h6 {
            font-weight: 700;
            margin-bottom: 6px;
        }

        .quick-card p {
            font-size: 12px;
            color: #8a93a5;
            margin: 0;
        }

        .footer {
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
            margin-top: 45px;
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

            .welcome-box .welcome-icon {
                display: none;
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

    <a href="dashboard.php" class="menu-link active">
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

    <a href="usuarios.php" class="menu-link">
        <i class="fa-solid fa-user-gear"></i>
        <span>Usuarios</span>
    </a>

    <div class="sidebar-bottom">
        <a href="dashboard.php?logout=1" class="menu-link logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Cerrar sesión</span>
        </a>
    </div>

</aside>


<!-- CONTENIDO PRINCIPAL -->
<main class="main-content">

    <div class="topbar">

        <div>
            <h2>Panel principal</h2>
            <p>Resumen general de tu salón de belleza</p>
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


    <!-- BIENVENIDA -->
    <section class="welcome-box">

        <h3>¡Bienvenido, <?= htmlspecialchars($nombreAdmin) ?>!</h3>

        <p>
            Administra tu salón de belleza desde un solo lugar.
            Aquí puedes controlar los servicios, empleados y citas.
        </p>

        <i class="fa-solid fa-spa welcome-icon"></i>

    </section>


    <!-- ESTADÍSTICAS -->
    <h5 class="section-title">Resumen del negocio</h5>

    <div class="row g-4 mb-5">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">

                <div class="stat-top">
                    <div>
                        <p>Servicios registrados</p>
                        <h3><?= $totalServicios ?></h3>
                    </div>

                    <div class="stat-icon icon-gold">
                        <i class="fa-solid fa-scissors"></i>
                    </div>
                </div>

            </div>
        </div>


        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">

                <div class="stat-top">
                    <div>
                        <p>Empleados registrados</p>
                        <h3><?= $totalEmpleados ?></h3>
                    </div>

                    <div class="stat-icon icon-blue">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>

            </div>
        </div>


        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">

                <div class="stat-top">
                    <div>
                        <p>Citas registradas</p>
                        <h3><?= $totalCitas ?></h3>
                    </div>

                    <div class="stat-icon icon-green">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>

            </div>
        </div>


        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">

                <div class="stat-top">
                    <div>
                        <p>Usuarios registrados</p>
                        <h3><?= $totalUsuarios ?></h3>
                    </div>

                    <div class="stat-icon icon-purple">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                </div>

            </div>
        </div>

    </div>


    <!-- ACCESOS RÁPIDOS -->
    <h5 class="section-title">Accesos rápidos</h5>

    <div class="row g-4">

        <div class="col-12 col-md-6 col-xl-3">
            <a href="servicios.php" class="quick-card">
                <i class="fa-solid fa-scissors"></i>
                <h6>Gestionar servicios</h6>
                <p>Agregar, editar y eliminar servicios.</p>
            </a>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <a href="empleados.php" class="quick-card">
                <i class="fa-solid fa-users"></i>
                <h6>Gestionar empleados</h6>
                <p>Administrar el personal del salón.</p>
            </a>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <a href="citas.php" class="quick-card">
                <i class="fa-solid fa-calendar-check"></i>
                <h6>Gestionar citas</h6>
                <p>Revisar y administrar las citas.</p>
            </a>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <a href="usuarios.php" class="quick-card">
                <i class="fa-solid fa-user-gear"></i>
                <h6>Gestionar usuarios</h6>
                <p>Administrar las cuentas del sistema.</p>
            </a>
        </div>

    </div>


    <footer class="footer">
        © <?= date('Y') ?> Estilo & Belleza. Todos los derechos reservados.
    </footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
