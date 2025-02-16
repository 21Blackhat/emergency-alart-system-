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

$user_id = $_GET['user_id'] ?? 0;
$admin_id = $_SESSION['admin_id'];

// Fetch chat messages
$query = "SELECT sender_id, message, timestamp FROM chat WHERE user_id = ? ORDER BY timestamp ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding: 20px; }
        .container { max-width: 600px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px gray; margin: auto; text-align: left; }
        .chat-box { height: 400px; overflow-y: scroll; border: 1px solid #ddd; padding: 10px; background: #f9f9f9; }
        .message { margin-bottom: 10px; padding: 8px; border-radius: 5px; max-width: 75%; }
        .admin { background: blue; color: white; text-align: right; float: right; clear: both; }
        .user { background: gray; color: white; text-align: left; float: left; clear: both; }
        .input-box { display: flex; gap: 10px; margin-top: 10px; }
        .input-box input { flex-grow: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .button { padding: 10px; background: red; color: white; border: none; cursor: pointer; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="container">
        <h2>💬 Chat with User #<?php echo $user_id; ?></h2>
        <div id="chat-box" class="chat-box">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="message <?php echo ($row['sender_id'] == $admin_id) ? 'admin' : 'user'; ?>">
                    <?php echo $row['message']; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <form action="send_message.php" method="POST" class="input-box">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="text" name="message" placeholder="Type a message..." required>
            <button class="button" type="submit">Send</button>
        </form>
    </div>

</body>
</html>
