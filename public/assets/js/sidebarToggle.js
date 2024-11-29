document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const toggleButton = document.querySelector(".sidebar-toggle");
    const contentArea = document.querySelector(".content-area");
    const overlay = document.createElement("div");
    overlay.className = "overlay";

    document.body.appendChild(overlay);

    // Ensure initial state matches the default expanded sidebar
    sidebar.classList.add("expanded");
    contentArea.classList.add("shifted");
    overlay.classList.add("active");

    function openSidebar() {
        sidebar.classList.add("expanded");
        contentArea.classList.add("shifted");
        overlay.classList.add("active");
    }

    function closeSidebar() {
        sidebar.classList.remove("expanded");
        contentArea.classList.remove("shifted");
        overlay.classList.remove("active");
    }

    function toggleSidebar() {
        if (sidebar.classList.contains("expanded")) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    toggleButton.addEventListener("click", toggleSidebar);

    // Close sidebar when clicking outside (on overlay)
    overlay.addEventListener("click", function () {
        closeSidebar();
    });
});
