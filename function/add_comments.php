<?php
include "../koneksi.php";
session_start();

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $id_proc_ch = $_POST['id_proc_ch'];
        $idnik = $_SESSION['idnik'];
        $comment = trim($_POST['comment']);

        if (empty($comment)) {
            throw new Exception('Comment cannot be empty');
        }

        $koneksi->begin_transaction();

        // Insert comment
        $comment = mysqli_real_escape_string($koneksi, $comment);
        $query = "INSERT INTO proc_comments (id_proc_ch, idnik, comment) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sss", $id_proc_ch, $idnik, $comment);
        $stmt->execute();
        $comment_id = $stmt->insert_id;

        // Handle file attachments
        if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
            $upload_dir = "../../file/uploads/comments/"; // Path ke folder uploads

            // Buat directory jika belum ada
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = $_FILES['attachments']['name'][$key];
                    $file_size = $_FILES['attachments']['size'][$key];
                    $file_type = $_FILES['attachments']['type'][$key];

                    // Validasi ukuran file (5MB limit)
                    if ($file_size > 5 * 1024 * 1024) {
                        throw new Exception("File size exceeds 5MB limit: " . $file_name);
                    }

                    // Generate nama file yang aman
                    $secure_filename = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
                    $file_path = $upload_dir . $secure_filename;

                    if (move_uploaded_file($tmp_name, $file_path)) {
                        $query = "INSERT INTO proc_comment_attachments (id_comments, file_name, file_path, file_type, file_size) 
                                 VALUES (?, ?, ?, ?, ?)";
                        $stmt = $koneksi->prepare($query);
                        $stmt->bind_param("isssi", $comment_id, $file_name, $secure_filename, $file_type, $file_size);
                        $stmt->execute();
                    } else {
                        throw new Exception("Failed to upload file: " . $file_name);
                    }
                }
            }
        }

        $koneksi->commit();
        $response['status'] = 'success';
        $response['message'] = 'Comment and attachments added successfully';
    } catch (Exception $e) {
        if ($koneksi->connect_errno) {
            $koneksi->rollback();
        }
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
