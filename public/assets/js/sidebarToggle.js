function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const toggleButton = document.getElementById('sidebar-toggle');

    if (sidebar.classList.contains('expanded')) {
        // Collapse the sidebar
        sidebar.classList.remove('expanded');
        content.classList.remove('shifted');
        toggleButton.style.left = '0';
    } else {
        // Expand the sidebar
        sidebar.classList.add('expanded');
        content.classList.add('shifted');
        toggleButton.style.left = '250px';
    }
}

window.onload = function () {
    const toggleButton = document.getElementById('sidebar-toggle');
    if (toggleButton) {
        toggleButton.addEventListener('click', toggleSidebar);
    }
};
