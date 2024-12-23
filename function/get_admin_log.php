<?php
require_once '../koneksi.php';
header('Content-Type: application/json');

try {
    // Get request parameters
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

    // Base query
    $sql = "SELECT l.*, u.nama as user_name 
            FROM proc_admin_log l 
            LEFT JOIN user u ON l.idnik = u.idnik 
            WHERE 1=1";

    $countSql = "SELECT COUNT(*) as total FROM proc_admin_log";

    // Search condition
    $params = [];
    $types = "";

    if (!empty($search)) {
        $sql .= " AND (l.idnik LIKE ? OR u.nama LIKE ? OR l.action_type LIKE ? OR l.table_name LIKE ? OR l.record_id LIKE ?)";
        $searchParam = "%$search%";
        $params = array_fill(0, 5, $searchParam);
        $types .= str_repeat("s", 5);
    }

    // Date filter
    if (!empty($_POST['date_start'])) {
        $sql .= " AND DATE(l.timestamp) >= ?";
        $params[] = $_POST['date_start'];
        $types .= "s";
    }
    if (!empty($_POST['date_end'])) {
        $sql .= " AND DATE(l.timestamp) <= ?";
        $params[] = $_POST['date_end'];
        $types .= "s";
    }

    // Action type filter
    if (!empty($_POST['action_type'])) {
        $sql .= " AND l.action_type = ?";
        $params[] = $_POST['action_type'];
        $types .= "s";
    }

    // Get total records count
    $totalRecords = $koneksi->query($countSql)->fetch_assoc()['total'];

    // Get filtered records count
    $stmtCount = $koneksi->prepare(str_replace("l.*, u.nama as user_name", "COUNT(*) as total", $sql));
    if (!empty($params)) {
        $stmtCount->bind_param($types, ...$params);
    }
    $stmtCount->execute();
    $filteredRecords = $stmtCount->get_result()->fetch_assoc()['total'];
    $stmtCount->close();

    // Add sorting and pagination
    $sql .= " ORDER BY l.timestamp DESC LIMIT ?, ?";
    $params[] = $start;
    $params[] = $length;
    $types .= "ii";

    // Execute main query
    $stmt = $koneksi->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        // Handle old_value and new_value properly
        $oldValue = !empty($row['old_value']) ? $row['old_value'] : null;
        $newValue = !empty($row['new_value']) ? $row['new_value'] : null;

        // Ensure JSON is valid before returning
        if ($oldValue) {
            $decodedOld = json_decode($oldValue);
            if (json_last_error() === JSON_ERROR_NONE) {
                $oldValue = json_encode($decodedOld, JSON_UNESCAPED_UNICODE);
            }
        }

        if ($newValue) {
            $decodedNew = json_decode($newValue);
            if (json_last_error() === JSON_ERROR_NONE) {
                $newValue = json_encode($decodedNew, JSON_UNESCAPED_UNICODE);
            }
        }

        // Format action type badge
        $actionType = $row['action_type'];

        $data[] = [
            'log_id' => $row['log_id'],
            'idnik' => htmlspecialchars($row['user_name'] ?? $row['idnik']),
            'action_type' => $actionType,
            'table_name' => htmlspecialchars($row['table_name']),
            'record_id' => htmlspecialchars($row['record_id']),
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'timestamp' => date('Y-m-d H:i:s', strtotime($row['timestamp']))
        ];
    }

    $response = [
        'draw' => $draw,
        'recordsTotal' => intval($totalRecords),
        'recordsFiltered' => intval($filteredRecords),
        'data' => $data
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
} catch (Exception $e) {
    error_log("Admin Log Error: " . $e->getMessage());
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Failed to fetch data'
    ]);
    exit;
}
