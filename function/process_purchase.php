<?php
// Koneksi ke database
include '../koneksi.php'; // Sesuaikan dengan file koneksi Anda
date_default_timezone_set('Asia/Jakarta');
// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari request
    $id_approval = isset($_POST['id_approval']) ? $_POST['id_approval'] : '';
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Validasi input
    if (!empty($id_approval) && ($action == 'Approved' || $action == 'Rejected' || $action == 'Cancelled')) {
    // Persiapkan query berdasarkan action
    if ($action == 'Approved') {
        $status = 'Approved';
    } elseif ($action == 'Rejected') {
        $status = 'Rejected';
    } elseif ($action == 'Cancelled') {
        // Tentukan status ketika di-cancel. Misal kembali ke "Pending"
        $status = 'Pending';
    }
		  $currentDate = date('Y-m-d');
         $query = "UPDATE budget_approval_mr SET status = ?, date_approval = ? WHERE id_approval = ?";
        // Persiapkan statement
       if ($stmt = mysqli_prepare($koneksi, $query)) {
        // Bind variabel ke statement sebagai parameter
        mysqli_stmt_bind_param($stmt, "ssi", $status, $currentDate, $id_approval);
        
        // Eksekusi statement
        if (mysqli_stmt_execute($stmt)) {
            echo "Success: MR status updated to " . $status . " on " . $currentDate;
        } else {
            echo "Error: Could not execute query: " . mysqli_error($koneksi);
        }
        
        // Tutup statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Could not prepare query: " . mysqli_error($koneksi);
    }
    } else {
    echo "Error: Invalid input";
    }
} else {
    echo "Error: Invalid request method";
}

// Tutup koneksi
mysqli_close($koneksi);
?>
