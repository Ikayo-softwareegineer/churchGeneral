// includes/js/include.js

document.addEventListener("DOMContentLoaded", () => {
  // Fetch header (from includes/)
  fetch("/Church Management System/includes/header.html")
    .then(res => res.text())
    .then(data => {
      document.getElementById("header").innerHTML = data;
    })
    .catch(err => console.error("Header load failed:", err));

  // Fetch footer (from includes/)
  fetch("/Church Management System/includes/footer.html")
    .then(res => res.text())
    .then(data => {
      document.getElementById("footer").innerHTML = data;

      // Animate the progress bar text (TV-style marquee effect) AFTER footer is loaded
      const marqueeContainer = document.getElementById("progress-marquee-container");
      const marquees = marqueeContainer ? marqueeContainer.querySelectorAll('.progress-marquee') : null;
      if (marqueeContainer && marquees && marquees.length === 2) {
        const marqueeWidth = marquees[0].offsetWidth;
        let pos = 0;
        const speed = 1; // pixels per frame

        function animate() {
          pos -= speed;
          if (Math.abs(pos) >= marqueeWidth) {
            pos = 0;
          }
          marquees[0].style.transform = `translateX(${pos}px)`;
          marquees[1].style.transform = `translateX(${pos + marqueeWidth}px)`;
          requestAnimationFrame(animate);
        }
        animate();
      }
    })
    .catch(err => console.error("Footer load failed:", err));
});
