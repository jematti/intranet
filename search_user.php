<?php
include("conexion_db.php");

$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$position_id = $_POST['position_id'] ?? ''; // Asegúrate de recoger el valor de position_id

// Construir la consulta SQL con declaraciones preparadas
$sql = "SELECT u.*, p.position_name, r.repository_name, r.building, r.department
        FROM `user` u
        JOIN `positions` p ON u.position_id = p.position_id
        JOIN `repositories` r ON u.repository_id = r.repository_id
        WHERE u.active_status = 1";  // Solo seleccionar usuarios activos

$params = [];
$types = '';

if (!empty($nombre)) {
    $sql .= " AND u.firstname LIKE ?";
    $params[] = "%$nombre%";
    $types .= 's';
}
if (!empty($apellido)) {
    $sql .= " AND u.lastname LIKE ?";
    $params[] = "%$apellido%";
    $types .= 's';
}
if (!empty($telefono)) {
    $sql .= " AND u.phone LIKE ?";
    $params[] = "%$telefono%";
    $types .= 's';
}
if (!empty($position_id)) { // Filtrar por position_id en lugar de position_name
    $sql .= " AND u.position_id = ?";
    $params[] = $position_id;
    $types .= 'i'; // 'i' porque position_id es un entero
}

$sql .= " LIMIT 5";  // Limitar los resultados a los primeros 5 registros

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$results = $result->fetch_all(MYSQLI_ASSOC);
?>
