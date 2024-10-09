<?php
// Conectar con la base de datos
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_POST['update'])) {
    // Recoger los datos del formulario
    $repository_id = $_POST['repository_id'];
    $repository_name = mysqli_real_escape_string($conn, $_POST['repository_name']);
    $building = mysqli_real_escape_string($conn, $_POST['building']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);

    // Consulta SQL para actualizar los datos del repositorio
    $sql = "UPDATE repositories 
            SET repository_name = '$repository_name', building = '$building', department = '$department' 
            WHERE repository_id = $repository_id";

    // Ejecutar la consulta
    if (mysqli_query($conn, $sql)) {
        // Redirigir a la página de repositorios con un mensaje de éxito
        header("Location: repositories.php?message=Repositorio actualizado correctamente");
    } else {
        // Mostrar un mensaje de error si ocurre algún problema
        echo "Error al actualizar el repositorio: " . mysqli_error($conn);
    }
}

// Cerrar la conexión
mysqli_close($conn);
?>
