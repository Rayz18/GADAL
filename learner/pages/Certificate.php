<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificates</title>
    <link rel="stylesheet" href="../../public/assets/css/LearnerNavBar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

</head>

<body>
    <?php include '../../public/includes/LearnerNavBar.php'; ?>
    <br>
    <br>
    <br>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1 class="h1">CERTIFICATES</h1>
        </div>
        <!-- Filter Section -->
        <div class="d-flex justify-content-end align-items-center gap-3 mb-4">
            <label for="offering" class="form-label">Offering</label>
            <select id="offering" class="form-select w-auto" onchange="filterCertificates()">
                <option value="all">All</option>
                <option value="training">Training</option>
                <option value="course">Course</option>
            </select>
        </div>

        <!-- List of certificates -->
        <ul class="list-unstyled certificate-list">
            <li class="mb-4" data-category="training">
                <a href="certificate1.php" class="text-decoration-none text-primary">Webinar on Laws on Gender-Based Violence</a>
                <p class="text-muted">Issued on January 02, 2024</p>
            </li>
            <li class="mb-4" data-category="training">
                <a href="#" class="text-decoration-none text-primary">Webinar on mghfdignfj</a>
                <p class="text-muted">Issued on January 01, 2024</p>
            </li>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterCertificates() {
            const selectedOffering = document.getElementById("offering").value;
            const certificates = document.querySelectorAll(".certificate-list li");

            certificates.forEach(cert => {
                const category = cert.getAttribute("data-category");

                if (selectedOffering === "all" || category === selectedOffering) {
                    cert.style.display = ""; // Show the certificate
                } else {
                    cert.style.display = "none"; // Hide the certificate
                }
            });
        }

        window.onload = filterCertificates;
    </script>
    <br>
    <br>
    <br>
    <br>
    <?php include '../../public/includes/footer.php'; ?>
</body>

</html>
