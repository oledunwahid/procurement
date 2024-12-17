<?php
include("../koneksi.php");
header('Content-Type: application/json');

if (isset($_POST["add-purchase-request"])) {
    $nik_request = $_POST['nik_request'];

    // Cek existing request dengan prepared statement
    $cek_query = "SELECT * FROM proc_purchase_requests WHERE nik_request = ? AND status = 'Created'";
    $stmt = mysqli_prepare($koneksi, $cek_query);
    mysqli_stmt_bind_param($stmt, "s", $nik_request);
    mysqli_stmt_execute($stmt);
    $cek_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($cek_result) > 0) {
        $data_exist = mysqli_fetch_assoc($cek_result);
        $existing_id_proc_ch = $data_exist['id_proc_ch'];

        // Get user details for notification
        $user_query = "SELECT nama FROM user WHERE idnik = ?";
        $user_stmt = mysqli_prepare($koneksi, $user_query);
        mysqli_stmt_bind_param($user_stmt, "s", $nik_request);
        mysqli_stmt_execute($user_stmt);
        $user_result = mysqli_stmt_get_result($user_stmt);
        $user = mysqli_fetch_assoc($user_result);

        // Get WhatsApp configuration
        $config_query = "SELECT token FROM T_L_EIP_HRGA_HRGA_RF_WHATSAPP_CONFIG WHERE is_active = 1 ORDER BY id DESC LIMIT 1";
        $config_result = mysqli_query($koneksi, $config_query);
        $config = mysqli_fetch_assoc($config_result);

        if ($config) {
            // Get all admin WhatsApp numbers
            $admin_query = "SELECT paw.no_wa, u.nama 
                          FROM proc_admin_wa paw
                          JOIN user u ON paw.idnik = u.idnik
                          WHERE paw.is_active = 1";
            $admin_result = mysqli_query($koneksi, $admin_query);

            while ($admin = mysqli_fetch_assoc($admin_result)) {
                $message = "Halo {$admin['nama']},\n\n"
                    . "Ada existing request yang dibuka kembali:\n"
                    . "Request ID: {$existing_id_proc_ch}\n"
                    . "Requester: {$user['nama']}\n\n"
                    . "Silakan cek di: https://proc.maagroup.co.id/\n\n"
                    . "Note: Pesan ini dikirim secara otomatis.";

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
                        'target' => $admin['no_wa'],
                        'message' => $message,
                        'countryCode' => '62'
                    ),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization:' . $config['token']
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
            }
        }

        $user_stmt->close();
        header("location:../index.php?page=UserDetailPurchase&id=$existing_id_proc_ch");
        exit;
    } else {
        $tanggal_req = date('Y-m-d H:i:s');
        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $timestamp = $currentDateTime->format('ymdHis');
        $id_proc_ch = "CH" . $timestamp;
        $status = 'Created';

        // Insert new request dengan prepared statement
        $insert_query = "INSERT INTO proc_purchase_requests (id_proc_ch, created_request, nik_request, status) VALUES (?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($koneksi, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "ssss", $id_proc_ch, $tanggal_req, $nik_request, $status);
        $result = mysqli_stmt_execute($insert_stmt);

        if ($result) {
            // Get WhatsApp configuration for new request notification
            $config_query = "SELECT token FROM T_L_EIP_HRGA_HRGA_RF_WHATSAPP_CONFIG WHERE is_active = 1 ORDER BY id DESC LIMIT 1";
            $config_result = mysqli_query($koneksi, $config_query);
            $config = mysqli_fetch_assoc($config_result);

            if ($config) {
                // Get user details
                $user_query = "SELECT nama FROM user WHERE idnik = ?";
                $user_stmt = mysqli_prepare($koneksi, $user_query);
                mysqli_stmt_bind_param($user_stmt, "s", $nik_request);
                mysqli_stmt_execute($user_stmt);
                $user_result = mysqli_stmt_get_result($user_stmt);
                $user = mysqli_fetch_assoc($user_result);

                // Get admin numbers
                $admin_query = "SELECT paw.no_wa, u.nama 
                              FROM proc_admin_wa paw
                              JOIN user u ON paw.idnik = u.idnik
                              WHERE paw.is_active = 1";
                $admin_result = mysqli_query($koneksi, $admin_query);

                while ($admin = mysqli_fetch_assoc($admin_result)) {
                    $message = "Halo {$admin['nama']},\n\n"
                        . "Ada request pembelian baru:\n"
                        . "Request ID: {$id_proc_ch}\n"
                        . "Requester: {$user['nama']}\n"
                        . "Tanggal: {$tanggal_req}\n\n"
                        . "Silakan cek di: https://proc.maagroup.co.id/\n\n"
                        . "Note: Pesan ini dikirim secara otomatis.";

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
                            'target' => $admin['no_wa'],
                            'message' => $message,
                            'countryCode' => '62'
                        ),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization:' . $config['token']
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                }

                $user_stmt->close();
            }

            header("location:../index.php?page=UserDetailPurchase&id=$id_proc_ch");
        } else {
            $_SESSION['error'] = "Gagal membuat request: " . mysqli_error($koneksi);
            header("location:../index.php?page=UserDetailPurchase");
        }

        $insert_stmt->close();
    }

    if (isset($stmt)) mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
} else {
    die("Akses dilarang...");
}
