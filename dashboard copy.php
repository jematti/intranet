<?php
// Iniciar la sesión solo si no ha sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'app/funcionts/admin/validator.php';
include("conexion_db.php");
include_once 'app/complements/header.php';
?>

<!-- navegador principal -->
<?php include 'app/complements/navbar-main.php'; ?>
<!-- fin navegador principal -->

<!-- barra de navegación lateral -->
<?php include 'app/funcionts/sidebar.php'; ?>
<!-- fin de barra de navegación lateral -->

<!-- contenido -->
<main role="main" class="main-content">
    <!-- Contenido del dashboard -->
</main>

<?php
include_once 'app/complements/footer.php';
?>
