<?php
ob_start();
error_reporting(0);
header('Content-Type: application/json');

function logError($message)
{
    file_put_contents("../logs/ajax_errors.log", "[" . date('Y-m-d H:i:s') . "] $message\n", FILE_APPEND);
}

try {
    require_once '../koneksi.php';
    require_once 'admin_log.php';

    if (empty($_POST['id_proc_ch'])) {
        throw new Exception('ID is required');
    }

    $koneksi->begin_transaction();

    // Get standard parameters
    $id_proc_ch = trim($_POST['id_proc_ch']);
    $nama_barang = trim($_POST['nama_barang'] ?? '');
    $qty = trim($_POST['qty'] ?? '');
    $unit_price = str_replace(['.', ','], '', $_POST['unit_price'] ?? '0');
    $uom = trim($_POST['uom'] ?? '');
    $detail_specification = trim($_POST['detail_specification'] ?? '');
    $detail_notes = trim($_POST['detail_notes'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $niklogin = trim($_POST['niklogin'] ?? '');

    // Check if user has role 51 access
    $hasRole51 = isset($_POST['hasRole51']) && $_POST['hasRole51'] == '1';

    if (empty($nama_barang) || empty($qty) || empty($uom) || empty($category)) {
        throw new Exception('All required fields must be filled');
    }

    // Check user's access to this category if they don't have role 51
    if (!$hasRole51 && !empty($niklogin)) {
        // Check if user has access to this category
        $accessCheck = $koneksi->prepare("SELECT COUNT(*) as count FROM proc_admin_category WHERE id_category = ? AND idnik = ?");
        $accessCheck->bind_param("ss", $category, $niklogin);
        $accessCheck->execute();
        $accessResult = $accessCheck->get_result()->fetch_assoc();

        if ($accessResult['count'] == 0) {
            throw new Exception('You do not have permission to add items to this category');
        }
        $accessCheck->close();
    }

    $stmt = $koneksi->prepare("INSERT INTO proc_request_details (id_proc_ch, nama_barang, qty, uom, detail_specification, unit_price, detail_notes, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $koneksi->error);
    }

    $stmt->bind_param("sssssdss", $id_proc_ch, $nama_barang, $qty, $uom, $detail_specification, $unit_price, $detail_notes, $category);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    admin_log('INSERT', 'proc_request_details', $id_proc_ch, null, [
        'nama_barang' => $nama_barang,
        'qty' => $qty,
        'uom' => $uom,
        'detail_specification' => $detail_specification,
        'unit_price' => $unit_price,
        'detail_notes' => $detail_notes,
        'category' => $category
    ]);

    // Send WhatsApp notification
    try {
        $config = $koneksi->query("SELECT token FROM T_L_EIP_HRGA_HRGA_RF_WHATSAPP_CONFIG WHERE is_active = 1 ORDER BY id DESC LIMIT 1")->fetch_assoc();

        if ($config) {
            $picStmt = $koneksi->prepare("SELECT DISTINCT paw.no_wa, u.nama FROM proc_admin_wa paw JOIN proc_admin_category pac ON paw.idnik = pac.idnik JOIN user u ON paw.idnik = u.idnik WHERE pac.id_category = ? AND paw.is_active = 1");
            $picStmt->bind_param("s", $category);
            $picStmt->execute();
            $picResult = $picStmt->get_result();

            $reqStmt = $koneksi->prepare("SELECT pr.*, u.nama as requester_name FROM proc_purchase_requests pr JOIN user u ON pr.nik_request = u.idnik WHERE pr.id_proc_ch = ?");
            $reqStmt->bind_param("s", $id_proc_ch);
            $reqStmt->execute();
            $request = $reqStmt->get_result()->fetch_assoc();

            while ($pic = $picResult->fetch_assoc()) {
                $message = "Halo {$pic['nama']},\n\nAda item baru yang memerlukan perhatian Anda:\nRequest ID: {$request['id_proc_ch']}\nRequester: {$request['requester_name']}\n\nDetail Item:\nNama: {$nama_barang}\nSpesifikasi: {$detail_specification}\nQty: {$qty} {$uom}\n\nSilakan cek di: https://proc.maagroup.co.id/\n\nNote: Pesan ini dikirim secara otomatis.";

                $postData = json_encode([
                    "api_key" => "IBFKVTNWOPSKMXP7",
                    "number_key" => "iJWj1tmCt6WueRAH",
                    "phone_no" => '6281574434332',
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
            $picStmt->close();
            $reqStmt->close();
        }
    } catch (Exception $wa_error) {
        logError("WhatsApp Error: " . $wa_error->getMessage());
    }

    $koneksi->commit();
    while (ob_get_level()) ob_end_clean();
    echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
} catch (Exception $e) {
    if ($koneksi && $koneksi->ping()) {
        $koneksi->rollback();
    }
    logError("Insert Error: " . $e->getMessage());
    while (ob_get_level()) ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    if ($koneksi && $koneksi->ping()) $koneksi->close();
}
