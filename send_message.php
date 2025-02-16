<?php
session_start();
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Debugging: Print received POST data
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Check for required fields
if (!isset($_POST['message']) || empty($_POST['message'])) {
    die("❌ Error: Message is missing!");
}

$recipient_type = $_POST['recipient_type'] ?? "all";
$message = $_POST['message'];
$admin_id = $_SESSION['admin_id']; // Ensure admin is logged in

if ($recipient_type === "specific") {
    if (!isset($_POST['student_id']) || empty($_POST['student_id'])) {
        die("❌ Error: receiver_id is still missing! Check form input.");
    }
    $receiver_id = $_POST['student_id'];
    
    // Insert notification for a specific student
    $query = "INSERT INTO notifications (user_id, message, status, created_at) VALUES (?, ?, 'unread', NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $receiver_id, $message);
} else {
    // Insert notification for ALL students
    $query = "INSERT INTO notifications (user_id, message, status, created_at) 
              SELECT id, ?, 'unread', NOW() FROM users";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $message);
}

// Execute the query
if ($stmt->execute()) {
    echo "✅ Notification sent successfully!";
} else {
    echo "❌ Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
