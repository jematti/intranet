<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';
include_once 'app/complements/header.php';

// Obtener las secciones disponibles
$sections_query = mysqli_query($conn, "SELECT * FROM sections WHERE status = 1") or die(mysqli_error($conn));
?>

<!-- navegador principal -->
<?php include 'app/complements/navbar-main.php'; ?>
<!-- fin navegador principal -->

<!-- barra de navegación lateral -->
<?php include 'app/funcionts/sidebar.php'; ?>
<!-- fin de barra de navegación lateral -->

<!-- contenido -->
<main role="main" class="main-content">
    <div id="content">
        <div class="alert alert-info">
            <h3>Categorías</h3>
        </div>
        <button class="btn btn-success" data-toggle="modal" data-target="#form_modal">Agregar</button>
        <br /><br />
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Sección</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consultar categorías y secciones
                $query = mysqli_query($conn, "SELECT c.category_id, c.category_name, s.section_id, s.section_name 
                                              FROM categories c 
                                              JOIN sections s ON c.section_id = s.section_id 
                                              WHERE c.status = 1") or die(mysqli_error($conn));
                while ($fetch = mysqli_fetch_array($query)) {
                ?>
                    <tr class="del_category<?php echo $fetch['category_id'] ?>">
                        <td><?php echo $fetch['category_id'] ?></td>
                        <td><?php echo $fetch['category_name'] ?></td>
                        <td><?php echo $fetch['section_name'] ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="openEditModal('<?php echo $fetch['category_id']; ?>', '<?php echo addslashes($fetch['category_name']); ?>', '<?php echo $fetch['section_id']; ?>')">Editar</button>
                            <button class="btn btn-danger" type="button" onclick="deleteCategory(<?php echo $fetch['category_id']; ?>)">Eliminar</button>
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
                        <h4 class="text-danger">¿Está seguro de que desea eliminar esta categoría?</h4>
                    </center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btn_yes">Continuar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar/Editar Categoría -->
    <div class="modal fade" id="form_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="category_form" method="POST" action="save_category.php">
                    <div class="modal-header">
                        <h4 class="modal-title">Agregar/Editar Categoría</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="category_id" name="category_id" />
                        <div class="form-group">
                            <label>Nombre de la Categoría</label>
                            <input type="text" id="category_name" name="category_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Sección</label>
                            <select id="section_id" name="section_id" class="form-control" required>
                                <option value="">Seleccione una sección</option>
                                <?php
                                // Resetear el puntero del resultado de la consulta para reutilizarla
                                mysqli_data_seek($sections_query, 0);
                                while ($section = mysqli_fetch_array($sections_query)) {
                                    echo "<option value='{$section['section_id']}'>{$section['section_name']}</option>";
                                }
                                ?>
                            </select>
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
// Abrir el modal para editar una categoría
function openEditModal(category_id, category_name, section_id) {
    document.getElementById('category_id').value = category_id;
    document.getElementById('category_name').value = category_name;
    document.getElementById('section_id').value = section_id;
    
    $('#form_modal').modal('show');
}

// Función para eliminar una categoría
function deleteCategory(categoryId) {
    if (confirm('¿Está seguro de que desea eliminar esta categoría?')) {
        $.post('delete_category.php', { delete: true, category_id: categoryId }, function(response) {
            window.location.reload();
        });
    }
}
</script>
</body>

</html>
