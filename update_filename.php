<?php
require 'conexion_db.php';

if (isset($_POST['store_id']) && isset($_POST['new_filename'])) {
    $store_id = $_POST['store_id'];
    $new_filename = mysqli_real_escape_string($conn, $_POST['new_filename']);

    // Obtener información del archivo actual
    $query = "SELECT filename, user_id FROM storage WHERE store_id = $store_id";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $current_filename = $row['filename'];
        $user_id = $row['user_id'];

        // Rutas del archivo
        $current_filepath = "files/$user_id/$current_filename";
        $new_filepath = "files/$user_id/$new_filename";

        // Renombrar el archivo en el servidor y actualizar la base de datos
        if (file_exists($current_filepath)) {
            if (rename($current_filepath, $new_filepath)) {
                $update_query = "UPDATE storage SET filename = '$new_filename' WHERE store_id = $store_id";
                echo (mysqli_query($conn, $update_query)) ? 'success' : 'error_db';
            } else {
                echo 'error_file';
            }
        } else {
            echo 'file_not_found';
        }
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>
