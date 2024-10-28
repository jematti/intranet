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
            <h3>Cargos/Posiciones</h3>
        </div>
        
        <!-- Barra de búsqueda -->
        <div class="form-group">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre del cargo/posición" onkeyup="searchTable()">
        </div>
        
        <button class="btn btn-success" data-toggle="modal" data-target="#form_modal"><span class="glyphicon glyphicon-plus"></span> Agregar</button>
        <br /><br />
        
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Cargo/Posición</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Obtener todas las posiciones
                $query = mysqli_query($conn, "SELECT * FROM positions") or die(mysqli_error($conn));
                while ($fetch = mysqli_fetch_array($query)) {
                    // Determinar el estado actual y configurar el botón
                    $statusButtonClass = $fetch['status'] == 1 ? 'btn-danger' : 'btn-success';
                    $statusButtonText = $fetch['status'] == 1 ? 'Deshabilitar' : 'Habilitar';
                ?>
                    <tr class="del_position<?php echo $fetch['position_id'] ?>">
                        <td><?php echo $fetch['position_id'] ?></td>
                        <td><?php echo $fetch['position_name'] ?></td>
                        <td>
                            <!-- Botón de editar -->
                            <button class="btn btn-warning" data-toggle="modal" data-target="#edit_modal<?php echo $fetch['position_id'] ?>"><span class="glyphicon glyphicon-edit"></span> Editar</button>
                            <!-- Botón de habilitar/deshabilitar -->
                            <button class="btn <?php echo $statusButtonClass; ?>" type="button" onclick="confirmTogglePositionStatus(<?php echo $fetch['position_id']; ?>, <?php echo $fetch['status']; ?>)">
                                <?php echo $statusButtonText; ?>
                            </button>
                        </td>
                    </tr>
                    <!-- Modal para editar posición -->
                    <div class="modal fade" id="edit_modal<?php echo $fetch['position_id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="update_position.php">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Actualizar Cargo/Posición</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Nombre del Cargo/Posición</label>
                                            <input type="text" name="position_name" value="<?php echo $fetch['position_name'] ?>" class="form-control" required />
                                        </div>
                                        <input type="hidden" name="position_id" value="<?php echo $fetch['position_id'] ?>" />
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

    <!-- Modal para agregar posición -->
    <div class="modal fade" id="form_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="position_form" method="POST" action="save_position.php">
                    <div class="modal-header">
                        <h4 class="modal-title">Agregar Cargo/Posición</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre del Cargo/Posición</label>
                            <input type="text" name="position_name" class="form-control" required>
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
function confirmTogglePositionStatus(positionId, currentStatus) {
    const action = currentStatus == 1 ? 'deshabilitar' : 'habilitar';
    if (confirm(`¿Está seguro de que desea ${action} este cargo/posición?`)) {
        togglePositionStatus(positionId, currentStatus);
    }
}

function togglePositionStatus(positionId, currentStatus) {
    // Enviar solicitud AJAX para cambiar el estado de la posición
    $.post('toggle_position_status.php', { position_id: positionId, status: currentStatus }, function(response) {
        if (response.success) {
            // Recargar la página para reflejar el cambio
            window.location.reload();
        } else {
            alert('Error al cambiar el estado del cargo/posición.');
        }
    }, 'json');
}

// Función para buscar en la tabla de cargos/posiciones
function searchTable() {
    var input = document.getElementById("searchInput");
    var filter = input.value.toLowerCase();
    var rows = document.querySelectorAll("#table tbody tr");

    rows.forEach(row => {
        var positionName = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
        if (positionName.indexOf(filter) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>
</body>
</html>
