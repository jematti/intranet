<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include("conexion_db.php");

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
                    <button class="btn btn-info text-white font-weight-bold rounded-pill ml-2 shadow" data-toggle="modal" data-target="#newsModal" style="padding: 8px 10px; font-size: 16px;">
                        <i class="fas fa-newspaper mr-2"></i> Noticias del día
                    </button>                    
                </li>
                <!-- Botón de Ver Tutorial -->
                <li class="nav-item">
                    <button class="btn btn-danger text-white font-weight-bold rounded-pill ml-2 shadow" data-toggle="modal" data-target="#videoModal" style="padding: 8px 10px; font-size: 16px;">
                        <i class="fas fa-play-circle mr-2"></i> Ver Tutorial
                    </button>
                </li>

                <?php if ($userLoggedIn): ?>
                    <!-- Botón de administración o perfil del usuario -->
                    <li class="nav-item ml-2">
                        <?php if ($role_id == 1 || $role_id == 2 || $role_id == 4): ?>
                            <a class="btn btn-primary rounded-pill text-white font-weight-bold shadow" href="dashboard.php" style="padding: 8px 10px; font-size: 16px;">
                                <i class="fas fa-tools mr-2"></i> Administración
                            </a>
                        <?php else: ?>
                            <a class="btn btn-success rounded-pill text-white font-weight-bold shadow" href="perfil_user.php" style="padding: 8px 10px; font-size: 16px;">
                                <i class="fas fa-user-edit mr-2"></i> Modificar Perfil
                            </a>
                        <?php endif; ?>
                    </li>
                    <!-- Dropdown del usuario logueado -->
                    <li class="nav-item dropdown ml-2">
                        <a class="btn btn-light rounded-pill text-dark dropdown-toggle shadow" href="#" id="navbarDropdownUser" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 10px 20px; font-size: 16px;">
                            <i class="fas fa-user-circle mr-2"></i> <?php echo htmlspecialchars($userName); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="navbarDropdownUser" style="background-color: #ffffff; border-radius: 8px;">
                            <a class="dropdown-item text-dark" href="logout.php">
                                <i class="fas fa-sign-out-alt mr-2"></i> Salir
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item ml-2">
                        <a class="btn btn-primary text-white font-weight-bold shadow" href="login.php" style="padding: 8px 10px; font-size: 16px;">
                            <i class="fas fa-sign-in-alt mr-2"></i> Acceder
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Modal para reproducir el video -->
<div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(to right, #2c83c6, #1b5c8e);">
                <h5 class="modal-title text-white" id="videoModalLabel">Tutorial: Manejo de la Intranet</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: #f9f9f9;">
                <video controls class="w-100" style="border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                    <source src="assets/videos/intranet_empleados.mp4" type="video/mp4">
                    Tu navegador no soporta la reproducción de videos.
                </video>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

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
