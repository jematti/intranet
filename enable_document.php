<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");

if (isset($_POST['enable'])) {
    $store_id = intval($_POST['store_id']);
    $user_id = $_SESSION['user_id']; // Obtener el ID del usuario logueado
    $ip_address = $_SERVER['REMOTE_ADDR']; // Obtener la dirección IP del usuario
    $action = "enable";
    $entity = "storage";
    $details = "Se habilitó el documento con ID $store_id";

    // Actualizar el estado del documento en la tabla `storage`
    $query = "UPDATE storage SET status = 1 WHERE store_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $store_id);

    if ($stmt->execute()) {
        // Registrar en la tabla de auditoría
        $audit_query = "INSERT INTO audit_log (user_id, action, entity, entity_id, details, ip_address) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_audit = $conn->prepare($audit_query);
        $stmt_audit->bind_param("ississ", $user_id, $action, $entity, $store_id, $details, $ip_address);

        if ($stmt_audit->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Documento habilitado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al registrar la acción en la auditoría.']);
        }

        $stmt_audit->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al habilitar el documento.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida.']);
}

// Cerrar conexión a la base de datos
mysqli_close($conn);
?>
