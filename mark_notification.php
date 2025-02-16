<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized access.");
}

$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    exit("Database connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $notif_id = $_GET['id'];
    $update_query = "UPDATE notifications SET status = 'read' WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $notif_id);
    $stmt->execute();
    echo "Success";
}
?>
