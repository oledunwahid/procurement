<?php 
// mengaktifkan session pada php
session_start();
$url = $_SESSION['yuhu'] ;
// menghubungkan php dengan koneksi database
include 'koneksi.php';

// menangkap data yang dikirim dari form login
$username = $_POST['username'];
$password = $_POST['password'];


// menyeleksi data user dengan username dan password yang sesuai
$login = mysqli_query($koneksi,"select * from login where username='$username' and password='$password' and status_login ='Aktif' ");
// menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($login);

// cek apakah username dan password di temukan pada database
if($cek > 0){

	$data = mysqli_fetch_assoc($login);

	// cek jika user login sebagai admin
	if($data['role']=="admin"){

		// buat session login dan username
		$_SESSION['username'] = $username;
		$_SESSION['role'] = "admin";
		
	
		if($url ==  false || $url == null ){
			header("location:index.php?page=Dashboard");
		}else{

			header("location:$url");
		}
		

	// cek jika user login sebagai user
	}else if($data['role']=="user"){
		// buat session login dan username
		$_SESSION['username'] = $username;
		$_SESSION['role'] = "user";
		
		
	
			if($url ==  false || $url == null ){
			header("location:index.php?page=Dashboard");
		}else{

			header("location:$url");
		}

	}
	
}else{
	header("location:login.php?pesan=gagal");
}
