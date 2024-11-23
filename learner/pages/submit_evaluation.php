<?php
include '../../config/config.php';
session_start();

$seminar_id = $_POST['seminar_id'] ?? null;
$learner_id = $_SESSION['learner_id'] ?? null;
$course_id = $_POST['course_id'] ?? null;

if (!$seminar_id || !$learner_id || !$course_id) {
    die("Invalid seminar, learner, or course ID.");
}

$errors = [];
$evaluation_success = false;

$conn->begin_transaction();

try {
    // Insert the evaluation record
    $stmt = $conn->prepare("INSERT INTO evaluations (seminar_id, learner_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $seminar_id, $learner_id);
    $stmt->execute();
    $evaluation_id = $stmt->insert_id;
    $stmt->close();

    // Retrieve the fields for the seminar's evaluation
    $stmt = $conn->prepare("SELECT * FROM evaluation_fields WHERE seminar_id = ?");
    $stmt->bind_param("i", $seminar_id);
    $stmt->execute();
    $fields_result = $stmt->get_result();
    $stmt->close();

    while ($field = $fields_result->fetch_assoc()) {
        $field_id = $field['field_id'];
        $field_label = $field['field_label'];
        $field_type = $field['field_type'];
        $is_required = $field['required'];
        $response = $_POST["field_$field_id"] ?? null;

        if ($is_required && empty($response)) {
            $errors[] = "The field '$field_label' is required.";
            continue;
        }

        if (is_array($response)) {
            $response = implode(", ", $response);
        }

        $stmt = $conn->prepare("INSERT INTO evaluation_responses (evaluation_id, field_id, response) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $evaluation_id, $field_id, $response);
        $stmt->execute();
        $stmt->close();
    }

    if (empty($errors)) {
        $conn->commit();
        $evaluation_success = true;
    } else {
        $conn->rollback();
    }
} catch (Exception $e) {
    $conn->rollback();
    $errors[] = "Failed to submit evaluation: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5 d-flex justify-content-center">
    <div class="bg-white p-4 rounded shadow w-100" style="max-width: 600px;">
        <?php if ($evaluation_success): ?>
            <div class="alert alert-success text-center">Evaluation successfully submitted!</div>
            <a href="CourseContent.php?course_id=<?php echo htmlspecialchars($course_id); ?>&tab=seminar"
                class="btn btn-primary w-100 mt-3">View Seminar</a>
        <?php else: ?>
            <div class="alert alert-danger text-center">Failed to submit evaluation. Please correct the following errors:
            </div>
            <ul class="text-danger">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>

</html>