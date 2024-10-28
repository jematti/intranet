<?php
include("conexion_db.php");

if (isset($_POST['repository_id'])) {
    $repository_id = $_POST['repository_id'];
    
    $sections_query = mysqli_query($conn, "SELECT section_id, section_name FROM sections WHERE repository_id = '$repository_id' AND status = 1");
    
    $sections = [];
    while ($row = mysqli_fetch_assoc($sections_query)) {
        $sections[] = $row;
    }

    // Devolver las secciones en formato JSON
    echo json_encode($sections);
}
?>
