<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_POST['section_id'])) {
    $section_id = $_POST['section_id'];

    // Obtener el estado actual
    $query = mysqli_query($conn, "SELECT status FROM sections WHERE section_id = '$section_id'");
    $fetch = mysqli_fetch_assoc($query);
    $new_status = $fetch['status'] == 1 ? 0 : 1;

    // Actualizar el estado
    mysqli_query($conn, "UPDATE sections SET status = '$new_status' WHERE section_id = '$section_id'");
    echo json_encode(['success' => true]);
}
