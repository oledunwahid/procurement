<?php
include '../koneksi.php';
header('Content-Type: application/json');

error_log("Received POST data: " . print_r($_POST, true));

$id_category = $_POST['id_category'];
$location = $_POST['location'];

// Escaping string untuk keamanan
$id_category = mysqli_real_escape_string($koneksi, $id_category);
$location = mysqli_real_escape_string($koneksi, $location);

$data = array();

if ($location === 'Site') {
    // Data PIC untuk Site
    $site_pics = array(
        // Laroenai
        array('idnik' => '20000308', 'nama' => 'Irwan'),
        array('idnik' => '21000805', 'nama' => 'Fairus Mubakri'),
        array('idnik' => '23003837', 'nama' => 'Ady'),
        // OBI
        array('idnik' => '19000136', 'nama' => 'Joko Santoso'),
        array('idnik' => '21001784', 'nama' => 'Victo'),
        array('idnik' => '22001752', 'nama' => 'Rakan'),
        array('idnik' => '23004251', 'nama' => 'Rona Justhafist')
    );

    $data = $site_pics;
} else {
    // Query untuk PIC Head Office (menggunakan query yang sudah ada)
    $query = "SELECT DISTINCT u.idnik, u.nama 
              FROM user u
              JOIN proc_admin_location pal ON u.idnik = pal.idnik
              WHERE pal.location = 'HO'
              AND u.idnik NOT IN ('19000133', '20000308', '19000136')";

    $result = $koneksi->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'idnik' => $row['idnik'],
                'nama' => $row['nama']
            );
        }
    }
}

error_log("Returning data: " . print_r($data, true));
echo json_encode($data);
