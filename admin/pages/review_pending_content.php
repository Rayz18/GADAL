<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

$content_type = $_GET['type'] ?? null;
if (!$content_type) {
    echo "No content type specified.";
    exit;
}

$content_queries = [
    "Programs" => "SELECT program_id, program_name, program_img FROM programs WHERE status = 'pending'",
    "Courses" => "SELECT course_id, course_name, course_img, course_desc, offered_mode, start_date, end_date FROM courses WHERE status = 'pending'",
    "Learning Materials" => "SELECT LM_id, module_title, module_discussion, video_url, pdf_url FROM learning_materials WHERE status = 'pending'",
    "Post-Test Questions" => "SELECT post_test_id, question_text, option_a, option_b, option_c, correct_option FROM post_test_questions WHERE status = 'pending'",
    "Pre-Test Questions" => "SELECT pre_test_id, question_text, option_a, option_b, option_c, correct_option FROM pre_test_questions WHERE status = 'pending'",
    "Quiz Questions" => "SELECT quiz_id, question_text, option_a, option_b, option_c, correct_option FROM quiz_questions WHERE status = 'pending'",
];

// Validate and fetch content type
$content_type = $_GET['type'] ?? null;
if (!$content_type || !array_key_exists($content_type, $content_queries)) {
    die("Invalid content type specified.");
}

$query = $content_queries[$content_type];
$result = $conn->query($query);
if (!$result) {
    die("Failed to fetch content: " . $conn->error);
}

$columns = $result->num_rows > 0 ? array_keys($result->fetch_assoc()) : [];
$result->data_seek(0);

$image_columns = ['program_img', 'course_img', 'material_path', 'poster_path'];
$video_columns = ['video_path', 'course_video_path', 'video_url'];
$file_columns = ['file_path', 'pdf_url']; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Review Pending <?php echo htmlspecialchars($content_type); ?></title>
    <link rel="stylesheet" href="../../admin/assets/css/review_pending_content.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../includes/assets/Toggle.js" defer></script>
    <?php include '../../public/includes/AdminHeader.php'; ?>
</head>
        
<body>
<script src="path/to/ajax-content-moderation.js"></script>

<div id="content" class="content">
            
