<div class="sidebar expanded" id="sidebar">
    <!-- Navigation Links -->
    <ul class="menu list-unstyled mt-3">
        <li>
            <a href="CourseContent.php?course_id=<?php echo $course_id; ?>&tab=pre-test"
                class="menu-item <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'pre-test') ? 'active' : ''; ?>">
                Pre-Test
            </a>
        </li>
        <li>
            <a href="CourseContent.php?course_id=<?php echo $course_id; ?>&tab=learning-materials"
                class="menu-item <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'learning-materials') ? 'active' : ''; ?>">
                Learning Materials
            </a>
        </li>
        <li>
            <a href="CourseContent.php?course_id=<?php echo $course_id; ?>&tab=quiz"
                class="menu-item <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'quiz') ? 'active' : ''; ?>">
                Quiz
            </a>
        </li>
        <li>
            <a href="CourseContent.php?course_id=<?php echo $course_id; ?>&tab=post-test"
                class="menu-item <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'post-test') ? 'active' : ''; ?>">
                Post-Test
            </a>
        </li>
    </ul>
</div>

<!-- Sidebar Toggle Button -->
<div class="sidebar-toggle" id="sidebar-toggle"></div>