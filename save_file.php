<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

if (isset($_FILES['fileUpload'])) {
    // Obtener los datos del formulario
    $user_id = $_POST['user_id'];
    $repository_id = $_POST['repository_id']; // Repositorio del usuario
    $section_id = $_POST['section_id'];       // Sección seleccionada
    $category_id = $_POST['category_id'];     // Categoría seleccionada
    $file_name = $_FILES['fileUpload']['name'];
    $file_type = $_FILES['fileUpload']['type'];
    $file_temp = $_FILES['fileUpload']['tmp_name'];
    $date = date("Y-m-d, h:i A", strtotime("+8 HOURS")); // Fecha de subida
    
    // Ruta de almacenamiento
    $location = "files/" . $user_id . "/" . $file_name;

    // Verificar si el directorio existe, si no, crearlo
    if (!file_exists("files/" . $user_id)) {
        mkdir("files/" . $user_id, 0777, true); // Crear carpeta para el usuario si no existe
    }

    // Mover el archivo y guardar la información en la base de datos
    if (move_uploaded_file($file_temp, $location)) {
        // Verificar si el category_id existe en la tabla categories
        $check_category_query = "SELECT * FROM `categories` WHERE `category_id` = ?";
        $stmt_check = $conn->prepare($check_category_query);
        $stmt_check->bind_param("i", $category_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Verificar si el user_id existe en la tabla user
            $check_user_query = "SELECT * FROM `user` WHERE `user_id` = ?";
            $stmt_check_user = $conn->prepare($check_user_query);
            $stmt_check_user->bind_param("i", $user_id);
            $stmt_check_user->execute();
            $result_check_user = $stmt_check_user->get_result();

            if ($result_check_user->num_rows > 0) {
                // El category_id, user_id y demás existen, proceder con la inserción
                $insert_query = "INSERT INTO `storage` (filename, file_type, date_uploaded, user_id, repository_id, section_id, category_id, uploaded_by, status) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
                $stmt_insert = $conn->prepare($insert_query);
                $stmt_insert->bind_param("sssiiiii", $file_name, $file_type, $date, $user_id, $repository_id, $section_id, $category_id, $user_id);

                if ($stmt_insert->execute()) {
                    // Mostrar alerta de éxito y redirigir
                    echo "<script>alert('Archivo subido exitosamente.'); window.location.href = 'upload_document.php';</script>";
                    exit();
                } else {
                    // Mostrar alerta de error al guardar en la base de datos
                    echo "<script>alert('Error al guardar la información en la base de datos: " . $stmt_insert->error . "'); window.history.back();</script>";
                }

                $stmt_insert->close();
            } else {
                // Mostrar alerta de error si el user_id no existe
                echo "<script>alert('El user_id proporcionado no existe.'); window.history.back();</script>";
            }

            $stmt_check_user->close();
        } else {
            // Mostrar alerta de error si el category_id no existe
            echo "<script>alert('El category_id proporcionado no existe.'); window.history.back();</script>";
        }

        $stmt_check->close();
    } else {
        // Mostrar alerta de error al mover el archivo
        echo "<script>alert('Error al mover el archivo al directorio de almacenamiento.'); window.history.back();</script>";
    }
} else {
    // Mostrar alerta si no se recibió ningún archivo
    echo "<script>alert('No se recibió ningún archivo.'); window.history.back();</script>";
}

?>
