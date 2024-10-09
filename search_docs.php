<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

// Obtener los parámetros de los filtros
$repositoryId = isset($_GET['repository_id']) ? $_GET['repository_id'] : '';
$sectionId = isset($_GET['section_id']) ? $_GET['section_id'] : '';
$categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$fileType = isset($_GET['file_type']) ? $_GET['file_type'] : '';
$uploadedBy = isset($_GET['uploaded_by']) ? $_GET['uploaded_by'] : '';

// Construir la consulta SQL con los filtros aplicados
$query = "SELECT s.filename, s.file_type, s.date_uploaded, u.firstname, u.lastname, s.store_id 
          FROM storage s 
          JOIN user u ON s.uploaded_by = u.user_id 
          WHERE s.status = 1";

// Agregar filtros según estén presentes
if (!empty($repositoryId)) {
    $query .= " AND s.repository_id = '$repositoryId'";
}

if (!empty($sectionId)) {
    $query .= " AND s.section_id = '$sectionId'";
}

if (!empty($categoryId)) {
    $query .= " AND s.category_id = '$categoryId'";
}

if (!empty($keyword)) {
    $query .= " AND s.filename LIKE '%$keyword%'";
}

if (!empty($dateFrom) && !empty($dateTo)) {
    $query .= " AND s.date_uploaded BETWEEN '$dateFrom' AND '$dateTo'";
} elseif (!empty($dateFrom)) {
    $query .= " AND s.date_uploaded >= '$dateFrom'";
} elseif (!empty($dateTo)) {
    $query .= " AND s.date_uploaded <= '$dateTo'";
}

if (!empty($fileType)) {
    $query .= " AND s.file_type = '$fileType'";
}

if (!empty($uploadedBy)) {
    $query .= " AND s.uploaded_by = '$uploadedBy'";
}

// Ejecutar la consulta
$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'filename' => $row['filename'],
        'file_type' => $row['file_type'],
        'date_uploaded' => $row['date_uploaded'],
        'uploaded_by' => $row['firstname'] . ' ' . $row['lastname'], // Nombre completo del usuario
        'store_id' => $row['store_id']
    ];
}

// Devolver los datos en formato JSON
echo json_encode($data);
?>
