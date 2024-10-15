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
            <h3>Área organizacional</h3>
        </div>
        <button class="btn btn-success" data-toggle="modal" data-target="#form_modal"><span class="glyphicon glyphicon-plus"></span> Agregar</button>
        <br /><br />
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Área organizacional</th>
                    <th>Edificio</th>
                    <th>Departamento</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Obtener todas las áreas organizacionales
                $query = mysqli_query($conn, "SELECT * FROM repositories") or die(mysqli_error($conn));
                while ($fetch = mysqli_fetch_array($query)) {
                    // Determinar el estado actual y configurar el botón
                    $statusButtonClass = $fetch['status'] == 1 ? 'btn-danger' : 'btn-success';
                    $statusButtonText = $fetch['status'] == 1 ? 'Deshabilitar' : 'Habilitar';
                ?>
                    <tr class="del_repository<?php echo $fetch['repository_id'] ?>">
                        <td><?php echo $fetch['repository_id'] ?></td>
                        <td><?php echo $fetch['repository_name'] ?></td>
                        <td><?php echo $fetch['building'] ?></td>
                        <td><?php echo $fetch['department'] ?></td>
                        <td>
                            <!-- Botón de editar -->
                            <button class="btn btn-warning" data-toggle="modal" data-target="#edit_modal<?php echo $fetch['repository_id'] ?>"><span class="glyphicon glyphicon-edit"></span> Editar</button>
                            <!-- Botón de habilitar/deshabilitar -->
                            <button class="btn <?php echo $statusButtonClass; ?>" type="button" onclick="confirmToggleRepositoryStatus(<?php echo $fetch['repository_id']; ?>, <?php echo $fetch['status']; ?>)">
                                <?php echo $statusButtonText; ?>
                            </button>
                        </td>
                    </tr>
                    <!-- Modal para editar repositorio -->
                    <div class="modal fade" id="edit_modal<?php echo $fetch['repository_id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="update_repository.php">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Actualizar Área organizacional</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Nombre del Área organizacional</label>
                                            <input type="text" name="repository_name" value="<?php echo $fetch['repository_name'] ?>" class="form-control" required />
                                        </div>
                                        <div class="form-group">
                                            <label>Edificio</label>
                                            <input type="text" name="building" value="<?php echo $fetch['building'] ?>" class="form-control" required />
                                        </div>
                                        <div class="form-group">
                                            <label>Departamento</label>
                                            <input type="text" name="department" value="<?php echo $fetch['department'] ?>" class="form-control" required />
                                        </div>
                                        <input type="hidden" name="repository_id" value="<?php echo $fetch['repository_id'] ?>" />
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

    <!-- Modal para agregar repositorio -->
    <div class="modal fade" id="form_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="repository_form" method="POST" action="save_repository.php">
                    <div class="modal-header">
                        <h4 class="modal-title">Agregar Área organizacional</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre del Área organizacional</label>
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
function confirmToggleRepositoryStatus(repositoryId, currentStatus) {
    const action = currentStatus == 1 ? 'deshabilitar' : 'habilitar';
    if (confirm(`¿Está seguro de que desea ${action} este Área organizacional?`)) {
        toggleRepositoryStatus(repositoryId, currentStatus);
    }
}

function toggleRepositoryStatus(repositoryId, currentStatus) {
    // Enviar solicitud AJAX para cambiar el estado del repositorio
    $.post('toggle_repository_status.php', { repository_id: repositoryId, status: currentStatus }, function(response) {
        if (response.success) {
            // Recargar la página para reflejar el cambio
            window.location.reload();
        } else {
            alert('Error al cambiar el estado del Área organizacional.');
        }
    }, 'json');
}
</script>
</body>
</html>
