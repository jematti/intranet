<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

$response = array();

if (isset($_POST['store_id'])) {
    $store_id = $_POST['store_id'];

    // Obtener el estado actual del archivo
    $query = "SELECT `status` FROM `storage` WHERE `store_id` = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $store_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_status = $row['status'];

        // Alternar el estado del archivo
        $new_status = $current_status == 1 ? 0 : 1;
        
        // Actualizar el estado en la base de datos
        $update_query = "UPDATE `storage` SET `status` = ? WHERE `store_id` = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("ii", $new_status, $store_id);

        if ($stmt_update->execute()) {
            $response['success'] = true;
            $response['newStatus'] = $new_status; // Devolver el nuevo estado
        } else {
            $response['success'] = false;
            $response['message'] = "Error al actualizar el estado.";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Archivo no encontrado.";
    }
    
    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = "No se ha proporcionado un ID de archivo.";
}

echo json_encode($response);
?>
