<?php
require_once '../../config/config.php';

$$programs_query = $conn->query("SELECT * FROM programs WHERE status = 'pending'");

if ($programs_query->num_rows > 0) {
    echo "<ul>";
    while ($program = $programs_query->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($program['program_name']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No pending programs.</p>";
}
?>