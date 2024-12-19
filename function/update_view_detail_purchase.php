<?php
include '../koneksi.php';
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'ID tidak ditemukan');

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $id_proc_ch = $_POST['id_proc_ch'];
    $nama_barang = $_POST['nama_barang'];
    $category = $_POST['category'];
    $uom = $_POST['uom'];
    $qty = $_POST['qty'];
    $detail_specification = $_POST['detail_specification'];
    $urgency_status = $_POST['urgency_status'];

    // Start transaction
    mysqli_begin_transaction($koneksi);

    try {
        // Get old values for logging
        $oldDataQuery = "SELECT * FROM proc_request_details WHERE id = ?";
        $stmtOld = mysqli_prepare($koneksi, $oldDataQuery);
        mysqli_stmt_bind_param($stmtOld, "i", $id);
        mysqli_stmt_execute($stmtOld);
        $oldResult = mysqli_stmt_get_result($stmtOld);
        $oldData = mysqli_fetch_assoc($oldResult);
        mysqli_stmt_close($stmtOld);

        // Prepare old value for logging
        $oldValue = json_encode([
            'nama_barang' => $oldData['nama_barang'],
            'qty' => $oldData['qty'],
            'uom' => $oldData['uom'],
            'category' => $oldData['category'],
            'detail_specification' => $oldData['detail_specification'],
            'urgency_status' => $oldData['urgency_status']
        ]);

        // Suggestions handling
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

        // Update proc_request_details
        $queryDetails = "UPDATE proc_request_details SET 
                        id_proc_ch = ?, 
                        nama_barang = ?, 
                        qty = ?, 
                        uom = ?, 
                        detail_specification = ?, 
                        category = ?,
                        urgency_status = ? 
                        WHERE id = ?";
        $stmtDetails = mysqli_prepare($koneksi, $queryDetails);
        mysqli_stmt_bind_param(
            $stmtDetails,
            "sssssssi",
            $id_proc_ch,
            $nama_barang,
            $qty,
            $uom,
            $detail_specification,
            $category,
            $urgency_status,
            $id
        );

        // Execute the update query
        if (mysqli_stmt_execute($stmtDetails)) {
            // Prepare new value for logging
            $newValue = json_encode([
                'nama_barang' => $nama_barang,
                'qty' => $qty,
                'uom' => $uom,
                'category' => $category,
                'detail_specification' => $detail_specification,
                'urgency_status' => $urgency_status
            ]);

            // Insert log entry
            $logQuery = "INSERT INTO proc_admin_log (idnik, action_type, table_name, record_id, old_value, new_value) 
                        VALUES (?, 'UPDATE', 'proc_request_details', ?, ?, ?)";
            $stmtLog = mysqli_prepare($koneksi, $logQuery);
            $idnik = $_SESSION['idnik'] ?? '0'; // Assume session contains user ID
            mysqli_stmt_bind_param(
                $stmtLog,
                "ssss",
                $idnik,
                $id,
                $oldValue,
                $newValue
            );
            mysqli_stmt_execute($stmtLog);
            mysqli_stmt_close($stmtLog);

            $response['status'] = 'success';
            $response['message'] = 'Data berhasil diupdate';

            // Commit transaction
            mysqli_commit($koneksi);
        } else {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_close($stmtDetails);
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $response['message'] = "Error: " . $e->getMessage();
    }
}

echo json_encode($response);
mysqli_close($koneksi);
