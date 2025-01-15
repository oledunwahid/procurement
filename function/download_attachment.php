<?php
include "../koneksi.php";
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT * FROM proc_comment_attachments WHERE id_attachment = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $file_path = "../../file/uploads/comments/" . $row['file_path'];

        if (file_exists($file_path)) {
            // Set header berdasarkan tipe file
            header('Content-Type: ' . $row['file_type']);
            header('Content-Disposition: inline; filename="' . $row['file_name'] . '"');
            header('Content-Length: ' . filesize($file_path));

            // Output file
            readfile($file_path);
            exit;
        }
    }
}

// If file not found or error
header("HTTP/1.0 404 Not Found");
echo "File not found";
