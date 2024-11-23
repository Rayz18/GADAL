<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destroy the session
session_unset();
session_destroy();

// Redirect to Home.php
header('Location: /GADAL/Home.php');
exit();