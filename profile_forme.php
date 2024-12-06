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
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $middlename = mysqli_real_escape_string($conn, $_POST['middlename']);
    $thirdname = mysqli_real_escape_string($conn, $_POST['thirdname']);
    $lastname_father = mysqli_real_escape_string($conn, $_POST['lastname_father']);
    $lastname_mother = mysqli_real_escape_string($conn, $_POST['lastname_mother']);
    $married_lastname = mysqli_real_escape_string($conn, $_POST['married_lastname']);
    $marital_status = mysqli_real_escape_string($conn, $_POST['marital_status']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $blood_type = mysqli_real_escape_string($conn, $_POST['blood_type']);
    $document_type = mysqli_real_escape_string($conn, $_POST['document_type']);
    $document_number = mysqli_real_escape_string($conn, $_POST['document_number']);
    $document_expiry_date = mysqli_real_escape_string($conn, $_POST['document_expiry_date']);
    $document_issued_in = mysqli_real_escape_string($conn, $_POST['document_issued_in']);
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $birth_country = mysqli_real_escape_string($conn, $_POST['birth_country']);
    $birth_city = mysqli_real_escape_string($conn, $_POST['birth_city']);
    $birth_province = mysqli_real_escape_string($conn, $_POST['birth_province']);
    $residence_department = mysqli_real_escape_string($conn, $_POST['residence_department']);
    $residence_municipality = mysqli_real_escape_string($conn, $_POST['residence_municipality']);
    $residence_zone = mysqli_real_escape_string($conn, $_POST['residence_zone']);
    $residence_street = mysqli_real_escape_string($conn, $_POST['residence_street']);
    $residence_number = mysqli_real_escape_string($conn, $_POST['residence_number']);
    $residence_building = mysqli_real_escape_string($conn, $_POST['residence_building']);
    $residence_floor = mysqli_real_escape_string($conn, $_POST['residence_floor']);
    $residence_apartment = mysqli_real_escape_string($conn, $_POST['residence_apartment']);
    $emergency_contact_name = mysqli_real_escape_string($conn, $_POST['emergency_contact_name']);
    $emergency_contact_phone = mysqli_real_escape_string($conn, $_POST['emergency_contact_phone']);
    $emergency_contact_relationship = mysqli_real_escape_string($conn, $_POST['emergency_contact_relationship']);
    $basic_education_course = mysqli_real_escape_string($conn, $_POST['basic_education_course']);
    $basic_education_year = mysqli_real_escape_string($conn, $_POST['basic_education_year']);
    $basic_education_institution = mysqli_real_escape_string($conn, $_POST['basic_education_institution']);
    $basic_education_location = mysqli_real_escape_string($conn, $_POST['basic_education_location']);

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
    $query = "UPDATE user 
              SET firstname = '$firstname', middlename = '$middlename', thirdname = '$thirdname',
                  lastname_father = '$lastname_father', lastname_mother = '$lastname_mother', married_lastname = '$married_lastname',
                  marital_status = '$marital_status', gender = '$gender', blood_type = '$blood_type', 
                  document_type = '$document_type', document_number = '$document_number', 
                  document_expiry_date = '$document_expiry_date', document_issued_in = '$document_issued_in',
                  birth_date = '$birth_date', birth_country = '$birth_country', birth_city = '$birth_city',
                  birth_province = '$birth_province', residence_department = '$residence_department', 
                  residence_municipality = '$residence_municipality', residence_zone = '$residence_zone',
                  residence_street = '$residence_street', residence_number = '$residence_number',
                  residence_building = '$residence_building', residence_floor = '$residence_floor',
                  residence_apartment = '$residence_apartment', emergency_contact_name = '$emergency_contact_name',
                  emergency_contact_phone = '$emergency_contact_phone', emergency_contact_relationship = '$emergency_contact_relationship',
                  basic_education_course = '$basic_education_course', basic_education_year = '$basic_education_year',
                  basic_education_institution = '$basic_education_institution', basic_education_location = '$basic_education_location'
              WHERE user_id = '$user_id'";

    if (mysqli_query($conn, $query)) {
        $success = "Datos actualizados exitosamente.";
    } else {
        $error = "Error al actualizar los datos: " . mysqli_error($conn);
    }
}

