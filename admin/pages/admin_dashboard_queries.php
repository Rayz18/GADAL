<?php
// Include the config file to establish the DB connection
include('../../config/config.php');


// Function to return data in JSON format
function sendJsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}


// Get learners enrolled per course
function getLearnersPerCourse($conn)
{
    $sql = "SELECT c.course_name, COUNT(e.enrollment_id) AS total_enrolled
            FROM courses c
            LEFT JOIN enrollments e ON c.course_id = e.course_id
            GROUP BY c.course_name";
    return mysqli_query($conn, $sql);
}


// Get proportion of courses offered per program
function getCoursesPerProgram($conn)
{
    $sql = "SELECT p.program_name, COUNT(c.course_id) AS total_courses
            FROM programs p
            LEFT JOIN courses c ON p.program_id = c.program_id
            GROUP BY p.program_name";
    return mysqli_query($conn, $sql);
}


// Get enrollment trends over time (monthly)
function getEnrollmentTrends($conn)
{
    $sql = "SELECT DATE_FORMAT(e.enrolled_at, '%Y-%m') AS month, COUNT(e.enrollment_id) AS total_enrolled
            FROM enrollments e
            GROUP BY month
            ORDER BY month";
    return mysqli_query($conn, $sql);
}


// Get average pre-test and post-test scores per course
function getTestScoresPerCourse($conn)
{
    $sql = "SELECT c.course_name,
                   AVG(pt.score) AS avg_pre_test,
                   AVG(post.score) AS avg_post_test
            FROM courses c
            LEFT JOIN pre_test_results pt ON c.course_id = pt.course_id
            LEFT JOIN post_test_results post ON c.course_id = post.course_id
            GROUP BY c.course_name";
    return mysqli_query($conn, $sql);
}


// Get average quiz scores per course
function getQuizScoresPerCourse($conn)
{
    $sql = "SELECT c.course_name, AVG(qr.score) AS avg_quiz_score
            FROM courses c
            LEFT JOIN quiz_results qr ON c.course_id = qr.course_id
            GROUP BY c.course_name";
    return mysqli_query($conn, $sql);
}


// Get number of male and female enrolled in each course
function getGenderEnrollmentPerCourse($conn)
{
    $sql = "SELECT c.course_name,
                   SUM(CASE WHEN l.gender = 'Male' THEN 1 ELSE 0 END) AS male_count,
                   SUM(CASE WHEN l.gender = 'Female' THEN 1 ELSE 0 END) AS female_count
            FROM courses c
            LEFT JOIN enrollments e ON c.course_id = e.course_id
            LEFT JOIN learners l ON e.learner_id = l.learner_id
            GROUP BY c.course_name";
    return mysqli_query($conn, $sql);
}


// Check for the action parameter and execute the corresponding function
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'getLearnersPerCourse':
            $result = getLearnersPerCourse($conn);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            sendJsonResponse($data);
            break;


        case 'getCoursesPerProgram':
            $result = getCoursesPerProgram($conn);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            sendJsonResponse($data);
            break;


        case 'getEnrollmentTrends':
            $result = getEnrollmentTrends($conn);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            sendJsonResponse($data);
            break;


        case 'getTestScoresPerCourse':
            $result = getTestScoresPerCourse($conn);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            sendJsonResponse($data);
            break;


        case 'getQuizScoresPerCourse':
            $result = getQuizScoresPerCourse($conn);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            sendJsonResponse($data);
            break;


        case 'getGenderEnrollmentPerCourse':
            $result = getGenderEnrollmentPerCourse($conn);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            sendJsonResponse($data);
            break;


        default:
            sendJsonResponse(['error' => 'Invalid action']);
    }
} else {
    sendJsonResponse(['error' => 'No action specified']);
}
?>