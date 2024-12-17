<?php
include '../koneksi.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $action = $_POST['action'];

    if ($action == 'add' || $action == 'edit') {
        $id_category = mysqli_real_escape_string($koneksi, $_POST['id_category']);
        $idnik = mysqli_real_escape_string($koneksi, $_POST['idnik']);
        $no_wa = mysqli_real_escape_string($koneksi, $_POST['no_wa']);

        // Format nomor WA
        $no_wa = preg_replace('/^0/', '', $no_wa); // Remove leading zero if exists

        if ($action == 'add') {
            // Check if combination already exists
            $check_query = "SELECT id FROM proc_admin_category 
                           WHERE id_category = '$id_category' AND idnik = '$idnik'";
            $check_result = mysqli_query($koneksi, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                throw new Exception("This category is already assigned to the selected PIC");
            }

            // Begin transaction
            mysqli_begin_transaction($koneksi);

            // Insert into proc_admin_category
            $query1 = "INSERT INTO proc_admin_category (id_category, idnik) 
                      VALUES ('$id_category', '$idnik')";

            // Insert/Update proc_admin_wa
            $query2 = "INSERT INTO proc_admin_wa (idnik, no_wa) 
                      VALUES ('$idnik', '$no_wa') 
                      ON DUPLICATE KEY UPDATE no_wa = VALUES(no_wa)";

            if (mysqli_query($koneksi, $query1) && mysqli_query($koneksi, $query2)) {
                mysqli_commit($koneksi);
                $response = [
                    'success' => true,
                    'message' => 'Category assignment added successfully'
                ];
            } else {
                throw new Exception(mysqli_error($koneksi));
            }
        } elseif ($action == 'edit') {
            $id = mysqli_real_escape_string($koneksi, $_POST['id']);

            mysqli_begin_transaction($koneksi);

            $query1 = "UPDATE proc_admin_category 
                     SET id_category = '$id_category', 
                         idnik = '$idnik' 
                     WHERE id = '$id'";

            $query2 = "INSERT INTO proc_admin_wa (idnik, no_wa) 
                      VALUES ('$idnik', '$no_wa') 
                      ON DUPLICATE KEY UPDATE no_wa = VALUES(no_wa)";

            if (mysqli_query($koneksi, $query1) && mysqli_query($koneksi, $query2)) {
                mysqli_commit($koneksi);
                $response = [
                    'success' => true,
                    'message' => 'Category assignment updated successfully'
                ];
            } else {
                throw new Exception(mysqli_error($koneksi));
            }
        }
    } elseif ($action == 'delete') {
        $id = mysqli_real_escape_string($koneksi, $_POST['id']);
        $query = "DELETE FROM proc_admin_category WHERE id = '$id'";

        if (mysqli_query($koneksi, $query)) {
            $response = [
                'success' => true,
                'message' => 'Category assignment deleted successfully'
            ];
        } else {
            throw new Exception(mysqli_error($koneksi));
        }
    }
} catch (Exception $e) {
    if (isset($koneksi)) mysqli_rollback($koneksi);
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
