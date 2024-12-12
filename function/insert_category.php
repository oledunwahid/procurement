<?php
include '../koneksi.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $action = $_POST['action'];

    if ($action == 'add') {
        $id_category = mysqli_real_escape_string($koneksi, $_POST['id_category']);
        $idnik = mysqli_real_escape_string($koneksi, $_POST['idnik']);

        // Check if combination already exists
        $check_query = "SELECT id FROM proc_admin_category 
                       WHERE id_category = '$id_category' AND idnik = '$idnik'";
        $check_result = mysqli_query($koneksi, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            throw new Exception("This category is already assigned to the selected PIC");
        }

        $query = "INSERT INTO proc_admin_category (id_category, idnik) 
                  VALUES ('$id_category', '$idnik')";
    } elseif ($action == 'edit') {
        $id = mysqli_real_escape_string($koneksi, $_POST['id']);
        $id_category = mysqli_real_escape_string($koneksi, $_POST['id_category']);
        $idnik = mysqli_real_escape_string($koneksi, $_POST['idnik']);

        $query = "UPDATE proc_admin_category 
                 SET id_category = '$id_category', 
                     idnik = '$idnik' 
                 WHERE id = '$id'";
    } elseif ($action == 'delete') {
        $id = mysqli_real_escape_string($koneksi, $_POST['id']);
        $query = "DELETE FROM proc_admin_category WHERE id = '$id'";
    }

    if (mysqli_query($koneksi, $query)) {
        $response = [
            'success' => true,
            'message' => $action == 'delete' ? 'Category assignment deleted successfully' : ($action == 'edit' ? 'Category assignment updated successfully' :
                    'Category assignment added successfully')
        ];
    } else {
        throw new Exception(mysqli_error($koneksi));
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
