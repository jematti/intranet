<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_POST['section_id'])) {
    $section_id = $_POST['section_id'];
    $query = mysqli_query($conn, "SELECT * FROM `categories` WHERE `section_id` = '$section_id'") or die(mysqli_error($conn));
    $categories = [];
    while ($row = mysqli_fetch_array($query)) {
        $categories[] = [
            'category_id' => $row['category_id'],
            'category_name' => $row['category_name']
        ];
    }
    echo json_encode($categories); // Devolver los resultados en formato JSON
}
?>
