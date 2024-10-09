<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';
include_once 'app/complements/header.php';
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

<!-- contenido -->
<main role="main" class="main-content">
    <div id="content">
        <br /><br /><br />
        <div class="alert alert-info">
            <h3>Repositorios</h3>
        </div>
        <button class="btn btn-success" data-toggle="modal" data-target="#form_modal"><span class="glyphicon glyphicon-plus"></span> Agregar</button>
        <br /><br />
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Repositorio</th>
                    <th>Edificio</th>
                    <th>Departamento</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = mysqli_query($conn, "SELECT * FROM repositories") or die(mysqli_error($conn));
                while ($fetch = mysqli_fetch_array($query)) {
                ?>
                    <tr class="del_repository<?php echo $fetch['repository_id'] ?>">
                        <td><?php echo $fetch['repository_id'] ?></td>
                        <td><?php echo $fetch['repository_name'] ?></td>
                        <td><?php echo $fetch['building'] ?></td>
                        <td><?php echo $fetch['department'] ?></td>
                        <td>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#edit_modal<?php echo $fetch['repository_id'] ?>"><span class="glyphicon glyphicon-edit"></span> Editar</button>
                            <button class="btn btn-danger" type="button" onclick="deleteRepository(<?php echo $fetch['repository_id']; ?>)">Eliminar</button>
                        </td>
                    </tr>
                    <div class="modal fade" id="edit_modal<?php echo $fetch['repository_id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="update_repository.php">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Actualizar Repositorio</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Nombre del Repositorio</label>
                                            <input type="text" name="repository_name" value="<?php echo $fetch['repository_name'] ?>" class="form-control" required="required" />
                                        </div>
                                        <div class="form-group">
                                            <label>Edificio</label>
                                            <input type="text" name="building" value="<?php echo $fetch['building'] ?>" class="form-control" required="required" />
                                        </div>
                                        <div class="form-group">
                                            <label>Departamento</label>
                                            <input type="text" name="department" value="<?php echo $fetch['department'] ?>" class="form-control" required="required" />
                                        </div>
                                            <input type="hidden" name="repository_id" value="<?php echo $fetch['repository_id'] ?>" class="form-control" />
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cerrar</button>
                                        <button name="update" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span> Actualizar</button>
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

    <div class="modal fade" id="modal_confirm" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Sistema</h3>
                </div>
                <div class="modal-body">
                    <center>
                        <h4 class="text-danger">¿Está seguro de que desea eliminar este repositorio?</h4>
                    </center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btn_yes">Continuar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- agregar repositorio -->
    <div class="modal fade" id="form_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="repository_form" method="POST" action="save_repository.php">
                    <div class="modal-header">
                        <h4 class="modal-title">Agregar Repositorio</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre del Repositorio</label>
                            <input type="text" name="repository_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Edificio</label>
                            <input type="text" name="building" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Departamento</label>
                            <input type="text" name="department" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <button type="submit" name="save" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php
include_once 'app/complements/footer.php';
?>

<script>
function deleteRepository(repositoryId) {
    if (confirm('¿Está seguro de que desea eliminar este repositorio?')) {
        $.post('delete_repository.php', { delete: true, repository_id: repositoryId }, function(response) {
            window.location.reload();
        });
    }
}
</script>
</body>

</html>
