<?php
// Start atau resume session
session_start();

include 'koneksi.php';

// Menangkap data yang dikirim dari form login
$token = $_POST['token'];

// Menyeleksi data user dengan token yang sesuai dan status login aktif
$login = mysqli_query($koneksi, "SELECT * FROM login WHERE token='$token' AND status_login='Aktif'");
// Menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($login);

if ($cek > 0) {
    $data = mysqli_fetch_assoc($login);

    // Set session berdasarkan data dari tabel login
    $_SESSION['idnik'] = $data['idnik'];
    $idnik = $data['idnik']; // Ambil idnik
    $_SESSION['url-dituju'] = isset($_SESSION['url-dituju']) ? $_SESSION['url-dituju'] : 'index.php?page=Dashboard';

    // Ambil role dari tabel user_roles
    $sqlroles = mysqli_query($koneksi, "SELECT id_role FROM user_roles WHERE idnik='$idnik'");
    $roles = [];
    while ($rowrole = mysqli_fetch_assoc($sqlroles)) {
        $roles[] = $rowrole['id_role'];
    }
    $_SESSION['role'] = $roles; // Simpan role dalam session<br>



    // Setelah berhasil login, redirect ke halaman yang ditentukan
    $url = $_SESSION['url-dituju'];
    header("location:$url");
    exit();
} else {
    $_SESSION['login_error'] = 'Token tidak sesuai atau akun tidak aktif';
    header("location:logintoken.php");
    exit();
}
