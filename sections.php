<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';
include_once 'app/complements/header.php';

// Recuperar el repositorio al que pertenece el usuario logueado
$repository_id = '';
$repository_name = '';
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $query_user = mysqli_query($conn, "SELECT r.repository_id, r.repository_name FROM user u JOIN repositories r ON u.repository_id = r.repository_id WHERE u.user_id = '$user_id' LIMIT 1");
    if ($row_user = mysqli_fetch_assoc($query_user)) {
        $repository_id = $row_user['repository_id'];
        $repository_name = $row_user['repository_name'];
    }
}

// Mostrar alertas en función del estado
$status = $_GET['status'] ?? null;
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
    <br><br><br>

    <!-- Mostrar alertas en función del parámetro status -->
    <?php if ($status == 'added'): ?>
        <div class="alert alert-success">¡Sección agregada con éxito!</div>
    <?php elseif ($status == 'updated'): ?>
        <div class="alert alert-success">¡Sección actualizada con éxito!</div>
    <?php elseif ($status == 'error'): ?>
        <div class="alert alert-danger">Hubo un error al procesar la solicitud. Intente nuevamente.</div>
    <?php endif; ?>

    <div id="content">
        <div class="alert alert-info">
            <h3>Secciones</h3>
        </div>
        <button class="btn btn-success" data-toggle="modal" data-target="#form_modal"><span class="glyphicon glyphicon-plus"></span> Agregar</button>
        <br /><br />
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la Sección</th>
                    <th>Repositorio</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = mysqli_query($conn, "SELECT s.section_id, s.section_name, s.repository_id, r.repository_name 
                                            FROM sections s 
                                            JOIN repositories r ON s.repository_id = r.repository_id 
                                            WHERE s.status = 1") or die(mysqli_error($conn));
                while ($fetch = mysqli_fetch_array($query)) {
                ?>
                    <tr class="del_section<?php echo $fetch['section_id'] ?>">
                        <td><?php echo $fetch['section_id'] ?></td>
                        <td><?php echo $fetch['section_name'] ?></td>
                        <td><?php echo $fetch['repository_name'] ?></td>
                        <td>
                            <button class="btn btn-warning" 
                                    data-section-id="<?php echo $fetch['section_id']; ?>" 
                                    data-section-name="<?php echo htmlspecialchars(addslashes($fetch['section_name'])); ?>" 
                                    data-repository-id="<?php echo $fetch['repository_id']; ?>" 
                                    onclick="openEditModal(this)">
                                <span class="glyphicon glyphicon-edit"></span> Editar
                            </button>
                            <button class="btn btn-danger" type="button" onclick="deleteSection(<?php echo $fetch['section_id']; ?>)">Eliminar</button>
                        </td>
                    </tr>
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
                    <center>
                        <h4 class="text-danger">¿Está seguro de que desea eliminar esta sección?</h4>
                    </center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btn_yes">Continuar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar/Editar Sección -->
    <div class="modal fade" id="form_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="section_form" method="POST" action="save_section.php">
                    <div class="modal-header">
                        <h4 class="modal-title">Agregar/Editar Sección</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="section_id" name="section_id" />
                        <div class="form-group">
                            <label>Nombre de la Sección</label>
                            <input type="text" id="section_name" name="section_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Repositorio</label>
                            <!-- Campo de repositorio, solo lectura y pre-rellenado con el repositorio del usuario -->
                            <input type="text" id="repository_name" class="form-control" value="<?php echo $repository_name; ?>" disabled>
                            <input type="hidden" id="repository_id" name="repository_id" value="<?php echo $repository_id; ?>">
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
// Abrir Modal para editar sección
function openEditModal(button) {
    // Obtener los valores de los atributos data-* del botón
    var section_id = button.getAttribute('data-section-id');
    var section_name = button.getAttribute('data-section-name');
    var repository_id = button.getAttribute('data-repository-id');

    // Rellenar el modal con los datos existentes
    document.getElementById('section_id').value = section_id;
    document.getElementById('section_name').value = section_name;
    document.getElementById('repository_id').value = repository_id;

    // Abrir el modal
    $('#form_modal').modal('show');
}

// Función para eliminar sección
function deleteSection(sectionId) {
    if (confirm('¿Está seguro de que desea eliminar esta sección?')) {
        $.post('delete_section.php', { delete: true, section_id: sectionId }, function(response) {
            window.location.reload();
        });
    }
}
</script>
</body>

</html>
