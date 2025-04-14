<?php
$host = "10.120.0.129"; // server
$user = "emp"; // username
$pass = "piwpiw"; // password
$database = "emp"; // nama database

$koneksi = mysqli_connect($host, $user, $pass, $database); // menggunakan mysqli_connect

if (mysqli_connect_error()) { // mengecek apakah koneksi database error
	echo 'Gagal melakukan koneksi ke Database : ' . mysqli_connect_error(); // pesan ketika koneksi database error
}
