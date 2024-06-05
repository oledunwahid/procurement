<?php
include '../koneksi.php';

// Fetch Category data
$sql = "SELECT id_category, nama_category FROM proc_category";
$result = $koneksi->query($sql);

$categoryData = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categoryData[] = $row;
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($categoryData);

$koneksi->close();
