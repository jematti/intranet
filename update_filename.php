<?php
require 'conexion_db.php';
session_start(); // Asegúrate de que la sesión esté iniciada

if (isset($_POST['store_id']) && isset($_POST['new_filename'])) {
    $store_id = intval($_POST['store_id']);
    $new_filename = mysqli_real_escape_string($conn, $_POST['new_filename']);
    $user_id = $_SESSION['user_id'] ?? null; // ID del usuario que realiza la acción

    if (!$user_id) {
        echo 'error_user';
        exit();
    }

    // Obtener información del archivo actual
    $query = "SELECT filename, user_id FROM storage WHERE store_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $store_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $current_filename = $row['filename'];
        $file_owner_id = $row['user_id'];

        // Rutas del archivo
        $current_filepath = "files/$file_owner_id/$current_filename";
        $new_filepath = "files/$file_owner_id/$new_filename";

        // Validar que el nuevo nombre de archivo no esté vacío
        if (empty(trim($new_filename))) {
            echo 'error_empty_filename';
            exit();
        }

        // Renombrar el archivo en el servidor y actualizar la base de datos
        if (file_exists($current_filepath)) {
            if (rename($current_filepath, $new_filepath)) {
                $update_query = "UPDATE storage SET filename = ? WHERE store_id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("si", $new_filename, $store_id);

                if ($update_stmt->execute()) {
                    // Registrar el cambio en la tabla de auditoría
                    $action = "edit";
                    $entity = "storage";
                    $details = "Nombre del archivo cambiado de '$current_filename' a '$new_filename'.";
                    $audit_query = "INSERT INTO audit_log (user_id, action, entity, entity_id, details, timestamp) 
                                    VALUES (?, ?, ?, ?, ?, NOW())";
                    $audit_stmt = $conn->prepare($audit_query);
                    $audit_stmt->bind_param("issis", $user_id, $action, $entity, $store_id, $details);

                    if (!$audit_stmt->execute()) {
                        error_log("Error al insertar en audit_log: " . $audit_stmt->error);
                    }

                    echo 'success';
                } else {
                    echo 'error_db';
                }
            } else {
                echo 'error_file';
            }
        } else {
            echo 'file_not_found';
        }
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>
