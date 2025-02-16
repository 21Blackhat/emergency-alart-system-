<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method. You must submit the form.");
}

// Database connection
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube";
$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get form data
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Check if fields are empty
if (empty($username) || empty($email) || empty($password)) {
    die("Error: All fields are required.");
}

// Enforce password length (max 12 characters)
if (strlen($password) > 12) {
    die("Error: Password must be at most 12 characters long.");
}

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if email exists
$check_sql = "SELECT id FROM account WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    die("Error: Email already registered. Try logging in.");
}
$check_stmt->close();

// Insert new user
$sql = "INSERT INTO account (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $email, $hashed_password);

if ($stmt->execute()) {
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['username'] = $username;
    header("Location: home.php"); // Redirect after successful registration
    exit();
} else {
    die("Error inserting record: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
