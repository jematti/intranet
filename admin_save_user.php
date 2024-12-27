<?php
// Incluir la conexión a la base de datos
include("conexion_db.php");
session_start(); // Iniciar la sesión para obtener el ID del usuario logueado

// Obtener el ID del usuario logueado
$logged_user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger y sanitizar los datos enviados desde el formulario
    $ci = mysqli_real_escape_string($conn, $_POST['ci']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $personal_email = mysqli_real_escape_string($conn, $_POST['personal_email']);
    $cell_phone = mysqli_real_escape_string($conn, $_POST['cell_phone']);
    $repository_phone = mysqli_real_escape_string($conn, $_POST['repository_phone']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $position_id = intval($_POST['position_id']);
    $repository_id = intval($_POST['repository_id']);
    $section_id = intval($_POST['section_id']);
    $role_id = intval($_POST['role_id']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $profile_img = NULL;

    // Validar que no se seleccione el rol de Super Admin
    if ($role_id == 1) {
        echo "<script>alert('No se puede asignar el rol de Super Admin.'); window.location = 'admin_user.php';</script>";
        exit();
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
                $img_upload_path = 'uploads/profile_images/' . $new_img_name;
                if (move_uploaded_file($img_tmp_name, $img_upload_path)) {
                    $profile_img = $new_img_name;
                } else {
                    echo "<script>alert('Error al subir la imagen.'); window.location = 'admin_user.php';</script>";
                    exit();
                }
            } else {
                echo "<script>alert('La imagen supera el tamaño máximo permitido de 5MB.'); window.location = 'admin_user.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Formato de imagen no permitido.'); window.location = 'admin_user.php';</script>";
            exit();
        }
    }

    // Verificar si se trata de una operación de agregar o editar
    if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        // Actualización del usuario existente
        $user_id = intval($_POST['user_id']);
        $password_update = !empty($password) ? ", `password` = '" . md5($password) . "'" : "";
        $profile_img_update = $profile_img !== NULL ? ", `profile_img` = '$profile_img'" : "";

        $update_query = "UPDATE `user` 
                         SET `ci` = '$ci', `firstname` = '$firstname', `lastname` = '$lastname',
                             `username` = '$username', `email` = '$email', `personal_email` = '$personal_email', 
                             `cell_phone` = '$cell_phone', `repository_phone` = '$repository_phone', 
                             `phone` = '$phone', `position_id` = $position_id, `repository_id` = $repository_id, 
                             `section_id` = $section_id, `role_id` = $role_id
                             $password_update $profile_img_update
                         WHERE `user_id` = $user_id";

        if (mysqli_query($conn, $update_query)) {
            // Registro en la tabla de auditoría
            $action = "update";
            $details = "El usuario $firstname $lastname (ID: $user_id) fue actualizado.";
            $ip_address = $_SERVER['REMOTE_ADDR'];

            $log_query = "INSERT INTO audit_log (`user_id`, `action`, `entity`, `entity_id`, `details`, `ip_address`) 
                          VALUES ('$logged_user_id', '$action', 'user', '$user_id', '$details', '$ip_address')";
            mysqli_query($conn, $log_query);

            echo "<script>alert('Usuario actualizado correctamente.'); window.location = 'admin_user.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar el usuario.'); window.location = 'admin_user.php';</script>";
        }
    } else {
        // Agregar un nuevo usuario
        $password_hash = md5($password);

        $insert_query = "INSERT INTO `user` (`ci`, `firstname`, `lastname`, `username`, `password`, `email`, 
                                              `personal_email`, `cell_phone`, `repository_phone`, `phone`, 
                                              `position_id`, `repository_id`, `section_id`, `role_id`, 
                                              `status`, `active_status`, `profile_img`)
                         VALUES ('$ci', '$firstname', '$lastname', '$username', '$password_hash', '$email', 
                                 '$personal_email', '$cell_phone', '$repository_phone', '$phone', 
                                 $position_id, $repository_id, $section_id, $role_id, 
                                 'active', 1, '$profile_img')";

        if (mysqli_query($conn, $insert_query)) {
            $new_user_id = mysqli_insert_id($conn);

            // Registro en la tabla de auditoría
            $action = "add";
            $details = "Se agregó un nuevo usuario: $firstname $lastname (ID: $new_user_id).";
            $ip_address = $_SERVER['REMOTE_ADDR'];

            $log_query = "INSERT INTO audit_log (`user_id`, `action`, `entity`, `entity_id`, `details`, `ip_address`) 
                          VALUES ('$logged_user_id', '$action', 'user', '$new_user_id', '$details', '$ip_address')";
            mysqli_query($conn, $log_query);

            echo "<script>alert('Usuario agregado correctamente.'); window.location = 'admin_user.php';</script>";
        } else {
            echo "<script>alert('Error al agregar el usuario.'); window.location = 'admin_user.php';</script>";
        }
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
