<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");
include_once 'app/complements/header.php';
?>

<!-- Incluir FontAwesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

<!-- navegador principal -->
<?php include 'app/complements/navbar-main.php' ?>
<!-- fin navegador principal -->
<!-- Modal de Noticias (incluido desde archivo separado) -->
<?php include 'news_modal.php'; ?>
<!-- fin Modal de Noticias -->

<br><br><br>

<!-- Contenido Principal -->
<div class="container-fluid">
    <div class="row">
        <!-- Contenido Principal sin sidebar -->
        <main role="main" class="col-md-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h2>Centro de Documentación</h2>
            </div>

            <!-- Filtros de Búsqueda -->
            <div class="form-row">
                <!-- Filtro por Repositorio -->
                <div class="form-group col-md-3">
                    <label for="repository_id">Área Organizacional</label>
                    <select id="repository_id" class="form-control">
                        <option value="">Todos las Áreas</option>
                        <?php
                        $query = mysqli_query($conn, "SELECT * FROM `repositories`");
                        while ($repository = mysqli_fetch_array($query)) {
                            echo "<option value='{$repository['repository_id']}'>{$repository['repository_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Filtro por Sección -->
                <div class="form-group col-md-3">
                    <label for="section_id">Unidad Organizacional</label>
                    <select id="section_id" class="form-control" disabled>
                        <option value="">Todas las Unidades</option>
                    </select>
                </div>

                <!-- Filtro por Carpeta -->
                <div class="form-group col-md-3">
                    <label for="category_id">Carpeta</label>
                    <select id="category_id" class="form-control" disabled>
                        <option value="">Todas las Carpetas</option>
                    </select>
                </div>

                <!-- Filtro por Palabra Clave -->
                <div class="form-group col-md-3">
                    <label for="search_keyword">Palabra Clave</label>
                    <input type="text" id="search_keyword" class="form-control" placeholder="Buscar por palabra clave...">
                </div>
            </div>

            <div class="form-row">
                <!-- Filtro por Fecha de Subida -->
                <div class="form-group col-md-3">
                    <label for="date_from">Fecha desde</label>
                    <input type="date" id="date_from" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label for="date_to">Fecha hasta</label>
                    <input type="date" id="date_to" class="form-control">
                </div>

                <!-- Filtro por Tipo de Archivo -->
                <div class="form-group col-md-3">
                    <label for="file_type">Tipo de Archivo</label>
                    <select id="file_type" class="form-control">
                        <option value="">Todos los Tipos</option>
                        <option value="application/pdf">PDF</option>
                        <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document">Word</option>
                        <option value="image/png">PNG</option>
                        <option value="image/jpeg">JPEG</option>
                        <!-- Agrega más tipos de archivo según sea necesario -->
                    </select>
                </div>

                <!-- Filtro por Usuario que Subió el Archivo -->
                <div class="form-group col-md-3">
                    <label for="uploaded_by">Subido por</label>
                    <select id="uploaded_by" class="form-control">
                        <option value="">Cualquiera</option>
                        <?php
                        $users = mysqli_query($conn, "SELECT * FROM `user` WHERE `active_status` = 1");
                        while ($user = mysqli_fetch_array($users)) {
                            echo "<option value='{$user['user_id']}'>{$user['firstname']} {$user['lastname']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
    <div class="form-group col-md-3">
        <button id="searchBtn" class="btn btn-primary btn-block">
            <i class="fas fa-search"></i> Buscar
        </button>
    </div>
    <div class="form-group col-md-3">
        <button id="clearBtn" class="btn btn-secondary btn-block">
            <i class="fas fa-broom"></i> Limpiar
        </button>
    </div>
</div>

<!-- Resultados de Búsqueda -->
<div class="col-12">
    <div class="card shadow mb-4">
        <div class="card-header">
            <strong class="card-title">Resultados de Documentos</strong>
        </div>
        <div class="card-body">
            <table id="table" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Nombre de Archivo</th>
                        <th>Tipo</th>
                        <th>Fecha de Subida</th>
                        <th>Subido por</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="file-table-body">
                    <!-- Aquí se insertará la información de los archivos subidos -->
                </tbody>
            </table>
            <div id="no-results" class="text-center text-muted" style="display: none;">
                No se encontraron resultados.
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

<!-- Scripts -->
<?php include_once 'app/complements/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const repositorySelect = document.getElementById('repository_id');
    const sectionSelect = document.getElementById('section_id');
    const categorySelect = document.getElementById('category_id');
    const searchKeyword = document.getElementById('search_keyword');
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    const fileType = document.getElementById('file_type');
    const uploadedBy = document.getElementById('uploaded_by');
    const searchBtn = document.getElementById('searchBtn');
    const clearBtn = document.getElementById('clearBtn');
    const tableBody = document.getElementById('file-table-body');
    const noResultsDiv = document.getElementById('no-results');

    // Función para habilitar y cargar secciones cuando se selecciona un repositorio
    repositorySelect.addEventListener('change', function () {
        const repositoryId = this.value;
        sectionSelect.disabled = true;
        categorySelect.disabled = true;
        sectionSelect.innerHTML = '<option value="">Todas las Secciones</option>';
        categorySelect.innerHTML = '<option value="">Todas las Categorías</option>';

        if (repositoryId) {
            fetch(`get_docs_sections.php?repository_id=${repositoryId}`)
                .then(response => response.json())
                .then(data => {
                    sectionSelect.disabled = false;
                    data.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.section_id;
                        option.textContent = section.section_name;
                        sectionSelect.appendChild(option);
                    });
                });
        }
    });

    // Función para habilitar y cargar categorías cuando se selecciona una sección
    sectionSelect.addEventListener('change', function () {
        const sectionId = this.value;
        categorySelect.disabled = true;
        categorySelect.innerHTML = '<option value="">Todas las Categorías</option>';

        if (sectionId) {
            fetch(`get_docs_categories.php?section_id=${sectionId}`)
                .then(response => response.json())
                .then(data => {
                    categorySelect.disabled = false;
                    data.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.category_id;
                        option.textContent = category.category_name;
                        categorySelect.appendChild(option);
                    });
                });
        }
    });

    // Función para realizar la búsqueda al hacer clic en el botón de búsqueda
    searchBtn.addEventListener('click', function () {
    const repositoryId = repositorySelect.value;
    const sectionId = sectionSelect.value;
    const categoryId = categorySelect.value;
    const keyword = searchKeyword.value;
    const fromDate = dateFrom.value;
    const toDate = dateTo.value;
    const fileTypeValue = fileType.value;
    const uploadedById = uploadedBy.value;

        fetch(`search_docs.php?repository_id=${repositoryId}&section_id=${sectionId}&category_id=${categoryId}&keyword=${keyword}&date_from=${fromDate}&date_to=${toDate}&file_type=${fileTypeValue}&uploaded_by=${uploadedById}`)
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = '';
            if (data.length > 0) {
                noResultsDiv.style.display = 'none';
                data.forEach(file => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${file.filename}...</td>
                        <td>${file.file_type}</td>
                        <td>${file.date_uploaded}</td>
                        <td>${file.uploaded_by}</td> <!-- Aquí se usa el campo correcto -->
                        <td>
                            <a href="download.php?store_id=${file.store_id}" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                noResultsDiv.style.display = 'block';
                clearFilters();
            }
        });
    });

    // Función para limpiar los filtros
    clearBtn.addEventListener('click', function () {
        clearFilters();
    });

    // Función para limpiar todos los filtros
    function clearFilters() {
        repositorySelect.value = '';
        sectionSelect.innerHTML = '<option value="">Todas las Secciones</option>';
        sectionSelect.disabled = true;
        categorySelect.innerHTML = '<option value="">Todas las Categorías</option>';
        categorySelect.disabled = true;
        searchKeyword.value = '';
        dateFrom.value = '';
        dateTo.value = '';
        fileType.value = '';
        uploadedBy.value = '';
        tableBody.innerHTML = '';
        noResultsDiv.style.display = 'none';
    }
});
</script>

<!-- CSS adicional para mejorar el estilo -->
<style>
    .form-control {
        margin-bottom: 10px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table thead th {
        text-align: center;
    }

    .table tbody td {
        text-align: center;
    }

    .btn {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }

    .card-title {
        font-size: 1.3rem;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .form-control, .btn {
            font-size: 0.9rem;
        }
    }
</style>

 
