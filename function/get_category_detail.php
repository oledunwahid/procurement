<?php
include '../koneksi.php';
header('Content-Type: application/json');

$id = $_POST['id'];
$id = mysqli_real_escape_string($koneksi, $id);

$query = "SELECT pac.*, pal.location 
          FROM proc_admin_category pac 
          LEFT JOIN proc_admin_location pal ON pac.idnik = pal.idnik 
          WHERE pac.id = '$id'";

$result = $koneksi->query($query);
if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Data not found']);
}
