<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_GET['repository_id'])) {
    $repository_id = intval($_GET['repository_id']);
    $query = mysqli_query($conn, "SELECT * FROM `sections` WHERE `repository_id` = $repository_id AND `status` = 1");

    $sections = array();
    while ($row = mysqli_fetch_assoc($query)) {
        $sections[] = $row;
    }

    echo json_encode($sections);
}
?>
