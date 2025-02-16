<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "youtube";

    // Get form data
    $username = filter_input(INPUT_POST, 'username');
    $password = filter_input(INPUT_POST, 'password');

    // Ensure fields are not empty
    if (!empty($username) && !empty($password)) {
        $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        } else {
            $sql = "INSERT INTO account (username, password) VALUES ('$username', '$password')";
            if ($conn->query($sql)) {
                echo "New record inserted successfully";
            } else {
                echo "Error inserting record: " . $conn->error;
            }
        }
        $conn->close();
    } else {
        echo "Username and password cannot be empty.";
    }
} else {
    die("405 Method Not Allowed: Please submit the form.");
}
?>
