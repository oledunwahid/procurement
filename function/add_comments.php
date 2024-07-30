<?php
include "../koneksi.php";
session_start();

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_proc_ch = $_POST['id_proc_ch'];
    $idnik = $_SESSION['idnik']; // Pastikan session sudah dimulai dan idnik tersedia
    $comment = trim($_POST['comment']);

    if (empty($comment)) {
        $response['status'] = 'error';
        $response['message'] = 'Comment cannot be empty';
    } else {
        $comment = mysqli_real_escape_string($koneksi, $comment);

        $query = "INSERT INTO proc_comments (id_proc_ch, idnik, comment) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sss", $id_proc_ch, $idnik, $comment);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Comment added successfully';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to add comment: ' . $stmt->error;
        }

        $stmt->close();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
