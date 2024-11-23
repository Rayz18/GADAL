<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificates</title>
    <link rel="stylesheet" href="../../includes/assets/LearnerNavBar.css">
    <link rel="stylesheet" href="../../learner/assets/css/Certificate.css">
</head>

<body>
    <?php include '../../includes/LearnerNavBar.php'; ?>
    <!-- Main Certificate Listing Page -->
    <div class="container">
        <h1>CERTIFICATES</h1>

        <div class="filters">
            <label for="offering">Offering</label>
            <select id="offering" onchange="filterCertificates()">
                <option value="all">All</option>
                <option value="training">Training</option>
                <option value="course">Course</option>
            </select>
        </div>

        <!-- List of certificates -->
        <ul class="certificate-list">
            <li data-category="training">
                <a href="certificate1.php">Webinar on Laws on Gender-Based Violence</a>
                <p>Issued on January 02, 2024</p>
            </li>
            <li data-category="training">
                <a href="#">Webinar on mghfdignfj</a>
                <p>Issued on January 01, 2024</p>
            </li>
            <!-- Add more certificates as needed -->
        </ul>
    </div>

    <script>
        function filterCertificates() {
            const selectedOffering = document.getElementById("offering").value;
            const certificates = document.querySelectorAll(".certificate-list li");

            certificates.forEach(cert => {
                const category = cert.getAttribute("data-category");

                if (selectedOffering === "all" || category === selectedOffering) {
                    cert.style.display = "";  // Show the certificate
                } else {
                    cert.style.display = "none";  // Hide the certificate
                }
            });
        }

        // Call the function initially to apply the default filter (show all)
        window.onload = filterCertificates;
    </script>
</body>

</html>