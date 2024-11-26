<?php
session_start();
include '../../config/config.php';

// Validate course_id
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Fetch course details
$course_query = $conn->prepare("SELECT course_name FROM courses WHERE course_id = ?");
$course_query->bind_param("i", $course_id);
$course_query->execute();
$course_result = $course_query->get_result();
$course = $course_result->fetch_assoc();
$course_name = $course ? htmlspecialchars($course['course_name']) : "Unknown Course";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center text-primary mb-4">Evaluation Form</h1>
        <h3 class="text-center mb-4"><?php echo $course_name; ?></h3>

        <form>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" class="form-control" readonly>
The evaluation form is designed to assess the overall effectiveness and quality of the course. Please review each question carefully and provide your feedback.
                </textarea>
            </div>

            <?php
            $questions = [
                "Overall, how would you rate the seminar/training?",
                "How would you rate the appropriateness of time and the proper use of resources provided?",
                "Objectives and expectations were clearly communicated and achieved.",
                "Session activities were appropriate and relevant to the achievement of the learning objectives.",
                "Sufficient time was allotted for group discussion and comments.",
                "Materials and audio-visual aids provided were useful.",
                "The resource person/trainer displayed thorough knowledge of, and provided relevant insights on the topic/s discussed.",
                "The resource person/trainer thoroughly explained and processed the learning activities throughout the training.",
                "The resource person/trainer created a good learning environment, sustained the attention of the participants, and encouraged their participation in the training duration.",
                "The resource person/trainer managed the time well, including some adjustments in the training schedule, if needed.",
                "The resource person/trainer demonstrated keenness to the participants’ needs and other requirements related to the training.",
                "The venue or platform used was conducive for learning."
            ];

            foreach ($questions as $index => $question) {
                echo '
                    <div class="mb-3">
                        <label class="form-label">' . ($index + 1) . '. ' . htmlspecialchars($question) . '</label>
                        <select class="form-select" readonly>
                            <option value="5">5 - Outstanding</option>
                            <option value="4">4 - Very Satisfactory</option>
                            <option value="3">3 - Satisfactory</option>
                            <option value="2">2 - Unsatisfactory</option>
                            <option value="1">1 - Poor</option>
                        </select>
                    </div>
                ';
            }
            ?>

            <div class="mb-3">
                <label for="helpful_feedback" class="form-label">Was the training helpful for you in the practice of
                    your profession? Why or why not?</label>
                <textarea id="helpful_feedback" class="form-control" readonly>
Yes/No (Explanation)
                </textarea>
            </div>

            <div class="mb-3">
                <label for="helpful_aspect" class="form-label">What aspect of the training has been helpful to you? What
                    other topics would you suggest for future trainings?</label>
                <textarea id="helpful_aspect" class="form-control" readonly>
Feedback
                </textarea>
            </div>

            <div class="mb-3">
                <label for="comments" class="form-label">Comments/Commendations/Complaints</label>
                <textarea id="comments" class="form-control" readonly>
Additional comments
                </textarea>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>