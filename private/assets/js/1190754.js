const menuToggle = document.getElementById("menu-toggle");
const sidebarWrapper = document.getElementById("sidebar-wrapper");

menuToggle.addEventListener("click", (e) => {
    e.preventDefault();
    sidebarWrapper.classList.toggle("collapsed");
});