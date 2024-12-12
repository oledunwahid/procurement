<?php
// Start atau resume session
session_start();

include 'koneksi.php';

// Menangkap data yang dikirim dari form login
$token = $_POST['token'];

// Prepared statement untuk login query
$query = "SELECT * 
          FROM login 
          LEFT JOIN user ON login.idnik = user.idnik 
          WHERE login.token = ? 
          AND user.status_login = 'Active'";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "s", $token);
mysqli_stmt_execute($stmt);
$login = mysqli_stmt_get_result($stmt);

// Menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($login);

if ($cek > 0) {
    $data = mysqli_fetch_assoc($login);

    // Set session berdasarkan data dari tabel login
    $_SESSION['idnik'] = $data['idnik'];
    $idnik = $data['idnik']; // Ambil idnik
    $_SESSION['url-dituju'] = isset($_SESSION['url-dituju']) ? $_SESSION['url-dituju'] : 'index.php?page=Dashboard';

    // Prepared statement untuk query roles
    $query_roles = "SELECT id_role FROM user_roles WHERE idnik = ?";
    $stmt_roles = mysqli_prepare($koneksi, $query_roles);
    mysqli_stmt_bind_param($stmt_roles, "s", $idnik);
    mysqli_stmt_execute($stmt_roles);
    $result_roles = mysqli_stmt_get_result($stmt_roles);

    $roles = [];
    while ($rowrole = mysqli_fetch_assoc($result_roles)) {
        $roles[] = $rowrole['id_role'];
    }
    $_SESSION['role'] = $roles; // Simpan role dalam session

    // Setelah berhasil login, redirect ke halaman yang ditentukan
    $url = $_SESSION['url-dituju'];
    header("location:$url");
    exit();
} else {
    $_SESSION['login_error'] = 'Token tidak sesuai atau akun tidak aktif';
    header("location:logintoken.php");
    exit();
}
