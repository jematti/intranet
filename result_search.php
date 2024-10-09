<?php
include_once 'app/complements/header.php';
?>

<!-- lista de contactos según la búsqueda -->
<div class="container-fluid ">
<hr>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="row align-items-center my-4">
                <div class="col">
                    <h2 class="h3 mb-0 page-title">Resultados de la Búsqueda</h2>
                </div>
                <div class="col-auto">
                    <a href="index.php" class="btn btn-danger">
                        <span class="fe fe-x fe-12 mr-2"></span>Cerrar
                    </a>
                </div>
            </div>
            <!-- table -->
            <div class="card shadow">
                <div class="card-body">
                    <table class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <!-- <th>ID</th> -->
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                                <th>Teléfono</th>
                                <th>Ciudad</th>
                                <th>Puesto</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if (!empty($results)) {
                                foreach ($results as $row) {
                                    echo '
                                    <tr>
                                        <td>
                                            <div class="avatar avatar-sm">';
                                                // Mostrar imagen de perfil si existe, de lo contrario, usar imagen predeterminada
                                                if ($row['profile_img']) {
                                                    echo '<img src="uploads/profile_images/' . $row['profile_img'] . '" alt="Profile Image" class="avatar-img rounded-circle" style="width:100px; height:100px;">';
                                                } else {
                                                    echo '<img src="./assets/avatars/face.jpg" alt="Default Profile Image" class="avatar-img rounded-circle" style="width:100px; height:100px;">';
                                                }
                                    echo '  </div>
                                        </td>
                                        <td>
                                            <p class="mb-0 text"><strong>' . $row["firstname"] . ' ' . $row["lastname"] . '</strong></p>
                                            <small class="mb-0 text">' . $row["email"] . '</small>
                                        </td>
                                        <td>
                                            <p class="mb-0 text">' . $row["repository_name"] . '</p>
                                            <small class="mb-0 text">' . $row["building"] . '</small>
                                        </td>
                                        <td class="text">' . $row["phone"] . '</td>
                                        <td class="text">' . $row["department"] . '</td>
                                        <td class="text">' . $row["position_name"] . '</td>
                                    </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6">No se encontraron resultados.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- fin de busqueda -->