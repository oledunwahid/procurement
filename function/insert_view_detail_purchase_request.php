<?php
include '../koneksi.php';
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'No data received');

if (isset($_POST['id_proc_ch'])) {
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $qty = $_POST['qty'];
    $uom = $_POST['uom'];
    $detail_specification = $_POST['detail_specification'];
    $category = $_POST['category'];
    $urgency_status = $_POST['urgency_status'];

    // Validasi urgency_status
    if (!in_array($urgency_status, ['normal', 'urgent'])) {
        $response['message'] = "Invalid urgency status";
        echo json_encode($response);
        exit;
    }

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

    // Menyiapkan prepared statement dengan urgency_status
    $stmt = $koneksi->prepare("INSERT INTO proc_request_details (id_proc_ch, nama_barang, qty, uom, detail_specification, category, urgency_status) VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Mengikat parameter ke statement
    $stmt->bind_param("sssssss", $id_proc_ch, $nama_barang, $qty, $uom, $detail_specification, $category, $urgency_status);

    // Send WhatsApp notification
    try {
        $config = $koneksi->query("SELECT token FROM T_L_EIP_HRGA_HRGA_RF_WHATSAPP_CONFIG WHERE is_active = 1 ORDER BY id DESC LIMIT 1")->fetch_assoc();

        if ($config) {
            // Modifikasi query untuk mendapatkan PIC sesuai kategori dan lokasi HO
            $picStmt = $koneksi->prepare("
                SELECT DISTINCT 
                    paw.no_wa, 
                    u.nama,
                    u.lokasi
                FROM proc_admin_wa paw 
                JOIN proc_admin_category pac ON paw.idnik = pac.idnik 
                JOIN user u ON paw.idnik = u.idnik 
                WHERE pac.id_category = ? 
                AND paw.is_active = 1 
                AND u.lokasi = 'HO'
            ");
            $picStmt->bind_param("s", $category);
            $picStmt->execute();
            $picResult = $picStmt->get_result();

            // Get requester details with location
            $reqStmt = $koneksi->prepare("
                SELECT 
                    pr.*,
                    u.nama as requester_name,
                    u.lokasi as requester_location
                FROM proc_purchase_requests pr 
                JOIN user u ON pr.nik_request = u.idnik 
                WHERE pr.id_proc_ch = ?
                AND u.lokasi = 'HO'
            ");
            $reqStmt->bind_param("s", $id_proc_ch);
            $reqStmt->execute();
            $request = $reqStmt->get_result()->fetch_assoc();

            if ($request) {
                while ($pic = $picResult->fetch_assoc()) {
                    $urgencyLabel = strtoupper($urgency_status);
                    $message = "Halo {$pic['nama']},\n\n" .
                        "Ada item baru yang memerlukan perhatian Anda:\n" .
                        "Request ID: {$request['id_proc_ch']}\n" .
                        "Requester: {$request['requester_name']}\n" .
                        "Lokasi: {$request['requester_location']}\n" .
                        "Status: {$urgencyLabel}\n\n" .
                        "Detail Item:\n" .
                        "Nama: {$nama_barang}\n" .
                        "Spesifikasi: {$detail_specification}\n" .
                        "Qty: {$qty} {$uom}\n\n" .
                        "Silakan cek di: https://proc.maagroup.co.id/\n\n" .
                        "Note: Pesan ini dikirim secara otomatis.";

                    $postData = json_encode([
                        "api_key" => "IBFKVTNWOPSKMXP7",
                        "number_key" => "iJWj1tmCt6WueRAH",
                        "phone_no" => $pic['no_wa'],
                        "message" => $message,
                        "wait_until_send" => "1"
                    ]);

                    $curl = curl_init();
                    curl_setopt_array($curl, [
                        CURLOPT_URL => 'https://api.watzap.id/v1/send_message',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $postData,
                        CURLOPT_HTTPHEADER => [
                            'Authorization: ' . $config['token'],
                            'Content-Type: application/x-www-form-urlencoded'
                        ]
                    ]);

                    curl_exec($curl);
                    curl_close($curl);
                }
            }

            $picStmt->close();
            $reqStmt->close();
        }
    } catch (Exception $wa_error) {
        logError("WhatsApp Error: " . $wa_error->getMessage());
    }

    // Menjalankan statement
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Data berhasil disimpan';
    } else {
        $response['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $koneksi->close();
}

echo json_encode($response);
