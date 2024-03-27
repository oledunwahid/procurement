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
            proc_purchase_requests.created_request,
            proc_request_details.nik_request,
            proc_request_details.title,
            proc_request_details.proc_pic,
            proc_request_details.status,
            proc_request_details.category,
        FROM
            proc_purchase_requests
        INNER JOIN
            proc_request_details ON proc_purchase_requests.id_request = proc_request_details.request_id";
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
