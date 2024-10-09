<?php
// Incluir la conexión a la base de datos
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

// Verificar si el formulario fue enviado
if (isset($_POST['save'])) {
    // Obtener los datos enviados desde el formulario
    $repository_name = mysqli_real_escape_string($conn, $_POST['repository_name']);
    $building = mysqli_real_escape_string($conn, $_POST['building']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);

    // Crear la consulta SQL para insertar los datos
    $sql = "INSERT INTO repositories (repository_name, building, department) VALUES ('$repository_name', '$building', '$department')";

    // Ejecutar la consulta
    if (mysqli_query($conn, $sql)) {
        // Si se inserta correctamente, redirigir a la página de repositorios con un mensaje de éxito
        header("Location: repositories.php?message=Repositorio agregado exitosamente");
    } else {
        // Si hay un error, redirigir a la página de repositorios con un mensaje de error
        echo "Error: " . mysqli_error($conn);
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
