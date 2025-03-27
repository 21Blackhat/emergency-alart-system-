<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Emergency Alert System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard">
        <h2>Welcome, <?php echo $_SESSION["name"]; ?>!</h2>
        <button onclick="reportEmergency()">Report Emergency</button>
        <button onclick="logout()">Logout</button>
    </div>

    <script>
        function reportEmergency() {
            alert("Emergency reported!");
        }

        function logout() {
            window.location.href = "logout.php";
        }
    </script>
</body>
</html>
