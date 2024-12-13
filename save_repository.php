<?php
// Incluir la conexión a la base de datos
include("conexion_db.php");
session_start(); // Iniciar la sesión para acceder al ID del usuario

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
        // Obtener el ID del repositorio recién agregado
        $repository_id = mysqli_insert_id($conn);

        // Registrar en la bitácora
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $action = "add";
            $details = "Se agregó un nuevo repositorio: $repository_name en $building ($department)";
            $log_sql = "INSERT INTO audit_log (user_id, action, entity, entity_id, details) VALUES ('$user_id', '$action', 'repositories', '$repository_id', '$details')";
            mysqli_query($conn, $log_sql);
        }

        // Redirigir a la página de repositorios con un mensaje de éxito
        header("Location: repositories.php?message=Repositorio agregado exitosamente");
    } else {
        // Si hay un error, mostrar el mensaje
        echo "Error: " . mysqli_error($conn);
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
