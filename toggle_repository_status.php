<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");

if (isset($_POST['repository_id']) && isset($_POST['status'])) {
    $repository_id = $_POST['repository_id'];
    $new_status = $_POST['status'] == 1 ? 0 : 1;

    // Actualizar el estado en la base de datos
    $query = "UPDATE repositories SET status = '$new_status' WHERE repository_id = '$repository_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
