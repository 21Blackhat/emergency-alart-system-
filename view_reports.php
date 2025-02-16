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

// Fetch user's emergency reports
$sql = "SELECT type, location, details, image, status, created_at FROM emergency_reports WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Emergency Reports</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding: 50px; }
        .container { max-width: 800px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px gray; margin: auto; text-align: left; }
        .report-item { padding: 15px; border-bottom: 1px solid #ddd; }
        .status { font-weight: bold; color: blue; }
        .status.pending { color: orange; }
        .status.in-progress { color: blue; }
        .status.resolved { color: green; }
        .button { display: inline-block; padding: 10px 20px; background-color: blue; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="container">
        <h2>📌 My Emergency Reports</h2>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="report-item">
                <h3><?php echo htmlspecialchars($row['type']); ?></h3>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                <p><?php echo htmlspecialchars($row['details']); ?></p>
                <?php if (!empty($row['image'])): ?>
                    <p><img src="<?php echo htmlspecialchars($row['image']); ?>" width="200"></p>
                <?php endif; ?>
                <p class="status <?php echo strtolower($row['status']); ?>"><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                <p class="notif-time"><?php echo $row['created_at']; ?></p>
            </div>
        <?php endwhile; ?>
        <a class="button" href="home.php">⬅ Back to Dashboard</a>
    </div>

</body>
</html>
