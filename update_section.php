<?php
include("conexion_db.php");

if (isset($_POST['update'])) {
    $section_id = $_POST['section_id'];
    $section_name = mysqli_real_escape_string($conn, $_POST['section_name']);
    $repository_id = mysqli_real_escape_string($conn, $_POST['repository_id']);

    // Consulta para actualizar la sección
    $sql = "UPDATE sections SET section_name = '$section_name', repository_id = '$repository_id' WHERE section_id = $section_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: sections.php?message=Sección actualizada correctamente");
    } else {
        echo "Error al actualizar la sección: " . mysqli_error($conn);
    }
}

// Cerrar la conexión
mysqli_close($conn);
?>
