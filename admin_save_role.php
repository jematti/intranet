<?php
include("conexion_db.php");

if (isset($_POST['save'])) {
    $role_name = mysqli_real_escape_string($conn, $_POST['role_name']);
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    // Insertar nuevo rol
    $query = "INSERT INTO `roles` (`role_name`) VALUES ('$role_name')";
    if (mysqli_query($conn, $query)) {
        $role_id = mysqli_insert_id($conn); // Obtener el ID del nuevo rol

        // Insertar permisos asociados
        foreach ($permissions as $permission_id) {
            $permission_id = intval($permission_id);
            $insert_perm = "INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('$role_id', '$permission_id')";
            mysqli_query($conn, $insert_perm);
        }

        echo "<script>alert('Rol y permisos agregados exitosamente.'); window.location = 'roles.php';</script>";
    } else {
        echo "<script>alert('Error al agregar el rol.'); window.location = 'roles.php';</script>";
    }
}
mysqli_close($conn);
?>
