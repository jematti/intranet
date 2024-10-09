<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';
include_once 'app/complements/header.php';

// Obtener los documentos subidos con sus categorías y secciones
$documents_query = mysqli_query($conn, "
    SELECT storage.store_id, storage.filename, storage.file_type, storage.date_uploaded, 
           categories.category_name, sections.section_name, storage.status
    FROM storage
    JOIN categories ON storage.category_id = categories.category_id
    JOIN sections ON categories.section_id = sections.section_id
") or die(mysqli_error($conn));
?>

<!-- navegador principal -->
<?php include 'app/complements/navbar-main.php'; ?>
<!-- fin navegador principal -->

<!-- barra de navegación lateral -->
<?php include 'app/funcionts/sidebar.php' ?>
<!-- fin de barra de navegación lateral -->

<!-- contenido -->
<main role="main" class="main-content">
    <div id="content">
        <br /><br /><br />
        <div class="alert alert-info">
            <h3>Documentos Subidos</h3>
        </div>
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Archivo</th>
                    <th>Tipo de Archivo</th>
                    <th>Fecha de Subida</th>
                    <th>Categoría</th>
                    <th>Sección</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_array($documents_query)) {
                    $status = $row['status'] == 1 ? 'Activo' : 'Inactivo';
                ?>
                    <tr>
                        <td><?php echo $row['store_id']; ?></td>
                        <td><?php echo $row['filename']; ?></td>
                        <td><?php echo $row['file_type']; ?></td>
                        <td><?php echo $row['date_uploaded']; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td><?php echo $row['section_name']; ?></td>
                        <td><?php echo $status; ?></td>
                        <td>
                            <?php if ($row['status'] == 1) { ?>
                                <button class="btn btn-danger" type="button" onclick="disableDocument(<?php echo $row['store_id']; ?>)">Deshabilitar</button>
                            <?php } else { ?>
                                <button class="btn btn-success" type="button" onclick="enableDocument(<?php echo $row['store_id']; ?>)">Habilitar</button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<?php include_once 'app/complements/footer.php'; ?>

<script>
function disableDocument(storeId) {
    if (confirm('¿Está seguro de que desea deshabilitar este documento?')) {
        $.post('disable_document.php', { disable: true, store_id: storeId }, function(response) {
            window.location.reload();
        });
    }
}

function enableDocument(storeId) {
    if (confirm('¿Está seguro de que desea habilitar este documento?')) {
        $.post('enable_document.php', { enable: true, store_id: storeId }, function(response) {
            window.location.reload();
        });
    }
}
</script>
</body>

</html>
