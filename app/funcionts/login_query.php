<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Encriptamos la contraseña usando MD5
    
    // Evitar inyección SQL
    $username = mysqli_real_escape_string($conn, $username);

    // Preparar y ejecutar la consulta para buscar el usuario
    $stmt = $conn->prepare("SELECT user_id, firstname, lastname, role_id FROM `user` WHERE `username` = ? AND `password` = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $firstname, $lastname, $role_id);
    $stmt->fetch();
    $row = $stmt->num_rows;

    if ($row > 0) {
        // Guardar datos en la sesión
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $firstname . ' ' . $lastname;
        $_SESSION['role_id'] = $role_id;

        // Redirigir al index.php
        header("Location: index.php");
        exit();
    } else {
        echo "<center><label class='text-danger'>Usuario o Contraseña Incorrecta</label></center>";
    }

    $stmt->close();
}
?>
