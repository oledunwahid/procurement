<?php
include '../koneksi.php';
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'ID tidak ditemukan');

function sendWhatsAppNotifications($koneksi, $id_proc_ch, $item_name, $urgency_status, $old_data, $new_data)
{
    // Get WhatsApp configuration
    $config_query = "SELECT token FROM T_L_EIP_HRGA_HRGA_RF_WHATSAPP_CONFIG WHERE is_active = 1 ORDER BY id DESC LIMIT 1";
    $config_result = mysqli_query($koneksi, $config_query);
    $config = mysqli_fetch_assoc($config_result);

    if ($config) {
        // Get request details
        $request_query = "SELECT pr.title, u.nama as requester_name 
                         FROM proc_purchase_requests pr 
                         JOIN user u ON pr.nik_request = u.idnik 
                         WHERE pr.id_proc_ch = ?";
        $stmt_req = mysqli_prepare($koneksi, $request_query);
        mysqli_stmt_bind_param($stmt_req, "s", $id_proc_ch);
        mysqli_stmt_execute($stmt_req);
        $request_result = mysqli_stmt_get_result($stmt_req);
        $request_data = mysqli_fetch_assoc($request_result);
        mysqli_stmt_close($stmt_req);

        // Get admin numbers
        $admin_query = "SELECT paw.no_wa, u.nama 
                       FROM proc_admin_wa paw
                       JOIN user u ON paw.idnik = u.idnik
                       WHERE paw.is_active = 1";
        $admin_result = mysqli_query($koneksi, $admin_query);

        // Format changes for message
        $changes = array();
        foreach ($new_data as $key => $value) {
            if ($old_data[$key] != $value) {
                $changes[] = ucfirst($key) . ": " . $old_data[$key] . " → " . $value;
            }
        }

        while ($admin = mysqli_fetch_assoc($admin_result)) {
            $message = "Halo {$admin['nama']},\n\n"
                . "Ada update pada item request pembelian:\n"
                . "Request ID: {$id_proc_ch}\n"
                . "Request Title: {$request_data['title']}\n"
                . "Requester: {$request_data['requester_name']}\n"
                . "Item Name: {$item_name}\n"
                . "Urgency Status: {$urgency_status}\n\n"
                . "Perubahan:\n" . implode("\n", $changes) . "\n\n"
                . "Update time: " . date('Y-m-d H:i:s') . "\n\n"
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
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $category = $_POST['category'];
    $uom = $_POST['uom'];
    $qty = $_POST['qty'];
    $detail_specification = $_POST['detail_specification'];
    $urgency_status = $_POST['urgency_status'];

    // Start transaction
    mysqli_begin_transaction($koneksi);

    try {
        // Get old values for logging
        $oldDataQuery = "SELECT * FROM proc_request_details WHERE id = ?";
        $stmtOld = mysqli_prepare($koneksi, $oldDataQuery);
        mysqli_stmt_bind_param($stmtOld, "i", $id);
        mysqli_stmt_execute($stmtOld);
        $oldResult = mysqli_stmt_get_result($stmtOld);
        $oldData = mysqli_fetch_assoc($oldResult);
        mysqli_stmt_close($stmtOld);

        // Prepare old value for logging
        $oldValue = json_encode([
            'nama_barang' => $oldData['nama_barang'],
            'qty' => $oldData['qty'],
            'uom' => $oldData['uom'],
            'category' => $oldData['category'],
            'detail_specification' => $oldData['detail_specification'],
            'urgency_status' => $oldData['urgency_status']
        ]);

        // Suggestions handling
        $file = fopen('suggestion.txt', 'r');
        $suggestionExists = false;
        while (($line = fgets($file)) !== false) {
            if (trim($line) === $nama_barang) {
                $suggestionExists = true;
                break;
            }
        }
        fclose($file);

        if (!$suggestionExists) {
            $file = fopen('suggestion.txt', 'a');
            fwrite($file, $nama_barang . "\n");
            fclose($file);
        }

        // Update proc_request_details
        $queryDetails = "UPDATE proc_request_details SET 
                        id_proc_ch = ?, 
                        nama_barang = ?, 
                        qty = ?, 
                        uom = ?, 
                        detail_specification = ?, 
                        category = ?,
                        urgency_status = ? 
                        WHERE id = ?";
        $stmtDetails = mysqli_prepare($koneksi, $queryDetails);
        mysqli_stmt_bind_param(
            $stmtDetails,
            "sssssssi",
            $id_proc_ch,
            $nama_barang,
            $qty,
            $uom,
            $detail_specification,
            $category,
            $urgency_status,
            $id
        );

        // Execute the update query
        if (mysqli_stmt_execute($stmtDetails)) {
            // Prepare new value for logging
            $newValue = json_encode([
                'nama_barang' => $nama_barang,
                'qty' => $qty,
                'uom' => $uom,
                'category' => $category,
                'detail_specification' => $detail_specification,
                'urgency_status' => $urgency_status
            ]);

            // Insert log entry
            $logQuery = "INSERT INTO proc_admin_log (idnik, action_type, table_name, record_id, old_value, new_value) 
                        VALUES (?, 'UPDATE', 'proc_request_details', ?, ?, ?)";
            $stmtLog = mysqli_prepare($koneksi, $logQuery);
            $idnik = $_SESSION['idnik'] ?? '0'; // Assume session contains user ID
            mysqli_stmt_bind_param(
                $stmtLog,
                "ssss",
                $idnik,
                $id,
                $oldValue,
                $newValue
            );
            mysqli_stmt_execute($stmtLog);
            mysqli_stmt_close($stmtLog);

            // Send WhatsApp notifications with old and new data comparison
            $oldDataArray = json_decode($oldValue, true);
            $newDataArray = json_decode($newValue, true);
            sendWhatsAppNotifications(
                $koneksi,
                $id_proc_ch,
                $nama_barang,
                $urgency_status,
                $oldDataArray,
                $newDataArray
            );

            $response['status'] = 'success';
            $response['message'] = 'Data berhasil diupdate';

            // Commit transaction
            mysqli_commit($koneksi);
        } else {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_close($stmtDetails);
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $response['message'] = "Error: " . $e->getMessage();
    }
}

echo json_encode($response);
mysqli_close($koneksi);
