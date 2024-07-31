<?php
include '../koneksi.php';
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'ID tidak ditemukan');

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Periksa apakah data dengan id_proc_ch tersebut ada
        $check_query = "SELECT id_proc_ch FROM proc_purchase_requests WHERE id_proc_ch = ?";
        $check_stmt = mysqli_prepare($koneksi, $check_query);
        mysqli_stmt_bind_param($check_stmt, "s", $id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) == 0) {
            throw new Exception("Data dengan ID tersebut tidak ditemukan.");
        }
        mysqli_stmt_close($check_stmt);

        // Hapus detail purchase request
        $query_detail = "DELETE FROM proc_request_details WHERE id_proc_ch = ?";
        $stmt_detail = mysqli_prepare($koneksi, $query_detail);
        mysqli_stmt_bind_param($stmt_detail, "s", $id);
        mysqli_stmt_execute($stmt_detail);
        mysqli_stmt_close($stmt_detail);

        // Hapus purchase request utama
        $query_main = "DELETE FROM proc_purchase_requests WHERE id_proc_ch = ?";
        $stmt_main = mysqli_prepare($koneksi, $query_main);
        mysqli_stmt_bind_param($stmt_main, "s", $id);
        mysqli_stmt_execute($stmt_main);
        mysqli_stmt_close($stmt_main);

        // Commit transaksi jika tidak ada error
        mysqli_commit($koneksi);

        $response['status'] = 'success';
        $response['message'] = 'Purchase request berhasil dihapus';
    } catch (Exception $e) {
        // Rollback jika terjadi error
        mysqli_rollback($koneksi);
        $response['message'] = "Error: " . $e->getMessage();
    }
} else {
    $response['message'] = "ID tidak diterima";
}

echo json_encode($response);
mysqli_close($koneksi);
