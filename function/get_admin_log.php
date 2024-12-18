<?php
require_once '../koneksi.php';
header('Content-Type: application/json');

function getUserName($koneksi, $idnik)
{
    $stmt = $koneksi->prepare("SELECT nama FROM user WHERE idnik = ?");
    $stmt->bind_param("s", $idnik);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user ? $user['nama'] : $idnik;
}

function formatActionType($type)
{
    $labels = [
        'INSERT' => '<span class="badge bg-success">New Entry</span>',
        'UPDATE' => '<span class="badge bg-warning">Updated</span>',
        'DELETE' => '<span class="badge bg-danger">Deleted</span>'
    ];
    return $labels[$type] ?? $type;
}

function formatTableName($table)
{
    $labels = [
        'proc_request_details' => 'Purchase Request Items',
        'proc_purchase_requests' => 'Purchase Requests',
        'proc_admin_category' => 'Category Assignments',
        'proc_admin_wa' => 'WhatsApp Contacts'
    ];
    return $labels[$table] ?? ucwords(str_replace(['proc_', '_'], ['', ' '], $table));
}

function formatJsonValue($value)
{
    if (empty($value)) return '-';

    try {
        $data = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            $formatted = [];
            $labelMappings = [
                'nama_barang' => 'Item Name',
                'qty' => 'Quantity',
                'uom' => 'Unit',
                'category' => 'Category',
                'unit_price' => 'Price',
                'detail_specification' => 'Specifications',
                'detail_notes' => 'Notes'
            ];

            foreach ($data as $key => $val) {
                $label = $labelMappings[$key] ?? ucwords(str_replace('_', ' ', $key));
                if ($key === 'unit_price') {
                    $val = 'Rp ' . number_format($val, 0, ',', '.');
                }
                $formatted[] = "<strong>{$label}:</strong> {$val}";
            }
            return implode('<br>', $formatted);
        }
    } catch (Exception $e) {
        error_log("JSON formatting error: " . $e->getMessage());
    }

    return htmlspecialchars($value);
}

try {
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;

    // Query building
    $sql = "SELECT l.*, u.nama as user_name 
            FROM proc_admin_log l 
            LEFT JOIN user u ON l.idnik = u.idnik 
            WHERE 1=1";
    $params = [];
    $types = "";

    // Apply filters
    if (!empty($_POST['search']['value'])) {
        $searchValue = $_POST['search']['value'];
        $sql .= " AND (l.idnik LIKE ? OR u.nama LIKE ? OR l.action_type LIKE ? OR l.table_name LIKE ? OR l.record_id LIKE ?)";
        $searchParam = "%{$searchValue}%";
        array_push($params, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
        $types .= "sssss";
    }

    // Date range filter
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

    // Action type and table filters
    if (!empty($_POST['action_type'])) {
        $sql .= " AND l.action_type = ?";
        $params[] = $_POST['action_type'];
        $types .= "s";
    }
    if (!empty($_POST['table_name'])) {
        $sql .= " AND l.table_name = ?";
        $params[] = $_POST['table_name'];
        $types .= "s";
    }

    // Get total and filtered counts
    $totalRecords = $koneksi->query("SELECT COUNT(*) FROM proc_admin_log")->fetch_row()[0];

    $stmtFiltered = $koneksi->prepare(str_replace("SELECT l.*, u.nama as user_name", "SELECT COUNT(*)", $sql));
    if (!empty($params)) {
        $stmtFiltered->bind_param($types, ...$params);
    }
    $stmtFiltered->execute();
    $filteredRecords = $stmtFiltered->get_result()->fetch_row()[0];
    $stmtFiltered->close();

    // Get data with ordering and pagination
    $sql .= " ORDER BY l." . $_POST['columns'][$_POST['order'][0]['column']]['data'] . " " . $_POST['order'][0]['dir'];
    $sql .= " LIMIT ?, ?";
    $params[] = $start;
    $params[] = $length;
    $types .= "ii";

    $stmt = $koneksi->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'log_id' => $row['log_id'],
            'idnik' => $row['user_name'] ?? $row['idnik'],
            'action_type' => formatActionType($row['action_type']),
            'table_name' => formatTableName($row['table_name']),
            'record_id' => $row['record_id'],
            'old_value' => formatJsonValue($row['old_value']),
            'new_value' => formatJsonValue($row['new_value']),
            'timestamp' => date('d M Y H:i:s', strtotime($row['timestamp']))
        ];
    }

    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => (int)$totalRecords,
        'recordsFiltered' => (int)$filteredRecords,
        'data' => $data
    ]);
} catch (Exception $e) {
    error_log("Admin Log Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch data']);
}
