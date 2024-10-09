<?php
    // Verifica si una sesión ya ha sido iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica si la sesión de usuario está establecida
    //if(!isset($_SESSION['user'])){
    //    header("location: index.php");
    //    exit(); // Asegura que el script se detiene después de redirigir
    //}
?>
