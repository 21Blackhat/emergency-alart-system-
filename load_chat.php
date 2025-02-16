<?php
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch chat messages
$chat_query = "SELECT account.username, chat.message, chat.timestamp FROM chat JOIN account ON chat.user_id = account.id ORDER BY chat.timestamp DESC";
$chat_result = $conn->query($chat_query);

while ($row = $chat_result->fetch_assoc()) {
    echo "<div class='message'><strong>{$row['username']}:</strong> {$row['message']}</div>";
}
?>
