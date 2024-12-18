<?php
include("conexion_db.php");

if (isset($_GET['store_id'])) {
    // Obtener el ID del archivo desde la URL
    $store_id = intval($_GET['store_id']);

    // Consultar la base de datos para obtener información del archivo
    $query = "SELECT filename, file_type, user_id FROM `storage` WHERE `store_id` = ? AND `status` = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $store_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $file = $result->fetch_assoc();

        // Ruta completa del archivo
        $file_path = "files/" . $file['user_id'] . "/" . $file['filename'];
        
        // Verificar si el archivo existe
        if (file_exists($file_path)) {
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Ver Video</title>
                <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body>
                <div class="container mt-5">
                    <h2 class="mb-4">Reproduciendo: <?php echo htmlspecialchars($file['filename'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <video controls autoplay style="width: 100%; height: auto;">
                        <source src="<?php echo htmlspecialchars($file_path, ENT_QUOTES, 'UTF-8'); ?>" type="<?php echo htmlspecialchars($file['file_type'], ENT_QUOTES, 'UTF-8'); ?>">
                        Tu navegador no soporta la reproducción de videos.
                    </video>
                    <a href="documents_tree.php" class="btn btn-primary mt-3">Regresar</a>
                </div>
            </body>
            </html>
            <?php
        } else {
            // Error si el archivo no existe en el servidor
            echo "<script>alert('El archivo no existe.'); window.history.back();</script>";
        }
    } else {
        // Error si no se encontró el registro en la base de datos
        echo "<script>alert('Archivo no encontrado o no disponible.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    // Error si no se proporciona el ID del archivo
    echo "<script>alert('ID del archivo no especificado.'); window.history.back();</script>";
}
?>
