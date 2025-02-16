<?php
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube";

// Create connection
$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully!";
}
?>
