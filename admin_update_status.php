<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $report_id = intval($_POST['report_id']);
    $status = htmlspecialchars(strip_tags($_POST['status']));

    // Update status
    $sql = "UPDATE emergency_reports SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $report_id);
    $stmt->execute();

    // Send notification to user
    $user_query = "SELECT user_id FROM emergency_reports WHERE id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $report_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    
    if ($user_result->num_rows > 0) {
        $user_row = $user_result->fetch_assoc();
        $user_id = $user_row['user_id'];

        $notif_message = "🚨 Your emergency report status has changed to: " . $status;
        $notif_sql = "INSERT INTO notifications (user_id, message, status) VALUES (?, ?, 'unread')";
        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("is", $user_id, $notif_message);
        $notif_stmt->execute();
    }

    header("Location: admin_panel.php");
    exit();
}
?>
