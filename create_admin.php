<?php
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube";

// Connect to database
$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Securely create a new admin with hashed password
$plain_password = "admin123";
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

// Insert the admin user
$sql = "INSERT INTO admins (username, password) VALUES ('admin', ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hashed_password);
if ($stmt->execute()) {
    echo "✅ Admin user created successfully!";
} else {
    echo "❌ Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
