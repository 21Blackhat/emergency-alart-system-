<?php
$plain_password = "admin123";
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

echo "Hashed Password: " . $hashed_password . "<br>";

if (password_verify($plain_password, $hashed_password)) {
    echo "✅ Password verification SUCCESS!";
} else {
    echo "❌ Password verification FAILED!";
}
?>
