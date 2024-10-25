<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

$userLoggedIn = false;
$userName = '';
$role_id = 0;

// Verificar si un usuario está logueado
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, role_id FROM `user` WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $role_id);
    $stmt->fetch();
    $userName = $username;
    $userLoggedIn = true;
    $stmt->close();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2c83c6; z-index: 1001; position: fixed; top: 0; left: 0; width: 100%;">
    <div class="container-fluid">
        <!-- Logo y título de la intranet -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/images/logo.jpeg" alt="Logo" style="height: 40px; border-radius: 10px;">
            <span style="color: #ffffff; font-weight: bold; margin-left: 10px;">FC-BCB</span>
        </a>

        <!-- Botón de menú para pantallas pequeñas -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">

                <!-- Botón de Página Principal -->
                <li class="nav-item">
                    <a class="btn btn-light text-dark rounded-pill ml-2 " href="index.php">
                        <i class="fe fe-home fe-16 mr-2 mb-1"></i> Página Principal
                    </a>
                </li>
                <!-- Botón de Noticias del día -->
                <li class="nav-item">
                    <button class="btn btn-light text-dark rounded-pill ml-2" data-toggle="modal" data-target="#newsModal">
                        Noticias del día
                    </button>                    
                </li>

                <?php if ($userLoggedIn): ?>
                    <!-- Botón de administración si el usuario tiene el rol adecuado -->
                    <li class="nav-item ml-2">
                        <?php if ($role_id == 1 || $role_id == 2 || $role_id == 4): ?>
                            <a class="btn btn-light rounded-pill text-dark" href="dashboard.php">Administración</a>
                        <?php else: ?>
                            <a class="btn btn-light rounded-pill text-dark" href="perfil_user.php">Modificar Perfil</a>
                        <?php endif; ?>
                    </li>
                    <!-- Dropdown del usuario logueado -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Usuario: <?php echo htmlspecialchars($userName); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownUser" style="background-color: #ffffff;">
                            <a class="dropdown-item" href="logout.php">Salir</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php" style="color: #ffffff;">Acceder</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


<!-- CSS para el switcher de modo oscuro/claro -->
<style>
    

    /* Alineación del switcher y el botón de administración */
    .navbar-nav .nav-item {
        display: flex;
        align-items: center; /* Alinea verticalmente los elementos dentro del navbar */
    }

    .navbar-nav .nav-link {
        padding: 6px 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .navbar-nav .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        margin-left: 10px;
    }

    .navbar-nav .nav-link.dropdown-toggle {
        padding-left: 12px;
        padding-right: 12px;
    }

    .btn-warning {
    background-color: #ffffff;
    color: #000;
    font-weight: bold;
    }

    .btn-warning:hover {
        background-color: #ffffff;
    }
    

</style>
