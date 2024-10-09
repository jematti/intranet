<?php
// Iniciar la sesión solo si no ha sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';
include_once 'app/complements/header.php';

// Consultar la información relevante de la base de datos
// Obtener el total de empleados
$queryEmpleados = "SELECT COUNT(*) as total_empleados FROM user WHERE role_id = 3"; // role_id = 3 para empleados
$resultEmpleados = $conn->query($queryEmpleados);
$rowEmpleados = $resultEmpleados->fetch_assoc();
$totalEmpleados = $rowEmpleados['total_empleados'];

// Obtener el total de administradores
$queryAdmins = "SELECT COUNT(*) as total_admins FROM user WHERE role_id IN (1, 2)"; // role_id = 1 y 2 para admins
$resultAdmins = $conn->query($queryAdmins);
$rowAdmins = $resultAdmins->fetch_assoc();
$totalAdmins = $rowAdmins['total_admins'];

// Obtener el total de secciones
$querySecciones = "SELECT COUNT(*) as total_secciones FROM sections";
$resultSecciones = $conn->query($querySecciones);
$rowSecciones = $resultSecciones->fetch_assoc();
$totalSecciones = $rowSecciones['total_secciones'];

// Obtener el total de categorías
$queryCategorias = "SELECT COUNT(*) as total_categorias FROM categories";
$resultCategorias = $conn->query($queryCategorias);
$rowCategorias = $resultCategorias->fetch_assoc();
$totalCategorias = $rowCategorias['total_categorias'];

// Obtener el total de documentos
$queryDocumentos = "SELECT COUNT(*) as total_documentos FROM storage";
$resultDocumentos = $conn->query($queryDocumentos);
$rowDocumentos = $resultDocumentos->fetch_assoc();
$totalDocumentos = $rowDocumentos['total_documentos'];
?>

<!-- navegador principal -->
<?php include 'app/complements/navbar-main.php'; ?>
<!-- fin navegador principal -->

<!-- Modal de Noticias (incluido desde archivo separado) -->
<?php include 'news_modal.php'; ?>
<!-- fin Modal de Noticias -->

<!-- barra de navegación lateral -->
<?php include 'app/funcionts/sidebar.php'; ?>
<!-- fin de barra de navegación lateral -->

<!-- contenido del dashboard -->
<main role="main" class="main-content">
    <br><br><br>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="row align-items-center mb-2">
                    <div class="col">
                        <h2 class="h5 page-title">Bienvenid@, <?php echo $_SESSION['user_name']; ?>!</h2>
                    </div>
                </div>

                <!-- Información General del Sistema -->
                <div class="row items-align-baseline">
                    <div class="col-md-12 col-lg-4">
                        <div class="card shadow eq-card mb-4">
                            <div class="card-body">
                                <h3>Total Empleados</h3>
                                <p class="h4"><?php echo $totalEmpleados; ?></p>
                                <p class="text-muted">Número total de empleados registrados en el sistema.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-4">
                        <div class="card shadow eq-card mb-4">
                            <div class="card-body">
                                <h3>Total Administradores</h3>
                                <p class="h4"><?php echo $totalAdmins; ?></p>
                                <p class="text-muted">Número total de administradores en el sistema.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-4">
                        <div class="card shadow eq-card mb-4">
                            <div class="card-body">
                                <h3>Total Secciones</h3>
                                <p class="h4"><?php echo $totalSecciones; ?></p>
                                <p class="text-muted">Cantidad de secciones registradas en el sistema.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Categorías y Documentos -->
                <div class="row items-align-baseline">
                    <div class="col-md-12 col-lg-4">
                        <div class="card shadow eq-card mb-4">
                            <div class="card-body">
                                <h3>Total Categorías</h3>
                                <p class="h4"><?php echo $totalCategorias; ?></p>
                                <p class="text-muted">Número total de categorías en el sistema.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-4">
                        <div class="card shadow eq-card mb-4">
                            <div class="card-body">
                                <h3>Total Documentos</h3>
                                <p class="h4"><?php echo $totalDocumentos; ?></p>
                                <p class="text-muted">Cantidad de documentos almacenados en el sistema.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- .col-12 -->
        </div> <!-- .row -->
    </div> <!-- .container-fluid -->
</main>

<?php
include_once 'app/complements/footer.php';
?>
