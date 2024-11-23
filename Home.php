<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="./includes/assets/LearnerNavBar.css">
    <link rel="stylesheet" href="./learner/assets/css/Home.css">
</head>

<body>
    <?php include './includes/LearnerNavBar.php'; ?>
    <!-- Image Slider Section -->
    <div class="slider-container">
        <div class="slider">
            <div class="slide fade">
                <img src="/GADAL/assets/images/A.jpg" class="slider-image">
            </div>
            <div class="slide fade">
                <img src="/GADAL/assets/images/B.jpg" class="slider-image">
            </div>
            <div class="slide fade">
                <img src="/GADAL/assets/images/C.jpg" class="slider-image">
            </div>
        </div>
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
    </div>

    <!-- JavaScript for interactivity -->
    <script>
        let slideIndex = 0;
        let slideInterval;

        // Function to show the slides
        function showSlides() {
            let slides = document.getElementsByClassName("slide");
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndex++;
            if (slideIndex > slides.length) {
                slideIndex = 1;
            }
            slides[slideIndex - 1].style.display = "block";
            slideInterval = setTimeout(showSlides, 2000); // Change image every 2 seconds
        }

        // Function to manually navigate slides
        function plusSlides(n) {
            clearTimeout(slideInterval); // Clear the current timer
            slideIndex += n - 1; // Adjust slide index
            showSlides(); // Call showSlides to display the correct slide
        }

        // Start the slideshow
        showSlides();
    </script>
</body>

</html>