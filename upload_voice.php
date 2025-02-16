<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit();
}

$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube"; // Your database name

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["voice_message"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["voice_message"]["name"]);
    move_uploaded_file($_FILES["voice_message"]["tmp_name"], $target_file);

    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO chat (user_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $target_file);
    $stmt->execute();
}

header("Location: chat.php");
?>
