<?php
include_once 'app/complements/header.php';
?>

<!-- lista de contactos según la búsqueda -->
<div class="container-fluid">
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
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                                <th>Teléfono</th>
                                <th>Ciudad</th>
                                <th>Puesto</th>
                                <th>Acciones</th>
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
                                        <td>
                                            <button class="btn btn-primary btn-sm view-details" 
                                                data-user-id="' . $row['user_id'] . '" 
                                                data-firstname="' . $row['firstname'] . '" 
                                                data-lastname="' . $row['lastname'] . '" 
                                                data-ci="' . $row['ci'] . '" 
                                                data-email="' . $row['email'] . '" 
                                                data-phone="' . $row['phone'] . '" 
                                                data-cell-phone="' . $row['cell_phone'] . '" 
                                                data-address="' . $row['address'] . '" 
                                                data-profile-img="' . ($row['profile_img'] ? 'uploads/profile_images/' . $row['profile_img'] : './assets/avatars/face.jpg') . '"
                                                data-toggle="modal" 
                                                data-target="#userModal">
                                                Ver más
                                            </button>
                                        </td>
                                    </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="7">No se encontraron resultados.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal único -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(45deg, #4A90E2, #50B3F0); color: white;">
                <h5 class="modal-title" id="userModalLabel">Detalles del Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: #f9f9f9;">
                <div class="modal-avatar-container text-center">
                    <img id="modal-profile-img" src="" alt="Profile Image" class="rounded-circle" style="width:120px; height:120px; border: 4px solid #ffffff; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <p><i class="fas fa-user icon" style="margin-right: 8px; color: #4A90E2;"></i><strong>Nombre:</strong> <span id="modal-fullname"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><i class="fas fa-id-card icon" style="margin-right: 8px; color: #4A90E2;"></i><strong>Cédula de Identidad:</strong> <span id="modal-ci"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><i class="fas fa-envelope icon" style="margin-right: 8px; color: #4A90E2;"></i><strong>Correo Institucional:</strong> <span id="modal-email"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><i class="fas fa-phone icon" style="margin-right: 8px; color: #4A90E2;"></i><strong>Teléfono:</strong> <span id="modal-phone"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><i class="fas fa-mobile-alt icon" style="margin-right: 8px; color: #4A90E2;"></i><strong>Celular:</strong> <span id="modal-cell-phone"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><i class="fas fa-home icon" style="margin-right: 8px; color: #4A90E2;"></i><strong>Dirección:</strong> <span id="modal-address"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', function () {
            // Actualizar los datos del modal dinámico
            document.getElementById('modal-profile-img').src = this.dataset.profileImg;
            document.getElementById('modal-fullname').innerText = this.dataset.firstname + ' ' + this.dataset.lastname;
            document.getElementById('modal-ci').innerText = this.dataset.ci || 'N/A';
            document.getElementById('modal-email').innerText = this.dataset.email || 'N/A';
            document.getElementById('modal-phone').innerText = this.dataset.phone || 'N/A';
            document.getElementById('modal-cell-phone').innerText = this.dataset.cellPhone || 'N/A';
            document.getElementById('modal-address').innerText = this.dataset.address || 'N/A';
        });
    });
</script>
