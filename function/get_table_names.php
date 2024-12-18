// get_table_names.php
<?php
require_once '../koneksi.php';
header('Content-Type: application/json');

try {
    $stmt = $koneksi->prepare("SELECT DISTINCT table_name FROM proc_admin_log ORDER BY table_name");
    $stmt->execute();
    $result = $stmt->get_result();

    $tableNames = [];
    while ($row = $result->fetch_assoc()) {
        $tableNames[] = $row['table_name'];
    }

    echo json_encode($tableNames);
} catch (Exception $e) {
    error_log("Error fetching table names: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch table names']);
}

$stmt->close();
$koneksi->close();
