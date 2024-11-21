
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'app/funcionts/admin/validator.php';
include("conexion_db.php");

// Verificar el rol del usuario
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
?>

<!-- Sidebar -->
<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>
    <nav class="vertnav navbar navbar-light">
        <!-- Logo -->
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="index.php">
                <img src="assets/images/logo.jpeg" alt="Logo" style="height: 40px;">
            </a>
        </div>

        <!-- Menú común para todos los usuarios -->
        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fe fe-home fe-16"></i>
                    <span class="ml-3 item-text">INTRANET - FC BCB</span>
                </a>
            </li>

            <!-- Enlace de Información Personal (disponible para todos los roles) -->
            <li class="nav-item">
                <a class="nav-link" href="perfil_user.php">
                    <i class="fe fe-user fe-16"></i>
                    <span class="ml-3 item-text">Información Personal</span>
                </a>
            </li>
        </ul>

        <!-- Opciones para Super Admin (role_id = 1) -->
        <?php if ($role_id == 1): ?>
            <p class="text-muted nav-heading mt-4 mb-1"><span>Administración</span></p>
            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link" href="repositories.php">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Área organizacional</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sections.php">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Unidad Organizacional</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Carpetas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="positions.php">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Cargos/Posiciones</span>
                    </a>
                </li>
                <p class="text-muted nav-heading mt-4 mb-1"><span>Usuarios</span></p>
                <li class="nav-item">
                    <a class="nav-link" href="admin_user.php">
                        <i class="fe fe-user fe-16"></i>
                        <span class="ml-3 item-text">Usuarios</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="roles.php">
                        <i class="fe fe-user fe-16"></i>
                        <span class="ml-3 item-text">Roles</span>
                    </a>
                </li> -->
            </ul>
            <p class="text-muted nav-heading mt-4 mb-1"><span>Manejo de Documentación</span></p>
            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link" href="upload_document.php">
                        <i class="fe fe-file fe-16"></i>
                        <span class="ml-3 item-text">Subir Documentos</span>
                    </a>
                </li>
            </ul>
            <p class="text-muted nav-heading mt-4 mb-1"><span>Documentación</span></p>
            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link" href="admin_documents.php">
                        <i class="fe fe-file fe-16"></i>
                        <span class="ml-3 item-text">Lista de Documentos</span>
                    </a>
                </li>
            </ul>
        <?php endif; ?>

        <!-- Opciones para Administrador de Página (role_id = 2) -->
        <?php if ($role_id == 2): ?>
            <p class="text-muted nav-heading mt-4 mb-1"><span>Administración</span></p>
            <ul class="navbar-nav flex-fill w-100 mb-2">
                <!-- <li class="nav-item">
                    <a class="nav-link" href="sections.php">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Secciones</span>
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Carpetas</span>
                    </a>
                </li>
            </ul>
            <p class="text-muted nav-heading mt-4 mb-1"><span>Documentación</span></p>
            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link" href="upload_document.php">
                        <i class="fe fe-file fe-16"></i>
                        <span class="ml-3 item-text">Subir Documentos</span>
                    </a>
                </li>
            </ul>
        <?php endif; ?>
        <!-- Opciones para Administrador de Repositorio (role_id = 4) -->
        <?php if ($role_id == 4): ?>

            <p class="text-muted nav-heading mt-4 mb-1"><span>Administración</span></p>
            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link" href="sections.php">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Secciones</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="positions.php">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Cargos/Posiciones</span>
                    </a>
                </li>
                <p class="text-muted nav-heading mt-4 mb-1"><span>Usuarios</span></p>
                <li class="nav-item">
                    <a class="nav-link" href="admin_user.php">
                        <i class="fe fe-user fe-16"></i>
                        <span class="ml-3 item-text">Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Categorías</span>
                    </a>
                </li>
            </ul>
            <p class="text-muted nav-heading mt-4 mb-1"><span>Documentación</span></p>
            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link" href="upload_document.php">
                        <i class="fe fe-file fe-16"></i>
                        <span class="ml-3 item-text">Subir Documentos</span>
                    </a>
                </li>
            </ul>
        <?php endif; ?>
    </nav>
</aside>
