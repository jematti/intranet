<?php
require 'app/funcionts/admin/validator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';
?>
        <!-- Sidebar -->
<nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
    <div class="sidebar-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#">
                    <i class="fe fe-home fe-16"></i>
                    INTRANET - FC BCB
                </a>
            </li>

            <?php
            // Obtener todas las secciones de la base de datos
            $sections_query = mysqli_query($conn, "SELECT * FROM `sections`") or die(mysqli_error($conn));
            while ($section = mysqli_fetch_array($sections_query)) {
                echo '<li class="nav-item">';
                echo '<p class="text m-1">' . $section['section_name'] . '</p>'; // Mostrar nombre de la sección
                echo '</li>';
                
                // Obtener las categorías correspondientes a la sección actual
                $categories_query = mysqli_query($conn, "SELECT * FROM `categories` WHERE `section_id` = '" . $section['section_id'] . "'") or die(mysqli_error($conn));
                while ($category = mysqli_fetch_array($categories_query)) {
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link p-0" href="../intranet/documents_main.php">';
                    echo '<i class="fe fe-file fe-16"></i>';
                    echo $category['category_name']; // Mostrar nombre de la categoría
                    echo '</a>';
                    echo '</li>';
                }
            }
            ?>
        </ul>
    </div>
</nav>
