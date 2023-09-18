<?php  
	session_start();
	if (isset($_SESSION['sessionToken'])) {
		session_destroy();
	}
	header('location:../index.php');
?>