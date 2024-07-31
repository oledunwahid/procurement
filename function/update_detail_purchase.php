<?php
include '../koneksi.php';
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'ID tidak ditemukan');

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $uom = $_POST['uom'];
    $qty = $_POST['qty'];
    $unit_price = $_POST['unit_price'];
    $detail_specification = $_POST['detail_specification'];
    $detail_notes = $_POST['detail_notes'];

    $query = "UPDATE proc_request_details SET id_proc_ch = ?, nama_barang = ?, qty = ?, uom = ?, detail_specification = ?, unit_price = ?, detail_notes = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "sssssssi", $id_proc_ch, $nama_barang, $qty, $uom, $detail_specification, $unit_price, $detail_notes, $id);

    if (mysqli_stmt_execute($stmt)) {
        $response['status'] = 'success';
        $response['message'] = 'Data berhasil diupdate';
    } else {
        $response['message'] = "Error: " . mysqli_error($koneksi);
    }

    mysqli_stmt_close($stmt);
}

echo json_encode($response);
mysqli_close($koneksi);
