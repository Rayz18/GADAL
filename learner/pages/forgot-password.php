<?php
session_start();
require_once '../../config/config.php';

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
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['learner_id'] = $row['learner_id'];

        // Format contact number to Philippines format
        $formatted_contact_number = '+63' . substr($contact_number, 1);

        // Send OTP via PhilSMS API
        $api_url = "https://app.philsms.com/api/v3/sms/send";
        $api_token = "1209|3B0dU7ohHlLeMp8QexNR4oUA64R1Bb0Vs6ea2srV";
        $message = "Your OTP for password reset is: $otp";
        $sender_id = "PhilSMS";

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

        $response_data = json_decode($response, true);
        if ($response_data['status'] == 'success') {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../learner/assets/css/forgot-password.css">
    <link rel="stylesheet" href="../../learner/assets/css/login.css">
</head>

<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row justify-content-between align-items-center w-100">
        <!-- Logo and Heading Section (Left) -->
        <div class="col-md-6 d-flex flex-column align-items-start p-3">
            <div class="d-flex mb-3">
                <img src="../../public/assets/images/BSU.png" alt="University Logo" class="logo me-2">
                <img src="../../public/assets/images/GAD.png" alt="Department Logo" class="logo">
            </div>
            <h1 class="text-white display-4 fw-bold quote-text">Empowering Equality, Advancing Development</h1>
        </div>

        <!-- Forgot Password Form Section (Right) -->
        <div class="col-md-4">
            <div class="card login-card p-4 shadow-sm d-flex flex-column justify-content-between">
                <h2 class="text-center mb-4">Forgot Password</h2>
                <form action="forgot-password.php" method="POST" class="d-flex flex-column h-100">
                    <div class="mb-3">
                        <input type="text" id="contact_number" name="contact_number" class="form-control" placeholder="Enter your contact number" required>
                    </div>
                    <!-- Error message placed below the input field -->
                    <?php if (!empty($error_message)): ?>
                        <p id="error-message" class="text-danger error-message"><?= htmlspecialchars($error_message) ?></p>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary w-100 mt-auto">Send OTP</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Automatically hide the error message after 4 seconds
    document.addEventListener("DOMContentLoaded", () => {
        const errorMessage = document.getElementById("error-message");
        if (errorMessage) {
            setTimeout(() => {
                errorMessage.style.display = "none";
            }, 3000); // 3 seconds
        }
    });
</script>
</body>

</html>
