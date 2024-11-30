document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const toggleButton = document.querySelector(".sidebar-toggle");
    const contentArea = document.querySelector(".content-area");
    const overlay = document.createElement("div");
    overlay.className = "overlay";

    // Add overlay to the DOM
    document.body.appendChild(overlay);

    // Ensure initial state matches the default expanded sidebar
    sidebar.classList.add("expanded");
    contentArea.classList.add("shifted");
    overlay.classList.add("active");
    toggleButton.textContent = "❮"; // Set the initial toggle arrow

    function openSidebar() {
        sidebar.classList.add("expanded");
        contentArea.classList.add("shifted");
        overlay.classList.add("active");
        toggleButton.textContent = "❮"; // Arrow points left when open
    }

    function closeSidebar() {
        sidebar.classList.remove("expanded");
        contentArea.classList.remove("shifted");
        overlay.classList.remove("active");
        toggleButton.textContent = "❯"; // Arrow points right when closed
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

    // Learning Materials submenu toggle
    const learningMaterialsToggle = document.getElementById("learning-materials-toggle");
    const learningMaterialsSubmenu = document.getElementById("learning-materials-submenu");

    if (learningMaterialsToggle && learningMaterialsSubmenu) {
        learningMaterialsToggle.addEventListener("click", function () {
            learningMaterialsSubmenu.classList.toggle("visible");
        });
    }
});
