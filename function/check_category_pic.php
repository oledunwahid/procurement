<?php
include '../koneksi.php';
header('Content-Type: application/json');

try {
    if (!isset($_GET['category_id'])) {
        throw new Exception('Category ID is required');
    }

    $category_id = $_GET['category_id'];

    // Debug
    error_log("Checking category: " . $category_id);

    $query = "SELECT pac.idnik, u.nama 
              FROM proc_admin_category pac 
              LEFT JOIN user u ON pac.idnik = u.idnik 
              WHERE pac.id_category = ?";

    $stmt = mysqli_prepare($koneksi, $query);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt, "s", $category_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to execute statement: ' . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);

    $response = [
        'success' => true,
        'pic_list' => [],
        'pic_names' => []
    ];

    while ($row = mysqli_fetch_assoc($result)) {
        $response['pic_list'][] = $row['idnik'];
        $response['pic_names'][] = $row['nama'];
    }

    echo json_encode($response);
} catch (Exception $e) {
    error_log("Error in check_category_pic.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
