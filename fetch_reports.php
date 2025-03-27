<?php
require 'config.php';

$query = "SELECT emergencies.id, users.name AS user_name, emergencies.location, emergencies.status, emergencies.created_at 
          FROM emergencies 
          JOIN users ON emergencies.user_id = users.id 
          ORDER BY emergencies.created_at DESC";

$result = $conn->query($query);
$reports = [];

while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

echo json_encode($reports);
?>
