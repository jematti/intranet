<?php
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit();
/*
	session_start();
	unset($_SESSION['employee']);
	header("location: index.php");
*/
?>

