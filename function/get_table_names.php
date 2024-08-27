<?php
include '../koneksi.php';

$sql = "SELECT DISTINCT table_name FROM proc_admin_log ORDER BY table_name";
$result = mysqli_query($koneksi, $sql);

if (!$result) {
    die(json_encode(array('error' => 'Failed to fetch table names: ' . mysqli_error($koneksi))));
}

$tableNames = array();
while ($row = mysqli_fetch_assoc($result)) {
    $tableNames[] = $row['table_name'];
}

echo json_encode($tableNames);

mysqli_close($koneksi);
