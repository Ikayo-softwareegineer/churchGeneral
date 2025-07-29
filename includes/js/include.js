// includes/js/include.js

document.addEventListener("DOMContentLoaded", () => {
  // Fetch header (from includes/)
  fetch("/churchGeneral/includes/header.html")
    .then(res => res.text())
    .then(data => {
      document.getElementById("header").innerHTML = data;
    })
    .catch(err => console.error("Header load failed:", err));

  // Fetch footer (from includes/)
  fetch("/churchGeneral/includes/footer.html")
    .then(res => res.text())
    .then(data => {
      document.getElementById("footer").innerHTML = data;

      // Animate the progress bar text (TV-style marquee effect) AFTER footer is loaded
      // Animate the progress bar text (TV-style marquee effect) AFTER footer is loaded
const marqueeContainer = document.getElementById("progress-marquee-container");
const marquees = marqueeContainer ? marqueeContainer.querySelectorAll('.progress-marquee') : null;

if (marqueeContainer && marquees && marquees.length === 2) {
  let position = 0;
  const speed = 0.5; // Adjust for smoothness
  const spacing = 150; // Distance between the two messages
  const marqueeWidth = marquees[0].offsetWidth;

  // Ensure container and spans are positioned correctly
  marqueeContainer.style.position = "relative";
  marquees.forEach(marquee => {
    marquee.style.position = "absolute";
    marquee.style.whiteSpace = "nowrap";
    marquee.style.top = "0";
  });

  function animate() {
    position -= speed;

    // Reset when the first span completely leaves and the second follows
    if (Math.abs(position) >= marqueeWidth + spacing) {
      position = 0;
    }

    // Move both spans with a delay between them
    marquees[0].style.transform = `translateX(${position}px)`;
    marquees[1].style.transform = `translateX(${position + marqueeWidth + spacing}px)`;

    requestAnimationFrame(animate);
  }

  animate();
}


    })
    .catch(err => console.error("Footer load failed:", err));
});
