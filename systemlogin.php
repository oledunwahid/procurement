<?php
// Start or resume session
session_start();
$url = $_SESSION['url-dituju'];

// Include koneksi.php file
include 'koneksi.php';

// Capture data sent from login form
$username = $_POST['username'];
$password = $_POST['password'];
$rememberMe = isset($_POST['remember']) ? $_POST['remember'] : '';

// Select user data with the matching username
$login = mysqli_query($koneksi, "SELECT * FROM login WHERE username='$username' AND status_login='Aktif'");
$cek = mysqli_num_rows($login);

if ($cek > 0) {
	$data = mysqli_fetch_assoc($login);

	// Verify password
	if (password_verify($password, $data['password'])) {
		$_SESSION['url-dituju'] = $url;
		// Save user information in session
		if ($password === '123456') {
			$_SESSION['username'] = $username;
			$_SESSION['reset_password'] = true; // Set session variable for password reset
			$_SESSION['Messages'] = 'Please change your password for security reasons.';
			$_SESSION['Icon'] = 'warning'; // Use 'warning' icon
			header("location:changepassword2.php");
			exit();
		}

		$_SESSION['idnik'] = $data['idnik'];

		$idnik = $data['idnik'];
		$_SESSION['url-dituju'] = $url;

		$sqlroles = mysqli_query($koneksi, "SELECT id_role FROM user_roles WHERE idnik='$idnik'");
		$roles = [];
		while ($rowrole = mysqli_fetch_assoc($sqlroles)) {
			$roles[] = $rowrole['id_role'];
		}
		$_SESSION['role'] = $roles;

		// Query the user's name from the user table
		$user_query = mysqli_query($koneksi, "SELECT nama FROM user WHERE idnik='$idnik'");
		$user_data = mysqli_fetch_assoc($user_query);
		$user_name = $user_data['nama'];

		// If the password is '123456', redirect to the change password page

		// If "Remember me" is checked
		if ($rememberMe === 'yes') {
			$cookie_lifetime = 30 * 24 * 60 * 60; // 1 month
			setcookie('username', $username, time() + $cookie_lifetime);
		}

		$_SESSION['Messages'] = 'Login successful! Welcome, ' . htmlspecialchars($user_name) . '.';
		$_SESSION['Icon'] = 'success'; // Use 'success' icon
		$url = isset($_SESSION['url-dituju']) ? $_SESSION['url-dituju'] : 'index.php?page=Dashboard';
		header("location:$url");
		exit();
	} else {
		$_SESSION['Messages'] = 'Username or password is incorrect.';
		$_SESSION['Icon'] = 'error'; // Use 'error' icon
		header("location:login.php");
		exit();
	}
} else {
	$_SESSION['Messages'] = 'Username or password is incorrect.';
	$_SESSION['Icon'] = 'error'; // Use 'error' icon
	header("location:login.php");
	exit();
}
