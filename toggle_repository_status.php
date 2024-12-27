<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

try {
    if (isset($_POST['repository_id']) && isset($_POST['status'])) {
        $repository_id = intval($_POST['repository_id']);
        $current_status = intval($_POST['status']);
        $new_status = $current_status === 1 ? 0 : 1;
        $status_text = $new_status === 1 ? 'habilitado' : 'deshabilitado';

        $logged_user_id = $_SESSION['user_id'] ?? null; // Usuario que realiza la acción

        if (!$logged_user_id) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
            exit();
        }

        // Obtener el nombre del repositorio
        $repo_query = "SELECT repository_name FROM repositories WHERE repository_id = '$repository_id'";
        $repo_result = mysqli_query($conn, $repo_query);

        if ($repo_row = mysqli_fetch_assoc($repo_result)) {
            $repository_name = $repo_row['repository_name'];

            // Actualizar el estado del repositorio
            $update_query = "UPDATE repositories SET status = '$new_status' WHERE repository_id = '$repository_id'";
            if (mysqli_query($conn, $update_query)) {
                // Registrar en la bitácora
                $action = "toggle_status";
                $details = mysqli_real_escape_string($conn, "El repositorio '$repository_name' (ID: $repository_id) fue $status_text.");
                $ip_address = $_SERVER['REMOTE_ADDR'];

                $audit_query = "INSERT INTO audit_log (user_id, action, entity, entity_id, details, ip_address, timestamp) 
                                VALUES ('$logged_user_id', '$action', 'repositories', '$repository_id', '$details', '$ip_address', NOW())";
                mysqli_query($conn, $audit_query);

                echo json_encode(['success' => true, 'message' => "El repositorio '$repository_name' fue $status_text con éxito."]);
            } else {
                throw new Exception("Error al actualizar la base de datos: " . mysqli_error($conn));
            }
        } else {
            throw new Exception("Repositorio no encontrado.");
        }
    } else {
        throw new Exception("Datos incompletos enviados.");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
}
?>
