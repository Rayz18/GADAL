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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/LearnerNavBar.css">
    <link rel="stylesheet" href="../../learner/assets/css/Program.css"> <!-- External CSS -->
</head>

<body>
    <?php include '../../public/includes/LearnerNavBar.php'; ?>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1 class="h1">PROGRAMS</h1>
        </div>

        <!-- Search Bar -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <form class="d-flex" method="GET" action="program.php">
                    <input class="form-control me-2" type="search" placeholder="Search" name="search"
                        value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <button class="btn btn-outline-primary" type="submit">🔍</button>
                </form>
            </div>
        </div>

        <!-- Program List -->
        <div class="row row-cols-1 row-cols-md-2 g-4"> <!-- Two columns for medium screens -->
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
                    echo '<div class="col">'; // Bootstrap column
                    echo '    <a href="Course.php?program_id=' . $row['program_id'] . '" class="d-block text-center">';
                    if (!empty($row['program_img'])) {
                        echo '        <img src="../../staff/upload/' . htmlspecialchars($row['program_img']) . '" 
                                      class="program-image img-fluid" 
                                      alt="' . htmlspecialchars($row['program_name']) . '">';
                    } else {
                        echo '        <div class="default-placeholder">No Image Available</div>';
                    }
                    echo '    </a>';
                    echo '    <h5 class="mt-2 text-center">';
                    echo '        <a href="Course.php?program_id=' . $row['program_id'] . '" class="text-decoration-none text-primary">';
                    echo '            ' . htmlspecialchars($row['program_name']);
                    echo '        </a>';
                    echo '    </h5>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-center text-danger">No programs available at the moment.</p>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../../public/includes/footer.php'; ?>
</body>

</html>
