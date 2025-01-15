<?php
include "../koneksi.php";
session_start();

$id_proc_ch = $_GET['id_proc_ch'];

$query = "SELECT c.*, u.nama, u.divisi, 
          GROUP_CONCAT(CONCAT(a.id_attachment, ':', a.file_name) SEPARATOR '|') as attachments 
          FROM proc_comments c 
          JOIN user u ON c.idnik = u.idnik 
          LEFT JOIN proc_comment_attachments a ON c.id_comments = a.id_comments 
          WHERE c.id_proc_ch = ? 
          GROUP BY c.id_comments
          ORDER BY c.timestamp ASC";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $id_proc_ch);
$stmt->execute();
$result = $stmt->get_result();
function formatTimestamp($timestamp)
{
    $now = new DateTime();
    $commentTime = new DateTime($timestamp);
    $interval = $now->diff($commentTime);

    if ($interval->y > 0 || $interval->m > 0 || $interval->d > 0) {
        // Jika lebih dari sehari, tampilkan format "dd MMM YY - hh:mmAM/PM"
        return $commentTime->format('d M y - h:iA');
    } elseif ($interval->h > 0) {
        // Jika dalam hari yang sama tapi lebih dari sejam yang lalu
        return $commentTime->format('h:iA');
    } elseif ($interval->i > 0) {
        // Jika kurang dari sejam tapi lebih dari semenit
        return $interval->i . " mins ago";
    } else {
        // Jika kurang dari semenit
        return "just now";
    }
}

while ($row = $result->fetch_assoc()) {
    $isCurrentUser = isset($_SESSION['idnik']) && ($row['idnik'] == $_SESSION['idnik']);
    $bubbleClass = $isCurrentUser ? 'right' : 'left';
    $bgClass = $isCurrentUser ? 'bg-light-success' : 'bg-light';
    $formattedTime = formatTimestamp($row['timestamp']);

    echo "<div class='comment-bubble {$bubbleClass}'>";
    echo "<div class='card {$bgClass}'>";
    echo "<div class='card-body'>";
    echo "<p class='card-text'><strong>" . htmlspecialchars($row['nama']) . "</strong> (" . htmlspecialchars($row['divisi']) . ") - " . $formattedTime . "</p>";
    echo "<p class='card-text'>" . htmlspecialchars($row['comment']) . "</p>";

    // Display attachments if any
    if (!empty($row['attachments'])) {
        echo "<div class='attachments mt-2'>";
        foreach (explode('|', $row['attachments']) as $attachment) {
            list($id, $filename) = explode(':', $attachment);
            echo "<div class='attachment-item'>";
            echo "<i class='bi bi-paperclip'></i> ";
            echo "<a href='function/download_attachment.php?id=" . htmlspecialchars($id) . "' target='_blank'>" . htmlspecialchars($filename) . "</a>";
            echo "</div>";
        }
        echo "</div>";
    }

    echo "</div>";
    echo "</div>";
    echo "</div>";
}

$stmt->close();
