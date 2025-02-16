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

// Fetch emergency reports for logged-in user
$user_id = $_SESSION['user_id'];
$query = "SELECT type, location, details, status, timestamp FROM emergency_reports WHERE user_id = ?";
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
    <title>Track My Incidents</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px gray;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #333;
            color: white;
        }
        .status {
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;
        }
        .pending { background-color: orange; color: white; }
        .in-progress { background-color: blue; color: white; }
        .resolved { background-color: green; color: white; }
    </style>
</head>
<body>

    <div class="container">
        <h2>📍 My Emergency Reports</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                        <td>
                            <span class="status 
                                <?php echo ($row['status'] == 'Pending' ? 'pending' : 
                                           ($row['status'] == 'In Progress' ? 'in-progress' : 'resolved')); ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $row['timestamp']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No emergency reports found.</p>
        <?php endif; ?>
        <br>
        <a href="home.php">⬅ Back to Dashboard</a>
    </div>

</body>
</html>
