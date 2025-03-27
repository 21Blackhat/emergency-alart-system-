<?php
session_start();
require 'config.php';
require 'vendor/autoload.php'; // Twilio SDK & PHPMailer

use Twilio\Rest\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents("php://input"), true);

if (isset($_SESSION["user_id"]) && !empty($data["location"])) {
    $user_id = $_SESSION["user_id"];
    $location = $data["location"];

    // Insert emergency report
    $stmt = $conn->prepare("INSERT INTO emergencies (user_id, location, status) VALUES (?, ?, 'Pending')");
    $stmt->bind_param("is", $user_id, $location);
    if ($stmt->execute()) {
        $emergency_id = $stmt->insert_id;

        // Fetch user name
        $user_stmt = $conn->prepare("SELECT name FROM users WHERE id=?");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_stmt->bind_result($user_name);
        $user_stmt->fetch();
        $user_stmt->close();

        // Fetch responder contacts
        $responder_query = "SELECT email, phone FROM users WHERE user_type='responder'";
        $result = $conn->query($responder_query);
        while ($responder = $result->fetch_assoc()) {
            sendEmailNotification($responder["email"], $user_name, $location);
            sendSmsNotification($responder["phone"], $user_name, $location);
        }

        // Notify responders in real time
        file_get_contents("http://localhost:3000/notify", false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode(["location" => $location, "user_name" => $user_name, "emergency_id" => $emergency_id])
            ]
        ]));

        echo "Emergency reported and responders notified!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Function to Send Email Notification
function sendEmailNotification($email, $user_name, $location) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';
        $mail->Password = 'your-email-password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('your-email@gmail.com', 'Emergency Alert System');
        $mail->addAddress($email);

        $mail->Subject = '🚨 New Emergency Alert!';
        $mail->Body = "Hello,\n\nAn emergency has been reported by $user_name at location: $location.\n\nPlease respond immediately.";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email error: " . $mail->ErrorInfo);
    }
}

// Function to Send SMS Notification
function sendSmsNotification($phone, $user_name, $location) {
    $twilio_sid = "your-twilio-account-sid";
    $twilio_token = "your-twilio-auth-token";
    $twilio_number = "your-twilio-phone-number";

    $client = new Client($twilio_sid, $twilio_token);
    try {
        $client->messages->create(
            $phone,
            ["from" => $twilio_number, "body" => "🚨 Emergency Alert! $user_name reported an emergency at $location."]
        );
    } catch (Exception $e) {
        error_log("SMS error: " . $e->getMessage());
    }
}
?>
