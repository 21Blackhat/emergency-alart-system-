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

// Get unread notifications count
$user_id = $_SESSION['user_id'];
$notif_query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND status = 'unread'";
$stmt = $conn->prepare($notif_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notif_count = $result->fetch_assoc()['unread_count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Emergency Alert System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            width: 100%;
            background-color: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            background-color: red;
        }
        .navbar a:hover {
            background-color: darkred;
        }
        .notifications {
            position: relative;
            font-size: 20px;
            cursor: pointer;
        }
        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 12px;
        }
        .container {
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px gray;
            margin: 50px auto;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: blue;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
        }
        .button:hover {
            opacity: 0.8;
        }
    </style>
  <script>
    function checkNotifications() {
        fetch("fetch_notifications.php")
            .then(response => response.text())
            .then(data => {
                document.getElementById("notif-badge").innerText = data;
            })
            .catch(error => console.error("Error fetching notifications:", error));
    }

    // Run every 5 seconds (5000ms)
    setInterval(checkNotifications, 5000);
</script>

</head>
<body>

    <div class="navbar">
        <span>Welcome, <?php echo $_SESSION['username']; ?>!</span>
        
        <div class="notifications">
    <a href="notifications.php">🔔 Notifications</a>
    <span id="notif-badge" class="badge"><?php echo $notif_count; ?></span>
</div>


        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>📢 Dashboard - Emergency Alert System</h1>
        <p>Use this platform to report and track emergency situations on campus.</p>
        <a class="button" href="report_emergency.php">🚨 Report Emergency</a>
        <a class="button" href="view_reports.php">📌 View My Reports</a>
        <a class="button" href="notifications.php">🔔 View Notifications</a>
        <a class="button" href="chat.php">💬 Chat with Emergency Team</a>
        <a class="button" href="track_incidents.php">📍 Track My Reports</a>
    </div>

</body>
</html>
