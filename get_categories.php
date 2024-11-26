<?php
include("conexion_db.php");

// Verificar si se ha recibido el section_id
if (isset($_POST['section_id'])) {
    $section_id = intval($_POST['section_id']);

    // Consultar las categorías activas asociadas a la sección
    $query = mysqli_query($conn, "SELECT * FROM categories WHERE section_id = $section_id AND status = 1") or die(mysqli_error($conn));

    $categories = [];
    while ($category = mysqli_fetch_assoc($query)) {
        $categories[] = $category; // Agregar cada categoría al array
    }

    // Devolver las categorías en formato JSON
    echo json_encode($categories);
}
?>
