<?php
session_start();
include("conexion_db.php");

$message = ""; // Variable para el mensaje

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $message = "<div class='alert alert-success text-center' role='alert'>Sesión finalizada correctamente.</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = md5($_POST['password']); // Encriptamos la contraseña con MD5

    $stmt = $conn->prepare("SELECT user_id, firstname, lastname, role_id FROM `user` WHERE `username` = ? AND `password` = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $firstname, $lastname, $role_id);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        // Iniciar sesión
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $firstname . ' ' . $lastname;
        $_SESSION['role_id'] = $role_id;

        // Registrar evento de login en la bitácora
        $stmt_log = $conn->prepare("INSERT INTO `audit_log` (`user_id`, `action`, `ip_address`, `details`) VALUES (?, 'login', ?, 'Inicio de sesión exitoso')");
        $stmt_log->bind_param("is", $user_id, $_SERVER['REMOTE_ADDR']);
        $stmt_log->execute();
        $stmt_log->close();

        header("Location: index.php");
        exit();
    } else {
        $message = "<div class='alert alert-danger text-center' role='alert'>Credenciales incorrectas.</div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #4A90E2;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #357ABD;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 70%;
            transform: translateY(-50%);
            color: #6c757d;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .password-toggle:hover {
            color: #343a40;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Ingreso Usuario</h1>
        </div>
        <!-- Mostrar mensaje de alerta -->
        <?php if (!empty($message)) echo $message; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Ingrese su usuario" required autofocus>
            </div>
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Ingrese su contraseña" required>
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
            </div>
            <!-- Botón para acceder -->
            <button type="submit" class="btn btn-primary w-100 mb-3" name="login">Acceder</button>
            <!-- Botón para redirigir a la página de inicio -->
            <a href="index.php" class="btn btn-secondary w-100">Ir a la Página de Inicio</a>
        </form>

    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
