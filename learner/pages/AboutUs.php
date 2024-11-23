<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="../../public/assets/css/LearnerNavBar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../learner/assets/css/AboutUs.css">
</head>

<body>
    <?php include '../../public/includes/LearnerNavBar.php'; ?>

    <!-- Header Image Section -->
    <div class="container-fluid p-0">
        <img src="../../public/assets/images/Gender.jpg" alt="Header" class="img-fluid w-100">
    </div>

    <!-- Content Section -->
    <div class="container mt-4">
        <div class="row">

            <!-- Sidebar Section -->
            <aside class="col-md-3">
                <div class="card">
                    <div class="card-header text-white text-center" style="background-color: #B19CD9;">
                        ABOUT US
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item list-group-item-action" onclick="showSection('goals')">Goals</li>
                        <li class="list-group-item list-group-item-action"
                            onclick="showSection('organization-profile')">Organization Profile</li>
                        <li class="list-group-item list-group-item-action" onclick="showSection('privacy-notice')">
                            Privacy Notice</li>
                        <li class="list-group-item list-group-item-action" onclick="showSection('laws-and-policies')">
                            Laws and Policies</li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Section -->
            <main class="col-md-9">
                <div id="goals" class="section-container">
                    <h2 class="text-primary">GOALS</h2>
                    <p>The Gender and Development Unit at Batangas State University aims to promote gender equality
                        through policies, programs, and initiatives that empower women and men alike.</p>
                </div>

                <div id="organization-profile" class="section-container d-none">
                    <!-- Independent Containers for Organization Profile Subsections -->
                    <div class="about-gad mb-4">
                        <h2 class="text-primary">About Gender and Development</h2>
                        <p>The Gender and Development (GAD) Unit seeks to address the various needs and issues related
                            to gender equality, creating an inclusive environment for everyone at Batangas State
                            University.</p>
                        <p>Our goal is to mainstream gender issues and promote gender-responsive governance through
                            active collaboration with different university units and external partners.</p>
                    </div>

                    <div class="gad-plan-budget mb-4">
                        <h2 class="text-primary">GAD Plan and Budget</h2>
                        <p>Our GAD Plan and Budget includes strategic initiatives to address gender issues through
                            sustainable projects and capacity-building activities.</p>
                    </div>

                    <div class="gad-focal-system mb-4">
                        <h2 class="text-primary">GAD Focal Person System</h2>
                        <p>The GAD Focal Person System is established to coordinate and implement gender programs within
                            various university departments.</p>
                    </div>

                    <div class="project-activity-program mb-4">
                        <h2 class="text-primary">Project, Activity, and Program</h2>
                        <p>A variety of projects, activities, and programs are organized to address gender issues and
                            advocate for equality within the campus and community.</p>
                    </div>
                </div>

                <div id="privacy-notice" class="section-container d-none">
                    <h2 class="text-primary">PRIVACY NOTICE</h2>
                    <p>Our privacy notice outlines the information we collect, how it is used, and the steps we take to
                        ensure the protection of your data.</p>
                </div>

                <div id="laws-and-policies" class="section-container d-none">
                    <h2 class="text-primary">LAWS AND POLICIES</h2>
                    <p>Learn more about the national laws and institutional policies supporting gender equality and
                        development.</p>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript to toggle sections
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.section-container');
            sections.forEach(section => section.classList.add('d-none'));
            document.getElementById(sectionId).classList.remove('d-none');
        }
    </script>
</body>

</html>