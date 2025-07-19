document.querySelectorAll('.carousel').forEach(carouselEl => {
  new bootstrap.Carousel(carouselEl, {
    interval: 3000,
    wrap: true,
    pause: false
  });
});
