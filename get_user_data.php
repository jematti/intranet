<?php
require $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    
    $query = mysqli_query($conn, "SELECT * FROM `user` WHERE `user_id` = $user_id") or die(mysqli_error($conn));
    
    $user = mysqli_fetch_assoc($query);
    
    echo json_encode($user);  // Devolver todos los datos del usuario en formato JSON, incluyendo `section_id`
}
?>
