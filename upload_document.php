<?php

require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';
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
                        "SELECT u.*, r.repository_name, ro.role_name 
                        FROM `user` u 
                        LEFT JOIN `repositories` r ON u.repository_id = r.repository_id 
                        LEFT JOIN `roles` ro ON u.role_id = ro.role_id 
                        WHERE u.`user_id` = '$_SESSION[user_id]'"
                        ) or die(mysqli_error($conn));
                        
                        $fetch = mysqli_fetch_array($query);
                    
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- <div class="form-group mb-3">
                                    <label for="employee_no">ID Usuario</label>
                                    <input type="text" id="employee_no" class="form-control" value="<?php echo $fetch['user_id'] ?>" readonly>
                                </div> -->
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
                                    <label for="employee_repository">Repositorio</label>
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
                                            <!-- Mostrar repositorio del usuario (no editable) -->
                                            <div class="form-group">
                                                <label for="repository_name">Repositorio</label>
                                                <input type="text" id="repository_name" class="form-control" value="<?php echo $fetch['repository_name']; ?>" readonly>
                                                <input type="hidden" name="repository_id" value="<?php echo $fetch['repository_id']; ?>">
                                            </div>

                                           <!-- Selector de Sección (obligatorio) -->
                                            <div class="form-group">
                                                <label for="section_id">Sección</label>
                                                <select class="form-control" id="section_id" name="section_id" required>
                                                    <option value="">Seleccione una Sección</option>
                                                    <?php
                                                    $section_query = mysqli_query($conn, "SELECT * FROM `sections` WHERE `repository_id` = '{$fetch['repository_id']}'") or die(mysqli_error($conn));
                                                    while ($section = mysqli_fetch_array($section_query)) {
                                                        echo "<option value='" . $section['section_id'] . "'>" . $section['section_name'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <!-- Selector de Categoría (obligatorio) -->
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
                                                <button class="btn <?php echo $statusButtonClass; ?> toggle-status-btn" type="button" data-id="<?php echo $file['store_id']; ?>">
                                                    <?php echo $statusButtonText?>
                                                </button>
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
document.addEventListener('DOMContentLoaded', () => {
    // Delegación del evento de clic en los botones de estado
    document.querySelector('#file-table-body').addEventListener('click', function(event) {
        const target = event.target;
        if (target.classList.contains('toggle-status-btn')) {
            const storeId = target.getAttribute('data-id');
            toggleFileStatus(storeId, target);
        }
    });
});

function toggleFileStatus(storeId, buttonElement) {
    // Realiza una solicitud AJAX para cambiar el estado del archivo
    fetch(`status_document.php?store_id=${storeId}`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cambia el estado del botón basado en el nuevo estado
            if (data.newStatus === 1) {
                buttonElement.classList.remove('btn-success');
                buttonElement.classList.add('btn-danger');
                buttonElement.textContent = 'Deshabilitar';
            } else {
                buttonElement.classList.remove('btn-danger');
                buttonElement.classList.add('btn-success');
                buttonElement.textContent = 'Habilitar';
            }
        } else {
            alert('Error al cambiar el estado del archivo.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cambiar el estado del archivo.');
    });
}

</script>

<script>
$(document).ready(function(){
    $('#section_id').change(function(){
        var section_id = $(this).val();
        $.ajax({
            url: 'get_categories.php', // Archivo que procesará la solicitud AJAX
            type: 'post',
            data: {section_id: section_id},
            dataType: 'json',
            success: function(response){
                var len = response.length;
                $("#category_id").empty(); // Limpiar el selector de categorías
                $("#category_id").append("<option value=''>Seleccione una Categoría</option>");
                for(var i = 0; i < len; i++){
                    var id = response[i]['category_id'];
                    var name = response[i]['category_name'];
                    $("#category_id").append("<option value='"+id+"'>"+name+"</option>");
                }
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    // Delegación del evento de clic en los botones de estado
    document.querySelector('#file-table-body').addEventListener('click', function(event) {
        const target = event.target;
        if (target.classList.contains('toggle-status-btn')) {
            const storeId = target.getAttribute('data-id');
            toggleFileStatus(storeId, target);
        }
    });
});

function toggleFileStatus(storeId, buttonElement) {
    // Realiza una solicitud AJAX para cambiar el estado del archivo
    fetch('status_document.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `store_id=${storeId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cambia el estado del botón basado en el nuevo estado
            if (data.newStatus === 1) {
                buttonElement.classList.remove('btn-success');
                buttonElement.classList.add('btn-danger');
                buttonElement.textContent = 'Deshabilitar';
            } else {
                buttonElement.classList.remove('btn-danger');
                buttonElement.classList.add('btn-success');
                buttonElement.textContent = 'Habilitar';
            }
        } else {
            alert('Error al cambiar el estado del archivo.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cambiar el estado del archivo.');
    });
}


</script>
