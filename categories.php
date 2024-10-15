<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';
include_once 'app/complements/header.php';

// Recuperar el repositorio al que pertenece el usuario logueado
$repository_id = '';
$repository_name = '';
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $query_user = mysqli_query($conn, "SELECT r.repository_id, r.repository_name 
                                       FROM user u 
                                       JOIN repositories r ON u.repository_id = r.repository_id 
                                       WHERE u.user_id = '$user_id' 
                                       LIMIT 1");
    if ($row_user = mysqli_fetch_assoc($query_user)) {
        $repository_id = $row_user['repository_id'];
        $repository_name = $row_user['repository_name'];
    }
}
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
    <div id="content">
        <div class="alert alert-info">
            <h3>Categorías</h3>
        </div>
        <button class="btn btn-success" data-toggle="modal" data-target="#form_modal" onclick="loadSections(<?php echo $repository_id; ?>)">Agregar</button>
        <br /><br />
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Unidad Organizacional</th>
                    <th>Área organizacional</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consultar categorías, secciones y Área organizacional
                $query = mysqli_query($conn, "SELECT c.category_id, c.category_name, c.status, s.section_id, s.section_name, r.repository_id, r.repository_name 
                                              FROM categories c 
                                              JOIN sections s ON c.section_id = s.section_id 
                                              JOIN repositories r ON s.repository_id = r.repository_id 
                                              WHERE c.status IN (0, 1)") or die(mysqli_error($conn));
                
                // Iterar sobre los resultados
                while ($fetch = mysqli_fetch_array($query)) {
                    // Determinar la clase y el texto del botón según el estado de la categoría
                    $statusButtonClass = $fetch['status'] == 1 ? 'btn-danger' : 'btn-success';
                    $statusButtonText = $fetch['status'] == 1 ? 'Deshabilitar' : 'Habilitar';
                ?>
                    <tr class="del_category<?php echo $fetch['category_id'] ?>">
                        <td><?php echo $fetch['category_id'] ?></td>
                        <td><?php echo $fetch['category_name'] ?></td>
                        <td><?php echo $fetch['section_name'] ?></td>
                        <td><?php echo $fetch['repository_name'] ?></td>
                        <td>
                            <!-- Botón de Habilitar/Deshabilitar -->
                            <button class="btn <?php echo $statusButtonClass; ?>" type="button" onclick="confirmToggleCategoryStatus(<?php echo $fetch['category_id']; ?>, <?php echo $fetch['status']; ?>)">
                                <?php echo $statusButtonText; ?>
                            </button>

                            <!-- Editar botón -->
                            <button class="btn btn-warning" onclick="openEditModal('<?php echo $fetch['category_id']; ?>', '<?php echo addslashes($fetch['category_name']); ?>', '<?php echo $fetch['section_id']; ?>', '<?php echo $fetch['repository_id']; ?>')">Editar</button>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
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
                            <label>Área organizacional</label>
                            <!-- Campo Área organizacional, deshabilitado y pre-rellenado con el valor del usuario -->
                            <input type="text" id="repository_name" class="form-control" value="<?php echo $repository_name; ?>" disabled>
                            <input type="hidden" id="repository_id" name="repository_id" value="<?php echo $repository_id; ?>">
                        </div>
                        <div class="form-group">
                            <label>Unidad Organizacional</label>
                            <select id="section_id" name="section_id" class="form-control" required>
                                <option value="">Seleccione una Unidad Organizacional</option>
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
// Función para cargar las secciones basadas en el repositorio seleccionado
function loadSections(repository_id) {
    // Limpiar las secciones actuales
    document.getElementById('section_id').innerHTML = '<option value="">Seleccione una sección</option>';
    document.getElementById('section_id').disabled = true;

    // Si se selecciona un repositorio válido, cargar las secciones correspondientes
    if (repository_id) {
        $.post('get_sections_by_repository.php', { repository_id: repository_id }, function(response) {
            let sections = JSON.parse(response);
            sections.forEach(function(section) {
                let option = document.createElement('option');
                option.value = section.section_id;
                option.textContent = section.section_name;
                document.getElementById('section_id').appendChild(option);
            });
            document.getElementById('section_id').disabled = false; // Habilitar el campo
        });
    }
}

// Función para habilitar/deshabilitar categoría mediante AJAX
function toggleCategoryStatus(categoryId, currentStatus) {
    $.post('toggle_category_status.php', { category_id: categoryId, status: currentStatus }, function(response) {
        if (response.success) {
            // Recargar la página para reflejar el cambio o cambiar el botón dinámicamente
            window.location.reload();
        } else {
            alert('Error al cambiar el estado de la categoría.');
        }
    }, 'json');
}

// Confirmar antes de habilitar/deshabilitar categoría
function confirmToggleCategoryStatus(categoryId, currentStatus) {
    const action = currentStatus === 1 ? 'deshabilitar' : 'habilitar';
    if (confirm(`¿Estás seguro de que deseas ${action} esta categoría?`)) {
        toggleCategoryStatus(categoryId, currentStatus);
    }
}

// Abrir el modal para editar una categoría
function openEditModal(category_id, category_name, section_id, repository_id) {
    document.getElementById('category_id').value = category_id;
    document.getElementById('category_name').value = category_name;

    // Cargar las secciones del repositorio seleccionado
    loadSections(repository_id);

    // Una vez cargadas, seleccionar la sección correcta
    setTimeout(function() {
        document.getElementById('section_id').value = section_id;
    }, 500);

    // Mostrar el modal de edición
    $('#form_modal').modal('show');
}
</script>
</body>

</html>
