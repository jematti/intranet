<?php
require $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_POST['repository_id'])) {
    $repository_id = intval($_POST['repository_id']);
    
    // Consulta para obtener las secciones relacionadas con el repositorio
    $query = mysqli_query($conn, "SELECT section_id, section_name FROM `sections` WHERE repository_id = $repository_id AND status = 1") or die(mysqli_error($conn));
    
    $sections = array();
    while ($row = mysqli_fetch_assoc($query)) {
        $sections[] = $row;
    }
    
    // Devolver los resultados en formato JSON
    echo json_encode($sections);
}
?>
