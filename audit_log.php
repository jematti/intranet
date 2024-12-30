<?php
// Iniciar la sesión solo si no ha sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'app/funcionts/admin/validator.php';
include("conexion_db.php");
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

<!-- Contenedor principal -->
<?php if ($role_id == 1): ?>

    <main role="main" class="main-content">
    <br><br><br>

        <div class="container-fluid">
            <h2 class="page-title">Panel de Auditoría</h2>

            <!-- Filtros -->
            <form id="filtersForm" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <label for="start_date">Fecha de inicio</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $_GET['start_date'] ?? ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date">Fecha de fin</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $_GET['end_date'] ?? ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="search_user">Buscar por usuario</label>
                        <input type="text" id="search_user" name="search_user" class="form-control" placeholder="Nombre, apellido o usuario" value="<?php echo $_GET['search_user'] ?? ''; ?>">
                    </div>
                </div>
                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="audit_log.php" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>

            <!-- Tabla de auditoría -->
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Entidad</th>
                                <th>Detalles</th>
                                <th>IP</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Parámetros para filtros
                            $start_date = $_GET['start_date'] ?? '';
                            $end_date = $_GET['end_date'] ?? '';
                            $search_user = $_GET['search_user'] ?? '';
                            $limit = 20;
                            $page = $_GET['page'] ?? 1;
                            $offset = ($page - 1) * $limit;

                            // Consulta base
                            $query = "SELECT a.*, CONCAT(u.username, ' - ', u.firstname, ' ', u.lastname) AS user_fullname 
                                    FROM audit_log a 
                                    LEFT JOIN user u ON a.user_id = u.user_id 
                                    WHERE 1=1";

                            // Aplicar filtros
                            if ($start_date) {
                                $query .= " AND a.timestamp >= '$start_date'";
                            }
                            if ($end_date) {
                                $query .= " AND a.timestamp <= '$end_date 23:59:59'";
                            }
                            if ($search_user) {
                                $query .= " AND (u.username LIKE '%$search_user%' 
                                            OR u.firstname LIKE '%$search_user%' 
                                            OR u.lastname LIKE '%$search_user%')";
                            }

                            // Paginación
                            $query .= " ORDER BY a.timestamp DESC LIMIT $limit OFFSET $offset";
                            $result = mysqli_query($conn, $query);

                            // Mostrar registros
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                            <td>{$row['log_id']}</td>
                                            <td>{$row['user_fullname']}</td>
                                            <td>{$row['action']}</td>
                                            <td>{$row['entity']}</td>
                                            <td>{$row['details']}</td>
                                            <td>{$row['ip_address']}</td>
                                            <td>{$row['timestamp']}</td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No se encontraron registros</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                    <!-- Paginación -->
                    <?php
                    $count_query = "SELECT COUNT(*) as total FROM audit_log a LEFT JOIN user u ON a.user_id = u.user_id WHERE 1=1";

                    if ($start_date) {
                        $count_query .= " AND a.timestamp >= '$start_date'";
                    }
                    if ($end_date) {
                        $count_query .= " AND a.timestamp <= '$end_date 23:59:59'";
                    }
                    if ($search_user) {
                        $count_query .= " AND (u.username LIKE '%$search_user%' 
                                            OR u.firstname LIKE '%$search_user%' 
                                            OR u.lastname LIKE '%$search_user%')";
                    }

                    $count_result = mysqli_query($conn, $count_query);
                    $total_records = mysqli_fetch_assoc($count_result)['total'];
                    $total_pages = ceil($total_records / $limit);

                    if ($total_pages > 1) {
                        echo '<nav aria-label="Page navigation">';
                        echo '<ul class="pagination justify-content-center">';

                        for ($i = 1; $i <= $total_pages; $i++) {
                            $active = ($i == $page) ? 'active' : '';
                            echo "<li class='page-item $active'><a class='page-link' href='?page=$i&start_date=$start_date&end_date=$end_date&search_user=$search_user'>$i</a></li>";
                        }

                        echo '</ul>';
                        echo '</nav>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

<?php endif; ?>
<?php
include_once 'app/complements/footer.php';
?>
