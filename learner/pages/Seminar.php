<?php
session_start();
include '../../config/config.php';

// Check if learner is logged in
if (!isset($_SESSION['learner_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch approved seminars
$query = "SELECT * FROM seminars WHERE status = 'approved'";
$stmt = $conn->prepare($query);
$stmt->execute();
$seminars = $stmt->get_result();

$seminar_array = [];
while ($row = $seminars->fetch_assoc()) {
    $seminar_array[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seminars/Webinars</title>
    <link rel="stylesheet" href="../../learner/assets/css/seminar.css">
    <link rel="stylesheet" href="../../public/assets/css/LearnerNavBar.css">
</head>

<body>
    <?php include '../../public/includes/LearnerNavBar.php'; ?>

    <div class="content">
        <h1 class="page-title">Seminars/Webinars</h1>
        <div class="seminar-container">
            <?php if (count($seminar_array) > 0): ?>
                <?php foreach ($seminar_array as $seminar): ?>
                    <div class="seminar-item">
                        <img src="<?php echo htmlspecialchars($seminar['poster_path']); ?>" alt="Seminar Banner"
                            class="seminar-image">
                        <div class="seminar-text">
                            <h1 class="seminar-title"><?php echo htmlspecialchars($seminar['seminar_title']); ?></h1>
                            <p class="seminar-description"><?php echo nl2br(htmlspecialchars($seminar['description'])); ?></p>
                        </div>
                        <div class="seminar-details">
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($seminar['date']); ?></p>
                            <p><strong>Time:</strong> <?php echo htmlspecialchars($seminar['time']); ?></p>
                            <p><strong>Venue:</strong> <?php echo htmlspecialchars($seminar['venue']); ?></p>
                        </div>
                        <div class="seminar-buttons">
                            <?php if ($seminar['include_registration']): ?>
                                <a href="register.php?seminar_id=<?php echo $seminar['seminar_id']; ?>&course_id=<?php echo $seminar['course_id']; ?>"
                                    class="seminar-button">REGISTER</a>
                            <?php endif; ?>
                            <?php if ($seminar['include_attendance']): ?>
                                <a href="attendance.php?seminar_id=<?php echo $seminar['seminar_id']; ?>"
                                    class="seminar-button">ATTENDANCE</a>
                            <?php endif; ?>
                            <?php if ($seminar['include_evaluation']): ?>
                                <a href="evaluation.php?seminar_id=<?php echo $seminar['seminar_id']; ?>"
                                    class="seminar-button">EVALUATION</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No seminars available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>