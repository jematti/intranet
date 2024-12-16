<?php
include("conexion_db.php");
include_once 'app/complements/header.php';

// Verificar si se enviaron parámetros de búsqueda
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$repository_filter = isset($_GET['repository_filter']) ? mysqli_real_escape_string($conn, $_GET['repository_filter']) : '';
$position_filter = isset($_GET['position_filter']) ? mysqli_real_escape_string($conn, $_GET['position_filter']) : '';

// Consulta para obtener los usuarios activos con filtros aplicados
$query = "
    SELECT 
        u.user_id, u.firstname, u.lastname, u.ci, u.email, u.phone, u.cell_phone, u.landline_phone, u.repository_phone, u.personal_email, 
        u.birth_date, u.address, u.profile_img, u.status, u.active_status, 
        p.position_name, r.repository_name, r.building, r.department
    FROM user u
    LEFT JOIN positions p ON u.position_id = p.position_id
    LEFT JOIN repositories r ON u.repository_id = r.repository_id
    WHERE u.active_status = 1
";

// Agregar filtros
if ($search) {
    $query .= " AND (u.firstname LIKE '%$search%' OR u.lastname LIKE '%$search%' OR u.email LIKE '%$search%' OR u.phone LIKE '%$search%')";
}
if ($repository_filter) {
    $query .= " AND r.repository_id = '$repository_filter'";
}
if ($position_filter) {
    $query .= " AND p.position_id = '$position_filter'";
}

$query .= " ORDER BY r.repository_name, u.firstname";
$result = mysqli_query($conn, $query);

// Obtener datos para filtros
$repositories = mysqli_query($conn, "SELECT * FROM repositories WHERE status = 1");
$positions = mysqli_query($conn, "SELECT * FROM positions WHERE status = 1");
?>

<!-- Incluir FontAwesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

<!-- Navegador principal -->
<?php include 'app/complements/navbar-main.php'; ?>
<!-- Fin navegador principal -->

