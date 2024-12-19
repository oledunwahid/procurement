<?php
header('Content-Type: application/json');

$urgencyStatuses = [
    ['id' => 'normal', 'name' => 'Normal'],
    ['id' => 'urgent', 'name' => 'Urgent']
];

echo json_encode($urgencyStatuses);
