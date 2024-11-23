<?php
session_start();
require_once "../../config/config.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Programs Page</title>
    <link rel="stylesheet" href="../../includes/assets/LearnerNavBar.css">
    <link rel="stylesheet" href="../../learner/assets/css/Program.css">
</head>

<body>
    <?php include '../../includes/LearnerNavBar.php'; ?>
    <!-- Programs Section -->
    <div class="program-container">
        <div class="content">
            <div class="term-search">
                <h1>PROGRAMS</h1>
                <div class="search-container">
                    <form method="GET" action="program.php">
                        <input type="text" placeholder="Search" name="search"
                            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button type="submit">🔍</button>
                    </form>
                </div>
            </div>

            <!-- Program Images and Titles -->
            <div class="photo-grid"> <!-- Updated to wrap all photo cards -->
                <?php
                // Fetch only approved programs or filter by search term
                $approved = 'approved';
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search_term = "%" . $conn->real_escape_string($_GET['search']) . "%";
                    $stmt = $conn->prepare("SELECT * FROM programs WHERE status = ? AND program_name LIKE ?");
                    $stmt->bind_param("ss", $approved, $search_term);
                } else {
                    $stmt = $conn->prepare("SELECT * FROM programs WHERE status = ?");
                    $stmt->bind_param("s", $approved);
                }
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if any programs exist
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='photo-card'>";
                        echo "<a href='Course.php?program_id=" . $row['program_id'] . "'>";
                        echo "<img src='../staff/upload/" . htmlspecialchars($row['program_img']) . "' 
                                 onerror=\"this.src='../learner/assets/common/images/default-program.png'\" 
                                 alt='" . htmlspecialchars($row['program_name']) . "'></a>";
                        echo "<a class='photo-title'>" . htmlspecialchars($row['program_name']) . "</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p class='no-programs'>No programs available at the moment.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>