<div id="toggle-sidebar" class="toggle-sidebar"></div>
    <div class="dashboard-wrapper">
        <?php include '../../public/includes/AdminNavBar.php'; ?>
        <div class="review-content-container">
            <h1>Pending <?php echo htmlspecialchars($content_type); ?></h1>
            <table>
                <tr>
                    <?php if (!empty($columns)): ?>
                        <?php foreach ($columns as $column): ?>
                            <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $column))); ?></th>
                        <?php endforeach; ?>
                        <th>Action</th>
                    <?php else: ?>
                        <td colspan="100%">No pending <?php echo htmlspecialchars($content_type); ?> found.</td>
                    <?php endif; ?>
                </tr>
                <?php if (!empty($columns)): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr id="row-<?php echo htmlspecialchars($row['program_id'] ?? $row['course_id'] ?? $row['LM_id'] ?? $row['post_test_id'] ?? $row['pre_test_id'] ?? $row['quiz_id']); ?>">
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <td class="<?php echo (strlen($row[$column]) > 30) ? 'long-text' : ''; ?>"
                                    data-fulltext="<?php echo htmlspecialchars($row[$column]); ?>">
                                    <?php
                                    // Separate condition for poster_path with its own path setup for seminars
                                    if (strtolower($column) === 'poster_path' && !empty($row[$column])) {
                                        $poster_path = "../seminars/" . htmlspecialchars($row[$column]);
                                        ?>
                                        <img src="<?php echo $poster_path; ?>" alt="Poster" class="table-image">
                                        <?php
                                    } elseif (in_array(strtolower($column), $image_columns) && !empty($row[$column])) {
                                        $image_path = "../../staff/upload/" . htmlspecialchars($row[$column]);
                                        ?>
                                        <img src="<?php echo $image_path; ?>" alt="Image" class="table-image">
                                        <?php
                                    } elseif (in_array(strtolower($column), $video_columns) && !empty($row[$column])) {
                                        $video_path = '../../upload/materials/' . htmlspecialchars($row[$column]);
                                        ?>
                                        <video width="150" height="100" controls>
                                            <source src="<?php echo $video_path; ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                        <?php
                                    } elseif (in_array(strtolower($column), $file_columns) && !empty($row[$column])) {
                                        $file_path = '../../upload/materials/' . htmlspecialchars($row[$column]);
                                        $file_name = basename($row[$column]);
                                        ?>
                                        <a href="<?php echo $file_path; ?>" target="_blank">
                                            <?php echo htmlspecialchars($file_name); ?>
                                        </a>
                                        <?php
                                    } else {
                                        $text = htmlspecialchars($row[$column]);
                                        echo (strlen($text) > 30) ? substr($text, 0, 30) . '...' : $text;
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                            <button class="btn btn-success approve-btn" 
                                    data-id="<?php echo htmlspecialchars($row['program_id'] ?? $row['course_id'] ?? $row['LM_id'] ?? $row['post_test_id'] ?? $row['pre_test_id'] ?? $row['quiz_id']); ?>" 
                                    data-type="<?php echo htmlspecialchars($content_type); ?>">Approve</button>
                            <button class="btn btn-danger decline-btn" 
                                    data-id="<?php echo htmlspecialchars($row['program_id'] ?? $row['course_id'] ?? $row['LM_id'] ?? $row['post_test_id'] ?? $row['pre_test_id'] ?? $row['quiz_id']); ?>" 
                                    data-type="<?php echo htmlspecialchars($content_type); ?>">Decline</button>
                        </td>

                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </table>
        </div>

        <!-- Modal for displaying full content -->
        <div id="textModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <p id="modalText"></p>
            </div>
        </div>

        <!-- Modal for displaying clicked image -->
        <div id="imageModal" class="modal">
            <div class="modal-content">
                <span class="close-image-modal">&times;</span>
                <img id="modalImage" src="" alt="Image" style="max-width: 100%; max-height: 80vh;">
            </div>
        </div>

        <script>
            // Function to check if content overflows and shows ellipsis
            function hasEllipsis(element) {
                return element.offsetWidth < element.scrollWidth;
            }

            // Function to display image in modal
            function showImageModal(imageSrc) {
                const modalImage = document.getElementById("modalImage");
                modalImage.src = imageSrc;
                document.getElementById("imageModal").style.display = "flex";
            }

            // Attach click event to all images in the table to open them in the modal
            document.querySelectorAll("img.table-image").forEach(img => {
                img.addEventListener("click", function () {
                    showImageModal(this.src);
                });
            });

            // Close modal when 'x' is clicked
            document.querySelector(".close-image-modal").addEventListener("click", function () {
                document.getElementById("imageModal").style.display = "none";
            });

            // Close modal when clicking outside of modal content
            window.onclick = function (event) {
                if (event.target == document.getElementById("imageModal")) {
                    document.getElementById("imageModal").style.display = "none";
                }
            };

            document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.approve-btn, .decline-btn').forEach(button => {
        button.addEventListener('click', function () {
            const action = this.classList.contains('approve-btn') ? 'approve' : 'decline';
            const contentId = this.dataset.id;
            const contentType = this.dataset.type;

            if (!confirm(`Are you sure you want to ${action} this content?`)) return;

            fetch("moderate_content_action.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ action, content_type: contentType, content_id: contentId })
            })
            .then(response => response.text())
            .then(data => {
                console.log(`Response from server: ${data}`);
                if (data.includes("successfully")) {
                    alert(`Content ${action}d successfully.`);
                    // Remove the row from the table
                    const row = document.getElementById(`row-${contentId}`);
                    if (row) {
                        row.remove();
                    }
                } else {
                    alert("Failed to process the request.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while processing the request.");
            });
        });
    });
});

</script>
        
        <script>document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    const toggleButton = document.getElementById("toggle-sidebar");

    toggleButton.addEventListener("click", function () {
        if (sidebar.classList.contains("open")) {
            // Close the sidebar
            sidebar.classList.remove("open");
            content.classList.remove("shifted");
        } else {
            // Open the sidebar
            sidebar.classList.add("open");
            content.classList.add("shifted");
        }
    });
});</script>


        <div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        </div>
        <script src="path/to/your/script.js"></script>

</body>

</html>
