<?php
include '../koneksi.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $category = $_POST['category'];
    $uom = $_POST['uom'];
    $qty = $_POST['qty'];
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

    // Update proc_request_details
    $queryDetails = "UPDATE proc_request_details SET id_proc_ch = ?, nama_barang = ?, qty = ?, uom = ?, detail_specification = ?, category = ? WHERE id = ?";
    $stmtDetails = mysqli_prepare($koneksi, $queryDetails);
    mysqli_stmt_bind_param($stmtDetails, "ssssssi", $id_proc_ch, $nama_barang, $qty, $uom, $detail_specification, $category, $id);

    // Execute the query
    if (mysqli_stmt_execute($stmtDetails)) {
        echo "Data berhasil diupdate";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }

    // Close the statement
    mysqli_stmt_close($stmtDetails);
} else {
    echo "ID tidak ditemukan";
}

mysqli_close($koneksi);
