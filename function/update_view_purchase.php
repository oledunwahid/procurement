<?php
include '../koneksi.php';

if (isset($_POST['id_proc_ch'])) {
    // Mendapatkan data dari form
    $id_proc_ch = $_POST['id_proc_ch'];
    $title = $_POST['title'];
    $nik_request = $_POST['requester_name'];
    $category = $_POST['category'];
    $jobLocation = $_POST['jobLocation'];
    $total_price = $_POST['total_price'];
    $proc_pic = $_POST['proc_pic'];
    $total_price = str_replace('.', '', $total_price);
    $status = 'Open';

    $sqlNama = "SELECT  nama, wa FROM user WHERE idnik = '$proc_pic' ";
    $rowNama = mysqli_fetch_assoc(mysqli_query($koneksi, $sqlNama));
    $NamaPIC = $rowNama['nama'];
    $waPIC = $rowNama['wa'];

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

    $sql = "UPDATE proc_purchase_requests SET 
    title = '$title', 
    nik_request = '$nik_request', 
    category = '$category', 
    proc_pic = '$proc_pic',
    total_price = '$total_price', 
    job_location = '$jobLocation', 
    lampiran = '$lampiran', 
    status = '$status'
    WHERE id_proc_ch = '$id_proc_ch'";

    // Menjalankan query update
    if (mysqli_query($koneksi, $sql)) {
        $namaEmployee = 'Bapak/Ibu ' . $NamaPIC;
        // Ganti dengan nama yang sesuai
        $link = 'https://proc.maagroup.co.id/index.php?page=UserDetailPurchase&id=' . $id_proc_ch; // Ganti dengan URL yang valid

        $message = "Halo " . $namaEmployee . "!\n\nAda Price Request dengan ID #" . $id_proc_ch .  "\n\nInfo lebih lanjut tentang Price Request ini:"
            . $link;

        // Pengaturan untuk cURL
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $waPIC,
                'message' => $message,
                'countryCode' => '62', // Ganti kode negara jika perlu
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: vXmxpJo3+5kVsDAWt!y+' // Ganti TOKEN dengan token Anda
            ),
        ));
        // Melakukan request pengiriman pesan WhatsApp
        $response = curl_exec($curl);
        // Menutup koneksi cURL
        curl_close($curl);
        echo "Data berhasil diupdate";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
    }

    // Menutup koneksi
    mysqli_close($koneksi);
}
