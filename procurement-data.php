<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "emp";

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$query = "SELECT
            proc_purchase_requests.id_request,
            proc_request_details.created_request,
            proc_request_details.nik_request,
            proc_request_details.title,
            proc_request_details.proc_pic,
            proc_request_details.status,
            proc_request_details.category,
            proc_request_details.urgencies,
            proc_request_details.lampiran,
            proc_purchase_requests.nama_barang,
            proc_purchase_requests.qty,
            proc_purchase_requests.uom,
            proc_purchase_requests.remarks,
            proc_purchase_requests.unit_price
        FROM
            proc_request_details
        INNER JOIN
            proc_purchase_requests ON proc_purchase_requests.id_request = proc_request_details.id_request";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Error: " . mysqli_error($koneksi));
}

$data = array();

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

mysqli_free_result($result);
mysqli_close($koneksi);

// Mengembalikan data dalam format JSON
echo json_encode(array("data" => $data));
