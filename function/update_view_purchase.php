<?php
include '../koneksi.php';
header('Content-Type: application/json');

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize response
$response = array('status' => 'error', 'message' => 'ID tidak ditemukan');

// Debug: Log incoming data
error_log("POST Data: " . print_r($_POST, true));
error_log("FILES Data: " . print_r($_FILES, true));

if (isset($_POST['id_proc_ch'])) {
    mysqli_begin_transaction($koneksi);

    try {
        // Validate required fields
        $required_fields = ['id_proc_ch', 'title', 'requester_name'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Field {$field} is required");
            }
        }

        // Get and sanitize form data
        $id_proc_ch = mysqli_real_escape_string($koneksi, $_POST['id_proc_ch']);
        $title = mysqli_real_escape_string($koneksi, $_POST['title']);
        $nik_request = mysqli_real_escape_string($koneksi, $_POST['requester_name']);
        $total_price = !empty($_POST['total_price']) ? str_replace(['.', 'Rp', ' '], '', $_POST['total_price']) : 0;
        $proc_pic = isset($_POST['proc_pic']) ? mysqli_real_escape_string($koneksi, $_POST['proc_pic']) : '';
        $status = 'Open';

        // Verify record exists
        $checkQuery = "SELECT COUNT(*) as count FROM proc_purchase_requests WHERE id_proc_ch = ?";
        $stmtCheck = mysqli_prepare($koneksi, $checkQuery);
        mysqli_stmt_bind_param($stmtCheck, "s", $id_proc_ch);
        mysqli_stmt_execute($stmtCheck);
        $checkResult = mysqli_stmt_get_result($stmtCheck);
        $recordExists = mysqli_fetch_assoc($checkResult)['count'] > 0;
        mysqli_stmt_close($stmtCheck);

        if (!$recordExists) {
            throw new Exception("Record with ID {$id_proc_ch} not found");
        }

        // Get old data
        $oldDataQuery = "SELECT * FROM proc_purchase_requests WHERE id_proc_ch = ?";
        $stmtOld = mysqli_prepare($koneksi, $oldDataQuery);
        mysqli_stmt_bind_param($stmtOld, "s", $id_proc_ch);
        mysqli_stmt_execute($stmtOld);
        $oldResult = mysqli_stmt_get_result($stmtOld);
        $oldData = mysqli_fetch_assoc($oldResult);
        mysqli_stmt_close($stmtOld);

        // Handle file upload
        $lampiran = $oldData['lampiran'];
        if (!empty($_FILES['lampiran']['name'])) {
            $lampiran = $_FILES['lampiran']['name'];
            $file_tmp = $_FILES['lampiran']['tmp_name'];
            $upload_path = "../file/procurement/";

            // Ensure directory exists and is writable
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            if (!move_uploaded_file($file_tmp, $upload_path . $lampiran)) {
                throw new Exception("Failed to upload file: " . error_get_last()['message']);
            }
        }

        // Update purchase request
        $updateQuery = "UPDATE proc_purchase_requests SET 
            title = ?, 
            nik_request = ?, 
            proc_pic = ?,
            total_price = ?, 
            lampiran = ?, 
            status = ?
            WHERE id_proc_ch = ?";

        $stmtUpdate = mysqli_prepare($koneksi, $updateQuery);
        if (!$stmtUpdate) {
            throw new Exception("Prepare statement failed: " . mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param(
            $stmtUpdate,
            "sssssss",
            $title,
            $nik_request,
            $proc_pic,
            $total_price,
            $lampiran,
            $status,
            $id_proc_ch
        );

        if (!mysqli_stmt_execute($stmtUpdate)) {
            throw new Exception("Update failed: " . mysqli_stmt_error($stmtUpdate));
        }

        // Log the changes
        $oldValue = json_encode([
            'created_request' => $oldData['created_request'],
            'title' => $oldData['title'],
            'total_price' => $oldData['total_price'],
            'lampiran' => $oldData['lampiran'],
            'status' => $oldData['status']
        ]);

        $newValue = json_encode([
            'created_request' => date('Y-m-d H:i:s'),
            'title' => $title,
            'total_price' => $total_price,
            'lampiran' => $lampiran,
            'status' => $status
        ]);

        $logQuery = "INSERT INTO proc_admin_log 
                    (idnik, action_type, table_name, record_id, old_value, new_value) 
                    VALUES (?, 'UPDATE', 'proc_purchase_requests', ?, ?, ?)";
        $stmtLog = mysqli_prepare($koneksi, $logQuery);
        $idnik = isset($_SESSION['idnik']) ? $_SESSION['idnik'] : '';

        mysqli_stmt_bind_param(
            $stmtLog,
            "ssss",
            $idnik,
            $id_proc_ch,
            $oldValue,
            $newValue
        );

        if (!mysqli_stmt_execute($stmtLog)) {
            throw new Exception("Failed to log changes: " . mysqli_stmt_error($stmtLog));
        }

        mysqli_stmt_close($stmtLog);
        mysqli_stmt_close($stmtUpdate);

        mysqli_commit($koneksi);

        $response['status'] = 'success';
        $response['message'] = 'Data berhasil diupdate';
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        error_log("Update Error: " . $e->getMessage());
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
mysqli_close($koneksi);
