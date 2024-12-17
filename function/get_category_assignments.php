<?php
include "../koneksi.php";
header('Content-Type: application/json');

try {
    $query = "SELECT 
                pac.id, 
                pac.id_category, 
                pc.nama_category, 
                pac.idnik, 
                u.nama,
                paw.no_wa
              FROM proc_admin_category pac
              LEFT JOIN proc_category pc ON pac.id_category = pc.id_category
              LEFT JOIN user u ON pac.idnik = u.idnik
              LEFT JOIN proc_admin_wa paw ON pac.idnik = paw.idnik";

    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        throw new Exception("Query failed: " . mysqli_error($koneksi));
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Format nomor WA jika ada
        $row['no_wa'] = $row['no_wa'] ? $row['no_wa'] : '';
        $data[] = $row;
    }

    echo json_encode([
        'data' => $data,
        'recordsTotal' => count($data),
        'recordsFiltered' => count($data)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($koneksi);
