document.addEventListener("DOMContentLoaded", function () {
    // Select the toggle button and the dashboard wrapper
    const toggleButton = document.querySelector(".toggle-button"); // Adjust to your toggle button selector
    const dashboardWrapper = document.querySelector(".dashboard-wrapper");

    // Add click event to toggle button
    toggleButton.addEventListener("click", function () {
        // Toggle the 'sidebar-collapsed' class on the dashboard wrapper
        dashboardWrapper.classList.toggle("sidebar-collapsed");

        // Toggle the main content's expanded class to adjust table position
        const mainContent = document.querySelector(".main-content");
        mainContent.classList.toggle("expanded");
    });
});
