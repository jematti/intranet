<?php
// Incluir la conexión a la base de datos
include("conexion_db.php");

// Verificar si se envió el ID de posición y el estado actual
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['position_id']) && isset($_POST['status'])) {
    $position_id = intval($_POST['position_id']);
    $current_status = intval($_POST['status']);

    // Cambiar el estado: Si es 1 (activo), cambiar a 0 (inactivo), y viceversa
    $new_status = $current_status == 1 ? 0 : 1;

    // Actualizar el estado en la base de datos
    $update_status_query = "UPDATE positions SET status = $new_status WHERE position_id = $position_id";
    
    if (mysqli_query($conn, $update_status_query)) {
        echo json_encode(['success' => true, 'message' => 'Estado de la posición actualizado.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado de la posición.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos no válidos.']);
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
