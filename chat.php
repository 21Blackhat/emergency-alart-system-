<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
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

// Handle text and voice messages
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];

    // Check if voice message is uploaded
    if (!empty($_FILES["voice_message"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["voice_message"]["name"]);
        move_uploaded_file($_FILES["voice_message"]["tmp_name"], $target_file);
        $message = $target_file; // Store file path as message
    } else {
        $message = htmlspecialchars(strip_tags($_POST['message']));
    }

    // Insert message into chat table
    $sql = "INSERT INTO chat (user_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $message);
    $stmt->execute();
}

// Fetch chat messages
$chat_query = "SELECT account.username, chat.message, chat.timestamp FROM chat JOIN account ON chat.user_id = account.id ORDER BY chat.timestamp DESC";
$chat_result = $conn->query($chat_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Chat</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding: 20px; }
        .container { max-width: 600px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px gray; margin: auto; text-align: left; }
        .chat-box { height: 300px; overflow-y: scroll; border: 1px solid #ddd; padding: 10px; background: #f9f9f9; }
        .message { margin-bottom: 10px; padding: 8px; background: white; border-radius: 5px; }
        .button { padding: 10px; background: blue; color: white; border: none; cursor: pointer; border-radius: 5px; }
    </style>
    <script>
        function refreshChat() {
            fetch("load_chat.php")
                .then(response => response.text())
                .then(data => {
                    document.getElementById("chat-box").innerHTML = data;
                    document.getElementById("chat-box").scrollTop = document.getElementById("chat-box").scrollHeight; // Auto-scroll
                });
        }
        setInterval(refreshChat, 2000); // Refresh chat every 2 seconds
    </script>
</head>
<body>

    <div class="container">
        <h2>💬 Emergency Chat</h2>
        <div id="chat-box" class="chat-box">
            <?php while ($row = $chat_result->fetch_assoc()): ?>
                <div class="message">
                    <strong><?php echo $row['username']; ?>:</strong> 
                    <?php if (strpos($row['message'], 'uploads/') !== false): ?>
                        <audio controls>
                            <source src="<?php echo $row['message']; ?>" type="audio/mpeg">
                        </audio>
                    <?php else: ?>
                        <?php echo $row['message']; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <form action="chat.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="message" placeholder="Type a message..." required>
            <input type="file" name="voice_message" accept="audio/*">
            <button class="button" type="submit">Send</button>
        </form>
    </div>

</body>
</html>
