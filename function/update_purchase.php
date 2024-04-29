<?php
include '../koneksi.php'; // Sesuaikan dengan path file koneksi Anda

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mendapatkan data dari form
    $id_proc_ch = $_POST['id_proc_ch'];
    $title = $_POST['title'];
    $nik_request = $_POST['nik_request'];
    $proc_pic = $_POST['proc_pic'];
    $status = $_POST['status'];
    $category = $_POST['category'];
    $urgencies = $_POST['urgencies'];
    $total_price = $_POST['total_price'];

    // Memeriksa apakah ada file lampiran yang diunggah
    if (!empty($_FILES['lampiran']['name'])) {
        $lampiran = $_FILES['lampiran']['name'];
        $file_tmp = $_FILES['lampiran']['tmp_name'];
        move_uploaded_file($file_tmp, "uploads/" . $lampiran);
    } else {
        // Jika tidak ada file yang diunggah, gunakan nilai lampiran yang ada di database
        $sql_lampiran = "SELECT lampiran FROM proc_request_details WHERE id_proc_ch = '$id_proc_ch'";
        $result_lampiran = mysqli_query($koneksi, $sql_lampiran);
        $row_lampiran = mysqli_fetch_assoc($result_lampiran);
        $lampiran = $row_lampiran['lampiran'];
    }

    // Menyiapkan pernyataan SQL untuk memperbarui data purchase request
    $sql = "UPDATE proc_request_details SET 
            title = '$title',
            nik_request = '$nik_request',
            proc_pic = '$proc_pic',
            status = '$status',
            category = '$category',
            urgencies = '$urgencies',
            lampiran = '$lampiran'
            WHERE id_proc_ch = '$id_proc_ch'";

    // Menjalankan pernyataan SQL
    if (mysqli_query($koneksi, $sql)) {
        // Memperbarui total harga pada tabel proc_purchase_requests
        $sql_total_price = "UPDATE proc_purchase_requests SET total_price = '$total_price' WHERE id_proc_ch = '$id_proc_ch'";
        mysqli_query($koneksi, $sql_total_price);

        // Redirect kembali ke halaman purchase request setelah berhasil memperbarui
        header("Location: ../index.php?page=PurchaseRequests");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
    }

    // Menutup koneksi
    mysqli_close($koneksi);
}
