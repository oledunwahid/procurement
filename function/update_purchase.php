<?php
include '../koneksi.php';
session_start();
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'Terjadi kesalahan');

if (isset($_POST['id_proc_ch'])) {
    error_log("Received POST data: " . print_r($_POST, true));

    $id_proc_ch = $_POST['id_proc_ch'];
    $created_request = $_POST['created_request'];
    $requester_name = $_POST['requester_name'];
    $title = $_POST['title'];
    $total_price = str_replace(['Rp ', '.'], '', $_POST['total_price']);
    $status = $_POST['status']; // Diasumsikan status dikirim dari JavaScript

    $niklogin = isset($_SESSION['nik']) ? $_SESSION['nik'] : '';

    $isSupervisor = in_array($niklogin, ['19000133', '20000308', '19000136', '19000078']);

    // Ambil data lampiran yang ada
    $sql_lampiran = "SELECT lampiran FROM proc_purchase_requests WHERE id_proc_ch = ?";
    $stmt = mysqli_prepare($koneksi, $sql_lampiran);
    mysqli_stmt_bind_param($stmt, "s", $id_proc_ch);
    mysqli_stmt_execute($stmt);
    $result_lampiran = mysqli_stmt_get_result($stmt);
    $row_lampiran = mysqli_fetch_assoc($result_lampiran);
    $existing_lampiran = $row_lampiran['lampiran'];

    // Cek apakah ada file baru yang diupload
    if (!empty($_FILES['lampiran']['name'])) {
        $lampiran = $_FILES['lampiran']['name'];
        $file_tmp = $_FILES['lampiran']['tmp_name'];
        move_uploaded_file($file_tmp, "../file/procurement/" . $lampiran);
    } else {
        $lampiran = $existing_lampiran;
    }

    $sql = "UPDATE proc_purchase_requests SET created_request = ?, title = ?, total_price = ?, lampiran = ?, status = ? WHERE id_proc_ch = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $created_request, $title, $total_price, $lampiran, $status, $id_proc_ch);

    if (mysqli_stmt_execute($stmt)) {
        $response['status'] = 'success';
        $response['message'] = "Data berhasil diupdate";

        if ($status == 'Closed') {
            $sqlUser = "SELECT nama, wa FROM user WHERE nama = ?";
            $stmtUser = mysqli_prepare($koneksi, $sqlUser);
            mysqli_stmt_bind_param($stmtUser, "s", $requester_name);
            mysqli_stmt_execute($stmtUser);
            $resultUser = mysqli_stmt_get_result($stmtUser);
            $rowUser = mysqli_fetch_assoc($resultUser);

            if ($rowUser) {
                $namaUser = $rowUser['nama'];
                $waUser = $rowUser['wa'];

                $namaEmployee = 'Bapak/Ibu ' . $namaUser;
                $link = 'https://proc.maagroup.co.id/index.php?page=UserDetailPurchase&id=' . $id_proc_ch;

                $message = "Halo " . $namaEmployee . "!\n\nPrice Request dengan ID #" . $id_proc_ch . " telah ditutup oleh admin.\n\nInfo lebih lanjut tentang Price Request ini: " . $link;

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

                $responseWA = curl_exec($curl);
                curl_close($curl);
            } else {
                $response['message'] .= " Namun, data user tidak ditemukan untuk mengirim notifikasi.";
            }
        }
    } else {
        $response['message'] = "Error: " . mysqli_error($koneksi);
    }

    mysqli_stmt_close($stmt);
} else {
    $response['message'] = "Data tidak lengkap";
}

echo json_encode($response);
mysqli_close($koneksi);
