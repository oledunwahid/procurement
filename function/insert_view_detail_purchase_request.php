<?php
include '../koneksi.php'; // Sesuaikan dengan path file koneksi Anda

if (isset($_POST['id_proc_ch'])) {
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $qty = $_POST['qty'];
    $uom = $_POST['uom'];
    $detail_specification = $_POST['detail_specification'];

    // Memeriksa apakah nama barang sudah ada dalam file suggestions.txt
    $file = fopen('suggestion.txt', 'r');
    $suggestionExists = false;
    while (($line = fgets($file)) !== false) {
        if (trim($line) === $nama_barang) {
            $suggestionExists = true;
            break;
        }
    }
    fclose($file);

    // Jika nama barang belum ada dalam file suggestions.txt, tambahkan ke dalam file
    if (!$suggestionExists) {
        $file = fopen('suggestion.txt', 'a');
        fwrite($file, $nama_barang . "\n");
        fclose($file);
    }
    // Menyiapkan prepared statement dengan parameter yang sesuai
    $stmt = $koneksi->prepare("INSERT INTO proc_request_details (id_proc_ch, nama_barang, qty, uom, detail_specification) VALUES (?, ?, ?,?, ?)");

    // Mengikat parameter ke statement
    $stmt->bind_param("sssss", $id_proc_ch, $nama_barang, $qty, $uom, $detail_specification);

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
