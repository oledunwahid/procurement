<?php
include '../koneksi.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM proc_request_details WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "Data berhasil dihapus";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "ID tidak ditemukan";
}
