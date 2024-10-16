<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'app/funcionts/admin/validator.php';
include_once 'app/complements/header.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Procesar el formulario de datos personales
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $ci = mysqli_real_escape_string($conn, $_POST['ci']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $personal_email = mysqli_real_escape_string($conn, $_POST['personal_email']);
    $cell_phone = mysqli_real_escape_string($conn, $_POST['cell_phone']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $profile_img = NULL;

    // Manejo de la imagen de perfil
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
        $img_name = $_FILES['profile_img']['name'];
        $img_tmp_name = $_FILES['profile_img']['tmp_name'];
        $img_size = $_FILES['profile_img']['size'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_ext = array("jpg", "jpeg", "png", "gif");

        if (in_array($img_ext, $allowed_ext)) {
            if ($img_size < 5000000) {
                $new_img_name = uniqid("IMG-", true) . '.' . $img_ext;
                $img_upload_path = 'intranet/uploads/profile_images/' . $new_img_name;

                // Elimina la imagen anterior si no es la predeterminada
                $old_image_query = "SELECT profile_img FROM user WHERE user_id = '$user_id'";
                $old_image_result = mysqli_query($conn, $old_image_query);
                $old_image = mysqli_fetch_assoc($old_image_result)['profile_img'];

                if (move_uploaded_file($img_tmp_name, $_SERVER['DOCUMENT_ROOT'] . '/' . $img_upload_path)) {
                    $profile_img = $new_img_name;
                    $img_update_query = "UPDATE user SET profile_img = '$profile_img' WHERE user_id = '$user_id'";
                    mysqli_query($conn, $img_update_query);
                }
            }
        }
    }

    // Actualizar datos personales
    $query = "UPDATE user SET ci = '$ci', firstname = '$firstname', lastname = '$lastname', email = '$email',
              personal_email = '$personal_email', cell_phone = '$cell_phone', phone = '$phone', 
              birth_date = '$birth_date', address = '$address' WHERE user_id = '$user_id'";
    
    if (mysqli_query($conn, $query)) {
        $success = "Datos actualizados exitosamente.";
    } else {
        $error = "Error al actualizar los datos.";
    }
}

// Procesar el formulario de cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Verificar que la contraseña actual sea correcta
    $query = "SELECT password FROM user WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if (md5($current_password) == $user['password']) {
        if ($new_password === $confirm_password) {
            $new_password_hash = md5($new_password);
            $update_password_query = "UPDATE user SET password = '$new_password_hash' WHERE user_id = '$user_id'";
            if (mysqli_query($conn, $update_password_query)) {
                $success = "Contraseña actualizada.";
            } else {
                $error = "Error al actualizar la contraseña.";
            }
        } else {
            $error = "Las contraseñas no coinciden.";
        }
    } else {
        $error = "La contraseña actual no es correcta.";
    }
}

// Obtener los datos actuales del usuario
$query = "
    SELECT u.ci, u.firstname, u.lastname, u.email, u.personal_email, u.cell_phone, u.phone, u.birth_date, 
           u.address, u.profile_img, p.position_name, r.repository_name
    FROM user u
    LEFT JOIN positions p ON u.position_id = p.position_id
    LEFT JOIN repositories r ON u.repository_id = r.repository_id
    WHERE u.user_id = '$user_id'
";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <style>
        .avatar-container {
            width: 120px;
            height: 120px;
            margin-bottom: 20px;
        }
        .avatar-container img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <!-- navegador principal -->
    <?php include 'app/complements/navbar-main.php'; ?>
    <!-- fin navegador principal -->
    <!-- Modal de Noticias (incluido desde archivo separado) -->
    <?php include 'news_modal.php'; ?>
    <!-- fin Modal de Noticias -->
    <!-- barra de navegación lateral -->
    <?php include 'app/funcionts/sidebar.php'; ?>
    <!-- fin de barra de navegación lateral -->

    <main role="main" class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="row align-items-center my-4">
                        <div class="col">
                            <h2 class="h3 mb-0 page-title">Editar Perfil</h2>
                        </div>
                    </div>

                    <!-- Mostrar mensajes de éxito o error -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Sección del avatar -->
                    <div class="text-center">
                        <div class="avatar-container">
                            <?php if ($user['profile_img']) { ?>
                                <img src="/intranet/uploads/profile_images/<?php echo $user['profile_img']; ?>" alt="Imagen de Perfil">
                            <?php } else { ?>
                                <img src="./assets/avatars/face.jpg" alt="Default Profile Image">
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Formulario para editar perfil -->
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="form-group col-md-6">
                            <label for="profile_img">Actualizar Imagen de Perfil</label>
                            <input type="file" id="profile_img" name="profile_img" class="form-control" accept="image/*">
                        </div>
                        <hr class="my-4">
                        <h5 class="mb-2 mt-4">Datos Personales</h5>

                        <div class="form-group">
                            <label for="ci">Cédula de Identidad (CI)</label>
                            <input type="text" class="form-control" id="ci" name="ci" value="<?php echo htmlspecialchars($user['ci']); ?>" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="firstname">Nombre</label>
                                <input type="text" id="firstname" name="firstname" class="form-control" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="lastname">Apellido</label>
                                <input type="text" id="lastname" name="lastname" class="form-control" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="email">Correo Institucional</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="phone">Teléfono</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="birth_date">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($user['birth_date']); ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="cell_phone">Celular</label>
                                <input type="text" class="form-control" id="cell_phone" name="cell_phone" value="<?php echo htmlspecialchars($user['cell_phone']); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="personal_email">Correo Personal</label>
                            <input type="email" class="form-control" id="personal_email" name="personal_email" value="<?php echo htmlspecialchars($user['personal_email']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="address">Dirección</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
                        </div>

                        <!-- Mostrar posición y repositorio (solo lectura) -->
                        <div class="form-group">
                            <label for="position">Cargo</label>
                            <input type="text" class="form-control-plaintext" id="position" value="<?php echo htmlspecialchars($user['position_name']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="repository">Repositorio</label>
                            <input type="text" class="form-control-plaintext" id="repository" value="<?php echo htmlspecialchars($user['repository_name']); ?>" readonly>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- Formulario para cambiar contraseña -->
                    <form method="POST" action="">
                        <input type="hidden" name="change_password" value="1">
                        <h5 class="mb-2 mt-4">Cambiar Contraseña</h5>

                        <div class="form-group">
                            <label for="current_password">Contraseña Actual</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </main>

    <?php include_once 'app/complements/footer.php'; ?>

</body>
</html>
