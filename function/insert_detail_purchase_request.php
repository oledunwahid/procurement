<?php
include '../koneksi.php';
header('Content-Type: application/json');

if (isset($_POST['id_proc_ch'])) {
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $qty = $_POST['qty'];
    $unit_price = str_replace('.', '', $_POST['unit_price']);
    $uom = $_POST['uom'];
    $detail_specification = $_POST['detail_specification'];
    $detail_notes = $_POST['detail_notes'];
    $category = $_POST['category'];

    // Insert data
    $stmt = $koneksi->prepare("INSERT INTO proc_request_details (id_proc_ch, nama_barang, qty, uom, detail_specification, unit_price, detail_notes, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssdss", $id_proc_ch, $nama_barang, $qty, $uom, $detail_specification, $unit_price, $detail_notes, $category);

    if ($stmt->execute()) {
        // Get WhatsApp Configuration
        $configStmt = $koneksi->prepare("SELECT token FROM T_L_EIP_HRGA_HRGA_RF_WHATSAPP_CONFIG WHERE is_active = 1 ORDER BY id DESC LIMIT 1");
        $configStmt->execute();
        $configResult = $configStmt->get_result();
        $config = $configResult->fetch_assoc();

        if ($config) {
            // Get PIC WhatsApp numbers
            $picStmt = $koneksi->prepare("SELECT paw.no_wa, u.nama 
                FROM proc_admin_wa paw
                JOIN proc_admin_category pac ON paw.idnik = pac.idnik
                JOIN user u ON paw.idnik = u.idnik
                WHERE pac.id_category = ? AND paw.is_active = 1");
            $picStmt->bind_param("s", $category);
            $picStmt->execute();
            $picResult = $picStmt->get_result();

            // Get request details
            $reqStmt = $koneksi->prepare("SELECT pr.*, u.nama as requester_name 
                FROM proc_purchase_requests pr
                JOIN user u ON pr.nik_request = u.idnik
                WHERE pr.id_proc_ch = ?");
            $reqStmt->bind_param("s", $id_proc_ch);
            $reqStmt->execute();
            $request = $reqStmt->get_result()->fetch_assoc();

            while ($pic = $picResult->fetch_assoc()) {
                $message = "Halo {$pic['nama']},\n\n"
                    . "Ada item baru yang memerlukan perhatian Anda:\n"
                    . "Request ID: {$request['id_proc_ch']}\n"
                    . "Requester: {$request['requester_name']}\n\n"
                    . "Detail Item:\n"
                    . "Nama: {$nama_barang}\n"
                    . "Spesifikasi: {$detail_specification}\n"
                    . "Qty: {$qty} {$uom}\n\n"
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
                        'target' => $pic['no_wa'],
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

            $configStmt->close();
            $picStmt->close();
            $reqStmt->close();
        }

        echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }

    $stmt->close();
    $koneksi->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
}
