<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");
include_once 'app/complements/header.php';
?>
<style>
    .progress {
        width: 100%;
        background-color: #f3f3f3; /* Color del fondo */
        border: 1px solid #ccc;
        border-radius: 5px;
        margin: 20px 0;
        height: 25px;
        overflow: hidden;
    }

    .progress-bar {
        width: 0%; /* Comienza vacío */
        height: 100%;
        background-color: #4caf50; /* Verde */
        color: white;
        text-align: center;
        line-height: 25px; /* Alinea el texto verticalmente */
        font-size: 14px;
        transition: width 0.3s ease; /* Animación suave */
    }
</style>

<!-- navegador principal -->
<?php include 'app/complements/navbar-main.php' ?>
<!-- fin navegador principal -->

<!-- Modal de Noticias (incluido desde archivo separado) -->
<?php include 'news_modal.php'; ?>
<!-- fin Modal de Noticias -->
<!-- barra de navegación lateral -->
<?php include 'app/funcionts/sidebar.php' ?>
<!-- fin de barra de navegación lateral -->

<!-- contenido -->
<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="page-title">Perfil del Empleado</h2>
                <div class="card shadow mb-4 mt-4">
                    <div class="card-header">
                        <strong class="card-title">Detalles del Empleado</strong>
                    </div>
                    <div class="card-body">
                        <?php
                        $query = mysqli_query($conn, 
                        "SELECT u.*, r.repository_name, s.section_name, s.section_id, ro.role_name 
                        FROM `user` u 
                        LEFT JOIN `repositories` r ON u.repository_id = r.repository_id 
                        LEFT JOIN `sections` s ON u.section_id = s.section_id 
                        LEFT JOIN `roles` ro ON u.role_id = ro.role_id 
                        WHERE u.`user_id` = '$_SESSION[user_id]'"
                        ) or die(mysqli_error($conn));
                        
                        $fetch = mysqli_fetch_array($query);
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="employee_name">Nombre</label>
                                    <input type="text" id="employee_name" class="form-control" value="<?php echo $fetch['firstname'] . " " . $fetch['lastname'] ?>" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="employee_email">Correo Institucional</label>
                                    <input type="text" id="employee_email" class="form-control" value="<?php echo $fetch['email']; ?>" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="employee_role">Rol</label>
                                    <input type="text" id="employee_role" class="form-control" value="<?php echo $fetch['role_name']; ?>" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="employee_repository">Área organizacional</label>
                                    <input type="text" id="employee_repository" class="form-control" value="<?php echo $fetch['repository_name']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card shadow mb-4">
                                    <div class="card-header">
                                        <strong>Subir Archivo</strong>
                                    </div>
                                    <div class="card-body">
                                        <!-- Formulario para subir archivos -->
                                        <form action="save_file.php" method="post" enctype="multipart/form-data">
                                            <!-- Mostrar Área organizacional del usuario (no editable) -->
                                            <div class="form-group">
                                                <label for="repository_name">Área organizacional</label>
                                                <input type="text" id="repository_name" class="form-control" 
                                                    value="<?php echo $fetch['repository_name']; ?>" readonly>
                                                <input type="hidden" name="repository_id" value="<?php echo $fetch['repository_id']; ?>">
                                            </div>

                                            <!-- Unidad Organizacional -->
                                            <div class="form-group">
                                                <label for="section_name">Unidad Organizacional</label>
                                                <?php if ($fetch['role_id'] == 1 || $fetch['role_id'] == 4) { ?>
                                                    <select id="section_id" name="section_id" class="form-control">
                                                        <option value="">Seleccione una Unidad Organizacional</option>
                                                        <?php
                                                        $sections_query = mysqli_query($conn, 
                                                            "SELECT * FROM sections WHERE repository_id = '{$fetch['repository_id']}' AND status = 1");
                                                        while ($section = mysqli_fetch_array($sections_query)) {
                                                            $selected = ($section['section_id'] == $fetch['section_id']) ? 'selected' : '';
                                                            echo "<option value='{$section['section_id']}' $selected>{$section['section_name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                <?php } else { ?>
                                                    <input type="text" id="section_name" class="form-control" 
                                                        value="<?php echo $fetch['section_name']; ?>" readonly>
                                                    <input type="hidden" id="section_id" name="section_id" 
                                                        value="<?php echo $fetch['section_id']; ?>">
                                                <?php } ?>
                                            </div>

                                            <!-- Selector de carpeta -->
                                            <div class="form-group">
                                                <label for="category_id">Carpeta</label>
                                                <select class="form-control" id="category_id" name="category_id" required>
                                                    <option value="">Seleccione una Carpeta</option>
                                                </select>
                                            </div>
                                                <!-- Campo para seleccionar archivo -->
                                                <div class="form-group">
                                                    <label for="fileUpload">Seleccione el archivo</label>
                                                    <input type="file" class="form-control-file" id="fileUpload" name="fileUpload" required>
                                                </div>

                                                <!-- ID del usuario -->
                                                <input type="hidden" name="user_id" value="<?php echo $fetch['user_id']; ?>">

                                                <!-- Barra de progreso -->
                                                <div class="progress" id="uploadProgressContainer" style="display: none; height: 30px;">
                                                    <div id="uploadProgressBar" 
                                                        class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                                        role="progressbar" style="width: 0%;" 
                                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                        0%
                                                    </div>
                                                </div>

                                                <!-- Botones de acción -->
                                                <div class="form-group text-center mt-4">
                                                    <button type="submit" id="submitBtn" class="btn btn-success">Guardar Archivo</button>
                                                    <button type="reset" class="btn btn-danger">Cancelar</button>
                                                </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                <!-- Sección de archivos subidos -->
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <strong class="card-title">Archivos Subidos</strong>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nombre de Archivo</th>
                                        <th>Tipo</th>
                                        <th>Fecha de subida</th>
                                        <th>Descargar</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="file-table-body">
                                    <!-- Aquí se insertará la información de los archivos subidos -->
                                    <?php
                                        $files_query = mysqli_query($conn, "SELECT * FROM `storage` WHERE `user_id` = '$fetch[user_id]'") or die(mysqli_error($conn));
                                        while ($file = mysqli_fetch_array($files_query)) {
                                            $statusButtonClass = $file['status'] == 1 ? 'btn-danger' : 'btn-success';
                                            $statusButtonText = $file['status'] == 1 ? 'Deshabilitar' : 'Habilitar';
                                        ?>
                                        <tr class="del_file<?php echo $file['store_id']; ?>">
                                            <!-- Mostrar el nombre del archivo -->
                                            <td><?php echo $file['filename']; ?></td>
                                            <!-- Mostrar el tipo de archivo -->
                                            <td><?php echo $file['file_type']; ?></td>
                                            <!-- Mostrar la fecha de subida -->
                                            <td><?php echo $file['date_uploaded']; ?></td>
                                            <!-- Acción de archivo: Ver para videos, Descargar para otros archivos -->
                                            <td>
                                                <?php if (strpos($file['file_type'], 'video/') === 0) { ?>
                                                    <a href="view_video.php?store_id=<?php echo $file['store_id']; ?>" class="btn btn-info" target="_blank">
                                                        <span class="glyphicon glyphicon-eye-open"></span> Ver
                                                    </a>
                                                <?php } else { ?>
                                                    <a href="download.php?store_id=<?php echo $file['store_id']; ?>" class="btn btn-primary">
                                                        <span class="glyphicon glyphicon-download"></span> Descargar
                                                    </a>
                                                <?php } ?>
                                            </td>
                                            <!-- Botón de habilitar/deshabilitar -->
                                            <td>
                                                <?php if ($file['status'] == 1) { ?>
                                                    <button class="btn btn-danger" type="button" onclick="disableDocument(<?php echo $file['store_id']; ?>)">
                                                        Deshabilitar
                                                    </button>
                                                <?php } else { ?>
                                                    <button class="btn btn-success" type="button" onclick="enableDocument(<?php echo $file['store_id']; ?>)">
                                                        Habilitar
                                                    </button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Fin de la sección de archivos subidos -->

                <!-- Modal de confirmación de eliminación de archivo -->
                <div class="modal fade" id="modal_remove" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Sistema</h3>
                            </div>
                            <div class="modal-body">
                                <center>
                                    <h4 class="text-danger">¿Estás seguro de que quieres eliminar este archivo?</h4>
                                </center>
                            </div>
                            <div class="modal-footer">
                                <a type="button" class="btn btn-success" data-dismiss="modal">No</a>
                                <button type="button" class="btn btn-danger" id="btn_yes">Sí</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fin del modal -->

            </div>
        </div>
    </div>
</main>

<?php
include_once 'app/complements/footer.php';
?>

<script>
// Función para cargar las categorías basadas en la sección seleccionada al cargar la página
$(document).ready(function() {
    var section_id = $('#section_id').val();
    
    if (section_id) {
        $.ajax({
            url: 'get_categories.php',
            type: 'post',
            data: {section_id: section_id},
            dataType: 'json',
            success: function(response) {
                var len = response.length;
                $("#category_id").empty();
                $("#category_id").append("<option value=''>Seleccione una Categoría</option>");
                for (var i = 0; i < len; i++) {
                    if (response[i]['status'] == 1) { // Verificar si la categoría está activa
                        var id = response[i]['category_id'];
                        var name = response[i]['category_name'];
                        $("#category_id").append("<option value='" + id + "'>" + name + "</option>");
                    }
                }
            }
        });
    }
});
</script>

<script>
function disableDocument(storeId) {
    if (confirm('¿Está seguro de que desea deshabilitar este documento?')) {
        $.post('disable_document.php', { disable: true, store_id: storeId }, function(response) {
            window.location.reload();
        });
    }
}

function enableDocument(storeId) {
    if (confirm('¿Está seguro de que desea habilitar este documento?')) {
        $.post('enable_document.php', { enable: true, store_id: storeId }, function(response) {
            window.location.reload();
        });
    }
}
$('#repository_id').on('change', function() {
    var repository_id = $(this).val();
    if (repository_id) {
        $.post('get_sections.php', { repository_id: repository_id }, function(response) {
            var sections = JSON.parse(response);
            $('#section_id').empty().append('<option value="">Seleccione una Unidad Organizacional</option>');
            sections.forEach(function(section) {
                $('#section_id').append('<option value="' + section.section_id + '">' + section.section_name + '</option>');
            });
        });
    } else {
        $('#section_id').empty().append('<option value="">Seleccione una Unidad Organizacional</option>');
    }
});

// Escuchar cambios en el campo section_id para cargar las categorías
$('#section_id').on('change', function() {
    var section_id = $(this).val(); // Obtener el ID de la sección seleccionada

    if (section_id) {
        // Realizar una solicitud AJAX para obtener las categorías asociadas
        $.post('get_categories.php', { section_id: section_id }, function(response) {
            var categories = JSON.parse(response); // Convertir la respuesta a JSON
            $('#category_id').empty().append('<option value="">Seleccione una Carpeta</option>'); // Limpiar y agregar opción por defecto
            
            // Iterar sobre las categorías y agregarlas al select
            categories.forEach(function(category) {
                if (category.status == 1) { // Verificar que la categoría esté activa
                    $('#category_id').append('<option value="' + category.category_id + '">' + category.category_name + '</option>');
                }
            });
        });
    } else {
        // Limpiar el campo de categorías si no hay sección seleccionada
        $('#category_id').empty().append('<option value="">Seleccione una Carpeta</option>');
    }
});

</script>


<script>
document.querySelector('form').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevenir el envío predeterminado del formulario

    // Obtener elementos del DOM
    const form = e.target;
    const submitBtn = document.getElementById('submitBtn');
    const progressContainer = document.getElementById('uploadProgressContainer');
    const progressBar = document.getElementById('uploadProgressBar');
    const fileInput = document.getElementById('fileUpload');
    const file = fileInput.files[0]; // Obtener el archivo seleccionado

    // Validar tamaño del archivo
    const MIN_FILE_SIZE = 1024 * 100; // 100 KB como tamaño mínimo (ajústalo si lo deseas)

    if (file.size < MIN_FILE_SIZE) {
        // Si el archivo es pequeño, enviarlo directamente sin mostrar la barra
        simpleUpload(form, submitBtn);
        return;
    }

    // Mostrar la barra de progreso
    submitBtn.disabled = true;
    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';
    progressBar.innerText = '0%';

    // Crear objeto FormData
    const formData = new FormData(form);

    // Subir archivo con AJAX
    const xhr = new XMLHttpRequest();

    xhr.open('POST', form.action, true);

    // Actualizar barra de progreso
    xhr.upload.onprogress = function (event) {
        if (event.lengthComputable) {
            const percentComplete = Math.round((event.loaded / event.total) * 100);
            progressBar.style.width = percentComplete + '%';
            progressBar.innerText = percentComplete + '%';
        }
    };

    // Manejar la respuesta del servidor
    xhr.onload = function () {
        if (xhr.status === 200) {
            alert('Archivo subido exitosamente.');
            window.location.reload();
        } else {
            alert('Error al subir el archivo.');
        }

        // Resetear la barra de progreso
        progressContainer.style.display = 'none';
        progressBar.style.width = '0%';
        progressBar.innerText = '0%';
        submitBtn.disabled = false;
    };

    xhr.onerror = function () {
        alert('Error al conectar con el servidor.');
        progressContainer.style.display = 'none';
        submitBtn.disabled = false;
    };

    xhr.send(formData);
});

// Subida rápida sin barra de progreso
function simpleUpload(form, submitBtn) {
    const formData = new FormData(form);
    const xhr = new XMLHttpRequest();

    submitBtn.disabled = true;

    xhr.open('POST', form.action, true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            alert('Archivo subido exitosamente.');
            window.location.reload();
        } else {
            alert('Error al subir el archivo.');
        }
        submitBtn.disabled = false;
    };

    xhr.onerror = function () {
        alert('Error al conectar con el servidor.');
        submitBtn.disabled = false;
    };

    xhr.send(formData);
}
</script>

