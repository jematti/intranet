<?php
// Incluir la conexión a la base de datos
include("conexion_db.php");

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Recoger y sanitizar los datos enviados desde el formulario
    $ci = mysqli_real_escape_string($conn, $_POST['ci']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $personal_email = mysqli_real_escape_string($conn, $_POST['personal_email']);
    $cell_phone = mysqli_real_escape_string($conn, $_POST['cell_phone']);
    // Comentado para deshabilitar landline_phone
    // $landline_phone = mysqli_real_escape_string($conn, $_POST['landline_phone']);
    $repository_phone = mysqli_real_escape_string($conn, $_POST['repository_phone']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $position_id = intval($_POST['position_id']);
    $repository_id = intval($_POST['repository_id']);
    $section_id = intval($_POST['section_id']);
    $role_id = intval($_POST['role_id']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $profile_img = NULL; // Inicializar la variable de la imagen
    
    // Validar que no se seleccione el rol de Super Admin
    if ($role_id == 1) {
        echo "<script>alert('No se puede asignar el rol de Super Admin.'); window.location = 'admin_user.php';</script>";
        exit(); // Detener la ejecución del script si se intenta asignar este rol
    }

    // Manejo de la imagen de perfil
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
        $img_name = $_FILES['profile_img']['name'];
        $img_tmp_name = $_FILES['profile_img']['tmp_name'];
        $img_size = $_FILES['profile_img']['size'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_ext = array("jpg", "jpeg", "png", "gif");

        if (in_array($img_ext, $allowed_ext)) {
            if ($img_size < 5000000) { // Limitar tamaño a 5MB
                $new_img_name = uniqid("IMG-", true) . '.' . $img_ext;
                $img_upload_path = 'intranet/uploads/profile_images/' . $new_img_name;

                // Si es una actualización de usuario, eliminar la imagen anterior
                if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
                    $user_id = intval($_POST['user_id']);
                    $old_image_query = "SELECT profile_img FROM user WHERE user_id = '$user_id'";
                    $old_image_result = mysqli_query($conn, $old_image_query);
                    $old_image = mysqli_fetch_assoc($old_image_result)['profile_img'];

                    if (move_uploaded_file($img_tmp_name, $_SERVER['DOCUMENT_ROOT'] . '/' . $img_upload_path)) {
                        $profile_img = $new_img_name;

                        // Elimina la imagen anterior si no es la imagen por defecto
                        if ($old_image && $old_image != 'intranet/uploads/profile_images/default.jpg') {
                            $old_image_path = $_SERVER['DOCUMENT_ROOT'] . '/intranet/uploads/profile_images/' . $old_image;
                            if (file_exists($old_image_path)) {
                                unlink($old_image_path);
                            }
                        }
                    } else {
                        $error = "Error al mover la imagen. Inténtalo de nuevo.";
                    }
                } else {
                    // Si es un nuevo usuario, simplemente subir la imagen
                    if (move_uploaded_file($img_tmp_name, $_SERVER['DOCUMENT_ROOT'] . '/' . $img_upload_path)) {
                        $profile_img = $new_img_name;
                    } else {
                        $error = "Error al mover la imagen. Inténtalo de nuevo.";
                    }
                }
            } else {
                $error = "La imagen es demasiado grande. El tamaño máximo permitido es de 5MB.";
            }
        } else {
            $error = "Formato de imagen no permitido. Los formatos aceptados son JPG, JPEG, PNG, GIF.";
        }
    }

    // Verificar si se trata de una operación de agregar o editar
    if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        // Actualización del usuario existente
        $user_id = intval($_POST['user_id']);
        
        // Si se envió una contraseña nueva, se encripta
        $password_update = "";
        if (!empty($password)) {
            $password_hash = md5($password);
            $password_update = ", `password` = '$password_hash'";
        }
        
        // Si se subió una imagen nueva, actualizamos también el campo profile_img
        $profile_img_update = "";
        if ($profile_img !== NULL) {
            $profile_img_update = ", `profile_img` = '$profile_img'";
        }
        
        $update_query = "UPDATE `user` 
                         SET `ci` = '$ci', `firstname` = '$firstname', `lastname` = '$lastname',
                             `username` = '$username', `email` = '$email', `personal_email` = '$personal_email', 
                             `cell_phone` = '$cell_phone',
                             `repository_phone` = '$repository_phone', `phone` = '$phone', `birth_date` = '$birth_date',
                             `address` = '$address', `position_id` = $position_id, `repository_id` = $repository_id, 
                             `section_id` = $section_id, `role_id` = $role_id
                             $password_update
                             $profile_img_update
                         WHERE `user_id` = $user_id";
        
        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('Usuario actualizado correctamente.'); window.location = 'admin_user.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar el usuario.'); window.location = 'admin_user.php';</script>";
        }
        
    } else {
        // Agregar un nuevo usuario
        $password_hash = md5($password); // Hash de la contraseña

        $insert_query = "INSERT INTO `user` (`ci`, `firstname`, `lastname`, `username`, `password`, `email`, 
                                              `personal_email`, `cell_phone`, 
                                              `repository_phone`, 
                                              `phone`, `birth_date`, `address`, `position_id`, `repository_id`, 
                                              `section_id`, `role_id`, `status`, `active_status`, `profile_img`)
                         VALUES ('$ci', '$firstname', '$lastname', '$username', '$password_hash', '$email', 
                                 '$personal_email', '$cell_phone', '$repository_phone', '$phone', 
                                 '$birth_date', '$address', $position_id, $repository_id, $section_id, $role_id, 
                                 'active', 1, '$profile_img')";
        
        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('Usuario agregado correctamente.'); window.location = 'admin_user.php';</script>";
        } else {
            echo "<script>alert('Error al agregar el usuario.'); window.location = 'admin_user.php';</script>";
        }
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
