<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube";  // Check if this matches exactly in phpMyAdmin


// Connect to database
$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Ensure form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method. You must submit the form.");
}

// Get form data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Check if email and password are empty
if (empty($email) || empty($password)) {
    die("Error: All fields are required.");
}

// Fetch user data from database
$sql = "SELECT id, username, password, failed_attempts, last_attempt FROM account WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $username, $hashed_password, $failed_attempts, $last_attempt);
    $stmt->fetch();

    // Check if the user is blocked for 10 minutes
    if ($failed_attempts >= 5) {
        $current_time = time();
        $blocked_until = strtotime($last_attempt) + (10 * 60); // Block for 10 minutes

        if ($current_time < $blocked_until) {
            die("Too many failed attempts. Try again after 10 minutes.");
        } else {
            // Reset failed attempts after 10 minutes
            $reset_sql = "UPDATE account SET failed_attempts = 0 WHERE email = ?";
            $reset_stmt = $conn->prepare($reset_sql);
            $reset_stmt->bind_param("s", $email);
            $reset_stmt->execute();
        }
    }

    // Verify password
    if (password_verify($password, $hashed_password)) {
        // Start user session
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;

        // Reset failed attempts on successful login
        $reset_sql = "UPDATE account SET failed_attempts = 0 WHERE email = ?";
        $reset_stmt = $conn->prepare($reset_sql);
        $reset_stmt->bind_param("s", $email);
        $reset_stmt->execute();

        // Redirect to home page
        header("Location: home.php");
        exit();
    } else {
        // Increment failed attempts
        $failed_attempts++;
        $update_sql = "UPDATE account SET failed_attempts = ?, last_attempt = NOW() WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("is", $failed_attempts, $email);
        $update_stmt->execute();

        $remaining_attempts = 5 - $failed_attempts;
        die("Invalid credentials! Attempts left: $remaining_attempts");
    }
} else {
    die("No user found with this email.");
}

$stmt->close();
$conn->close();
?>
