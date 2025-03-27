<?php
require 'config.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    echo "Unauthorized access!";
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data["id"])) {
    echo "Invalid request!";
    exit();
}

$reportId = $data["id"];
$stmt = $conn->prepare("UPDATE emergencies SET status='Resolved' WHERE id=?");
$stmt->bind_param("i", $reportId);

if ($stmt->execute()) {
    echo "Emergency marked as Resolved!";
} else {
    echo "Error updating status: " . $stmt->error;
}

$stmt->close();
?>
