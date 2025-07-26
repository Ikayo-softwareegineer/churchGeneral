// Toggle sidebar
document.getElementById("toggleSidebar").addEventListener("click", () => {
  const sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("hidden");
});

// Collapse/expand child buttons
document.querySelectorAll(".parent-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    const childContainer = btn.nextElementSibling;
    childContainer.style.display = childContainer.style.display === "block" ? "none" : "block";
  });
});
