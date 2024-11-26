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

// Questions for evaluation
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .description-box {
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 16px;
            color: #333;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="text-center mb-4">
            <h1 class="text-primary">Evaluation Form</h1>
            <h3><?php echo $course_name; ?></h3>
        </div>

        <div class="form-container mx-auto">
            <div class="description-box">
                <p>Dear Participants, <br> Please evaluate the training/seminar in accordance with the criteria
                    specified below. We assure you that your responses will be kept in strict confidentiality. Thank
                    you!</p>
            </div>

            <form action="submit_evaluation.php" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                <?php foreach ($questions as $index => $question): ?>
                    <div class="mb-3">
                        <label class="form-label"><?php echo ($index + 1) . ". " . htmlspecialchars($question); ?></label>
                        <select name="question_<?php echo $index + 1; ?>" class="form-select" required>
                            <option value="" disabled selected>Select a rating</option>
                            <option value="5">5 - Outstanding</option>
                            <option value="4">4 - Very Satisfactory</option>
                            <option value="3">3 - Satisfactory</option>
                            <option value="2">2 - Unsatisfactory</option>
                            <option value="1">1 - Poor</option>
                        </select>
                    </div>
                <?php endforeach; ?>

                <div class="mb-3">
                    <label for="helpful_feedback" class="form-label">Was the training helpful for you in the practice of
                        your profession? Why or why not?</label>
                    <textarea id="helpful_feedback" name="helpful_feedback" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="helpful_aspect" class="form-label">What aspect of the training has been helpful to you?
                        What other topics would you suggest for future trainings?</label>
                    <textarea id="helpful_aspect" name="helpful_aspect" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="comments" class="form-label">Comments/Commendations/Complaints</label>
                    <textarea id="comments" name="comments" class="form-control" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>