<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';
include_once 'app/complements/header.php';

// Obtener los roles disponibles
$roles_query = mysqli_query($conn, "SELECT * FROM roles") or die(mysqli_error($conn));

// Obtener las posiciones disponibles
$positions_query = mysqli_query($conn, "SELECT * FROM positions") or die(mysqli_error($conn));

// Obtener los repositorios disponibles
$repositories_query = mysqli_query($conn, "SELECT * FROM repositories") or die(mysqli_error($conn));
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
    <div id="content">
        <br /><br /><br />
        <div class="alert alert-info"><h3>Administradores</h3></div>
        <button class="btn btn-success" onclick="openModal(false);"><span class="glyphicon glyphicon-plus"></span> Agregar Usuario</button>
        <br /><br />
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th>CI</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Celular</th>
                    <th>Repositorio</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $query = mysqli_query($conn, "SELECT u.*, r.role_name, p.position_name, repo.repository_name 
                                                FROM `user` u 
                                                JOIN `roles` r ON u.role_id = r.role_id 
                                                LEFT JOIN `positions` p ON u.position_id = p.position_id 
                                                LEFT JOIN `repositories` repo ON u.repository_id = repo.repository_id") 
                                                or die(mysqli_error($conn));
                    while($fetch = mysqli_fetch_array($query)){
                ?>
                <tr class="del_user<?php echo $fetch['user_id']?>">
                    <td><?php echo $fetch['ci']?></td>
                    <td><?php echo $fetch['firstname']?></td>
                    <td><?php echo $fetch['lastname']?></td>
                    <td><?php echo $fetch['username']?></td>
                    <td><?php echo $fetch['email']?></td>
                    <td><?php echo $fetch['cell_phone'] ? $fetch['cell_phone'] : 'N/A' ?></td>
                    <td><?php echo $fetch['position_name'] ? $fetch['position_name'] : 'N/A' ?></td>
                    <td><?php echo $fetch['repository_name'] ? $fetch['repository_name'] : 'N/A' ?></td>
                    <td><?php echo $fetch['role_name']?></td>
                    <td>
                        <?php if($fetch['active_status'] == 1): ?>
                            <span class="badge badge-success">Activo</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <center>
                            <button class="btn btn-warning mb-1" onclick="getUserData(<?php echo $fetch['user_id']?>);"><span class="glyphicon glyphicon-edit"></span> Editar</button>
                            <button class="btn btn-danger btn-toggle-status" data-id="<?php echo $fetch['user_id']?>" data-status="<?php echo $fetch['active_status']?>">
                                <?php echo ($fetch['active_status'] == 1) ? 'Desactivar' : 'Activar'; ?>
                            </button>
                        </center>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </tbody>

        </table>
    </div>

    <!-- Modal para Agregar/Editar Usuario -->
    <div class="modal fade" id="form_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="user_form" method="POST" action="admin_save_user.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal_title">Agregar Usuario</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- Campo oculto para user_id -->
                                <input type="hidden" name="user_id" id="user_id">
                                <div class="col-md-6">
                                    <!-- Primera columna -->
                                    <div class="form-group">
                                        <label for="ci">CI <span class="text-danger">*</span></label>
                                        <input type="text" name="ci" class="form-control" id="ci" required/>
                                    </div>
                                    <div class="form-group">
                                        <label for="firstname">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" name="firstname" class="form-control" id="firstname" required/>
                                    </div>
                                    <div class="form-group">
                                        <label for="lastname">Apellidos <span class="text-danger">*</span></label>
                                        <input type="text" name="lastname" class="form-control" id="lastname" required/>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Correo Institucional</label>
                                        <input type="email" name="email" class="form-control" id="email"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="cell_phone">Celular</label>
                                        <input type="text" name="cell_phone" class="form-control" id="cell_phone"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Dirección</label>
                                        <input type="text" name="address" class="form-control" id="address"/>
                                    </div>
                                    
                                </div>
                                <div class="col-md-6">
                                    <!-- Segunda columna -->
                                    <div class="form-group">
                                        <label for="username">Usuario <span class="text-danger">*</span></label>
                                        <input type="text" name="username" class="form-control" id="username" required/>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Contraseña (dejar en blanco para mantener actual)</label>
                                        <input type="password" name="password" class="form-control" id="password" required/>
                                    </div>
                                    <div class="form-group">
                                        <label for="personal_email">Correo Personal</label>
                                        <input type="email" name="personal_email" class="form-control" id="personal_email"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Teléfono</label>
                                        <input type="text" name="phone" class="form-control" id="phone"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="birth_date">Fecha de Nacimiento</label>
                                        <input type="date" name="birth_date" class="form-control" id="birth_date"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="profile_img">Imagen de Perfil (Opcional)</label>
                                        <input type="file" name="profile_img" class="form-control" id="profile_img" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Posición, Repositorio, y Rol -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="position_id">Posición <span class="text-danger">*</span></label>
                                        <select name="position_id" class="form-control" id="position_id" required>
                                            <option value="">Seleccione una posición</option>
                                            <?php
                                            mysqli_data_seek($positions_query, 0);
                                            while ($position = mysqli_fetch_array($positions_query)) {
                                                echo "<option value='{$position['position_id']}'>{$position['position_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="repository_id">Repositorio <span class="text-danger">*</span></label>
                                        <select name="repository_id" class="form-control" id="repository_id" required>
                                            <option value="">Seleccione un repositorio</option>
                                            <?php
                                            mysqli_data_seek($repositories_query, 0);
                                            while ($repo = mysqli_fetch_array($repositories_query)) {
                                                echo "<option value='{$repo['repository_id']}'>{$repo['repository_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="role_id">Rol <span class="text-danger">*</span></label>
                                        <select name="role_id" class="form-control" id="role_id" required>
                                            <?php
                                            mysqli_data_seek($roles_query, 0);
                                            while ($role = mysqli_fetch_array($roles_query)) {
                                                echo "<option value='{$role['role_id']}'>{$role['role_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="submit_btn" name="save" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

<?php include_once 'app/complements/footer.php'; ?>

<script type="text/javascript">
// Función para abrir el modal y cargar datos del usuario
function openModal(isEdit, user = {}) {
    if (isEdit) {
        $('#modal_title').text('Editar Usuario');
        $('#submit_btn').text('Actualizar');
        $('#user_id').val(user.user_id);
        $('#ci').val(user.ci);
        $('#firstname').val(user.firstname);
        $('#lastname').val(user.lastname);
        $('#username').val(user.username);
        $('#email').val(user.email);
        $('#personal_email').val(user.personal_email);
        $('#cell_phone').val(user.cell_phone);
        $('#phone').val(user.phone);
        $('#birth_date').val(user.birth_date);
        $('#address').val(user.address);
        $('#position_id').val(user.position_id);
        $('#repository_id').val(user.repository_id);
        $('#role_id').val(user.role_id);
    } else {
        $('#modal_title').text('Agregar Usuario');
        $('#submit_btn').text('Guardar');
        $('#user_form')[0].reset();
        $('#user_id').val('');
    }
    $('#form_modal').modal('show');
}

// Función AJAX para obtener los datos del usuario
function getUserData(userId) {
    $.ajax({
        url: 'get_user_data.php',
        type: 'POST',
        data: { user_id: userId },
        success: function(response) {
            var user = JSON.parse(response);
            openModal(true, user);
        },
        error: function() {
            alert("Error al obtener datos del usuario.");
        }
    });
}

$(document).ready(function(){
    $('.btn-toggle-status').on('click', function(){
        var user_id = $(this).data('id');
        var current_status = $(this).data('status');

        $.ajax({
            url: 'toggle_user_status.php',
            type: 'POST',
            data: { user_id: user_id, status: current_status },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'success') {
                    alert(result.message);
                    location.reload();  // Recargar la página para ver el cambio
                } else {
                    alert('Error: ' + result.message);
                }
            },
            error: function() {
                alert("Error al intentar cambiar el estado del usuario.");
            }
        });
    });
});
</script>
