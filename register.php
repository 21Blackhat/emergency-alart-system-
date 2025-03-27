<?php
session_start();
require 'db.php';

// Ensure the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("405 Method Not Allowed");
}

// Secure input function to prevent XSS (Cross-Site Scripting)
function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Get form data and sanitize
$name = cleanInput($_POST['name']);
$email = cleanInput($_POST['email']);
$password = cleanInput($_POST['password']);
$phone = cleanInput($_POST['phone']);

// Check if all fields are filled
if (empty($name) || empty($email) || empty($password) || empty($phone)) {
    die("Error: All fields are required.");
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Error: Invalid email format.");
}

// Check if email already exists (Prevent duplicate users)
$check_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
$check_email->bind_param("s", $email);
$check_email->execute();
$check_email->store_result();

if ($check_email->num_rows > 0) {
    die("Error: Email already exists! Try logging in.");
}

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Insert user into the database using a prepared statement
$stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);

if ($stmt->execute()) {
    // Auto-login after registration
    $_SESSION["user_id"] = $stmt->insert_id;
    $_SESSION["name"] = $name;
    $_SESSION["email"] = $email;
    $_SESSION["user_type"] = 'student'; // Default role

    echo "Success: Registration complete! Redirecting...";
    header("Location: dashboard.php");
    exit();
} else {
    die("Error inserting user: " . $stmt->error);
}

// Close database connections
$stmt->close();
$check_email->close();
$conn->close();
?>
