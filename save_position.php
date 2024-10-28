<?php
// Incluir la conexión a la base de datos
include("conexion_db.php");

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger y sanitizar los datos enviados desde el formulario
    $position_name = mysqli_real_escape_string($conn, $_POST['position_name']);

    // Verificar si el nombre del cargo/posición ya existe
    $check_query = mysqli_query($conn, "SELECT * FROM positions WHERE position_name = '$position_name'");
    if (mysqli_num_rows($check_query) > 0) {
        // Si el nombre ya existe, mostrar una alerta y redirigir
        echo "<script>alert('Este cargo/posición ya existe.'); window.location = 'positions.php';</script>";
        exit(); // Detener el script para evitar la inserción
    }

    // Si no existe, proceder con la inserción
    $insert_query = "INSERT INTO positions (position_name, status) VALUES ('$position_name', 1)";
    if (mysqli_query($conn, $insert_query)) {
        echo "<script>alert('Cargo/Posición agregado correctamente.'); window.location = 'positions.php';</script>";
    } else {
        echo "<script>alert('Error al agregar el cargo/posición.'); window.location = 'positions.php';</script>";
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
