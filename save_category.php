<?php
// Incluir la conexión a la base de datos
include $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

// Comprobar si el formulario fue enviado
if (isset($_POST['save'])) {
    // Recoger los datos desde el formulario
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $section_id = intval($_POST['section_id']);  // Asegurar que section_id sea un número entero

    // Comprobar si estamos creando una nueva categoría o actualizando una existente
    if ($category_id > 0) {
        // Actualizar categoría existente
        $query = "UPDATE `categories` SET `category_name` = ?, `section_id` = ? WHERE `category_id` = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sii', $category_name, $section_id, $category_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Categoría actualizada exitosamente'); window.location = 'categories.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar la categoría'); window.location = 'categories.php';</script>";
        }
    } else {
        // Insertar nueva categoría
        $query = "INSERT INTO `categories` (`category_name`, `section_id`, `status`) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $category_name, $section_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Categoría agregada exitosamente'); window.location = 'categories.php';</script>";
        } else {
            echo "<script>alert('Error al agregar la categoría'); window.location = 'categories.php';</script>";
        }
    }

    // Cerrar la declaración
    $stmt->close();
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
