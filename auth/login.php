
<?php
session_start();

require_once __DIR__ . '/../config/conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario !== '' && $password !== '') {

        $stmt = $pdo->prepare(
            "SELECT id, nombre, rol, password
             FROM usuarios
             WHERE usuario = ?"
        );

        $stmt->execute([$usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_role'] = $user['rol'];

            // Si es administrador, entra al panel principal
            if ($user['rol'] === 'admin') {
                header('Location: ../admin/dashboard.php');
                exit;
            }

            // Si es usuario normal, entra al inicio
            header('Location: ../index.php');
            exit;

        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }

    } else {
        $error = 'Por favor, complete todos los campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Acceso administrativo - Estilo & Belleza</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .login-card {
            background: white;
            width: 100%;
            max-width: 420px;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0,0,0,.25);
        }

        .login-card h2 {
            color: #1e3a8a;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        .login-card p {
            text-align: center;
            color: #64748b;
            margin-bottom: 25px;
        }

        .form-control {
            padding: 12px;
            border-radius: 9px;
        }

        .btn-login {
            background: #1e3a8a;
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 9px;
            font-weight: bold;
        }

        .btn-login:hover {
            background: #3b82f6;
            color: white;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #1e3a8a;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="login-card">

        <h2>Estilo & Belleza</h2>

        <p>Ingresa tus datos para acceder al sistema</p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="mb-3">
                <label class="form-label">Usuario</label>

                <input
                    type="text"
                    name="usuario"
                    class="form-control"
                    placeholder="Ingresa tu usuario"
                    required
                >
            </div>

            <div class="mb-4">
                <label class="form-label">Contraseña</label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Ingresa tu contraseña"
                    required
                >
            </div>

            <button type="submit" class="btn btn-login">
                Iniciar sesión
            </button>

        </form>

        <a href="../index.php" class="back-link">
            Volver al inicio
        </a>

    </div>

</body>
</html>
