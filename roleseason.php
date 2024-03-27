<?php 
	session_start();

$_SESSION['yuhu'] = $_SERVER['REQUEST_URI'];


if (!isset($_SESSION['username'])){
	header("location:login.php?gagal");

}

		
?>