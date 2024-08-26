<?php
include '../koneksi.php';

// Konfigurasi DataTables
$table = 'proc_admin_log';
$primaryKey = 'log_id';

$columns = array(
    array('db' => 'log_id', 'dt' => 0),
    array('db' => 'idnik',  'dt' => 1),
    array('db' => 'action_type', 'dt' => 2),
    array('db' => 'table_name', 'dt' => 3),
    array('db' => 'record_id', 'dt' => 4),
    array('db' => 'old_value', 'dt' => 5),
    array('db' => 'new_value', 'dt' => 6),
    array('db' => 'timestamp', 'dt' => 7)
);

// Parameters dari DataTables
$limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) === 'desc' ? 'DESC' : 'ASC';
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Hitung total records tanpa filter
$sqlTotal = "SELECT COUNT(*) as count FROM $table";
$resultTotal = mysqli_query($koneksi, $sqlTotal);
$rowTotal = mysqli_fetch_assoc($resultTotal);
$totalRecords = $rowTotal['count'];

// Buat query dengan search dan filter
$sqlFiltered = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", array_column($columns, 'db')) . "
                FROM $table
                WHERE 1 = 1";

$params = array();

if (!empty($search)) {
    $sqlFiltered .= " AND (idnik LIKE ? OR action_type LIKE ? OR table_name LIKE ? OR record_id LIKE ?)";
    $searchParam = "%$search%";
    $params = array_fill(0, 4, $searchParam);
}

// Add date range filter
if (!empty($_POST['dateRange'])) {
    $dates = explode(' - ', $_POST['dateRange']);
    $start_date = date('Y-m-d', strtotime($dates[0]));
    $end_date = date('Y-m-d', strtotime($dates[1]));
    $sqlFiltered .= " AND DATE(timestamp) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
}

// Add action type filter
if (!empty($_POST['actionType'])) {
    $sqlFiltered .= " AND action_type = ?";
    $params[] = $_POST['actionType'];
}

// Add table name filter
if (!empty($_POST['tableName'])) {
    $sqlFiltered .= " AND table_name = ?";
    $params[] = $_POST['tableName'];
}

$sqlFiltered .= " ORDER BY " . $columns[$orderColumn]['db'] . " $orderDir LIMIT ?, ?";
$params[] = $start;
$params[] = $limit;

// Prepare and execute the query
$stmt = mysqli_prepare($koneksi, $sqlFiltered);
if ($stmt === false) {
    die(json_encode(array('error' => 'Failed to prepare statement: ' . mysqli_error($koneksi))));
}

if (!empty($params)) {
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

if (!mysqli_stmt_execute($stmt)) {
    die(json_encode(array('error' => 'Failed to execute statement: ' . mysqli_stmt_error($stmt))));
}

$result = mysqli_stmt_get_result($stmt);

// Hitung total records dengan filter
$sqlFilteredCount = "SELECT FOUND_ROWS() as count";
$resultFilteredCount = mysqli_query($koneksi, $sqlFilteredCount);
$rowFilteredCount = mysqli_fetch_assoc($resultFilteredCount);
$totalRecordsFiltered = $rowFilteredCount['count'];

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = array_values($row);
}

// Output dalam format JSON
$jsonOutput = json_encode(array(
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecordsFiltered),
    "data" => $data
));

echo $jsonOutput;

mysqli_close($koneksi);
