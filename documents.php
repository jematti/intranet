<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");
include_once 'app/complements/header.php';
?>

<!-- navegador principal  -->
<?php include 'app/complements/navbar-main.php' ?>
<!-- fin navegador principal  -->
<!-- Modal de Noticias (incluido desde archivo separado) -->
<?php include 'news_modal.php'; ?>
<!-- fin Modal de Noticias -->
<br><br><br>

<!-- Contenedor principal con selector de vistas -->
<div class="container-fluid">
    <div class="row">
        <!-- Contenido Principal -->
        <main role="main" class="col-md-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h2>Centro de Documentación</h2>
                <!-- Botones para cambiar vista -->
                <div>
                    <button class="btn btn-primary" id="btn-tabla" onclick="mostrarVista('tabla')">Vista Tabla</button>
                    <button class="btn btn-secondary" id="btn-acordeon" onclick="mostrarVista('acordeon')">Vista Acordeón</button>
                </div>
            </div>

            <!-- Vista en Tabla -->
            <div id="vista-tabla" class="document-view">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <strong class="card-title">Documentos Disponibles (Vista Tabla)</strong>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nombre de Archivo</th>
                                        <th>Tipo</th>
                                        <th>Fecha de subida</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="file-table-body">
                                    <!-- Aquí se insertará la información de los archivos subidos -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vista en Acordeón -->
            <div id="vista-acordeon" class="document-view" style="display: none;">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <strong class="card-title">Documentación (Vista Acordeón)</strong>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="documentAccordion">
                                <?php
                                // Obtener todos los repositorios
                                $query_repositories = mysqli_query($conn, "SELECT * FROM `repositories`");

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

                                            // Obtener secciones por repositorio
                                            $query_sections = mysqli_query($conn, "SELECT * FROM `sections` WHERE `repository_id` = {$repoId}");
                                            while ($section = mysqli_fetch_assoc($query_sections)) {
                                                $sectionId = $section['section_id'];
                                                echo "
                                                <div class='ml-4'>
                                                    <button class='btn btn-link section-toggle' type='button' data-toggle='collapse' data-target='#collapseSection{$sectionId}' aria-expanded='false' aria-controls='collapseSection{$sectionId}'>
                                                        <i class='fas fa-chevron-right mr-2'></i> {$section['section_name']}
                                                    </button>
                                                    <div id='collapseSection{$sectionId}' class='collapse' data-parent='#collapseRepo{$repoId}'>
                                                        <div class='ml-4'>";

                                                        // Obtener categorías por sección
                                                        $query_categories = mysqli_query($conn, "SELECT * FROM `categories` WHERE `section_id` = {$sectionId}");
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

                                                                    // Obtener archivos por categoría solo si están activos
                                                                    $query_files = mysqli_query($conn, "SELECT * FROM `storage` WHERE `category_id` = {$categoryId} AND `status` = 1");
                                                                    if (mysqli_num_rows($query_files) > 0) {
                                                                        while ($file = mysqli_fetch_assoc($query_files)) {
                                                                            echo "
                                                                            <tr>
                                                                                <td>{$file['filename']}</td>
                                                                                <td>{$file['date_uploaded']}</td>
                                                                                <td>
                                                                                    <button class='btn btn-success btn-sm' onclick='confirmDownload({$file['store_id']})'>
                                                                                        Descargar <i class='fas fa-download'></i>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>";
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
            </div>

        </main>
    </div>
</div>

<?php
include_once 'app/complements/footer.php';
?>

<script>
    // Función para mostrar la vista seleccionada
    function mostrarVista(vista) {
        const vistaTabla = document.getElementById('vista-tabla');
        const vistaAcordeon = document.getElementById('vista-acordeon');
        const btnTabla = document.getElementById('btn-tabla');
        const btnAcordeon = document.getElementById('btn-acordeon');

        if (vista === 'tabla') {
            vistaTabla.style.display = 'block';
            vistaAcordeon.style.display = 'none';
            btnTabla.classList.add('btn-primary');
            btnAcordeon.classList.remove('btn-primary');
            btnAcordeon.classList.add('btn-secondary');
        } else {
            vistaTabla.style.display = 'none';
            vistaAcordeon.style.display = 'block';
            btnAcordeon.classList.add('btn-primary');
            btnTabla.classList.remove('btn-primary');
            btnTabla.classList.add('btn-secondary');
        }
    }

    // Función para confirmar la descarga
    function confirmDownload(storeId) {
        if (confirm('¿Estás seguro de que deseas descargar este archivo?')) {
            window.location.href = 'download.php?store_id=' + storeId;
        }
    }
</script>

<style>
    /* Estilos por nivel en la vista de acordeón */
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

    /* Cambiar color de los botones al hacer hover */
    .repo-toggle:hover, .section-toggle:hover, .category-toggle:hover {
        color: #007bff;
        text-decoration: none;
    }

    /* Ajustar el estilo de las tablas */
    table.table th, table.table td {
        padding: 10px;
        border-bottom: 1px solid #dee2e6;
    }

    table.table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
</style>
