<div class="sidebar expanded" id="sidebar">
    <!-- Navigation Links -->
    <ul class="menu list-unstyled mt-3">
        <li>
            <a href="CourseContent.php?course_id=<?php echo $course_id; ?>&tab=pre-test"
                class="menu-item <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'pre-test') ? 'active' : ''; ?>">
                Pre-Test
            </a>
        </li>
        <li class="dropdown">
            <a href="javascript:void(0);" class="menu-item <?php echo (!empty($learning_materials)) ? 'dropdown-toggle' : ''; ?>" id="learning-materials-toggle">
                Learning Materials
            </a>
            <?php if (!empty($learning_materials)): ?>
                <ul class="submenu list-unstyled mt-2 ms-3 <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'learning-materials') ? 'visible' : ''; ?>"
                    id="learning-materials-submenu">
                    <?php foreach ($learning_materials as $index => $material): ?>
                        <li>
                            <a href="CourseContent.php?course_id=<?php echo $course_id; ?>&tab=learning-materials&module=<?php echo urlencode($index); ?>"
                                class="menu-item <?php echo (isset($_GET['module']) && $_GET['module'] == $index) ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($material['module_title'] ?? 'Untitled Module'); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
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
