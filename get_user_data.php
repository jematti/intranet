<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    
    // Consulta para obtener los datos del usuario
    $query = mysqli_query($conn, "SELECT * FROM `user` WHERE `user_id` = $user_id");
    $user = mysqli_fetch_assoc($query);
    
    // Enviar los datos en formato JSON
    echo json_encode($user);
}
?>
