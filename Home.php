<?php
// Include the database configuration file
if (file_exists('./config/config.php')) {
    include './config/config.php';
} else {
    die("Config file not found!");
}

// Check if the database connection is established
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Query to fetch approved Face to Face courses
$queryFaceToFace = "SELECT program_id, course_name, course_img, course_date FROM courses WHERE status = 'approved' AND offered_mode = 'face_to_face' ORDER BY course_date DESC";
$resultFaceToFace = $conn->query($queryFaceToFace);

// Query to fetch approved Online courses
$queryOnline = "SELECT program_id, course_name, course_img, course_date FROM courses WHERE status = 'approved' AND offered_mode = 'Online' ORDER BY course_date DESC";
$resultOnline = $conn->query($queryOnline);

// Check if the queries were successful
if (!$resultFaceToFace || !$resultOnline) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="./public/assets/css/LearnerNavBar.css">
    <link rel="stylesheet" href="./public/assets/css/Home.css">
</head>

<body>
    <?php include './public/includes/LearnerNavBar.php'; ?>

    <!-- Updated Image Slider Section -->
    <div class="custom-slider-container">
        <div class="custom-slider">
            <div class="custom-slide fade">
                <img src="./public/assets/images/A.jpg" class="custom-slider-image" alt="Image A">
            </div>
            <div class="custom-slide fade">
                <img src="./public/assets/images/B.jpg" class="custom-slider-image" alt="Image B">
            </div>
            <div class="custom-slide fade">
                <img src="./public/assets/images/C.jpg" class="custom-slider-image" alt="Image C">
            </div>
        </div>
        <div class="mission-statement" style="animation-delay: 1s;">
            <p>The Batangas State University's Gender and Development Office aims to provide gender-responsive programs, projects, and services that address the needs and concerns of men and women.</p>
        </div>
    </div>

    <!-- Courses Offered: Face-to-Face -->
    <div class="latest-trainings-wrapper face-to-face-wrapper">
        <h2 class="section-title">Face-to-Face Courses</h2>
        <div class="trainings-container face-to-face-container">
            <?php
            $displayedCards = 0;

            if ($resultFaceToFace->num_rows > 0) {
                while ($row = $resultFaceToFace->fetch_assoc()) {
                    $program_id = $row['program_id'];
                    $course_name = $row['course_name'];
                    $course_img = "./staff/upload/" . $row['course_img'];
                    $course_date = date('F d, Y', strtotime($row['course_date']));

                    $hiddenClass = $displayedCards >= 4 ? 'hidden-card' : '';
                    echo "
                    <div class='training-card face-to-face-card $hiddenClass'>
                        <div class='card-inner'>
                            <!-- Ribbon -->
                            <div class='ribbon ribbon-available'>
                                <span>Available</span>
                            </div>
                            <!-- Card Front -->
                            <div class='card-front' style='background-image: url($course_img);'></div>
                            <!-- Card Back -->
                            <div class='card-back'>
                                <h3 class='course-title'>
                                    <span class='quote'>&quot;</span>
                                    <span class='course-name'>$course_name</span>
                                    <span class='quote'>&quot;</span>
                                </h3>
                                <p class='course-date'>$course_date</p>
                                <a href='../GADAL/learner/pages/Course.php?program_id=$program_id' class='view-course-btn'>View Course</a>
                            </div>
                        </div>
                    </div>
                    ";
                    $displayedCards++;
                }
            }

            // Add placeholder cards if less than 4 courses
            while ($displayedCards < 4) {
                echo "
                <div class='training-card face-to-face-card'>
                    <div class='card-inner'>
                        <div class='ribbon ribbon-pending'>
                            <span>Coming Soon</span>
                        </div>
                        <div class='card-front' style='background-color: #D3D3D3;'></div>
                        <div class='card-back'>
                            <p>To be approved.</p>
                            <a href='#' class='view-course'>Learn More</a>
                        </div>
                    </div>
                </div>
                ";
                $displayedCards++;
            }
            ?>
        </div>
        <div class="toggle-btn-container">
            <button id="toggle-face-to-face" class="toggle-cards-btn">See More</button>
        </div>
    </div>

    <!-- Courses Offered: Online -->
    <div class="latest-trainings-wrapper online-wrapper">
        <h2 class="section-title">Online Courses</h2>
        <div class="trainings-container online-container">
            <?php
            $displayedCards = 0;

            if ($resultOnline->num_rows > 0) {
                while ($row = $resultOnline->fetch_assoc()) {
                    $program_id = $row['program_id'];
                    $course_name = $row['course_name'];
                    $course_img = "./staff/upload/" . $row['course_img'];
                    $course_date = date('F d, Y', strtotime($row['course_date']));

                    $hiddenClass = $displayedCards >= 4 ? 'hidden-card' : '';
                    echo "
                    <div class='training-card online-card $hiddenClass'>
                        <div class='card-inner'>
                            <!-- Ribbon -->
                            <div class='ribbon ribbon-available'>
                                <span>Available</span>
                            </div>
                            <!-- Card Front -->
                            <div class='card-front' style='background-image: url($course_img);'></div>
                            <!-- Card Back -->
                            <div class='card-back'>
                                <h3 class='course-title'>
                                    <span class='quote'>&quot;</span>
                                    <span class='course-name'>$course_name</span>
                                    <span class='quote'>&quot;</span>
                                </h3>
                                <p class='course-date'>$course_date</p>
                                <a href='../GADAL/learner/pages/Course.php?program_id=$program_id' class='view-course-btn'>View Course</a>
                            </div>
                        </div>
                    </div>
                    ";
                    $displayedCards++;
                }
            }

            // Add placeholder cards if less than 4 courses
            while ($displayedCards < 4) {
                echo "
                <div class='training-card online-card'>
                    <div class='card-inner'>
                        <div class='ribbon ribbon-pending'>
                            <span>Coming Soon</span>
                        </div>
                        <div class='card-front' style='background-color: #D3D3D3;'></div>
                        <div class='card-back'>
                            <p>To be approved.</p>
                            <a href='#' class='view-course'>Learn More</a>
                        </div>
                    </div>
                </div>
                ";
                $displayedCards++;
            }
            ?>
        </div>
        <div class="toggle-btn-container">
            <button id="toggle-online" class="toggle-cards-btn">See More</button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
    const toggleFaceToFaceBtn = document.getElementById('toggle-face-to-face');
    const toggleOnlineBtn = document.getElementById('toggle-online');

    const faceToFaceCards = document.querySelectorAll('.face-to-face-card');
    const onlineCards = document.querySelectorAll('.online-card');

    const toggleVisibility = (button, cards) => {
        if (button.textContent === 'See More') {
            cards.forEach(card => card.classList.remove('hidden-card')); // Show all cards
            button.textContent = 'See Less';
        } else {
            cards.forEach((card, index) => {
                if (index >= 4) card.classList.add('hidden-card'); // Hide cards after the 4th
            });
            button.textContent = 'See More';
        }
    };

    toggleFaceToFaceBtn.addEventListener('click', () => toggleVisibility(toggleFaceToFaceBtn, faceToFaceCards));
    toggleOnlineBtn.addEventListener('click', () => toggleVisibility(toggleOnlineBtn, onlineCards));
});

    </script>
    <scrip>    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let slideIndex = 0;

            function showSlides() {
                const slides = document.querySelectorAll(".custom-slide");
                slides.forEach(slide => slide.style.display = "none");
                slideIndex++;
                if (slideIndex > slides.length) {
                    slideIndex = 1;
                }
                slides[slideIndex - 1].style.display = "block";
                setTimeout(showSlides, 2500); // Change image every 3 seconds
            }

            showSlides();

            // Intersection Observer for Mission Statement Animation
            const missionStatement = document.querySelector('.mission-statement');

            if (missionStatement) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            missionStatement.classList.add('animate');
                        }
                    });
                });

                observer.observe(missionStatement);
            } else {
                console.error('Mission statement element not found');
            }
        });
    </script>
</body>
</html>
