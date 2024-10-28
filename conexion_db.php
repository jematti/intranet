<?php
// Conexión a la base de datos
$conn = mysqli_connect("localhost", "root", "", "db_datos");

// Verificar si la conexión es exitosa
if (!$conn) {
    die("Error: Failed to connect to database!");
}

// Establecer el conjunto de caracteres para la conexión
$conn->set_charset("utf8");

?>
