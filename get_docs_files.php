<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

$category_id = $_GET['category_id'];
$query = mysqli_query($conn, "SELECT * FROM `storage` WHERE `category_id` = '$category_id'");
$files = [];
while ($file = mysqli_fetch_assoc($query)) {
    $files[] = $file;
}
echo json_encode($files);
?>
