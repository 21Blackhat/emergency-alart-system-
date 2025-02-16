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

$user_id = $_SESSION['user_id'];

// Fetch all notifications
$notif_query = "SELECT id, message, created_at, status FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($notif_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px gray;
            margin: auto;
            text-align: left;
        }
        .notif-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notif-message {
            font-size: 16px;
        }
        .notif-time {
            font-size: 12px;
            color: gray;
        }
        .new-badge {
            background-color: red;
            color: white;
            padding: 3px 7px;
            font-size: 12px;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: blue;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
    <script>
        function markAsRead(notifId) {
            fetch('mark_notification.php?id=' + notifId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('notif-' + notifId).classList.remove('new-badge');
                    document.getElementById('notif-' + notifId).innerText = '';
                });
        }
    </script>
</head>
<body>

    <div class="container">
        <h2>🔔 Your Notifications</h2>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="notif-item">
                <div>
                    <p class="notif-message"><?php echo htmlspecialchars($row['message']); ?></p>
                    <p class="notif-time"><?php echo $row['created_at']; ?></p>
                </div>
                <?php if ($row['status'] == 'unread'): ?>
                    <span id="notif-<?php echo $row['id']; ?>" class="new-badge" onclick="markAsRead(<?php echo $row['id']; ?>)">New</span>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
        <a class="button" href="home.php">⬅ Back to Dashboard</a>
    </div>

</body>
</html>
