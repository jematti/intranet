<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");
session_start();

if (isset($_POST['section_id'])) {
    $section_id = $_POST['section_id'];
    $user_id = $_SESSION['user_id'] ?? null;

    // Obtener el estado actual
    $query = mysqli_query($conn, "SELECT status, section_name FROM sections WHERE section_id = '$section_id'");
    $fetch = mysqli_fetch_assoc($query);
    $current_status = $fetch['status'];
    $section_name = $fetch['section_name'];
    $new_status = $current_status == 1 ? 0 : 1;
    $status_text = $new_status == 1 ? 'habilitada' : 'deshabilitada';

    // Actualizar el estado
    $update_query = mysqli_query($conn, "UPDATE sections SET status = '$new_status' WHERE section_id = '$section_id'");

    if ($update_query) {
        // Registrar en la bitácora
       // if ($user_id) {
            $action = "status_change";
            $details = "La sección '$section_name' (ID: $section_id) fue $status_text.";
            $log_query = "INSERT INTO audit_log (user_id, action, entity, entity_id, details) 
                          VALUES ('$user_id', '$action', 'sections', '$section_id', '$details')";
            mysqli_query($conn, $log_query);
        //}

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
}
?>
