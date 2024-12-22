<?php
include '../koneksi.php';
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'No data received');

if (isset($_POST['id_proc_ch'])) {
    mysqli_begin_transaction($koneksi);

    try {
        // Get form data
        $id_proc_ch = mysqli_real_escape_string($koneksi, $_POST['id_proc_ch']);
        $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
        $qty = mysqli_real_escape_string($koneksi, $_POST['qty']);
        $uom = mysqli_real_escape_string($koneksi, $_POST['uom']);
        $detail_specification = mysqli_real_escape_string($koneksi, $_POST['detail_specification']);
        $category = mysqli_real_escape_string($koneksi, $_POST['category']);
        $urgency_status = mysqli_real_escape_string($koneksi, $_POST['urgency_status']);
        $user_id = mysqli_real_escape_string($koneksi, $_POST['user_id'] ?? '0');
        $user_name = mysqli_real_escape_string($koneksi, $_POST['user_name'] ?? 'System');

        // Validate urgency_status
        if (!in_array($urgency_status, ['normal', 'urgent'])) {
            throw new Exception("Invalid urgency status");
        }

        // Handle suggestions
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

        // Insert into proc_request_details
        $stmt = $koneksi->prepare("INSERT INTO proc_request_details 
            (id_proc_ch, nama_barang, qty, uom, detail_specification, category, urgency_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssssss",
            $id_proc_ch,
            $nama_barang,
            $qty,
            $uom,
            $detail_specification,
            $category,
            $urgency_status
        );

        if (!$stmt->execute()) {
            throw new Exception("Error inserting data: " . $stmt->error);
        }

        $insertId = $stmt->insert_id;
        $stmt->close();

        // Log the insertion
        $newValue = json_encode([
            'id_proc_ch' => $id_proc_ch,
            'nama_barang' => $nama_barang,
            'qty' => $qty,
            'uom' => $uom,
            'detail_specification' => $detail_specification,
            'category' => $category,
            'urgency_status' => $urgency_status,
            'created_by' => $user_name,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $logQuery = "INSERT INTO proc_admin_log 
            (idnik, action_type, table_name, record_id, old_value, new_value) 
            VALUES (?, 'INSERT', 'proc_request_details', ?, NULL, ?)";

        $stmtLog = $koneksi->prepare($logQuery);
        $stmtLog->bind_param("sis", $user_id, $insertId, $newValue);

        if (!$stmtLog->execute()) {
            throw new Exception("Failed to log the action: " . $stmtLog->error);
        }
        $stmtLog->close();

        // Get category name for notification
        $catStmt = $koneksi->prepare("SELECT nama_category FROM proc_category WHERE id_category = ?");
        $catStmt->bind_param("s", $category);
        $catStmt->execute();
        $categoryName = $catStmt->get_result()->fetch_assoc()['nama_category'];
        $catStmt->close();

        // Send WhatsApp notifications
        $config = $koneksi->query("SELECT token FROM T_L_EIP_HRGA_HRGA_RF_WHATSAPP_CONFIG WHERE is_active = 1 ORDER BY id DESC LIMIT 1")->fetch_assoc();

        if ($config) {
            // Get PIC information
            $picStmt = $koneksi->prepare("
                SELECT DISTINCT 
                    paw.no_wa, 
                    u.nama,
                    u.lokasi,
                    pc.nama_category as category_name
                FROM proc_admin_wa paw 
                JOIN proc_admin_category pac ON paw.idnik = pac.idnik 
                JOIN user u ON paw.idnik = u.idnik 
                JOIN proc_category pc ON pac.id_category = pc.id_category
                WHERE pac.id_category = ? 
                AND paw.is_active = 1 
                AND u.lokasi = 'HO'
            ");
            $picStmt->bind_param("s", $category);
            $picStmt->execute();
            $picResult = $picStmt->get_result();

            // Get request details
            $reqStmt = $koneksi->prepare("
                SELECT 
                    pr.*,
                    u.nama as requester_name,
                    u.lokasi as requester_location,
                    u.department
                FROM proc_purchase_requests pr 
                JOIN user u ON pr.nik_request = u.idnik 
                WHERE pr.id_proc_ch = ?
            ");
            $reqStmt->bind_param("s", $id_proc_ch);
            $reqStmt->execute();
            $request = $reqStmt->get_result()->fetch_assoc();

            if ($request) {
                while ($pic = $picResult->fetch_assoc()) {
                    $urgencyLabel = strtoupper($urgency_status);
                    $message = "🔔 *Notifikasi Purchase Request*\n\n" .
                        "*Detail Request:*\n" .
                        "Request ID: {$request['id_proc_ch']}\n" .
                        "Requester: {$request['requester_name']}\n" .
                        "Departemen: {$request['departemen']}\n" .
                        "Lokasi: {$request['requester_location']}\n" .
                        "Status: {$urgencyLabel} ‼️\n\n" .
                        "*Detail Item:*\n" .
                        "Nama: {$nama_barang}\n" .
                        "Kategori: {$categoryName}\n" .
                        "Spesifikasi: {$detail_specification}\n" .
                        "Qty: {$qty} {$uom}\n\n" .
                        "🔍 Silakan cek di: https://proc.maagroup.co.id/\n\n" .
                        "_Note: Pesan ini dikirim secara otomatis._";

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
                            'target' => $pic['no_wa'],
                            'message' => $message,
                            'countryCode' => '62'
                        ),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: ' . $config['token']
                        ),
                    ));

                    $waResponse = curl_exec($curl);
                    curl_close($curl);
                }
            }

            $picStmt->close();
            $reqStmt->close();
        }

        mysqli_commit($koneksi);
        $response['status'] = 'success';
        $response['message'] = 'Data berhasil disimpan';
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $response['message'] = $e->getMessage();
        error_log("Error in insert_view_detail_purchase_request: " . $e->getMessage());
    }
}

echo json_encode($response);
mysqli_close($koneksi);
