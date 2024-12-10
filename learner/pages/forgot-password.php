<?php
// Include database connection
include '../../config/config.php';

session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_number = $_POST['contact_number'];

    // Check if the contact number exists in the database
    $sql = "SELECT learner_id, contact_number FROM learners WHERE contact_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $contact_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Generate OTP
        $otp = rand(100000, 999999); // Generate a random 6-digit OTP
        $_SESSION['otp'] = $otp; // Store OTP in session
        $_SESSION['learner_id'] = $row['learner_id']; // Store learner ID in session

        // Format contact number to Philippines format (e.g., 09171234567 to +639171234567)
        $formatted_contact_number = '+63' . substr($contact_number, 1);

        // Send OTP via SMS using PhilSMS API
        $api_url = "https://app.philsms.com/api/v3/sms/send";
        $api_token = "1209|3B0dU7ohHlLeMp8QexNR4oUA64R1Bb0Vs6ea2srV"; // Your API token
        $message = "Your OTP for password reset is: $otp";
        $sender_id = "PhilSMS"; // Replace with your sender ID

        $data = [
            "recipient" => $formatted_contact_number,
            "sender_id" => $sender_id,
            "type" => "plain",
            "message" => $message,
        ];

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $api_token",
            "Content-Type: application/json",
            "Accept: application/json",
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        // Handle the response
        $response_data = json_decode($response, true);
        if ($response_data['status'] == 'success') {
            // OTP sent successfully, redirect to OTP verification page
            header('Location: verify-otp.php');
            exit;
        } else {
            $error_message = "Error sending OTP: " . $response_data['message'];
        }
    } else {
        $error_message = "No account found with that contact number!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../../learner/assets/css/login.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2 class="login-title">Forgot Password</h2>
            <form action="#" method="POST">
                <div class="form-group">
                    <input type="text" id="contact_number" name="contact_number" class="form-input" placeholder="Enter your contact number" required>
                </div>
                <div class="error-message-container">
                    <?php if (isset($error_message)): ?>
                        <p class="error-message"><?= $error_message ?></p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="submit-btn">Send OTP</button>
            </form>
        </div>
    </div>
</body>
</html>