// Obtener los datos actuales del usuario
$query = "SELECT * FROM user WHERE user_id = '$user_id'";
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
                    <h2 class="h3 mb-4">Editar Perfil</h2>

                    <!-- Mensajes de éxito o error -->
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (isset($success) && $success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <!-- Avatar -->
                    <div class="text-center mb-4">
                        <div class="avatar-container mb-3">
                            <?php if ($user['profile_img']): ?>
                                <img src="/intranet/uploads/profile_images/<?php echo $user['profile_img']; ?>" alt="Imagen de Perfil" class="rounded-circle" style="width: 150px; height: 150px;">
                            <?php else: ?>
                                <img src="./assets/avatars/face.jpg" alt="Default Profile Image" class="rounded-circle" style="width: 150px; height: 150px;">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="profile_img">Actualizar Imagen</label>
                            <input type="file" name="profile_img" id="profile_img" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="update_profile" value="1">

                        <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Datos Personales</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstname">Primer Nombre</label>
                                    <input type="text" id="firstname" name="firstname" class="form-control" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="middlename">Segundo Nombre</label>
                                    <input type="text" id="middlename" name="middlename" class="form-control" value="<?php echo htmlspecialchars($user['middlename']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="thirdname">Tercer Nombre</label>
                                    <input type="text" id="thirdname" name="thirdname" class="form-control" value="<?php echo htmlspecialchars($user['thirdname']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastname_father">Apellido Paterno</label>
                                    <input type="text" id="lastname_father" name="lastname_father" class="form-control" value="<?php echo htmlspecialchars($user['lastname_father']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastname_mother">Apellido Materno</label>
                                    <input type="text" id="lastname_mother" name="lastname_mother" class="form-control" value="<?php echo htmlspecialchars($user['lastname_mother']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="married_lastname">Apellido de Casada</label>
                                    <input type="text" id="married_lastname" name="married_lastname" class="form-control" value="<?php echo htmlspecialchars($user['married_lastname']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="marital_status">Estado Civil</label>
                                    <input type="text" id="marital_status" name="marital_status" class="form-control" value="<?php echo htmlspecialchars($user['marital_status']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender">Sexo</label>
                                    <select id="gender" name="gender" class="form-control">
                                        <option value="M" <?php echo ($user['gender'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                        <option value="F" <?php echo ($user['gender'] == 'F') ? 'selected' : ''; ?>>Femenino</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="blood_type">Grupo Sanguíneo</label>
                                    <input type="text" id="blood_type" name="blood_type" class="form-control" value="<?php echo htmlspecialchars($user['blood_type']); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documentación -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Documentación</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="document_type">Tipo de Documento</label>
                                    <input type="text" id="document_type" name="document_type" class="form-control" value="<?php echo htmlspecialchars($user['document_type']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="document_number">Número de Documento</label>
                                    <input type="text" id="document_number" name="document_number" class="form-control" value="<?php echo htmlspecialchars($user['document_number']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="document_expiry_date">Fecha de Caducidad</label>
                                    <input type="date" id="document_expiry_date" name="document_expiry_date" class="form-control" value="<?php echo htmlspecialchars($user['document_expiry_date']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="document_issued_in">Lugar de Expedición</label>
                                    <input type="text" id="document_issued_in" name="document_issued_in" class="form-control" value="<?php echo htmlspecialchars($user['document_issued_in']); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                        <!-- Ubicación -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Ubicación</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="birth_date">Fecha de Nacimiento</label>
                                        <input type="date" id="birth_date" name="birth_date" class="form-control" value="<?php echo htmlspecialchars($user['birth_date']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="birth_country">País de Nacimiento</label>
                                        <input type="text" id="birth_country" name="birth_country" class="form-control" value="<?php echo htmlspecialchars($user['birth_country']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="birth_city">Ciudad de Nacimiento</label>
                                        <input type="text" id="birth_city" name="birth_city" class="form-control" value="<?php echo htmlspecialchars($user['birth_city']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="birth_province">Provincia de Nacimiento</label>
                                        <input type="text" id="birth_province" name="birth_province" class="form-control" value="<?php echo htmlspecialchars($user['birth_province']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="residence_department">Departamento de Residencia</label>
                                        <input type="text" id="residence_department" name="residence_department" class="form-control" value="<?php echo htmlspecialchars($user['residence_department']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="residence_municipality">Municipio</label>
                                        <input type="text" id="residence_municipality" name="residence_municipality" class="form-control" value="<?php echo htmlspecialchars($user['residence_municipality']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="residence_zone">Zona</label>
                                        <input type="text" id="residence_zone" name="residence_zone" class="form-control" value="<?php echo htmlspecialchars($user['residence_zone']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="residence_street">Avenida o Calle</label>
                                        <input type="text" id="residence_street" name="residence_street" class="form-control" value="<?php echo htmlspecialchars($user['residence_street']); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="residence_number">Número</label>
                                        <input type="text" id="residence_number" name="residence_number" class="form-control" value="<?php echo htmlspecialchars($user['residence_number']); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="residence_building">Edificio</label>
                                        <input type="text" id="residence_building" name="residence_building" class="form-control" value="<?php echo htmlspecialchars($user['residence_building']); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="residence_floor">Piso</label>
                                        <input type="text" id="residence_floor" name="residence_floor" class="form-control" value="<?php echo htmlspecialchars($user['residence_floor']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="residence_apartment">Número de Departamento</label>
                                        <input type="text" id="residence_apartment" name="residence_apartment" class="form-control" value="<?php echo htmlspecialchars($user['residence_apartment']); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contacto de Emergencia -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Contacto de Emergencia</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="emergency_contact_name">Nombre</label>
                                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-control" value="<?php echo htmlspecialchars($user['emergency_contact_name']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="emergency_contact_phone">Teléfono</label>
                                        <input type="text" id="emergency_contact_phone" name="emergency_contact_phone" class="form-control" value="<?php echo htmlspecialchars($user['emergency_contact_phone']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="emergency_contact_relationship">Relación</label>
                                        <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" class="form-control" value="<?php echo htmlspecialchars($user['emergency_contact_relationship']); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Educación Básica -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Educación Básica</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="basic_education_course">Último Curso Vencido</label>
                                        <input type="text" id="basic_education_course" name="basic_education_course" class="form-control" value="<?php echo htmlspecialchars($user['basic_education_course']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="basic_education_year">Año del Curso</label>
                                        <input type="text" id="basic_education_year" name="basic_education_year" class="form-control" value="<?php echo htmlspecialchars($user['basic_education_year']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="basic_education_institution">Institución</label>
                                        <input type="text" id="basic_education_institution" name="basic_education_institution" class="form-control" value="<?php echo htmlspecialchars($user['basic_education_institution']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="basic_education_location">Lugar</label>
                                        <input type="text" id="basic_education_location" name="basic_education_location" class="form-control" value="<?php echo htmlspecialchars($user['basic_education_location']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="has_basic_degree">¿Título de Educación Básica?</label>
                                        <select id="has_basic_degree" name="has_basic_degree" class="form-control">
                                            <option value="1" <?php echo ($user['has_basic_degree'] == 1) ? 'selected' : ''; ?>>Sí</option>
                                            <option value="0" <?php echo ($user['has_basic_degree'] == 0) ? 'selected' : ''; ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón de Guardar -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>



    <?php include_once 'app/complements/footer.php'; ?>

</body>
</html>
