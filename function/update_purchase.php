<?php
include '../koneksi.php';

if (isset($_POST['id_proc_ch'])) {
    // Mendapatkan data dari form
    $id_proc_ch = $_POST['id_proc_ch'];
    $title = $_POST['title'];
    $nik_request = $_POST['requester_name'];
    $total_price = $_POST['total_price'];
    $total_price = str_replace('.', '', $total_price);
    $status = $_POST['status'];

    // Memeriksa apakah pengguna adalah supervisor atau PIC biasa
    $isSupervisor = in_array($niklogin, ['19000133', '20000308', '19000136', '19000078']);

    if ($isSupervisor) {
        // Jika pengguna adalah supervisor, ambil nilai dari form
        $jobLocation = $_POST['jobLocation'];
        $proc_pic = $_POST['proc_pic'];
        $category = $_POST['category'];
    } else {
        // Jika pengguna adalah PIC biasa, ambil nilai dari database
        $sql_data = "SELECT job_location, proc_pic, category FROM proc_purchase_requests WHERE id_proc_ch = '$id_proc_ch'";
        $result_data = mysqli_query($koneksi, $sql_data);
        $row_data = mysqli_fetch_assoc($result_data);
        $jobLocation = $row_data['job_location'];
        $proc_pic = $row_data['proc_pic'];
        $category = $row_data['category'];
    }

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
    $sql = "UPDATE proc_purchase_requests SET title = '$title', nik_request = '$nik_request', proc_pic = '$proc_pic', total_price = '$total_price', job_location = '$jobLocation', lampiran = '$lampiran', status = '$status', category = '$category' WHERE id_proc_ch = '$id_proc_ch'";

    // Menjalankan query update
    if (mysqli_query($koneksi, $sql)) {
        if ($status == 'Closed') {
            // Jika status diubah menjadi 'Closed', kirim pesan WhatsApp ke user
            $sqlUser = "SELECT nama, wa FROM user WHERE idnik = '$nik_request'";
            $rowUser = mysqli_fetch_assoc(mysqli_query($koneksi, $sqlUser));
            $namaUser = $rowUser['nama'];
            $waUser = $rowUser['wa'];

            $namaEmployee = 'Bapak/Ibu ' . $namaUser;
            $link = 'https://proc.maagroup.co.id/index.php?page=UserDetailPurchase&id=' . $id_proc_ch;

            $message = "Halo " . $namaEmployee . "!\n\nPrice Request dengan ID #" . $id_proc_ch . " telah ditutup oleh admin.\n\nInfo lebih lanjut tentang Price Request ini: " . $link;

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
                    'target' => $waUser,
                    'message' => $message,
                    'countryCode' => '62',
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: vXmxpJo3+5kVsDAWt!y+'
                ),
            ));

            // Melakukan request pengiriman pesan WhatsApp ke user
            $response = curl_exec($curl);
            curl_close($curl);
        }

        echo "Data berhasil diupdate";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
    }


    // Menutup koneksi
    mysqli_close($koneksi);
}
