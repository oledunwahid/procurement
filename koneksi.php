<?php
$host = "localhost"; // server
$user = "root"; // username
$pass = ""; // password
$database = "emp"; // nama database

$koneksi = mysqli_connect($host, $user, $pass, $database); // menggunakan mysqli_connect

if (mysqli_connect_error()) { // mengecek apakah koneksi database error
	echo 'Gagal melakukan koneksi ke Database : ' . mysqli_connect_error(); // pesan ketika koneksi database error
}
