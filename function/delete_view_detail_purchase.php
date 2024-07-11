<?php
include '../koneksi.php';
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'ID tidak ditemukan');

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM proc_request_details WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        $response['status'] = 'success';
        $response['message'] = 'Data berhasil dihapus';
    } else {
        $response['message'] = "Error: " . mysqli_error($koneksi);
    }

    mysqli_stmt_close($stmt);
}

echo json_encode($response);
mysqli_close($koneksi);
