<?php
// Incluir la conexión a la base de datos
include("conexion_db.php");
session_start(); // Iniciar la sesión para manejar los mensajes de notificación

// Comprobar si el formulario fue enviado
if (isset($_POST['save'])) {
    // Recoger los datos desde el formulario
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $section_id = isset($_POST['section_id']) ? intval($_POST['section_id']) : 0;

    // Obtener el ID del usuario logueado
    $user_id = $_SESSION['user_id'] ?? null;

    try {
        // Comprobar si estamos creando una nueva categoría o actualizando una existente
        if ($category_id > 0) {
            // Actualizar categoría existente
            $query = "UPDATE `categories` SET `category_name` = ?, `section_id` = ? WHERE `category_id` = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sii', $category_name, $section_id, $category_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Categoría actualizada exitosamente.";
                $_SESSION['message_type'] = "success";

                // Registrar en la bitácora
                if ($user_id) {
                    $action = "update";
                    $details = "Se actualizó la categoría ID $category_id con el nombre '$category_name'.";
                    $log_query = "INSERT INTO `audit_log` (`user_id`, `action`, `entity`, `entity_id`, `details`, `ip_address`) 
                                  VALUES (?, ?, 'categories', ?, ?, ?)";
                    $log_stmt = $conn->prepare($log_query);
                    $log_stmt->bind_param('isiss', $user_id, $action, $category_id, $details, $_SERVER['REMOTE_ADDR']);
                    $log_stmt->execute();
                    $log_stmt->close();
                }
            } else {
                throw new Exception("Error al actualizar la categoría.");
            }
        } else {
            // Insertar nueva categoría
            $query = "INSERT INTO `categories` (`category_name`, `section_id`, `status`) VALUES (?, ?, 1)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $category_name, $section_id);

            if ($stmt->execute()) {
                $new_category_id = $stmt->insert_id; // Obtener el ID de la nueva categoría
                $_SESSION['message'] = "Categoría agregada exitosamente.";
                $_SESSION['message_type'] = "success";

                // Registrar en la bitácora
                if ($user_id) {
                    $action = "add";
                    $details = "Se agregó una nueva categoría con el nombre '$category_name' (ID: $new_category_id).";
                    $log_query = "INSERT INTO `audit_log` (`user_id`, `action`, `entity`, `entity_id`, `details`, `ip_address`) 
                                  VALUES (?, ?, 'categories', ?, ?, ?)";
                    $log_stmt = $conn->prepare($log_query);
                    $log_stmt->bind_param('isiss', $user_id, $action, $new_category_id, $details, $_SERVER['REMOTE_ADDR']);
                    $log_stmt->execute();
                    $log_stmt->close();
                }
            } else {
                throw new Exception("Error al agregar la categoría.");
            }
        }
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "danger";
    } finally {
        // Cerrar la declaración
        if (isset($stmt)) {
            $stmt->close();
        }

        // Redirigir de vuelta a categories.php
        header("Location: categories.php");
        exit;
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
