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

// Get input values
$message = $_POST['message'];
$recipient_type = $_POST['recipient_type'];
$student_id = $_POST['student_id'] ?? null;

if ($recipient_type == "all") {
    // Send to all students
    $query = "INSERT INTO notifications (user_id, message, status) SELECT id, ?, 'unread' FROM account"; 
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $message);
} else {
    // Send to a specific student
    $query = "INSERT INTO notifications (user_id, message, status) VALUES (?, ?, 'unread')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $student_id, $message);
}

// Execute the query
if ($stmt->execute()) {
    echo "✅ Notification sent successfully!";
} else {
    echo "❌ Error sending notification: " . $conn->error;
}

// Close database connection
$stmt->close();
$conn->close();
?>
