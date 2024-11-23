<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Retrieve the content type from the URL
$content_type = $_GET['type'] ?? null;
if (!$content_type) {
    echo "No content type specified.";
    exit;
}

// Map content types to their database queries
$content_queries = [
    "Programs" => "SELECT * FROM programs WHERE status = 'pending'",
    "Courses" => "SELECT * FROM courses WHERE status = 'pending'",
    "Sections" => "SELECT * FROM course_sections WHERE status = 'pending'",
    "Learning Materials" => "SELECT * FROM learning_materials WHERE status = 'pending'",
    "Course Videos" => "SELECT * FROM course_videos WHERE status = 'pending'",
    "Post-Test Questions" => "SELECT * FROM post_test_questions WHERE status = 'pending'",
    "Pre-Test Questions" => "SELECT * FROM pre_test_questions WHERE status = 'pending'",
    "Seminars" => "SELECT * FROM seminars WHERE status = 'pending'",
];

$query = $content_queries[$content_type] ?? null;
if (!$query) {
    echo "Invalid content type.";
    exit;
}

$result = $conn->query($query);
if (!$result) {
    echo "Failed to fetch content.";
    exit;
}

// Fetch columns if there are rows in the result
$columns = $result->num_rows > 0 ? array_keys($result->fetch_assoc()) : [];
$result->data_seek(0);

$image_columns = ['program_img', 'course_img', 'material_path', 'poster_path'];
$video_columns = ['video_path', 'course_video_path'];
$file_columns = ['file_path']; // Add any columns that should be clickable file paths
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Review Pending <?php echo htmlspecialchars($content_type); ?></title>
    <link rel="stylesheet" href="../../includes/assets/AdminNavBar.css">
    <link rel="stylesheet" href="../../admin/assets/css/review_pending_content.css">
    <script src="../../../includes/assets/Toggle.js" defer></script>
</head>

<body>
    <div class="dashboard-wrapper">
        <?php include '../../includes/AdminNavBar.php'; ?>
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
                                        $video_path = "../videos/" . htmlspecialchars($row[$column]);
                                        ?>
                                        <video width="150" height="100" controls>
                                            <source src="<?php echo $video_path; ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                        <?php
                                    } elseif (in_array(strtolower($column), $file_columns) && !empty($row[$column])) {
                                        $file_path = "../add_learning_materials/" . htmlspecialchars($row[$column]);
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
                                <form action="moderate_content_action.php" method="post">
                                    <input type="hidden" name="content_mdrtn_id" value="<?php echo $row[$columns[0]]; ?>">
                                    <input type="hidden" name="content_type" value="<?php echo strtolower($content_type); ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                                    <button type="submit" name="action" value="decline" class="btn btn-danger">Decline</button>
                                </form>
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

            // Add click event to cells with ellipsis overflow
            document.querySelectorAll("td").forEach(cell => {
                if (hasEllipsis(cell)) {
                    cell.classList.add("long-text"); // Apply the ellipsis-checking style
                    cell.addEventListener("click", function () {
                        document.getElementById("modalText").innerText = cell.getAttribute("data-fulltext") || cell.textContent;
                        document.getElementById("textModal").style.display = "flex";
                    });
                }
            });

            // Close modal when 'x' is clicked
            document.querySelector(".close-modal").addEventListener("click", function () {
                document.getElementById("textModal").style.display = "none";
            });

            // Close modal when clicking outside of modal content
            window.onclick = function (event) {
                if (event.target == document.getElementById("textModal")) {
                    document.getElementById("textModal").style.display = "none";
                }
            };

        </script>
</body>

</html>