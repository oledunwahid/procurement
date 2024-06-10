<?php
include '../koneksi.php'; // Sesuaikan dengan path file koneksi Anda

if (isset($_POST['id_proc_ch'])) {
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $qty = $_POST['qty'];
    $uom = $_POST['uom'];
    $detail_specification = $_POST['detail_specification'];
    $category = $_POST['category'];


    $file = fopen('suggestion.txt', 'r');
    $suggestionExists = false;
    while (($line = fgets($file)) !== false) {
        if (trim($line) === $nama_barang) {
            $suggestionExists = true;
            break;
        }
    }
    fclose($file);

    if (!$suggestionExists) {
        $file = fopen('suggestion.txt', 'a');
        fwrite($file, $nama_barang . "\n");
        fclose($file);
    }

    // Menyiapkan prepared statement dengan parameter yang sesuai
    $stmt = $koneksi->prepare("INSERT INTO proc_request_details (id_proc_ch, nama_barang, qty, uom, detail_specification, category) VALUES (?, ?, ?, ?, ?, ?)");

    // Mengikat parameter ke statement
    $stmt->bind_param("ssssss", $id_proc_ch, $nama_barang, $qty, $uom, $detail_specification, $category);

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
