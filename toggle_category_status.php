<?php
include("conexion_db.php");
session_start(); // Iniciar la sesión para obtener el ID del usuario logueado

// Verificar que se reciban los datos correctos
if (isset($_POST['category_id']) && isset($_POST['status'])) {
    $category_id = intval($_POST['category_id']);
    $current_status = intval($_POST['status']);
    $new_status = $current_status == 1 ? 0 : 1; // Cambiar el estado actual
    $status_text = $new_status == 1 ? 'habilitada' : 'deshabilitada';

    // Consulta para actualizar el estado de la categoría
    $query = "UPDATE categories SET status = '$new_status' WHERE category_id = '$category_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Obtener el ID del usuario logueado
        $user_id = $_SESSION['user_id'] ?? null;

        // Registrar en la bitácora
        if ($user_id) {
            $action = "status_change";
            $details = "La categoría ID $category_id fue $status_text.";
            $log_query = "INSERT INTO audit_log (user_id, action, entity, entity_id, details, ip_address) 
                          VALUES ('$user_id', '$action', 'categories', '$category_id', '$details', '{$_SERVER['REMOTE_ADDR']}')";
            mysqli_query($conn, $log_query);
        }

        // Responder con JSON indicando éxito
        echo json_encode(['success' => true]);
    } else {
        // Responder con JSON indicando fallo
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
} else {
    // Responder con JSON indicando que faltan datos
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
}
?>
