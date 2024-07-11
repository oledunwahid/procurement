<?php
session_start();
$_SESSION['url-dituju'] = $_SERVER['REQUEST_URI'];

if (isset($_COOKIE['login_token'])) {
	$token = $_COOKIE['login_token'];

	$_SESSION['idnik'];
}

if (!isset($_SESSION['idnik'])) {
	header("location:login.php");
	exit;
}
