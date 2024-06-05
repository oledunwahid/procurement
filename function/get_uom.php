<?php
include '../koneksi.php';

// Check connection
if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

// Fetch UOM data
$sql = "SELECT uom_name FROM uom";
$result = $koneksi->query($sql);

$uomData = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $uomData[] = $row;
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($uomData);

$koneksi->close();