<head>
    <style>
        /* Estilo general de la página */
        body {
            background-color: #f0f4f8;
            font-family: Arial, sans-serif;
        }

        /* Estilo de las tarjetas */
        .card {
            border: none;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
            background-color: #ffffff;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        /* Estilo del avatar */
        .avatar-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Estilo del nombre y cargo */
        .card-title {
            color: #333333;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .small {
            color: #777777;
        }

        /* Estilo del badge */
        .badge-light {
            color: #ffffff;
            background-color: #4A90E2;
            font-size: 0.9rem;
            padding: 0.4rem 0.6rem;
            border-radius: 5px;
        }

        /* Estilo de la sección del footer */
        .card-footer {
            background-color: #f9f9f9;
            border-top: 1px solid #eeeeee;
            padding: 0.75rem;
        }

        /* Estilo del modal */
        .modal-header {
            background: linear-gradient(45deg, #4A90E2, #50B3F0);
            color: #ffffff;
        }

        .modal-body {
            background-color: #f9f9f9;
        }

        .modal-body p {
            margin-bottom: 0.5rem;
        }

        .modal-body .col-md-6 {
            margin-bottom: 0.75rem;
        }

        /* Estilo de los botones del modal */
        .btn-outline-primary {
            border-color: #4A90E2;
            color: #4A90E2;
        }

        .btn-outline-primary:hover {
            background-color: #4A90E2;
            color: #ffffff;
        }

        /* Imagen del usuario en el modal */
        .modal-avatar-container {
            text-align: center;
            margin-bottom: 1rem;
        }

        .modal-avatar-container img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Estilo de los campos ocultos */
        .hidden-field {
            display: none;
        }

        /* Estilo de los iconos */
        .icon {
            margin-right: 8px;
            color: #4A90E2;
        }
    </style>
</head>

<!-- contenido -->
<main role="main">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Barra de búsqueda y filtros -->
                <div class="row align-items-center my-4">
                    <div class="col">
                        <h2 class="h3 mb-0 page-title">Agenda de Contactos</h2>
                    </div>
                </div>
                <form method="GET" action="" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, correo o teléfono" value="<?php echo $search; ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="repository_filter" class="form-control">
                                <option value="">Filtrar por repositorio</option>
                                <?php while ($repo = mysqli_fetch_assoc($repositories)) { ?>
                                    <option value="<?php echo $repo['repository_id']; ?>" <?php echo ($repository_filter == $repo['repository_id']) ? 'selected' : ''; ?>>
                                        <?php echo $repo['repository_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="position_filter" class="form-control">
                                <option value="">Filtrar por posición</option>
                                <?php while ($position = mysqli_fetch_assoc($positions)) { ?>
                                    <option value="<?php echo $position['position_id']; ?>" <?php echo ($position_filter == $position['position_id']) ? 'selected' : ''; ?>>
                                        <?php echo $position['position_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                            <a href="contacts-grid.php" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Limpiar</a>
                        </div>
                    </div>
                </form>

                <!-- Usuarios organizados por repositorio -->
                <?php
                $current_repo = null;
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($current_repo != $row['repository_name']) {
                        if ($current_repo != null) {
                            echo "</div>";  // Cerrar la fila de contactos del repositorio anterior
                        }
                        $current_repo = $row['repository_name'];
                        echo "<h4 class='mb-3 text-primary'>{$current_repo}</h4>";
                        echo "<div class='row'>";
                    }
                ?>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body text-center">
                                <div class="avatar-container">
                                    <a href="#">
                                        <img src="<?php echo $row['profile_img'] ? 'uploads/profile_images/' . $row['profile_img'] : './assets/avatars/face.jpg'; ?>" 
                                             class="rounded-circle avatar-img" 
                                             alt="Profile Image">
                                    </a>
                                </div>
                                <h5 class="card-title mt-3"><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></h5>
                                <p class="text-muted mb-1"><?php echo $row['position_name']; ?></p>
                                <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#userModal<?php echo $row['user_id']; ?>">Ver más</button>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <span><i class="fas fa-envelope icon"></i><?php echo $row['email']; ?></span>
                                <span><i class="fas fa-phone icon"></i><?php echo $row['phone']; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para mostrar todos los detalles -->
                    <div class="modal fade" id="userModal<?php echo $row['user_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="userModalLabel<?php echo $row['user_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="userModalLabel<?php echo $row['user_id']; ?>"><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="modal-avatar-container">
                                        <img src="<?php echo $row['profile_img'] ? 'uploads/profile_images/' . $row['profile_img'] : './assets/avatars/face.jpg'; ?>" 
                                             alt="Profile Image">
                                    </div>
                                    <div class="row">
                                        <!-- Ocultar secciones que no tienen datos -->
                                        <?php if ($row['firstname'] || $row['lastname']) { ?>
                                        <div class="col-md-6">
                                            <p><i class="fas fa-user icon"></i><strong>Nombre:</strong> <?php echo $row['firstname'] . ' ' . $row['lastname']; ?></p>
                                        </div>
                                        <?php } ?>
                                     
                                        <?php if ($row['email']) { ?>
                                        <div class="col-md-6">
                                            <p><i class="fas fa-envelope icon"></i><strong>Correo Institucional:</strong> <?php echo $row['email']; ?></p>
                                        </div>
                                        <?php } ?>
                                        <?php if ($row['personal_email']) { ?>
                                        <div class="col-md-6">
                                            <p><i class="fas fa-envelope icon"></i><strong>Correo Personal:</strong> <?php echo $row['personal_email']; ?></p>
                                        </div>
                                        <?php } ?>
                                        <?php if ($row['repository_phone']) { ?>
                                        <div class="col-md-6">
                                            <p><i class="fas fa-phone icon"></i><strong>Teléfono (Repositorio):</strong> <?php echo $row['repository_phone']; ?></p>
                                        </div>
                                        <?php } ?>
                                        <?php if ($row['phone']) { ?>
                                        <div class="col-md-6">
                                            <p><i class="fas fa-phone icon"></i><strong>Teléfono (Interno):</strong> <?php echo $row['phone']; ?></p>
                                        </div>
                                        <?php } ?>
                                        <?php if ($row['cell_phone']) { ?>
                                        <div class="col-md-6">
                                            <p><i class="fas fa-mobile-alt icon"></i><strong>Celular (Corporativo):</strong> <?php echo $row['cell_phone']; ?></p>
                                        </div>
                                        <?php } ?>
                                        
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- fin de contenido -->

<?php
include_once 'app/complements/footer.php';
?>
