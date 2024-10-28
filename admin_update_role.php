<?php
include("conexion_db.php");

if (isset($_POST['edit'])) {
    $role_id = intval($_POST['role_id']);
    $role_name = mysqli_real_escape_string($conn, $_POST['role_name']);
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    // Actualizar nombre del rol
    $query = "UPDATE `roles` SET `role_name` = '$role_name' WHERE `role_id` = '$role_id'";
    if (mysqli_query($conn, $query)) {

        // Eliminar permisos actuales
        $delete_perms = "DELETE FROM `role_permissions` WHERE `role_id` = '$role_id'";
        mysqli_query($conn, $delete_perms);

        // Insertar nuevos permisos
        foreach ($permissions as $permission_id) {
            $permission_id = intval($permission_id);
            $insert_perm = "INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('$role_id', '$permission_id')";
            mysqli_query($conn, $insert_perm);
        }

        echo "<script>alert('Rol y permisos actualizados exitosamente.'); window.location = 'roles.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el rol.'); window.location = 'roles.php';</script>";
    }
}
mysqli_close($conn);
?>
