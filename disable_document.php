<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_POST['disable'])) {
    $store_id = $_POST['store_id'];
    $query = "UPDATE storage SET status = 0 WHERE store_id = '$store_id'";
    mysqli_query($conn, $query) or die(mysqli_error($conn));
}
?>
