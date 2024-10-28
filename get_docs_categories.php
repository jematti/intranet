<?php
include("conexion_db.php");

if (isset($_GET['section_id'])) {
    $section_id = intval($_GET['section_id']);
    $query = mysqli_query($conn, "SELECT * FROM `categories` WHERE `section_id` = $section_id AND `status` = 1");

    $categories = array();
    while ($row = mysqli_fetch_assoc($query)) {
        $categories[] = $row;
    }

    echo json_encode($categories);
}
?>
