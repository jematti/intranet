<?php
include("conexion_db.php");
session_start(); // Iniciar la sesión para manejar notificaciones

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $position_name = mysqli_real_escape_string($conn, $_POST['position_name']);
    $position_id = isset($_POST['position_id']) ? intval($_POST['position_id']) : 0;

    $user_id = $_SESSION['user_id'] ?? null;

    try {
        if ($position_id > 0) {
            // Actualizar posición
            $query = "UPDATE `positions` SET `position_name` = ? WHERE `position_id` = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $position_name, $position_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Cargo/Posición actualizado correctamente.";
                $_SESSION['message_type'] = "success";

                // Registrar en la bitácora
                if ($user_id) {
                    $action = "update";
                    $details = "Se actualizó el cargo/posición ID $position_id con el nombre '$position_name'.";
                    $log_query = "INSERT INTO `audit_log` (`user_id`, `action`, `entity`, `entity_id`, `details`, `ip_address`) 
                                  VALUES (?, ?, 'positions', ?, ?, ?)";
                    $log_stmt = $conn->prepare($log_query);
                    $log_stmt->bind_param('isiss', $user_id, $action, $position_id, $details, $_SERVER['REMOTE_ADDR']);
                    $log_stmt->execute();
                    $log_stmt->close();
                }
            } else {
                throw new Exception("Error al actualizar el cargo/posición.");
            }
        } else {
            // Insertar nueva posición
            // Comprobar duplicado
            $check_query = $conn->prepare("SELECT * FROM `positions` WHERE `position_name` = ?");
            $check_query->bind_param('s', $position_name);
            $check_query->execute();
            $result = $check_query->get_result();

            if ($result->num_rows > 0) {
                $_SESSION['message'] = "El cargo/posición ya existe.";
                $_SESSION['message_type'] = "warning";
            } else {
                $query = "INSERT INTO `positions` (`position_name`, `status`) VALUES (?, 1)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('s', $position_name);

                if ($stmt->execute()) {
                    $new_position_id = $stmt->insert_id;
                    $_SESSION['message'] = "Cargo/Posición agregado correctamente.";
                    $_SESSION['message_type'] = "success";

                    // Registrar en la bitácora
                    if ($user_id) {
                        $action = "add";
                        $details = "Se agregó un nuevo cargo/posición con el nombre '$position_name' (ID: $new_position_id).";
                        $log_query = "INSERT INTO `audit_log` (`user_id`, `action`, `entity`, `entity_id`, `details`, `ip_address`) 
                                      VALUES (?, ?, 'positions', ?, ?, ?)";
                        $log_stmt = $conn->prepare($log_query);
                        $log_stmt->bind_param('isiss', $user_id, $action, $new_position_id, $details, $_SERVER['REMOTE_ADDR']);
                        $log_stmt->execute();
                        $log_stmt->close();
                    }
                } else {
                    throw new Exception("Error al agregar el cargo/posición.");
                }
            }
        }
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "danger";
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        header("Location: positions.php");
        exit;
    }
}
mysqli_close($conn);
?>
