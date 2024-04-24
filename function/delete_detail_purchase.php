<?php
include '../koneksi.php'; // Sesuaikan dengan path file koneksi Anda

// Mengecek apakah ID tersedia
if(isset($_POST['id_detail_mr'])) {
    $id_detail_mr = $_POST['id_detail_mr'];

    // Query untuk menghapus data
    $query = "DELETE FROM budget_detail_mr WHERE id_detail_mr = '$id_detail_mr'";
    $result = mysqli_query($koneksi, $query);

    if($result) {
        echo "Data berhasil dihapus";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    echo "ID Detail MR tidak ditemukan";
}
?>
