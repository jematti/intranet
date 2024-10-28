<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';
include_once 'app/complements/header.php';

// Obtener los datos del usuario logueado
$user_id = $_SESSION['user_id'];
$role_id = null;
$repository_id = null;

// Obtener la información del usuario logueado, incluyendo el role_id y el repository_id
$user_info_query = mysqli_query($conn, "SELECT role_id, repository_id FROM user WHERE user_id = '$user_id'") or die(mysqli_error($conn));
if ($user_info = mysqli_fetch_array($user_info_query)) {
    $role_id = $user_info['role_id'];
    $repository_id = $user_info['repository_id'];
}

// Obtener los roles disponibles
$roles_query = mysqli_query($conn, "SELECT * FROM roles") or die(mysqli_error($conn));

// Obtener las posiciones disponibles y activas
$positions_query = mysqli_query($conn, "SELECT * FROM positions WHERE status = 1 ORDER BY position_name ASC") or die(mysqli_error($conn));

// Obtener todos los repositorios disponibles solo si el usuario es Super Admin (role_id = 1)
if ($role_id == 1) {
    $repositories_query = mysqli_query($conn, "SELECT * FROM repositories") or die(mysqli_error($conn));
}
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
        <div class="alert alert-info"><h3>Usuarios</h3></div>
        
        <!-- Barra de búsqueda -->
        <div class="form-group">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre o CI" onkeyup="searchTable()">
        </div>

        <!-- Mostrar selector de repositorios solo si el usuario es Super Admin (role_id = 1) -->
        <?php if ($role_id == 1): ?>
        <div class="row mb-3">
            <div class="col-md-6">
                <select id="repositoryFilter" class="form-control" onchange="filterByRepository()">
                    <option value="">Mostrar todos</option>
                    <?php
                    mysqli_data_seek($repositories_query, 0);
                    while ($repo = mysqli_fetch_array($repositories_query)) {
                        echo "<option value='{$repo['repository_id']}'>{$repo['repository_name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <?php endif; ?>

        <!-- Botón para agregar usuario -->
        <button class="btn btn-success" onclick="openModal(false);"><span class="glyphicon glyphicon-plus"></span> Agregar Usuario</button>
        <br /><br />

        <!-- Tabla de usuarios -->
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Celular</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <?php
                // Filtrar por repositorio si el usuario es Administrador de Página (role_id = 2)
                $query = "SELECT u.*, r.role_name, p.position_name, repo.repository_name 
                          FROM `user` u 
                          JOIN `roles` r ON u.role_id = r.role_id 
                          LEFT JOIN `positions` p ON u.position_id = p.position_id 
                          LEFT JOIN `repositories` repo ON u.repository_id = repo.repository_id ";

                if ($role_id == 2) {
                    // Solo ver usuarios del mismo repositorio si es Administrador de Página
                    $query .= "WHERE u.repository_id = '$repository_id' AND u.role_id != 1"; // Excluir Super Admin
                } elseif ($role_id == 1) {
                    // Ver todos los usuarios excepto Super Admin (role_id = 1)
                    $query .= "WHERE u.role_id != 1";
                }

                $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
                while ($fetch = mysqli_fetch_array($result)) {
                ?>
                <tr class="del_user<?php echo $fetch['user_id']?>" data-repository-id="<?php echo $fetch['repository_id']?>">
                    <td><?php echo $fetch['firstname']?> <?php echo $fetch['lastname']?></td>
                    <td><?php echo $fetch['username']?></td>
                    <td><?php echo $fetch['email']?></td>
                    <td><?php echo $fetch['cell_phone'] ? $fetch['cell_phone'] : 'N/A' ?></td>
                    <td><?php echo $fetch['position_name'] ? $fetch['position_name'] : 'N/A' ?></td>
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
                            <button class="btn btn-danger btn-toggle-status" data-id="<?php echo $fetch['user_id']?>" data-status="<?php echo $fetch['active_status']?>"><?php echo ($fetch['active_status'] == 1) ? 'Desactivar' : 'Activar'; ?></button>
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
                                        <label for="landline_phone">Teléfono Fijo</label>
                                        <input type="text" name="landline_phone" class="form-control" id="landline_phone"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="repository_phone">Teléfono Fijo del Repositorio</label>
                                        <input type="text" name="repository_phone" class="form-control" id="repository_phone"/>
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
                                        <input type="password" name="password" class="form-control" id="password"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="personal_email">Correo Personal</label>
                                        <input type="email" name="personal_email" class="form-control" id="personal_email"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Teléfono (Interno)</label>
                                        <input type="text" name="phone" class="form-control" id="phone"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="birth_date">Fecha de Nacimiento</label>
                                        <input type="date" name="birth_date" class="form-control" id="birth_date"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Dirección</label>
                                        <input type="text" name="address" class="form-control" id="address"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="profile_img">Imagen de Perfil (Opcional)</label>
                                        <input type="file" name="profile_img" class="form-control" id="profile_img" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Posición, Repositorio, Sección y Rol -->
                                <div class="col-md-3">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="repository_id">Repositorio <span class="text-danger">*</span></label>
                                        <select name="repository_id" class="form-control" id="repository_id" required onchange="loadSections()">
                                            <option value="">Seleccione un repositorio</option>
                                            <?php
                                            if ($role_id == 1) {
                                                // Mostrar todos los repositorios si es Super Admin
                                                mysqli_data_seek($repositories_query, 0);
                                                while ($repo = mysqli_fetch_array($repositories_query)) {
                                                    echo "<option value='{$repo['repository_id']}'>{$repo['repository_name']}</option>";
                                                }
                                            } else {
                                                // Mostrar solo el repositorio del Administrador de Página
                                                $repo_query = mysqli_query($conn, "SELECT repository_id, repository_name FROM repositories WHERE repository_id = '$repository_id'") or die(mysqli_error($conn));
                                                if ($repo = mysqli_fetch_array($repo_query)) {
                                                    echo "<option value='{$repo['repository_id']}'>{$repo['repository_name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="section_id">Sección</label>
                                        <select name="section_id" class="form-control" id="section_id">
                                            <option value="">Seleccione una sección</option>
                                            <!-- Aquí se cargarán las secciones dinámicamente -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="role_id">Rol <span class="text-danger">*</span></label>
                                        <select name="role_id" class="form-control" id="role_id" required>
                                            <option value="">Seleccione un rol</option>
                                            <?php
                                            mysqli_data_seek($roles_query, 0);
                                            while ($role = mysqli_fetch_array($roles_query)) {
                                                // Omitir la opción de Super Admin (role_id = 1)
                                                if ($role['role_id'] != 1) {
                                                    echo "<option value='{$role['role_id']}'>{$role['role_name']}</option>";
                                                }
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
        $('#landline_phone').val(user.landline_phone);
        $('#repository_phone').val(user.repository_phone);
        $('#birth_date').val(user.birth_date);
        $('#address').val(user.address);
        $('#position_id').val(user.position_id);
        $('#repository_id').val(user.repository_id);
        $('#role_id').val(user.role_id);

        // No rellenar el campo de contraseña
        $('#password').val('');  // Vaciar el campo de contraseña

        // Cargar secciones pasando el section_id del usuario
        loadSections(user.section_id);
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


<script>

function searchTable() {
    var input = document.getElementById('searchInput');
    var filter = input.value.toLowerCase();
    var rows = document.querySelectorAll('#userTableBody tr');

    rows.forEach(function(row) {
        var name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
        var username = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        
        if (name.indexOf(filter) > -1 || username.indexOf(filter) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

function loadSections(userSectionId = null) {
    var repository_id = document.getElementById('repository_id').value;

    // Verificar si se ha seleccionado un repositorio
    if (repository_id) {
        // Crear la solicitud AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'load_sections.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        // Manejar la respuesta
        xhr.onload = function() {
            if (this.status == 200) {
                var sections = JSON.parse(this.responseText);
                var sectionSelect = document.getElementById('section_id');
                
                // Limpiar las opciones actuales
                sectionSelect.innerHTML = '<option value="">Seleccione una sección</option>';

                // Añadir las nuevas opciones
                sections.forEach(function(section) {
                    var option = document.createElement('option');
                    option.value = section.section_id;
                    option.textContent = section.section_name;
                    sectionSelect.appendChild(option);
                });

                // Si estamos en edición, seleccionar la sección del usuario
                if (userSectionId) {
                    sectionSelect.value = userSectionId;
                }
            }
        };

        // Enviar los datos
        xhr.send('repository_id=' + repository_id);
    } else {
        // Si no hay repositorio seleccionado, limpiar las secciones
        document.getElementById('section_id').innerHTML = '<option value="">Seleccione una sección</option>';
    }
}

// Cargar las secciones dinámicamente cuando se selecciona un repositorio
// Filtrar la tabla de usuarios por repositorio
function filterByRepository() {
    var repository_id = document.getElementById('repositoryFilter').value;
    var rows = document.querySelectorAll('#userTableBody tr');

    rows.forEach(function(row) {
        var repo_id = row.getAttribute('data-repository-id');

        if (repository_id === '' || repository_id === repo_id) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

</script>
