<?php
// Incluir la conexión a la base de datos
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['user_id']);
    $current_status = intval($_POST['status']);

    // Cambiar el estado (si está activo, pasarlo a inactivo, y viceversa)
    $new_status = $current_status == 1 ? 0 : 1;

    $query = "UPDATE `user` SET `active_status` = $new_status WHERE `user_id` = $user_id";

    if (mysqli_query($conn, $query)) {
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
