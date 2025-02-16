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
$type = $_POST['type'];
$details = $_POST['details'];

// Determine location input
if ($_POST['location_type'] == "map" && !empty($_POST['location_coords'])) {
    $location = $_POST['location_coords']; // GPS coordinates from map
} else {
    $location = $_POST['manual_location']; // Manually entered location
}

// Handle file upload
$image_path = "";
if (!empty($_FILES['image']['name'])) {
    $image_path = "uploads/" . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
}

// Insert into database
$sql = "INSERT INTO emergency_reports (user_id, type, location, details, image_path, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issss", $user_id, $type, $location, $details, $image_path);

if ($stmt->execute()) {
    echo "✅ Emergency report submitted successfully!";
} else {
    echo "❌ Error: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: home.php");
exit();
?>
