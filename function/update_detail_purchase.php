<?php
include '../koneksi.php';
session_start();
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'Terjadi kesalahan');
$debug_log = array();

function debug_log($message)
{
    global $debug_log;
    $debug_log[] = $message;
    error_log($message);
}

if (isset($_POST['id']) && isset($_POST['niklogin'])) {
    debug_log("Received POST data: " . print_r($_POST, true));

    $id = $_POST['id'];
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $uom = $_POST['uom'];
    $qty = $_POST['qty'];
    $unit_price = $_POST['unit_price'];
    $detail_specification = $_POST['detail_specification'];
    $detail_notes = $_POST['detail_notes'];
    $category = $_POST['category'];
    $urgency_status = $_POST['urgency_status'] ?? 'normal'; // Add default value
    $niklogin = $_POST['niklogin'];

    debug_log("Logged in user NIK: " . $niklogin);

    mysqli_begin_transaction($koneksi);

    try {
        // Get the old values
        $old_query = "SELECT nama_barang, qty, uom, category, detail_specification, unit_price, detail_notes, urgency_status 
                      FROM proc_request_details WHERE id = ?";
        $old_stmt = mysqli_prepare($koneksi, $old_query);
        if (!$old_stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($old_stmt, "i", $id);
        if (!mysqli_stmt_execute($old_stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($old_stmt));
        }
        $old_result = mysqli_stmt_get_result($old_stmt);
        $old_row = mysqli_fetch_assoc($old_result);

        debug_log("Old values fetched successfully");

        // Update the record
        $update_query = "UPDATE proc_request_details 
                        SET id_proc_ch = ?, nama_barang = ?, qty = ?, uom = ?, 
                            category = ?, detail_specification = ?, unit_price = ?, 
                            detail_notes = ?, urgency_status = ? 
                        WHERE id = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_query);
        if (!$update_stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param(
            $update_stmt,
            "sssssssssi",
            $id_proc_ch,
            $nama_barang,
            $qty,
            $uom,
            $category,
            $detail_specification,
            $unit_price,
            $detail_notes,
            $urgency_status,
            $id
        );
        if (!mysqli_stmt_execute($update_stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($update_stmt));
        }

        debug_log("Record updated successfully");

        // Log the change
        $old_value = json_encode([
            'nama_barang' => $old_row['nama_barang'],
            'qty' => $old_row['qty'],
            'category' => $old_row['category'],
            'uom' => $old_row['uom'],
            'detail_specification' => $old_row['detail_specification'],
            'unit_price' => $old_row['unit_price'],
            'detail_notes' => $old_row['detail_notes'],
            'urgency_status' => $old_row['urgency_status']
        ]);

        $new_value = json_encode([
            'nama_barang' => $nama_barang,
            'qty' => $qty,
            'category' => $category,
            'uom' => $uom,
            'detail_specification' => $detail_specification,
            'unit_price' => $unit_price,
            'detail_notes' => $detail_notes,
            'urgency_status' => $urgency_status
        ]);

        $log_query = "INSERT INTO proc_admin_log (idnik, action_type, table_name, record_id, old_value, new_value) 
                      VALUES (?, 'UPDATE', 'proc_request_details', ?, ?, ?)";
        $log_stmt = mysqli_prepare($koneksi, $log_query);
        if (!$log_stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($log_stmt, "ssss", $niklogin, $id, $old_value, $new_value);
        if (!mysqli_stmt_execute($log_stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($log_stmt));
        }

        debug_log("Log entry inserted successfully");

        mysqli_commit($koneksi);

        $response['status'] = 'success';
        $response['message'] = 'Data berhasil diupdate';
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $response['message'] = "Error: " . $e->getMessage();
        debug_log("Exception caught: " . $e->getMessage());
    } finally {
        if (isset($old_stmt)) mysqli_stmt_close($old_stmt);
        if (isset($update_stmt)) mysqli_stmt_close($update_stmt);
        if (isset($log_stmt)) mysqli_stmt_close($log_stmt);
    }
} else {
    $response['message'] = "Data tidak lengkap";
    debug_log("Incomplete data received");
}

$response['debug_log'] = $debug_log;
echo json_encode($response);
mysqli_close($koneksi);
