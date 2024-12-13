<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");
session_start(); // Asegúrate de que la sesión esté iniciada

if (isset($_POST['repository_id']) && isset($_POST['status'])) {
    $repository_id = $_POST['repository_id'];
    $new_status = $_POST['status'] == 1 ? 0 : 1;
    $status_text = $new_status == 1 ? 'habilitado' : 'deshabilitado';

    // Actualizar el estado en la base de datos
    $query = "UPDATE repositories SET status = '$new_status' WHERE repository_id = '$repository_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Registrar en la bitácora
        $user_id = $_SESSION['user_id'] ?? null; // ID del usuario que realiza la acción
        if ($user_id) {
            $action = "status_change";
            $details = "El repositorio ID $repository_id fue $status_text.";
            $log_sql = "INSERT INTO audit_log (user_id, action, entity, entity_id, details) 
                        VALUES ('$user_id', '$action', 'repositories', '$repository_id', '$details')";
            if (!mysqli_query($conn, $log_sql)) {
                error_log("Error al insertar en audit_log: " . mysqli_error($conn));
            }
        } else {
            error_log("ID de usuario no disponible para registrar en audit_log.");
        }

        // Respuesta JSON con instrucción de recarga
        echo json_encode(['success' => true]);
    } else {
        error_log("Error al actualizar estado: " . mysqli_error($conn));
        echo json_encode(['success' => false]);
    }
}
?>
