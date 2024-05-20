<?php
include '../koneksi.php';

if (isset($_POST['id_proc_ch'])) {
    // Mendapatkan data dari form
    $id_proc_ch = $_POST['id_proc_ch'];
    $title = $_POST['title'];
    $nik_request = $_POST['requester_name'];
    $jobLocation = $_POST['jobLocation'];
    $total_price = $_POST['total_price'];
    $proc_pic = $_POST['proc_pic'];
    $total_price = str_replace('.', '', $total_price);
    $status = $_POST['status']; // Mengambil nilai status dari form

    // Memeriksa apakah ada file lampiran yang diunggah
    if (!empty($_FILES['lampiran']['name'])) {
        $lampiran = $_FILES['lampiran']['name'];
        $file_tmp = $_FILES['lampiran']['tmp_name'];
        move_uploaded_file($file_tmp, "../file/procurement/" . $lampiran);
    } else {
        // Jika tidak ada file yang diunggah, gunakan nilai lampiran yang ada di database
        $sql_lampiran = "SELECT lampiran FROM proc_purchase_requests WHERE id_proc_ch = '$id_proc_ch'";
        $result_lampiran = mysqli_query($koneksi, $sql_lampiran);
        $row_lampiran = mysqli_fetch_assoc($result_lampiran);
        $lampiran = $row_lampiran['lampiran'];
    }

    // Menyiapkan pernyataan SQL untuk memperbarui data purchase request
    $sql = "UPDATE proc_purchase_requests SET title = '$title', nik_request = '$nik_request', proc_pic = '$proc_pic', total_price = '$total_price', job_location = '$jobLocation', lampiran = '$lampiran', status = '$status' WHERE id_proc_ch = '$id_proc_ch'";

    // Menjalankan query update
    if (mysqli_query($koneksi, $sql)) {
        echo "Data berhasil diupdate";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
    }

    // Menutup koneksi
    mysqli_close($koneksi);
}
