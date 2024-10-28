<?php
require 'app/funcionts/admin/validator.php';
include("conexion_db.php");
include_once 'app/complements/header.php';
?>

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
                                        <form action="save_file.php" method="post" enctype="multipart/form-data">
                                            <!-- Mostrar Área organizacional del usuario (no editable) -->
                                            <div class="form-group">
                                                <label for="repository_name">Área organizacional</label>
                                                <input type="text" id="repository_name" class="form-control" value="<?php echo $fetch['repository_name']; ?>" readonly>
                                                <input type="hidden" name="repository_id" value="<?php echo $fetch['repository_id']; ?>">
                                            </div>

                                            <!-- Unidad Organizacional (automática, no editable) -->
                                            <div class="form-group">
                                                <label for="section_name">Unidad Organizacional</label>
                                                <input type="text" id="section_name" class="form-control" value="<?php echo $fetch['section_name']; ?>" readonly>
                                                <input type="hidden" id="section_id" name="section_id" value="<?php echo $fetch['section_id']; ?>">
                                            </div>

                                            <!-- Selector de Categoría (obligatorio), solo categorías activas -->
                                            <div class="form-group">
                                                <label for="category_id">Categoría</label>
                                                <select class="form-control" id="category_id" name="category_id" required>
                                                    <option value="">Seleccione una Categoría</option>
                                                </select>
                                            </div>

                                            <!-- Campo para seleccionar archivo -->
                                            <div class="form-group">
                                                <label for="fileUpload">Seleccione el archivo</label>
                                                <input type="file" class="form-control-file" id="fileUpload" name="fileUpload">
                                            </div>

                                            <!-- Campo oculto para el ID del usuario -->
                                            <input type="hidden" name="user_id" value="<?php echo $fetch['user_id'] ?>">

                                            <!-- Botones de acción -->
                                            <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-success">Guardar Archivo</button>
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
                                            <td><?php echo substr($file['filename'], 0, 30); ?>...</td>
                                            <td><?php echo $file['file_type']; ?></td>
                                            <td><?php echo $file['date_uploaded']; ?></td>
                                            <td>
                                                <a href="download.php?store_id=<?php echo $file['store_id']; ?>" class="btn btn-primary">
                                                    <span class="glyphicon glyphicon-download"></span> Descargar
                                                </a>
                                            </td>
                                            <td>
                                                <?php if ($file['status'] == 1) { ?>
                                                    <button class="btn btn-danger" type="button" onclick="disableDocument(<?php echo $file['store_id']; ?>)">Deshabilitar</button>
                                                <?php } else { ?>
                                                    <button class="btn btn-success" type="button" onclick="enableDocument(<?php echo $file['store_id']; ?>)">Habilitar</button>
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
</script>

