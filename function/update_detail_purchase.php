<?php
include '../koneksi.php'; // Sesuaikan path ini sesuai dengan lokasi skrip koneksi database Anda

if (isset($_POST['id_proc_ch'], $_POST['nama_barang'], $_POST['uom'], $_POST['qty_barang'], $_POST['unit_price'])) {
    $id_proc_ch = $_POST['id_proc_ch'];
    $id_mr = $_POST['id_mr'];
    $nama_barang = $_POST['nama_barang'];
    $uom = $_POST['uom']; // Menambahkan variabel untuk uom
    $qty_barang = $_POST['qty_barang'];
    $unit_price = $_POST['unit_price'];

    // Menambahkan uom ke query update
    $query = "UPDATE proc_request_details SET nama_barang = ?, uom = ?, qty = ?, unit_price = ? WHERE id = ?";

    // Menyiapkan prepared statement untuk meningkatkan keamanan
    $stmt = mysqli_prepare($koneksi, $query);

    // Mengikat parameter ke statement yang disiapkan
    mysqli_stmt_bind_param($stmt, "ssidi", $nama_barang, $uom, $qty_barang, $unit_price, $id_proc_ch);

    // Menjalankan prepared statement
    if (mysqli_stmt_execute($stmt)) {
        echo "Data berhasil diupdate";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }

    // Menutup statement
    mysqli_stmt_close($stmt);
} else {
    echo "ID Detail MR atau data lainnya tidak ditemukan";
}
