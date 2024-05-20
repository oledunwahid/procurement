<?php
include '../koneksi.php';
$id_category = $_POST['id_category'];
$location = $_POST['location'];

// Daftar idnik PIC Supervisor
$pic_supervisors = array('19000133', '20000308', '19000136', '19000078');

// Escaping string untuk keamanan
$id_category = mysqli_real_escape_string($koneksi, $id_category);
$location = mysqli_real_escape_string($koneksi, $location);

// Query untuk mendapatkan daftar PIC selain PIC Supervisor
$query = "SELECT u.idnik, u.nama 
          FROM user u
          JOIN proc_admin_category pac ON u.idnik = pac.idnik
          JOIN proc_admin_location pal ON u.idnik = pal.idnik
          WHERE pac.id_category = '$id_category'
          AND pal.location = '$location'
          AND u.idnik NOT IN ('" . implode("','", $pic_supervisors) . "')";

$result = $koneksi->query($query);
$options = "";

while ($row = $result->fetch_assoc()) {
    $options .= "<option value='" . $row['idnik'] . "'>" . htmlspecialchars($row['nama']) . "</option>";
}

echo $options;
