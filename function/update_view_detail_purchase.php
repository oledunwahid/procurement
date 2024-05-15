<?php
include '../koneksi.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
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

    $query = "UPDATE proc_request_details SET id_proc_ch = ?, nama_barang = ?, qty = ?, uom = ?, detail_specification = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "sssssi", $id_proc_ch, $nama_barang, $qty, $uom, $detail_specification, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "Data berhasil diupdate";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "ID tidak ditemukan";
}
