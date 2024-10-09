<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5(mysqli_real_escape_string($conn, $_POST['password']));

    // Consulta para verificar el nombre de usuario, contraseña y estado activo
    $query = mysqli_query($conn, "SELECT * FROM `user` WHERE `username` = '$username' AND `password` = '$password'") or die(mysqli_error($conn));
    $fetch = mysqli_fetch_array($query);
    $row = $query->num_rows;

    if ($row > 0) {
        // Verificar si el usuario está activo
        if ($fetch['active_status'] == 1) {
            $_SESSION['user'] = $fetch['user_id'];
            $_SESSION['status'] = $fetch['status'];
            header("location:admin_main.php");
        } else {
            echo "<center><label class='text-danger'>Cuenta desactivada. Contacte al administrador.</label></center>";
        }
    } else {
        echo "<center><label class='text-danger'>Contraseña o Usuario Incorrecto</label></center>";
    }
}
?>
