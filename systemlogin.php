<?php
/// Start atau resume session
session_start();
$url = $_SESSION['url-dituju'];

include 'koneksi.php';

// Menangkap data yang dikirim dari form login
$username = $_POST['username'];
$password = $_POST['password'];
$rememberMe = isset($_POST['remember']) ? $_POST['remember'] : '';

// Menyeleksi data user dengan username yang sesuai
$login = mysqli_query($koneksi, "SELECT * FROM login WHERE username='$username' AND status_login='Aktif'");
// Menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($login);

if ($cek > 0) {
	$data = mysqli_fetch_assoc($login);

	// Verifikasi password
	if (password_verify($password, $data['password'])) {
		// Jika passwordnya masih '123456', arahkan ke halaman reset password
		$_SESSION['idnik'] = $data['idnik'];
		$idnik = $data['idnik']; // Ambil idnik
		$_SESSION['url-dituju'] = $url;

		$sqlroles = mysqli_query($koneksi, "SELECT id_role FROM user_roles WHERE idnik='$idnik'");
		$roles = [];
		while ($rowrole = mysqli_fetch_assoc($sqlroles)) {
			$roles[] = $rowrole['id_role'];
		}
		$_SESSION['role'] = $roles; // Simpan role dalam session<br>

		if ($password === '123456') {
			header("location:changepassword2.php");
			exit();
		}

		// Jika "Remember me" dicentang
		if ($rememberMe === 'yes') {
			$cookie_lifetime = 30 * 24 * 60 * 60; // 1 bulan
			setcookie('username', $username, time() + $cookie_lifetime);
			// Jangan simpan password di cookie, ini hanya contoh
			// Simpan token aman sebagai pengganti jika diperlukan
		}

		// Setelah berhasil login, redirect ke halaman yang ditentukan
		$url = isset($_SESSION['url-dituju']) ? $_SESSION['url-dituju'] : 'index.php?page=Dashboard';
		header("location:$url");
		exit();
	} else {
		$_SESSION['login_error'] = 'Username atau password tidak sesuai';
		header("location:login.php");
		exit();
	}
} else {
	$_SESSION['login_error'] = 'Username atau password tidak sesuai';
	header("location:login.php");
	exit();
}
