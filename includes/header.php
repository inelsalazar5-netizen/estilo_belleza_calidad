
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Estilo & Belleza - servicios profesionales de belleza y cuidado personal.">

    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>Estilo & Belleza</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fuentes elegantes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="assets/css/style.css?v=3">
</head>

<body>

<!-- MENÚ PRINCIPAL -->
<nav class="navbar navbar-expand-lg site-navbar">
    <div class="container">

        <!-- LOGOTIPO -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <span class="brand-mark">E&B</span>

            <span class="brand-name">
                Estilo <span class="brand-amp">&amp;</span> Belleza
            </span>
        </a>

        <!-- BOTÓN PARA CELULARES -->
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Abrir menú">

            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- ENLACES -->
        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        Inicio
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="servicios.php">
                        Servicios
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="nosotros.php">
                        Nosotros
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="contacto.php">
                        Contacto
                    </a>
                </li>

                <!-- ÚNICO ACCESO AL PANEL -->
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-login-nav" href="auth/login.php">
                        Iniciar sesión
                    </a>
                </li>

            </ul>

        </div>
    </div>
</nav>
