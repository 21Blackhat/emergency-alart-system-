<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Database Connection
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Validate POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method.");
}

if (!isset($_POST['report_id'], $_POST['status'])) {
    die("Missing report ID or status.");
}

$report_id = intval($_POST['report_id']); // Convert to integer to prevent SQL injection
$status = htmlspecialchars(strip_tags($_POST['status'])); // Sanitize status input

// Update emergency status
$sql = "UPDATE emergency_reports SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("si", $status, $report_id);
if (!$stmt->execute()) {
    die("Error updating status: " . $stmt->error);
}

// Fetch user_id for notification
$user_query = "SELECT user_id FROM emergency_reports WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
if (!$user_stmt) {
    die("Database error: " . $conn->error);
}

$user_stmt->bind_param("i", $report_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows > 0) {
    $user_row = $user_result->fetch_assoc();
    $user_id = $user_row['user_id'];

    // Send notification to user
    $notif_message = "🚨 Your emergency report has been updated to: " . $status;
    $notif_sql = "INSERT INTO notifications (user_id, message, status) VALUES (?, ?, 'unread')";
    $notif_stmt = $conn->prepare($notif_sql);
    if ($notif_stmt) {
        $notif_stmt->bind_param("is", $user_id, $notif_message);
        $notif_stmt->execute();
    }
}

// Redirect back to admin panel
header("Location: admin_panel.php");
exit();
?>
