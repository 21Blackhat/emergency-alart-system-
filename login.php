<?php
require 'db.php'; // db.php already starts the session if needed

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("405 Method Not Allowed");
}

// Secure input function
function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Get login form data and sanitize
$email = cleanInput($_POST["email"]);
$password = cleanInput($_POST["password"]);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Error: Invalid email format.");
}

// Fetch user details using prepared statement
$stmt = $conn->prepare("SELECT id, name, password, user_type FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $name, $hashed_password, $user_type);
$stmt->fetch();

// Verify password
if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
    $_SESSION["user_id"] = $id;
    $_SESSION["name"] = $name;
    $_SESSION["user_type"] = $user_type;

    if ($user_type == "admin") {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
} else {
    echo "Error: Invalid email or password!";
}

// Close connections
$stmt->close();
$conn->close();
?>
