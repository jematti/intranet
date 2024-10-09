<?php 
	include_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/conexion_db.php';

	if (isset($_REQUEST['store_id'])) {
		$store_id = $_REQUEST['store_id'];
		
		// Obtener la información del archivo, incluyendo el `user_id`
		$query = mysqli_query($conn, "SELECT s.filename, u.user_id FROM storage s JOIN user u ON s.user_id = u.user_id WHERE s.store_id = '$store_id'") or die(mysqli_error($conn));
		$fetch = mysqli_fetch_array($query);
		
		$filename = $fetch['filename']; // Nombre del archivo
		$user_id = $fetch['user_id']; // ID del usuario (user_id)
		
		// Definir la ruta completa del archivo basado en el user_id del usuario
		$file_path = "files/" . $user_id . "/" . $filename;
		
		// Verificar si el archivo existe
		if (file_exists($file_path)) {
			// Preparar la descarga del archivo
			header("Content-Disposition: attachment; filename=" . $filename);
			header("Content-Type: application/octet-stream;");
			header("Content-Length: " . filesize($file_path));

			// Enviar el archivo al navegador
			readfile($file_path);
			exit();
		} else {
			// Si el archivo no existe, mostrar alerta
			echo "<script>alert('El archivo no se encuentra en el servidor.'); window.history.back();</script>";
		}
	} else {
		// Si no se proporciona un `store_id`, mostrar alerta
		echo "<script>alert('No se ha proporcionado un ID de archivo válido.'); window.history.back();</script>";
	}
?>
