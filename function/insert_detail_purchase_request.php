<?php
include '../koneksi.php'; // Sesuaikan dengan path file koneksi Anda

if (isset($_POST['id_proc_ch'])) {
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $qty = $_POST['qty'];
    $unit_price = $_POST['unit_price'];
    $uom = $_POST['uom'];
    $detail_specification = $_POST['detail_specification'];

    // Menyiapkan prepared statement dengan parameter yang sesuai
    $stmt = $koneksi->prepare("INSERT INTO proc_request_details (id_proc_ch, nama_barang, qty, uom, detail_specification, unit_price) VALUES (?, ?, ?, ?,?, ?)");

    // Mengikat parameter ke statement
    $stmt->bind_param("sssssi", $id_proc_ch, $nama_barang, $qty, $uom, $detail_specification, $unit_price);

    // Menjalankan statement
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
        $stmt->close();
        $koneksi->close();
        exit;
    }

    $stmt->close();
    $koneksi->close();
    echo "success";
} else {
    echo "No data received";
}
