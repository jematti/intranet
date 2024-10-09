<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';
include_once 'app/complements/header.php';

// Obtener todos los permisos
$permissions_query = mysqli_query($conn, "SELECT * FROM permissions") or die(mysqli_error($conn));
?>

<!-- navegador principal -->
<?php include 'app/complements/navbar-main.php' ?>
<!-- fin navegador principal -->
<!-- Modal de Noticias (incluido desde archivo separado) -->
<?php include 'news_modal.php'; ?>
<!-- fin Modal de Noticias -->
<!-- barra de navegación lateral -->
<?php include 'app/funcionts/sidebar.php'; ?>
<!-- fin de barra de navegación lateral -->


<!-- contenido -->

<main role="main" class="main-content">
    <div id="content">
        <br /><br /><br />
        <div class="alert alert-info"><h3>Administrar Roles y Permisos</h3></div> 
        <button class="btn btn-success" data-toggle="modal" data-target="#form_modal"><span class="glyphicon glyphicon-plus"></span> Agregar Rol</button>
        <br /><br />
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Rol</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $query = mysqli_query($conn, "SELECT * FROM `roles`") or die(mysqli_error($conn));
                    while($fetch = mysqli_fetch_array($query)){
                ?>
                <tr class="del_role<?php echo $fetch['role_id']?>">
                    <td><?php echo $fetch['role_id']?></td>
                    <td><?php echo $fetch['role_name']?></td>
                    <td><center>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#edit_modal<?php echo $fetch['role_id']?>"><span class="glyphicon glyphicon-edit"></span> Editar</button> 
                        <button class="btn btn-danger btn-delete" id="<?php echo $fetch['role_id']?>" type="button">Eliminar</button>
                    </center></td>
                </tr>
                
                <!-- Modal para Editar Rol y Permisos -->
                <div class="modal fade" id="edit_modal<?php echo $fetch['role_id']?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="admin_update_role.php">    
                                <div class="modal-header">
                                    <h4 class="modal-title">Actualizar Rol</h4>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="role_id" value="<?php echo $fetch['role_id']?>"/>
                                    <div class="form-group">
                                        <label>Nombre del Rol</label>
                                        <input type="text" name="role_name" value="<?php echo $fetch['role_name']?>" class="form-control" required="required"/>
                                    </div>
                                    <div class="form-group">
                                        <label>Permisos</label>
                                        <div class="checkbox-group">
                                            <?php
                                            // Obtener permisos asignados
                                            $assigned_permissions_query = mysqli_query($conn, "SELECT permission_id FROM role_permissions WHERE role_id = {$fetch['role_id']}") or die(mysqli_error($conn));
                                            $assigned_permissions = [];
                                            while ($perm = mysqli_fetch_assoc($assigned_permissions_query)) {
                                                $assigned_permissions[] = $perm['permission_id'];
                                            }

                                            // Mostrar checkboxes para permisos
                                            mysqli_data_seek($permissions_query, 0); // Resetear la consulta de permisos
                                            while ($perm = mysqli_fetch_array($permissions_query)) {
                                                $checked = in_array($perm['permission_id'], $assigned_permissions) ? 'checked' : '';
                                                echo "<label><input type='checkbox' name='permissions[]' value='{$perm['permission_id']}' $checked> {$perm['permission_name']}</label><br>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cerrar</button>
                                    <button name="edit" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span> Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para Confirmar Eliminación -->
    <div class="modal fade" id="modal_confirm" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Sistema</h3>
                </div>
                <div class="modal-body">
                    <center><h4 class="text-danger">¿Está seguro de que desea eliminar este rol?</h4></center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btn_yes">Continuar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar Rol -->
    <div class="modal fade" id="form_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="role_form" method="POST" action="admin_save_role.php">    
                    <div class="modal-header">
                        <h4 class="modal-title">Agregar Rol</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre del Rol</label>
                            <input type="text" name="role_name" class="form-control" required="required"/>
                        </div>
                        <div class="form-group">
                            <label>Permisos</label>
                            <div class="checkbox-group">
                                <?php
                                mysqli_data_seek($permissions_query, 0); // Resetear la consulta de permisos
                                while ($perm = mysqli_fetch_array($permissions_query)) {
                                    echo "<label><input type='checkbox' name='permissions[]' value='{$perm['permission_id']}'> {$perm['permission_name']}</label><br>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cerrar</button>
                        <button name="save" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

<?php
include_once 'app/complements/footer.php';
?>

<script type="text/javascript">
$(document).ready(function(){
    $('.btn-delete').on('click', function(){
        var role_id = $(this).attr('id');
        $("#modal_confirm").modal('show');
        $('#btn_yes').attr('name', role_id);
    });

    $('#btn_yes').on('click', function(){
        var id = $(this).attr('name');
        $.ajax({
            type: "POST",
            url: "delete_role.php",
            data:{
                role_id: id
            },
            success: function(response){
                $("#modal_confirm").modal('hide');
                alert(response);
                location.reload();
            }
        });
    });
});
</script>
