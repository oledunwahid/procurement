<?php
include '../koneksi.php'; // Sesuaikan dengan path file koneksi Anda

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mendapatkan data dari form
    $id_proc_ch = $_POST['id_proc_ch'];
    $title = $_POST['title'];
    $nik_request = $_POST['nik_request'];
    $status = $_POST['status'];
    $category = $_POST['choices-single-default'];
    $jobLocation = $_POST['jobLocation'];
    $total_price = $_POST['total_price'];

    // Mendapatkan PIC berdasarkan kategori yang dipilih
    $sqlPic = "SELECT * FROM proc_category_pic WHERE id = '$category'";
    $resultPic = mysqli_query($koneksi, $sqlPic);
    $rowPic = mysqli_fetch_assoc($resultPic);
    $proc_pic = "";

    // Memeriksa nilai PIC berdasarkan kategori yang dipilih
    if ($rowPic['yohana_ratih_amalia'] == 1) {
        $proc_pic = "Yohana Ratih Amalia";
    } elseif ($rowPic['linda_permata_sari'] == 1) {
        $proc_pic = "Linda Permata Sari";
    } elseif ($rowPic['syifa_ramadhani'] == 1) {
        $proc_pic = "Syifa Ramadhani";
    } elseif ($rowPic['puji_astuti'] == 1) {
        $proc_pic = "Puji Astuti";
    } elseif ($rowPic['zana_chobita'] == 1) {
        $proc_pic = "Zana Chobita";
    } elseif ($rowPic['irwan'] == 1) {
        $proc_pic = "Irwan";
    } elseif ($rowPic['fairus_mubakri'] == 1) {
        $proc_pic = "Fairus Mubakri";
    } elseif ($rowPic['ady'] == 1) {
        $proc_pic = "Ady";
    } elseif ($rowPic['joko_santoso'] == 1) {
        $proc_pic = "Joko Santoso";
    } elseif ($rowPic['victo'] == 1) {
        $proc_pic = "Victo";
    } elseif ($rowPic['rakan'] == 1) {
        $proc_pic = "Rakan";
    } elseif ($rowPic['rona_justhafist'] == 1) {
        $proc_pic = "Rona Justhafist";
    } elseif ($rowPic['stheven_immanuel'] == 1) {
        $proc_pic = "Stheven Immanuel";
    } elseif ($rowPic['rizal_agus_fianto'] == 1) {
        $proc_pic = "Rizal Agus Fianto";
    } elseif ($rowPic['syifa_alifia'] == 1) {
        $proc_pic = "Syifa Alifia";
    } elseif ($rowPic['auriel'] == 1) {
        $proc_pic = "Auriel";
    }

    // Memeriksa apakah ada file lampiran yang diunggah
    if (!empty($_FILES['lampiran']['name'])) {
        $lampiran = $_FILES['lampiran']['name'];
        $file_tmp = $_FILES['lampiran']['tmp_name'];
        move_uploaded_file($file_tmp, "file/procurement" . $lampiran);
    } else {
        // Jika tidak ada file yang diunggah, gunakan nilai lampiran yang ada di database
        $sql_lampiran = "SELECT lampiran FROM proc_purchase_requests WHERE id_proc_ch = '$id_proc_ch'";
        $result_lampiran = mysqli_query($koneksi, $sql_lampiran);
        $row_lampiran = mysqli_fetch_assoc($result_lampiran);
        $lampiran = $row_lampiran['lampiran'];
    }

    // Menyiapkan pernyataan SQL untuk memperbarui data purchase request
    $sql = "UPDATE proc_purchase_requests SET title = '$title', nik_request = '$nik_request', proc_pic = '$proc_pic', status = '$status', category = '$category', lampiran = '$lampiran', total_price = '$total_price', job_location = '$jobLocation' WHERE id_proc_ch = '$id_proc_ch'";

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
