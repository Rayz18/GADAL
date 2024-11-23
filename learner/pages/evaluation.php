<?php
include '../../config/config.php';
session_start();

$seminar_id = $_GET['seminar_id'] ?? null;
$learner_id = $_SESSION['learner_id'] ?? null;
$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    $stmt = $conn->prepare("SELECT course_id FROM seminars WHERE seminar_id = ?");
    $stmt->bind_param("i", $seminar_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seminar = $result->fetch_assoc();
    $course_id = $seminar['course_id'] ?? null;

    if (!$course_id) {
        die("Unable to retrieve course ID. Debug Info: seminar_id = " . htmlspecialchars($seminar_id));
    }
}

// Check if the learner has an attendance record for the seminar
$attendance_check = $conn->prepare("SELECT * FROM attendance WHERE seminar_id = ? AND learner_id = ?");
$attendance_check->bind_param("ii", $seminar_id, $learner_id);
$attendance_check->execute();
$has_attended = $attendance_check->get_result()->num_rows > 0;
$attendance_check->close();

if (!$has_attended) {
    die("You must submit attendance for this seminar to access evaluation.");
}

$seminar_title = 'Seminar';
$evaluation_instructions = '';

// Check if the learner has already submitted evaluation
$evaluation_check = $conn->prepare("SELECT * FROM evaluations WHERE seminar_id = ? AND learner_id = ?");
$evaluation_check->bind_param("ii", $seminar_id, $learner_id);
$evaluation_check->execute();
$evaluation_exists = $evaluation_check->get_result()->num_rows > 0;
$evaluation_check->close();

if ($evaluation_exists) {
    $already_evaluated = true;

    // Fetch seminar title for the evaluated seminar
    $title_stmt = $conn->prepare("SELECT seminar_title FROM seminars WHERE seminar_id = ? LIMIT 1");
    $title_stmt->bind_param("i", $seminar_id);
    $title_stmt->execute();
    $seminar_data = $title_stmt->get_result()->fetch_assoc();
    if ($seminar_data) {
        $seminar_title = $seminar_data['seminar_title'];
    }
    $title_stmt->close();
} else {
    $already_evaluated = false;

    // Fetch seminar title and evaluation instructions
    $title_stmt = $conn->prepare("SELECT seminar_title, evaluation_instructions FROM seminars WHERE seminar_id = ? LIMIT 1");
    $title_stmt->bind_param("i", $seminar_id);
    $title_stmt->execute();
    $seminar_data = $title_stmt->get_result()->fetch_assoc();

    if ($seminar_data) {
        $seminar_title = $seminar_data['seminar_title'];
        $evaluation_instructions = $seminar_data['evaluation_instructions'] ?? '';
    }
    $title_stmt->close();

    // Fetch custom fields for the seminar's evaluation
    $stmt = $conn->prepare("SELECT * FROM evaluation_fields WHERE seminar_id = ?");
    $stmt->bind_param("i", $seminar_id);
    $stmt->execute();
    $fields = $stmt->get_result();
    $fields_exist = $fields->num_rows > 0;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seminar Evaluation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .instruction-text {
            white-space: pre-wrap;
            word-break: break-word;
        }
    </style>
</head>

<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="bg-white col-md-6 col-lg-8 p-4 rounded shadow-sm">
        <h1 class="fs-5 fw-semibold mb-5">Evaluation</h1>
        <h2 class="fs-3 fw-bold mb-5"><?php echo htmlspecialchars($seminar_title); ?></h2>

        <?php if ($already_evaluated): ?>
            <div class="alert alert-info text-center">
                You've already submitted evaluation for the seminar "<?php echo htmlspecialchars($seminar_title); ?>"!
            </div>
            <a href="CourseContent.php?course_id=<?php echo htmlspecialchars($course_id); ?>&tab=seminar"
                class="btn btn-primary w-100 mt-3">View Seminar</a>
        <?php else: ?>
            <p class="fs-6 text-muted mb-4 instruction-text"><?php echo htmlspecialchars($evaluation_instructions); ?></p>

            <?php if (!$fields_exist): ?>
                <div class="alert alert-warning text-center">
                    Evaluation is not yet available.
                </div>
            <?php else: ?>
                <form action="submit_evaluation.php" method="POST">
                    <input type="hidden" name="seminar_id" value="<?php echo htmlspecialchars($seminar_id); ?>">
                    <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course_id); ?>">

                    <?php while ($field = $fields->fetch_assoc()): ?>
                        <div class="mb-3">
                            <label class="form-label">
                                <?php echo htmlspecialchars($field['field_label']); ?>
                                <?php if ($field['required']): ?><span class="text-danger">*</span><?php endif; ?>
                            </label>

                            <?php if ($field['field_type'] === 'text'): ?>
                                <input type="text" name="field_<?php echo $field['field_id']; ?>" class="form-control" <?php echo $field['required'] ? 'required' : ''; ?>>
                            <?php elseif ($field['field_type'] === 'textarea'): ?>
                                <textarea name="field_<?php echo $field['field_id']; ?>" class="form-control" <?php echo $field['required'] ? 'required' : ''; ?>></textarea>
                            <?php elseif ($field['field_type'] === 'radio' || $field['field_type'] === 'dropdown'): ?>
                                <?php foreach (json_decode($field['options']) as $option): ?>
                                    <div class="form-check">
                                        <input type="<?php echo $field['field_type'] === 'radio' ? 'radio' : 'checkbox'; ?>[]"
                                            name="field_<?php echo $field['field_id']; ?>" value="<?php echo htmlspecialchars($option); ?>"
                                            class="form-check-input">
                                        <label class="form-check-label"><?php echo htmlspecialchars($option); ?></label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4 py-2">SUBMIT</button>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>