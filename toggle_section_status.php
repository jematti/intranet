<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

try {
    if (isset($_POST['section_id'])) {
        $section_id = intval($_POST['section_id']);
        $logged_user_id = $_SESSION['user_id'] ?? null;

        if (!$logged_user_id) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
            exit();
        }

        // Obtener el estado actual de la sección
        $query = mysqli_query($conn, "SELECT status, section_name FROM sections WHERE section_id = '$section_id'");
        if ($fetch = mysqli_fetch_assoc($query)) {
            $current_status = $fetch['status'];
            $section_name = $fetch['section_name'];
            $new_status = $current_status == 1 ? 0 : 1;
            $status_text = $new_status == 1 ? 'habilitada' : 'deshabilitada';

            // Actualizar el estado de la sección
            $update_query = mysqli_query($conn, "UPDATE sections SET status = '$new_status' WHERE section_id = '$section_id'");
            if ($update_query) {
                // Registrar en la bitácora
                $action = "toggle_status";
                $details = mysqli_real_escape_string($conn, "El usuario con ID $logged_user_id cambió el estado de la sección '$section_name' (ID: $section_id) a $status_text.");
                $ip_address = $_SERVER['REMOTE_ADDR'];

                $audit_query = "INSERT INTO `audit_log` (`user_id`, `action`, `entity`, `entity_id`, `details`, `ip_address`, `timestamp`) 
                                VALUES ('$logged_user_id', '$action', 'sections', '$section_id', '$details', '$ip_address', NOW())";
                mysqli_query($conn, $audit_query);

                echo json_encode(['success' => true, 'message' => "La sección fue $status_text con éxito."]);
            } else {
                throw new Exception("Error al actualizar la base de datos: " . mysqli_error($conn));
            }
        } else {
            throw new Exception("Sección no encontrada.");
        }
    } else {
        throw new Exception("Datos incompletos enviados.");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
}
?>
