<?php
include '../koneksi.php';
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'ID tidak ditemukan');

if (isset($_POST['id'])) {
    mysqli_begin_transaction($koneksi);

    try {
        $id = $_POST['id'];
        $user_id = $_POST['user_id'] ?? '0';
        $user_name = $_POST['user_name'] ?? 'System';

        // Get old data before deletion for logging
        $oldDataQuery = "SELECT * FROM proc_request_details WHERE id = ?";
        $stmtOld = mysqli_prepare($koneksi, $oldDataQuery);
        mysqli_stmt_bind_param($stmtOld, "i", $id);
        mysqli_stmt_execute($stmtOld);
        $oldResult = mysqli_stmt_get_result($stmtOld);
        $oldData = mysqli_fetch_assoc($oldResult);
        mysqli_stmt_close($stmtOld);

        // Prepare old value for logging
        $oldValue = json_encode([
            'id_proc_ch' => $oldData['id_proc_ch'],
            'nama_barang' => $oldData['nama_barang'],
            'qty' => $oldData['qty'],
            'uom' => $oldData['uom'],
            'detail_specification' => $oldData['detail_specification'],
            'category' => $oldData['category'],
            'urgency_status' => $oldData['urgency_status'],
            'deleted_by' => $user_name,
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        // Delete the record
        $query = "DELETE FROM proc_request_details WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            // Log the deletion
            $logQuery = "INSERT INTO proc_admin_log 
                        (idnik, action_type, table_name, record_id, old_value, new_value) 
                        VALUES (?, 'DELETE', 'proc_request_details', ?, ?, NULL)";

            $stmtLog = mysqli_prepare($koneksi, $logQuery);
            mysqli_stmt_bind_param($stmtLog, "sis", $user_id, $id, $oldValue);

            if (!mysqli_stmt_execute($stmtLog)) {
                throw new Exception("Failed to log deletion: " . mysqli_stmt_error($stmtLog));
            }

            mysqli_stmt_close($stmtLog);
            mysqli_commit($koneksi);

            $response['status'] = 'success';
            $response['message'] = 'Data berhasil dihapus';
        } else {
            throw new Exception("Error deleting record: " . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $response['message'] = $e->getMessage();
        error_log("Error in delete_view_detail_purchase: " . $e->getMessage());
    }
}

echo json_encode($response);
mysqli_close($koneksi);
