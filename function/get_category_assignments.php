<?php
include "../koneksi.php";
header('Content-Type: application/json');

try {
    $query = "SELECT 
                pac.id, 
                pac.id_category, 
                pc.nama_category, 
                pac.idnik, 
                u.nama 
              FROM proc_admin_category pac
              LEFT JOIN proc_category pc ON pac.id_category = pc.id_category
              LEFT JOIN user u ON pac.idnik = u.idnik";

    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        throw new Exception("Query failed: " . mysqli_error($koneksi));
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode([
        'data' => $data,
        'recordsTotal' => count($data),
        'recordsFiltered' => count($data)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
