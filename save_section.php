<?php
include("conexion_db.php");

$section_id = $_POST['section_id'];
$section_name = $_POST['section_name'];
$repository_id = $_POST['repository_id'];

// Si el ID de sección está vacío, significa que se está agregando una nueva sección
if (empty($section_id)) {
    $query = "INSERT INTO sections (section_name, repository_id, status) VALUES ('$section_name', '$repository_id', 1)";
    $result = mysqli_query($conn, $query);

    if ($result) {
        header('Location: sections.php?status=added'); // Redirigir con mensaje de éxito
        exit();
    } else {
        header('Location: sections.php?status=error'); // Redirigir con mensaje de error
        exit();
    }
} else {
    // Si hay un ID de sección, significa que estamos actualizando una sección existente
    $query = "UPDATE sections SET section_name='$section_name', repository_id='$repository_id' WHERE section_id='$section_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        header('Location: sections.php?status=updated'); // Redirigir con mensaje de éxito
        exit();
    } else {
        header('Location: sections.php?status=error'); // Redirigir con mensaje de error
        exit();
    }
}
