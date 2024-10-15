<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_POST['section_id'])) {
    $section_id = $_POST['section_id'];

    // Consulta para obtener las categorías activas asociadas a la sección
    $query = "SELECT category_id, category_name, status 
              FROM categories 
              WHERE section_id = '$section_id' 
              AND status = 1"; // Asegurarse de que las categorías estén activas (status = 1)

    $result = mysqli_query($conn, $query);

    $categories = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row; // Añadir cada categoría al array
        }
    }

    // Devolver los datos en formato JSON
    echo json_encode($categories);
}
?>
