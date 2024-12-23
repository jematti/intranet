<?php
session_start();
include("conexion_db.php");

if (isset($_SESSION['user_id'])) {
    // Registrar evento de logout en la bitácora
    $stmt = $conn->prepare("INSERT INTO `audit_log` (`user_id`, `action`, `ip_address`, `details`) VALUES (?, 'logout', ?, 'Cierre de sesión exitoso')");
    $stmt->bind_param("is", $_SESSION['user_id'], $_SERVER['REMOTE_ADDR']);
    $stmt->execute();
    $stmt->close();
}

// Regenerar el ID de sesión antes de destruirla para prevenir el secuestro de sesión
session_regenerate_id(true);

// Limpiar todas las variables de sesión
$_SESSION = [];

// Si se usa una cookie de sesión, eliminarla del navegador
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), // Nombre de la cookie de sesión
        '',             // Valor vacío
        time() - 42000, // Fecha de expiración en el pasado
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Cerrar la sesión de escritura para evitar más modificaciones
session_write_close();

// Redirigir al login con un mensaje de "sesión finalizada"
header("Location: login.php?logout=1");
exit();
?>
