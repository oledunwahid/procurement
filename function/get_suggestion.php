<?php
$query = $_GET['query'];
$suggestions = array();

// Baca file suggestions.txt
$file = fopen('suggestions.txt', 'r');
while (($line = fgets($file)) !== false) {
    $line = trim($line);
    if (stripos($line, $query) !== false) {
        $suggestions[] = $line;
    }
}
fclose($file);

// Kembalikan hasil dalam format JSON
echo json_encode($suggestions);
