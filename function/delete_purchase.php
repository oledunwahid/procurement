<?php
include '../koneksi.php';
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'ID tidak ditemukan');

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    // Default values if not provided
    $user_id = isset($_POST['user_id']) && !empty($_POST['user_id']) ? $_POST['user_id'] : '0';
    $user_name = isset($_POST['user_name']) && !empty($_POST['user_name']) ? $_POST['user_name'] : 'System';

    mysqli_begin_transaction($koneksi);

    try {
        // Get old data from main request before deletion
        $mainDataQuery = "SELECT * FROM proc_purchase_requests WHERE id_proc_ch = ?";
        $stmtMainOld = mysqli_prepare($koneksi, $mainDataQuery);
        if (!$stmtMainOld) {
            throw new Exception("Prepare statement failed: " . mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($stmtMainOld, "s", $id);
        if (!mysqli_stmt_execute($stmtMainOld)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmtMainOld));
        }

        $result = mysqli_stmt_get_result($stmtMainOld);
        $mainOldData = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmtMainOld);

        if (!$mainOldData) {
            throw new Exception("Data dengan ID tersebut tidak ditemukan.");
        }

        // Get old data from details before deletion
        $detailsDataQuery = "SELECT * FROM proc_request_details WHERE id_proc_ch = ?";
        $stmtDetailsOld = mysqli_prepare($koneksi, $detailsDataQuery);
        mysqli_stmt_bind_param($stmtDetailsOld, "s", $id);
        mysqli_stmt_execute($stmtDetailsOld);
        $detailsResult = mysqli_stmt_get_result($stmtDetailsOld);

        $detailsOldData = [];
        while ($row = mysqli_fetch_assoc($detailsResult)) {
            $detailsOldData[] = $row;
        }
        mysqli_stmt_close($stmtDetailsOld);

        // Log deletion of details
        if (!empty($detailsOldData)) {
            $oldDetailsValue = json_encode([
                'details' => $detailsOldData,
                'deleted_by' => $user_name,
                'deleted_at' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);

            $logDetailsQuery = "INSERT INTO proc_admin_log 
                            (idnik, action_type, table_name, record_id, old_value, new_value) 
                            VALUES (?, 'DELETE', 'proc_request_details', ?, ?, NULL)";
            $stmtLogDetails = mysqli_prepare($koneksi, $logDetailsQuery);
            mysqli_stmt_bind_param($stmtLogDetails, "sss", $user_id, $id, $oldDetailsValue);
            mysqli_stmt_execute($stmtLogDetails);
            mysqli_stmt_close($stmtLogDetails);
        }

        // Hapus detail purchase request
        $query_detail = "DELETE FROM proc_request_details WHERE id_proc_ch = ?";
        $stmt_detail = mysqli_prepare($koneksi, $query_detail);
        mysqli_stmt_bind_param($stmt_detail, "s", $id);
        mysqli_stmt_execute($stmt_detail);
        mysqli_stmt_close($stmt_detail);

        // Log deletion of main request
        $oldMainValue = json_encode([
            'id_proc_ch' => $mainOldData['id_proc_ch'],
            'created_request' => $mainOldData['created_request'],
            'title' => $mainOldData['title'],
            'nik_request' => $mainOldData['nik_request'],
            'proc_pic' => $mainOldData['proc_pic'],
            'total_price' => $mainOldData['total_price'],
            'lampiran' => $mainOldData['lampiran'],
            'status' => $mainOldData['status'],
            'deleted_by' => $user_name,
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        $logMainQuery = "INSERT INTO proc_admin_log 
                        (idnik, action_type, table_name, record_id, old_value, new_value) 
                        VALUES (?, 'DELETE', 'proc_purchase_requests', ?, ?, NULL)";
        $stmtLogMain = mysqli_prepare($koneksi, $logMainQuery);
        mysqli_stmt_bind_param($stmtLogMain, "sss", $user_id, $id, $oldMainValue);
        mysqli_stmt_execute($stmtLogMain);
        mysqli_stmt_close($stmtLogMain);

        // Hapus purchase request utama
        $query_main = "DELETE FROM proc_purchase_requests WHERE id_proc_ch = ?";
        $stmt_main = mysqli_prepare($koneksi, $query_main);
        mysqli_stmt_bind_param($stmt_main, "s", $id);
        mysqli_stmt_execute($stmt_main);
        mysqli_stmt_close($stmt_main);

        // Commit transaksi jika tidak ada error

        mysqli_commit($koneksi);
        $response['status'] = 'success';
        $response['message'] = 'Purchase request berhasil dihapus';
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
        error_log("Error in delete_purchase: " . $e->getMessage());
    }
} else {
    $response['message'] = "ID tidak diterima";
}

header('Content-Type: application/json');
echo json_encode($response);
mysqli_close($koneksi);
