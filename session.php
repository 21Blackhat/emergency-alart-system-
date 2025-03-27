<?php
session_start();
if (isset($_SESSION["user_id"])) {
    echo json_encode([
        "logged_in" => true,
        "username" => $_SESSION["name"],
        "is_admin" => ($_SESSION["user_type"] == "admin")
    ]);
} else {
    echo json_encode(["logged_in" => false]);
}
?>
