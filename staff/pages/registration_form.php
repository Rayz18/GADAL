<?php
session_start();
include '../../config/config.php';

// Validate course_id
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <h1 class="text-center text-primary mb-4">Registration Form</h1>
        <form>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" readonly>This is the predefined registration description.</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" value="Name" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Age</label>
                <input type="number" class="form-control" value="Age" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <input type="text" class="form-control" value="Gender" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Position/Designation</label>
                <input type="text" class="form-control" value="Position/Designation" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Office/Affiliation</label>
                <input type="text" class="form-control" value="Office/Affiliation" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-control" value="Contact Number" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" value="Email Address" readonly>
            </div>
        </form>
    </div>
</body>

</html>