<?php
include '../koneksi.php';
$id_category = $_POST['id_category'];
$location = $_POST['location']; // Escaping string untuk keamanan

$query = "SELECT u.idnik, u.nama FROM user u
          JOIN proc_admin_category pac ON u.idnik = pac.idnik
          JOIN proc_admin_location pal ON u.idnik = pal.idnik
          WHERE pac.id_category = '$id_category' AND pal.location = '$location' ";

$result = $koneksi->query($query);

$options = "";
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='" . $row['idnik'] . "'>" . htmlspecialchars($row['nama']) . "</option>";
}
echo $options;
