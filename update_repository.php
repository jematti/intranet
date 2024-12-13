<?php
// Conectar con la base de datos
include("conexion_db.php");
session_start(); // Iniciar sesión para acceder al ID del usuario

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
        // Registrar en la bitácora
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $action = "update";
            $details = "Se actualizó el repositorio ID: $repository_id a Nombre: $repository_name, Edificio: $building, Departamento: $department";
            $log_sql = "INSERT INTO audit_log (user_id, action, entity, entity_id, details) 
                        VALUES ('$user_id', '$action', 'repositories', '$repository_id', '$details')";
            mysqli_query($conn, $log_sql);
        }

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
