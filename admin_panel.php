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

// Get filter values
$status = $_GET['status'] ?? "";
$from_date = $_GET['from_date'] ?? "";
$to_date = $_GET['to_date'] ?? "";

// Construct SQL query with filters
$query = "SELECT id, user_id, type, location, details, status FROM emergency_reports WHERE 1";

if (!empty($status)) {
    $query .= " AND status = '$status'";
}

if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND timestamp BETWEEN '$from_date' AND '$to_date'";
}

$query .= " ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Emergency Alert System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding: 20px; }
        .container { max-width: 1000px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px gray; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #333; color: white; }
        select, input, button, textarea { padding: 5px; margin-top: 5px; width: 100%; }
        .logout { position: absolute; top: 10px; right: 10px; background: red; color: white; padding: 10px; text-decoration: none; border-radius: 5px; }
        .filters { margin-bottom: 15px; text-align: left; }
        .chat-icon { text-decoration: none; background: blue; color: white; padding: 8px 12px; border-radius: 5px; }
        .chat-icon:hover { background: darkblue; }
        .notif-alert { background: green; color: white; padding: 8px 12px; border-radius: 5px; }
        .notif-alert:hover { background: darkgreen; }
        .broadcast-container { margin-top: 20px; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0px 0px 10px gray; }
    </style>
    
</head>
<body>

    <a class="logout" href="logout.php">Logout</a>
    
    <div class="container">
        <h2>🚨 Admin Panel - Manage Emergencies</h2>
        
        <!-- Filters Section -->
        <form method="GET" action="admin_panel.php" class="filters">
            <label for="status">Status:</label>
            <select name="status">
                <option value="">All</option>
                <option value="Pending" <?php if ($status == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="In Progress" <?php if ($status == 'In Progress') echo 'selected'; ?>>In Progress</option>
                <option value="Resolved" <?php if ($status == 'Resolved') echo 'selected'; ?>>Resolved</option>
            </select>

            <label for="from_date">From:</label>
            <input type="date" name="from_date" value="<?php echo $from_date; ?>">

            <label for="to_date">To:</label>
            <input type="date" name="to_date" value="<?php echo $to_date; ?>">

            <button type="submit">Filter</button>
        </form>

        <!-- Emergency Reports Table -->
        <table>
            <tr>
                <th>Report ID</th>
                <th>User ID</th>
                <th>Type</th>
                <th>Location</th>
                <th>Details</th>
                <th>Status</th>
                <th>Update</th>
                <th>Chat</th>
                <th>Notification Alert</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td><?php echo $row['details']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <form action="update_status.php" method="POST">
                            <input type="hidden" name="report_id" value="<?php echo $row['id']; ?>">
                            <select name="status">
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Resolved">Resolved</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                    <!-- Chat button added -->
                    <td>
                        <a href="admin_chat.php?user_id=<?php echo $row['user_id']; ?>" class="chat-icon">💬 Chat</a>
                    </td>
                    <!-- Notification Alert Button -->
                    <td>
                        <a href="send_notification.php?user_id=<?php echo $row['user_id']; ?>" class="notif-alert">📢 Send Alert</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Send Notification Alert -->
    <div class="broadcast-container">
        <h2>📢 Send Notification Alert</h2>
        <form action="send_notification.php" method="POST">
            <textarea name="message" placeholder="Enter alert message..." required></textarea><br>
            
            <label>
                <input type="radio" name="recipient_type" value="all" checked> 📢 General Alert (All Students)
            </label><br>

            <label>
                <input type="radio" name="recipient_type" value="specific"> 🎯 Specific Student
            </label>
            
            <input type="number" name="student_id" placeholder="Enter Student ID (Only for Specific Alert)"><br>

            <button type="submit">🚀 Send Alert</button>
        </form>
    </div>

</body>
</html>