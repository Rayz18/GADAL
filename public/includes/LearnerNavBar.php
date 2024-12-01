<?php
// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Desktop Navigation Bar -->
<nav class="user-navigation-bar">
    <div class="user-nav-logo">
        <img src="/GADAL/public/assets/images/BSU.png" alt="BSU Logo" class="user-logo">
        <img src="/GADAL/public/assets/images/GAD.png" alt="GAD Logo" class="user-logo">
        <div class="user-nav-titles">
            <span class="user-nav-title main-title">BATANGAS STATE UNIVERSITY - THE NATIONAL ENGINEERING UNIVERSITY</span>
            <span class="user-nav-title sub-title">Gender and Development Unit</span>
        </div>
    </div>

    <ul class="user-nav-menu">
        <li><a href="/GADAL/Home.php">Home</a></li>
        <li><a href="/GADAL/learner/pages/AboutUs.php">About Us</a></li>
        <li><a href="/GADAL/learner/pages/Program.php">Program</a></li>
        <li><a href="/GADAL/learner/pages/Certificate.php">Certificate</a></li>
        <li><a href="/GADAL/learner/pages/ConnectWithUs.php">Connect With Us</a></li>
    </ul>

    <div class="user-profile-icon" onclick="togglePopup()">
        <img src="/GADAL/public/assets/images/icon.png" alt="Profile" class="user-profile">
    </div>

    <div id="profile-popup" class="profile-popup" style="display: none;">
        <div class="popup-content">
            <?php if (isset($_SESSION['learner_id'])): ?>
                <h2 class="popup-title">WELCOME BACK!</h2>
                <button onclick="logout()" class="popup-btn logout-btn">LOGOUT</button>
            <?php else: ?>
                <h2 class="popup-title">WELCOME!</h2>
                <p class="popup-text">Sign in to your account now.</p>
                <button class="popup-btn signin-btn">SIGN IN</button>
                <div class="separator"><span>OR</span></div>
                <button class="popup-btn signup-btn">SIGN UP</button>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Mobile Header -->
<header class="mobile-header">
    <div class="mobile-header-logo">
        <img src="/GADAL/public/assets/images/BSU.png" alt="BSU Logo" class="mobile-logo">
        <img src="/GADAL/public/assets/images/GAD.png" alt="GAD Logo" class="mobile-logo">
    </div>
    <div class="mobile-header-titles">
        <span class="mobile-main-title">BATANGAS STATE UNIVERSITY - THE NATIONAL ENGINEERING UNIVERSITY</span>
        <span class="mobile-sub-title">Gender and Development Unit</span>
    </div>
</header>

<!-- Mobile Floating Menu -->
<div class="mobile-floating-menu">
    <button id="mobile-menu-toggle" class="floating-button">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </button>
</div>

<div id="mobile-overlay" class="mobile-overlay hidden">
    <div class="overlay-content">
        <ul class="mobile-menu">
            <li><a href="/GADAL/Home.php">Home</a></li>
            <li><a href="/GADAL/learner/pages/AboutUs.php">About Us</a></li>
            <li><a href="/GADAL/learner/pages/Program.php">Program</a></li>
            <li><a href="/GADAL/learner/pages/Certificate.php">Certificate</a></li>
            <li><a href="/GADAL/learner/pages/ConnectWithUs.php">Connect With Us</a></li>
            <li>
                <?php if (isset($_SESSION['learner_id'])): ?>
                    <a href="/GADAL/learner/pages/logout.php">Logout</a>
                <?php else: ?>
                    <a href="/GADAL/learner/pages/login.php">Sign In</a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</div>

<script>
    // Toggle Mobile Overlay
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileOverlay = document.getElementById('mobile-overlay');

    mobileMenuToggle.addEventListener('click', () => {
        mobileOverlay.classList.toggle('hidden'); // Toggle hidden class
    });

    // Toggle the visibility of the profile popup
    function togglePopup() {
        const popup = document.getElementById('profile-popup');
        popup.style.display = (popup.style.display === "none" || popup.style.display === "") ? "block" : "none";
    }

    // Logout function to destroy session and redirect to login page
    function logout() {
        window.location.href = '/GADAL/learner/pages/logout.php';
    }
</script>

<style>
/* Desktop Navbar */
.user-navigation-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #B19CD9;
    padding: 10px 20px;
    color: rgb(67, 24, 116);
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    border-bottom: 2px solid rgba(0, 0, 0, 0.1);
}

.user-nav-menu {
    display: flex;
    gap: 15px;
}

.user-nav-menu li a {
    color: white;
    text-decoration: none;
}

.user-profile-icon img {
    height: 33px;
}

/* Mobile Header */
.mobile-header {
    display: none;
    background-color: #B19CD9;
    color: rgb(67, 24, 116);
    padding: 10px 20px;
    text-align: center;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
}

.mobile-header-logo {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.mobile-header-logo img {
    height: 40px;
}

.mobile-header-titles {
    margin-top: 5px;
}

.mobile-main-title {
    font-size: 12px;
    font-weight: bold;
}

.mobile-sub-title {
    font-size: 10px;
    font-weight: normal;
}

/* Mobile Floating Button */
.mobile-floating-menu {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 2000;
}

.floating-button {
    background-color: #B19CD9;
    border: none;
    border-radius: 50%;
    width: 60px; /* Button width */
    height: 60px; /* Button height */
    display: flex;
    flex-direction: column; /* Stack lines vertically */
    align-items: center; /* Center lines horizontally */
    justify-content: center; /* Center lines vertically */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    position: relative;
    z-index: 2000;
}

.floating-button .bar {
    width: 30px; /* Line width */
    height: 4px; /* Line height */
    background-color: white; /* Line color */
    margin: 3px 0; /* Space between lines */
    border-radius: 2px; /* Slight rounding for a modern look */
    transition: all 0.3s ease; /* Smooth transition for hover effects */
}

.floating-button:hover .bar {
    background-color: #e6e6e6; /* Change color on hover for interactivity */
}

/* Mobile Overlay */
.mobile-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(110, 90, 160, 0.95); /* Even darker violet with slightly higher opacity */
    z-index: 1999;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    color: white;
}

.mobile-overlay.hidden {
    display: none;
}

.overlay-content {
    text-align: center;
}

.mobile-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mobile-menu li {
    margin: 15px 0;
}

.mobile-menu li a {
    color: white;
    text-decoration: none;
    font-size: 1.5rem;
    font-weight: bold;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .user-navigation-bar {
        display: none;
    }

    .mobile-header {
        display: block;
    }

    .mobile-floating-menu {
        display: block;
    }
}
</style>