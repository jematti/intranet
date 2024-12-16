<?php
session_start();
include("conexion_db.php");

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
    
    $repository_phone = mysqli_real_escape_string($conn, $_POST['repository_phone']);

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
                $img_upload_path = 'uploads/profile_images/' . $new_img_name;
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/profile_images/';
                
                // Crear carpeta si no existe
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
    
                // Mover archivo
                if (move_uploaded_file($img_tmp_name, $upload_dir . $new_img_name)) {
                    $profile_img = $new_img_name;
                    $img_update_query = "UPDATE user SET profile_img = '$profile_img' WHERE user_id = '$user_id'";
                    mysqli_query($conn, $img_update_query);
                } else {
                    $error = "No se pudo guardar la imagen. Verifica permisos.";
                }
            } else {
                $error = "El tamaño del archivo debe ser menor a 5 MB.";
            }
        } else {
            $error = "Formato de archivo no permitido. Usa JPG, JPEG, PNG o GIF.";
        }
    }
    

    // Actualizar datos personales
    $query = "UPDATE user 
              SET ci = '$ci', firstname = '$firstname', lastname = '$lastname', email = '$email',
                  personal_email = '$personal_email', cell_phone = '$cell_phone', phone = '$phone', 
                  birth_date = '$birth_date', repository_phone = '$repository_phone'
              WHERE user_id = '$user_id'";
    
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
           u.address, u.landline_phone, u.repository_phone, u.profile_img, 
           p.position_name, r.repository_name
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
                            <?php 
                            $img_path = '/uploads/profile_images/' . $user['profile_img'];
                            $default_img = './assets/avatars/face.jpg';
                            if (!empty($user['profile_img']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $img_path)) { 
                            ?>
                                <img src="<?php echo $img_path; ?>" alt="Imagen de Perfil">
                            <?php } else { ?>
                                <img src="<?php echo $default_img; ?>" alt="Default Profile Image">
                            <?php } ?>
                        </div>
                    </div>


                    <!-- Formulario para editar perfil -->
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="update_profile" value="1">

                        <!-- Actualización de imagen de perfil -->
                        <div class="form-group col-md-6 mb-4">
                            <label for="profile_img" class="form-label">Actualizar Imagen de Perfil</label>
                            <input type="file" id="profile_img" name="profile_img" class="form-control" accept="image/*">
                        </div>

                        <!-- Información Personal -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light text-dark">
                                <h5 class="mb-0">Datos Personales</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ci" class="form-label">Cédula de Identidad (CI)</label>
                                        <input type="text" class="form-control" id="ci" name="ci" value="<?php echo htmlspecialchars($user['ci']); ?>" required>
                                    </div>
                                    <!-- <div class="col-md-6 mb-3">
                                        <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($user['birth_date']); ?>">
                                    </div> -->
                                    <div class="col-md-6 mb-3">
                                        <label for="firstname" class="form-label">Nombres</label>
                                        <input type="text" id="firstname" name="firstname" class="form-control" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="lastname" class="form-label">Apellidos</label>
                                        <input type="text" id="lastname" name="lastname" class="form-control" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light text-dark">
                                <h5 class="mb-0">Información de Contacto</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Correo Institucional</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="personal_email" class="form-label">Correo Personal</label>
                                        <input type="email" class="form-control" id="personal_email" name="personal_email" value="<?php echo htmlspecialchars($user['personal_email']); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="phone" class="form-label">Teléfono (Interno)</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="cell_phone" class="form-label">Celular (Corporativo)</label>
                                        <input type="text" class="form-control" id="cell_phone" name="cell_phone" value="<?php echo htmlspecialchars($user['cell_phone']); ?>">
                                    </div>
                                    <!-- <div class="col-md-4 mb-3">
                                        <label for="landline_phone" class="form-label">Teléfono Fijo</label>
                                        <input type="text" class="form-control" id="landline_phone" name="landline_phone" value="<?php echo htmlspecialchars($user['landline_phone']); ?>">
                                    </div> -->
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="repository_phone" class="form-label">Teléfono Fijo del Repositorio</label>
                                        <input type="text" class="form-control" id="repository_phone" name="repository_phone" value="<?php echo htmlspecialchars($user['repository_phone']); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Posición y Repositorio -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light text-dark">
                                <h5 class="mb-0">Detalles Organizacionales</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="position" class="form-label">Cargo</label>
                                        <input type="text" class="form-control-plaintext" id="position" value="<?php echo htmlspecialchars($user['position_name']); ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="repository" class="form-label">Área Organizacional</label>
                                        <input type="text" class="form-control-plaintext" id="repository" value="<?php echo htmlspecialchars($user['repository_name']); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón de Guardar Cambios -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-lg">Guardar Cambios</button>
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
