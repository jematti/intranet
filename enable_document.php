<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");

if (isset($_POST['enable'])) {
    $store_id = $_POST['store_id'];
    $query = "UPDATE storage SET status = 1 WHERE store_id = '$store_id'";
    mysqli_query($conn, $query) or die(mysqli_error($conn));
}
?>
