<?php
include '../koneksi.php';
session_start();
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'Terjadi kesalahan');
$debug_log = array();

function debug_log($message)
{
    global $debug_log;
    $debug_log[] = $message;
    error_log($message);
}

if (isset($_POST['id_proc_ch']) && isset($_POST['niklogin'])) {
    debug_log("Received POST data: " . print_r($_POST, true));

    $id_proc_ch = $_POST['id_proc_ch'];
    $created_request = $_POST['created_request'];
    $requester_name = $_POST['requester_name'];
    $title = $_POST['title'];
    $total_price = str_replace(['Rp ', '.'], '', $_POST['total_price']);
    $status = $_POST['status'];
    $niklogin = $_POST['niklogin'];

    if (empty($niklogin)) {
        $response['message'] = "NIK login tidak valid";
        debug_log("Invalid niklogin");
        echo json_encode($response);
        exit;
    }

    mysqli_begin_transaction($koneksi);

    try {
        // Get the old values
        $old_query = "SELECT created_request, title, total_price, lampiran, status FROM proc_purchase_requests WHERE id_proc_ch = ?";
        $old_stmt = mysqli_prepare($koneksi, $old_query);
        mysqli_stmt_bind_param($old_stmt, "s", $id_proc_ch);
        mysqli_stmt_execute($old_stmt);
        $old_result = mysqli_stmt_get_result($old_stmt);
        $old_row = mysqli_fetch_assoc($old_result);

        debug_log("Old values fetched successfully");

        // Handle file upload
        $sql_lampiran = "SELECT lampiran FROM proc_purchase_requests WHERE id_proc_ch = ?";
        $stmt = mysqli_prepare($koneksi, $sql_lampiran);
        mysqli_stmt_bind_param($stmt, "s", $id_proc_ch);
        mysqli_stmt_execute($stmt);
        $result_lampiran = mysqli_stmt_get_result($stmt);
        $row_lampiran = mysqli_fetch_assoc($result_lampiran);
        $existing_lampiran = $row_lampiran['lampiran'];

        if (!empty($_FILES['lampiran']['name'])) {
            $lampiran = $_FILES['lampiran']['name'];
            $file_tmp = $_FILES['lampiran']['tmp_name'];
            move_uploaded_file($file_tmp, "../file/procurement/" . $lampiran);
        } else {
            $lampiran = $existing_lampiran;
        }

        // Update the record
        $sql = "UPDATE proc_purchase_requests SET created_request = ?, title = ?, total_price = ?, lampiran = ?, status = ? WHERE id_proc_ch = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $created_request, $title, $total_price, $lampiran, $status, $id_proc_ch);
        $update_result = mysqli_stmt_execute($stmt);

        if ($update_result) {
            debug_log("Record updated successfully");

            // Log the change
            $old_value = json_encode([
                'created_request' => $old_row['created_request'],
                'title' => $old_row['title'],
                'total_price' => $old_row['total_price'],
                'lampiran' => $old_row['lampiran'],
                'status' => $old_row['status']
            ]);
            $new_value = json_encode([
                'created_request' => $created_request,
                'title' => $title,
                'total_price' => $total_price,
                'lampiran' => $lampiran,
                'status' => $status
            ]);

            $log_query = "INSERT INTO proc_admin_log (idnik, action_type, table_name, record_id, old_value, new_value) VALUES (?, 'UPDATE', 'proc_purchase_requests', ?, ?, ?)";
            $log_stmt = mysqli_prepare($koneksi, $log_query);
            mysqli_stmt_bind_param($log_stmt, "ssss", $niklogin, $id_proc_ch, $old_value, $new_value);
            $log_result = mysqli_stmt_execute($log_stmt);

            if ($log_result) {
                debug_log("Log entry inserted successfully");
            } else {
                debug_log("Failed to insert log entry: " . mysqli_error($koneksi));
            }

            $response['status'] = 'success';
            $response['message'] = "Data berhasil diupdate";

            // Handle WhatsApp notification for 'Closed' status
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

                    debug_log("WhatsApp notification sent");
                } else {
                    $response['message'] .= " Namun, data user tidak ditemukan untuk mengirim notifikasi.";
                    debug_log("User data not found for WhatsApp notification");
                }
            }
        } else {
            $response['message'] = "Error: " . mysqli_error($koneksi);
            debug_log("Failed to update record: " . mysqli_error($koneksi));
        }

        mysqli_commit($koneksi);
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $response['message'] = "Error: " . $e->getMessage();
        debug_log("Exception caught: " . $e->getMessage());
    } finally {
        if (isset($old_stmt)) mysqli_stmt_close($old_stmt);
        if (isset($stmt)) mysqli_stmt_close($stmt);
        if (isset($log_stmt)) mysqli_stmt_close($log_stmt);
        if (isset($stmtUser)) mysqli_stmt_close($stmtUser);
    }
} else {
    $response['message'] = "Data tidak lengkap";
    debug_log("Incomplete data received");
}

$response['debug_log'] = $debug_log;
echo json_encode($response);
mysqli_close($koneksi);
