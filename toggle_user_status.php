<?php
// Incluir la conexión a la base de datos
include("conexion_db.php");
session_start(); // Para acceder al ID del usuario logueado

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['user_id']);
    $current_status = intval($_POST['status']);

    // Cambiar el estado (si está activo, pasarlo a inactivo, y viceversa)
    $new_status = $current_status == 1 ? 0 : 1;

    $query = "UPDATE `user` SET `active_status` = $new_status WHERE `user_id` = $user_id";

    if (mysqli_query($conn, $query)) {
        // Auditoría
        $logged_user_id = $_SESSION['user_id']; // Usuario que realiza la acción
        $action = "toggle_status";
        $details = "El usuario con ID $user_id cambió su estado a " . ($new_status == 1 ? "activo" : "inactivo") . ".";
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $audit_query = "INSERT INTO `audit_log` (`user_id`, `action`, `entity`, `entity_id`, `details`, `ip_address`) 
                        VALUES ('$logged_user_id', '$action', 'user', '$user_id', '$details', '$ip_address')";

        mysqli_query($conn, $audit_query);

        echo json_encode([
            'status' => 'success',
            'new_status' => $new_status,
            'message' => 'Estado de usuario actualizado correctamente.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al actualizar el estado del usuario.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Solicitud inválida.'
    ]);
}

// Cerrar la conexión
mysqli_close($conn);
?>
