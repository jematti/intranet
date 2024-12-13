<?php
include("conexion_db.php");
session_start(); // Para acceder al ID del usuario logueado

$section_id = $_POST['section_id'];
$section_name = mysqli_real_escape_string($conn, $_POST['section_name']);
$repository_id = $_POST['repository_id'];
$user_id = $_SESSION['user_id'] ?? null; // ID del usuario logueado

// Si el ID de sección está vacío, significa que se está agregando una nueva sección
if (empty($section_id)) {
    $query = "INSERT INTO sections (section_name, repository_id, status) VALUES ('$section_name', '$repository_id', 1)";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Obtener el ID de la sección recién agregada
        $new_section_id = mysqli_insert_id($conn);

        // Registrar en la bitácora
        if ($user_id) {
            $action = "add";
            $details = "Se agregó una nueva sección: $section_name en el repositorio ID $repository_id";
            $log_query = "INSERT INTO audit_log (user_id, action, entity, entity_id, details) 
                          VALUES ('$user_id', '$action', 'sections', '$new_section_id', '$details')";
            mysqli_query($conn, $log_query);
        }

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
        // Registrar en la bitácora
        if ($user_id) {
            $action = "update";
            $details = "Se actualizó la sección ID $section_id a Nombre: $section_name, Repositorio ID: $repository_id";
            $log_query = "INSERT INTO audit_log (user_id, action, entity, entity_id, details) 
                          VALUES ('$user_id', '$action', 'sections', '$section_id', '$details')";
            mysqli_query($conn, $log_query);
        }

        header('Location: sections.php?status=updated'); // Redirigir con mensaje de éxito
        exit();
    } else {
        header('Location: sections.php?status=error'); // Redirigir con mensaje de error
        exit();
    }
}
