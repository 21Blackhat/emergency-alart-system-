<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "0"; // Return 0 if user is not logged in
    exit();
}

$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    echo "0"; // Return 0 if connection fails
    exit();
}

// Count unread notifications
$user_id = $_SESSION['user_id'];
$notif_query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND status = 'unread'";
$stmt = $conn->prepare($notif_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notif_count = $result->fetch_assoc()['unread_count'] ?? 0;

echo $notif_count;
?>
