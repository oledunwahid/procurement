<?php
include '../koneksi.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $uom = $_POST['uom'];
    $qty = $_POST['qty'];
    $unit_price = $_POST['unit_price'];

    $query = "UPDATE proc_request_details SET id_proc_ch = ?, nama_barang = ?, qty = ?, uom = ?, unit_price = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ssidsi", $id_proc_ch, $nama_barang, $qty, $uom, $unit_price, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "Data berhasil diupdate";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "ID tidak ditemukan";
}
