<?php
include $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_POST['edit'])) {
    $user_id = $_POST['user_id'];
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role_id = intval($_POST['role_id']);  // Asegúrate de que role_id sea un entero
    $password = !empty($_POST['password']) ? md5($_POST['password']) : null;

    // Actualizar la información del usuario
    if ($password) {
        $query = "UPDATE `user` SET `firstname` = '$firstname', `lastname` = '$lastname', `username` = '$username', `role_id` = '$role_id', `password` = '$password' WHERE `user_id` = '$user_id'";
    } else {
        $query = "UPDATE `user` SET `firstname` = '$firstname', `lastname` = '$lastname', `username` = '$username', `role_id` = '$role_id' WHERE `user_id` = '$user_id'";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Usuario actualizado exitosamente'); window.location = 'admin_user.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el usuario'); window.location = 'admin_user.php';</script>";
    }
}
mysqli_close($conn);
?>
