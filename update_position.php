<?php
// Incluir la conexión a la base de datos
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['position_id'])) {
    // Recoger y sanitizar los datos enviados desde el formulario
    $position_id = intval($_POST['position_id']);
    $position_name = mysqli_real_escape_string($conn, $_POST['position_name']);

    // Actualizar el nombre de la posición en la base de datos
    $update_query = "UPDATE positions SET position_name = '$position_name' WHERE position_id = $position_id";
    
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Posición actualizada correctamente.'); window.location = 'positions.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar la posición.'); window.location = 'positions.php';</script>";
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
