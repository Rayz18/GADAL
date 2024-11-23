<?php
// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Navigation Bar -->
<nav class="user-navigation-bar">
    <!-- Logo Section -->
    <div class="user-nav-logo">
        <!-- Use absolute paths for logos -->
        <img src="/GADAL/public/assets/images/BSU.png" alt="BSU Logo" class="user-logo">
        <img src="/GADAL/public/assets/images/GAD.png" alt="GAD Logo" class="user-logo">
        <div class="user-nav-titles">
            <span class="user-nav-title main-title">BATANGAS STATE UNIVERSITY - THE NATIONAL ENGINEERING
                UNIVERSITY</span>
            <span class="user-nav-title sub-title">Gender and Development Unit</span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <ul class="user-nav-menu">
        <!-- Use absolute paths for menu links -->
        <li><a href="/GADAL/Home.php">Home</a></li>
        <li><a href="/GADAL/learner/pages/AboutUs.php">About Us</a></li>
        <li><a href="/GADAL/learner/pages/Program.php">Program</a></li>
        <li><a href="/GADAL/learner/pages/Certificate.php">Certificate</a></li>
        <li><a href="/GADAL/learner/pages/ConnectWithUs.php">Connect With Us</a></li>
    </ul>

    <!-- Profile Icon -->
    <div class="user-profile-icon" onclick="togglePopup()">
        <!-- Use absolute path for profile icon -->
        <img src="/GADAL/public/assets/images/icon.png" alt="Profile" class="user-profile">
    </div>

    <!-- Profile Popup -->
    <div id="profile-popup" class="profile-popup" style="display: none;">
        <div class="popup-content">
            <?php if (isset($_SESSION['learner_id'])): ?>
                <!-- If the learner is logged in, show the Logout button -->
                <h2 class="popup-title">WELCOME BACK!</h2>
                <button onclick="logout()" class="popup-btn logout-btn">LOGOUT</button>
            <?php else: ?>
                <!-- If the learner is not logged in, show the Sign In and Sign Up options -->
                <h2 class="popup-title">WELCOME!</h2>
                <p class="popup-text">Sign in to your account now.</p>
                <button class="popup-btn signin-btn">SIGN IN</button>
                <div class="separator"><span>OR</span></div>
                <button class="popup-btn signup-btn">SIGN UP</button>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Toggle the visibility of the profile popup
        function togglePopup() {
            const popup = document.getElementById('profile-popup');
            popup.style.display = (popup.style.display === "none" || popup.style.display === "") ? "block" : "none";
        }

        // Event listeners to redirect to login and sign-up pages if not logged in
        document.querySelector('.signin-btn').addEventListener('click', function () {
            window.location.href = '/GADAL/learner/pages/login.php';
        });

        document.querySelector('.signup-btn').addEventListener('click', function () {
            window.location.href = '/GADAL/learner/pages/sign-up.php';
        });

        // Logout function to destroy session and redirect to login page
        function logout() {
            // Send a request to logout.php to handle session destruction
            window.location.href = '/GADAL/learner/pages/logout.php';
        }
    </script>
</nav>