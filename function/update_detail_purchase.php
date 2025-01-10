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
