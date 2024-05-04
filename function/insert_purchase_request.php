<?php
include("../koneksi.php");

if (isset($_POST["add-purchase-request"])) {
    $nik_request = $_POST['nik_request'];

    // Cek apakah sudah ada id_proc_ch dengan status 'Pending'
    $cek_query = "SELECT * FROM proc_purchase_requests WHERE nik_request = '$nik_request' AND status = 'Created' ";
    $cek_result = mysqli_query($koneksi, $cek_query);

    if (mysqli_num_rows($cek_result) > 0) {
        // Jika ditemukan, ambil id_proc_ch dari data yang ada
        $data_exist = mysqli_fetch_assoc($cek_result);
        $existing_id_proc_ch = $data_exist['id_proc_ch'];

        // Redirect ke halaman detail Purchase Request dengan id_proc_ch yang sudah ada
        if ($_SESSION['role'] == 'admin') {
            header("location:../index.php?page=DetailPurchase&id=$existing_id_proc_ch");
            exit;
        } else {
            header("location:../index.php?page=UserDetailPurchase&id=$existing_id_proc_ch");
            exit;
        }
    } else {
        // Jika tidak ditemukan, lanjutkan dengan proses insert
        $tanggal_req = date('Y-m-d H:i:s');
        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $timestamp = $currentDateTime->format('ymdHis');
        $RequestNumber = "CH" . $timestamp;

        $id_proc_ch = $RequestNumber;
        $created_request = $tanggal_req;
        $status = 'Created';
        $query = "INSERT INTO proc_purchase_requests (id_proc_ch, created_request, nik_request, status) VALUES ('$id_proc_ch','$created_request','$nik_request','$status')";
        $result = mysqli_query($koneksi, $query);

        if ($result) {
            if ($_SESSION['role'] == 'admin') {
                header("location:../index.php?page=DetailPurchase&id=$id_proc_ch");
            } else {
                header("location:../index.php?page=UserDetailPurchase&id=$id_proc_ch");
            }
        } else {
            // Handle jika insert proc_request_details gagal
            header("location:../index.php?page=DetailPurchase&id=$id_proc_ch");
        }
    }
} else {
    die("Akses dilarang...");
}
