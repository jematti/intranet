<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");
include_once 'app/complements/header.php';
?>

<!-- Incluir FontAwesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

<!-- navegador principal  -->
<?php include 'app/complements/navbar-main.php' ?>
<!-- fin navegador principal -->
<!-- Modal de Noticias (incluido desde archivo separado) -->
<?php include 'news_modal.php'; ?>
<!-- fin Modal de Noticias -->
<br><br><br>

<!-- Contenido Principal sin sidebar -->
<div class="container-fluid">
    <div class="row">
        <!-- Contenido Principal -->
        <main role="main" class="col-md-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h2>Centro de Documentación</h2>

                <!-- Botón que lleva al filtro de búsqueda -->
                <a href="documents_main.php" class="btn btn-primary" role="button">
                    <i class="fas fa-search"></i> Filtrar Documentos
                </a>
            </div>

            <!-- Sección de archivos organizados por repositorio, sección, categoría -->
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <strong class="card-title">Documentación</strong>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="documentAccordion">
                            <?php
                            // Obtener todos los repositorios habilitados
                            $query_repositories = mysqli_query($conn, "SELECT * FROM `repositories` WHERE `status` = 1");

                            while ($repository = mysqli_fetch_assoc($query_repositories)) {
                                $repoId = $repository['repository_id'];
                                echo "
                                <div class='card'>
                                    <div class='card-header' id='headingRepo{$repoId}'>
                                        <h2 class='mb-0'>
                                            <button class='btn btn-link btn-block text-left repo-toggle' type='button' data-toggle='collapse' data-target='#collapseRepo{$repoId}' aria-expanded='true' aria-controls='collapseRepo{$repoId}'>
                                                <i class='fas fa-chevron-right mr-2'></i> {$repository['repository_name']}
                                            </button>
                                        </h2>
                                    </div>
                                    <div id='collapseRepo{$repoId}' class='collapse' aria-labelledby='headingRepo{$repoId}' data-parent='#documentAccordion'>
                                        <div class='card-body'>";

                                        // Obtener secciones habilitadas por repositorio
                                        $query_sections = mysqli_query($conn, "SELECT * FROM `sections` WHERE `repository_id` = {$repoId} AND `status` = 1");
                                        while ($section = mysqli_fetch_assoc($query_sections)) {
                                            $sectionId = $section['section_id'];
                                            echo "
                                            <div class='ml-4'>
                                                <button class='btn btn-link section-toggle' type='button' data-toggle='collapse' data-target='#collapseSection{$sectionId}' aria-expanded='false' aria-controls='collapseSection{$sectionId}'>
                                                    <i class='fas fa-chevron-right mr-2'></i> {$section['section_name']}
                                                </button>
                                                <div id='collapseSection{$sectionId}' class='collapse' data-parent='#collapseRepo{$repoId}'>
                                                    <div class='ml-4'>";

                                                    // Obtener categorías habilitadas por sección
                                                    $query_categories = mysqli_query($conn, "SELECT * FROM `categories` WHERE `section_id` = {$sectionId} AND `status` = 1");
                                                    while ($category = mysqli_fetch_assoc($query_categories)) {
                                                        $categoryId = $category['category_id'];
                                                        echo "
                                                        <div class='ml-3'>
                                                            <button class='btn btn-link category-toggle' type='button' data-toggle='collapse' data-target='#collapseCategory{$categoryId}' aria-expanded='false' aria-controls='collapseCategory{$categoryId}'>
                                                                <i class='fas fa-chevron-right mr-2'></i> {$category['category_name']}
                                                            </button>
                                                            <div id='collapseCategory{$categoryId}' class='collapse' data-parent='#collapseSection{$sectionId}'>
                                                                <table class='table table-striped table-hover'>
                                                                    <thead>
                                                                        <tr>
                                                                            <th style='width: 60%;'>Nombre de Archivo</th>
                                                                            <th style='width: 20%;'>Fecha de Subida</th>
                                                                            <th style='width: 20%;'>Acción</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>";

                                                               // Obtener archivos habilitados por categoría
                                                                $query_files = mysqli_query($conn, "SELECT * FROM `storage` WHERE `category_id` = {$categoryId} AND `status` = 1");
                                                                if (mysqli_num_rows($query_files) > 0) {
                                                                    while ($file = mysqli_fetch_assoc($query_files)) {
                                                                        $fileType = $file['file_type'];
                                                                        $fileName = htmlspecialchars($file['filename'], ENT_QUOTES, 'UTF-8'); // Evitar inyección de código
                                                                        $storeId = intval($file['store_id']); // Sanitizar ID
                                                                        $dateUploaded = htmlspecialchars($file['date_uploaded'], ENT_QUOTES, 'UTF-8'); // Evitar inyección de código

                                                                        // Verificar si el archivo es un video
                                                                        if (strpos($fileType, 'video/') === 0) {
                                                                            echo "
                                                                            <tr>
                                                                                <td>{$fileName}</td>
                                                                                <td>{$dateUploaded}</td>
                                                                                <td>
                                                                                    <button class='btn btn-info btn-sm' onclick='openVideo(\"{$storeId}\")'>
                                                                                        Ver <i class='fas fa-eye'></i>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>";
                                                                        } else {
                                                                            echo "
                                                                            <tr>
                                                                                <td>{$fileName}</td>
                                                                                <td>{$dateUploaded}</td>
                                                                                <td>
                                                                                    <button class='btn btn-success btn-sm' onclick='confirmDownload({$storeId})'>
                                                                                        Descargar <i class='fas fa-download'></i>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>";
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo "<tr><td colspan='3' class='text-muted'>No hay archivos disponibles.</td></tr>";
                                                                }


                                                        echo "
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>";
                                                    }

                                            echo "
                                                    </div>
                                                </div>
                                            </div>";
                                        }

                                echo "
                                        </div>
                                    </div>
                                </div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<!-- fin de contenido -->

<?php
include_once 'app/complements/footer.php';
?>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Iniciar el componente de acordeón de Bootstrap
        $('.collapse').collapse();

        // Cambiar el ícono de chevron cuando se colapsa o expande
        $('.collapse').on('shown.bs.collapse', function () {
            $(this).parent().find('.fas').removeClass('fa-chevron-right').addClass('fa-chevron-down');
        }).on('hidden.bs.collapse', function () {
            $(this).parent().find('.fas').removeClass('fa-chevron-down').addClass('fa-chevron-right');
        });
    });

    // Función para abrir un video en una nueva pestaña
    function openVideo(storeId) {
        window.open('view_video.php?store_id=' + storeId, '_blank');
    }

    // Función para confirmar la descarga
    function confirmDownload(storeId) {
        if (confirm('¿Estás seguro de que deseas descargar este archivo?')) {
            window.location.href = 'download.php?store_id=' + storeId;
        }
    }

</script>

<!-- CSS para animaciones, estilo diferenciado y archivos -->
<style>
    /* Estilo por nivel */
    .repo-toggle {
        font-size: 1.5rem;
        font-weight: bold;
        color: #343a40;
        transition: color 0.3s;
    }

    .section-toggle {
        font-size: 1.3rem;
        font-weight: bold;
        color: #495057;
        transition: color 0.3s;
    }

    .category-toggle {
        font-size: 1.2rem;
        font-weight: normal;
        color: #6c757d;
        transition: color 0.3s;
    }

    /* Estilo de las tablas */
    table.table {
        width: 100%;
        border-collapse: collapse;
    }

    table.table th, table.table td {
        padding: 10px;
        border-bottom: 1px solid #dee2e6;
    }

    table.table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    /* Cambiar color de los botones al hacer hover */
    .repo-toggle:hover, .section-toggle:hover, .category-toggle:hover {
        color: #007bff;
        text-decoration: none;
    }

    /* Ajustar iconos */
    .fas {
        transition: transform 0.3s;
    }

    /* Ajustar el estilo de los archivos */
    .file-info p {
        margin: 0;
    }

    /* Estilo del botón de filtro */
    .btn-primary {
        background-color: #007bff;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
    }

    .btn-primary i {
        margin-right: 0.5rem;
    }
</style>
