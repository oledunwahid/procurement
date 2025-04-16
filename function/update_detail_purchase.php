<?php
header('Content-Type: application/json');
include '../koneksi.php';

// Disable error display in output
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    // Validate inputs
    if (!isset($_POST['id']) || !isset($_POST['id_proc_ch'])) {
        throw new Exception('Missing required parameters');
    }

    // Sanitize inputs
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $id_proc_ch = mysqli_real_escape_string($koneksi, $_POST['id_proc_ch']);
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $detail_specification = mysqli_real_escape_string($koneksi, $_POST['detail_specification']);
    $qty = (int)$_POST['qty'];
    $category = mysqli_real_escape_string($koneksi, $_POST['category']);
    $uom = mysqli_real_escape_string($koneksi, $_POST['uom']);
    $unit_price = str_replace('.', '', $_POST['unit_price']); // Remove thousand separators
    $urgency_status = mysqli_real_escape_string($koneksi, $_POST['urgency_status']);
    $detail_notes = mysqli_real_escape_string($koneksi, $_POST['detail_notes']);
    $niklogin = isset($_POST['niklogin']) ? mysqli_real_escape_string($koneksi, $_POST['niklogin']) : '';

    // Check if user has role 51 or admin access
    $hasRole51 = isset($_POST['hasRole51']) && $_POST['hasRole51'] == '1';

    // Category access check for non-role 51 users
    if (!$hasRole51 && !empty($niklogin)) {
        // Check if user has access to this category
        $accessCheckQuery = "SELECT COUNT(*) AS access_count FROM proc_admin_category 
                            WHERE id_category = '$category' AND idnik = '$niklogin'";
        $accessResult = mysqli_query($koneksi, $accessCheckQuery);

        if (!$accessResult) {
            throw new Exception('Failed to check access: ' . mysqli_error($koneksi));
        }

        $accessRow = mysqli_fetch_assoc($accessResult);
        if ($accessRow['access_count'] == 0) {
            throw new Exception('You do not have permission to update items in this category');
        }
    }

    // Prepare and execute update query
    $query = "UPDATE proc_request_details SET 
              nama_barang = ?, 
              detail_specification = ?,
              qty = ?,
              category = ?,
              uom = ?,
              unit_price = ?,
              urgency_status = ?,
              detail_notes = ?
              WHERE id = ? AND id_proc_ch = ?";

    $stmt = mysqli_prepare($koneksi, $query);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param(
        $stmt,
        'ssisssssss',
        $nama_barang,
        $detail_specification,
        $qty,
        $category,
        $uom,
        $unit_price,
        $urgency_status,
        $detail_notes,
        $id,
        $id_proc_ch
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to execute update: ' . mysqli_stmt_error($stmt));
    }

    // Log the update action
    $logQuery = "INSERT INTO proc_admin_log (idnik, action_type, table_name, record_id, old_value, new_value) 
                VALUES (?, 'UPDATE', 'proc_request_details', ?, NULL, ?)";

    $logStmt = mysqli_prepare($koneksi, $logQuery);
    if ($logStmt) {
        $newValue = json_encode([
            'nama_barang' => $nama_barang,
            'detail_specification' => $detail_specification,
            'qty' => $qty,
            'category' => $category,
            'uom' => $uom,
            'unit_price' => $unit_price,
            'urgency_status' => $urgency_status,
            'detail_notes' => $detail_notes,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        mysqli_stmt_bind_param($logStmt, 'sis', $niklogin, $id, $newValue);
        mysqli_stmt_execute($logStmt);
        mysqli_stmt_close($logStmt);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Data updated successfully'
    ]);
} catch (Exception $e) {
    // Log the error server-side
    error_log('Error in update_detail_purchase.php: ' . $e->getMessage());

    // Return error response to client
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update data',
        'debug_log' => $e->getMessage()
    ]);
}

// Close connection
mysqli_close($koneksi);
