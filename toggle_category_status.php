<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

// Verificar que se reciban los datos correctos
if (isset($_POST['category_id']) && isset($_POST['status'])) {
    $category_id = $_POST['category_id'];
    // Cambiar el estado actual: si está habilitado (1), cambiar a deshabilitado (0), y viceversa
    $new_status = $_POST['status'] == 1 ? 0 : 1;  

    // Consulta para actualizar el estado de la categoría
    $query = "UPDATE categories SET status = '$new_status' WHERE category_id = '$category_id'";
    $result = mysqli_query($conn, $query);

    // Responder con JSON para indicar si el cambio fue exitoso o no
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
