<?php
include '../koneksi.php'; // Sesuaikan dengan path file koneksi Anda

if (isset($_POST['id_proc_ch'], $_POST['nama_barang'], $_POST['qty_barang'], $_POST['price'], $_POST['uom'])) {
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $qty_barang = $_POST['qty_barang'];
    $price = $_POST['price'];
    $uom = $_POST['uom']; // Mengambil nilai uom dari POST request

    // Menyiapkan prepared statement dengan parameter tambahan untuk uom
    $stmt = $koneksi->prepare("INSERT INTO proc_request_details (id_proc_ch, nama_barang, qty, uom, unit_price) VALUES (?, ?, ?, ?, ?)");

    // Mengikat parameter ke statement, termasuk uom
    $stmt->bind_param("ssids", $id_proc_ch, $nama_barang, $qty_barang, $uom, $price);

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
