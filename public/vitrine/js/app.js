(function () {
  "use strict";

  /* Header scroll shadow */
  var header = document.querySelector(".site-header");
  function onScroll() {
    if (!header) return;
    if (window.scrollY > 12) header.classList.add("is-scrolled");
    else header.classList.remove("is-scrolled");
  }
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* Mobile menu */
  var toggle = document.querySelector(".menu-toggle");
  if (toggle && header) {
    toggle.addEventListener("click", function () {
      header.classList.toggle("nav-open");
      var open = header.classList.contains("nav-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }

  /* Carousel (accueil) */
  var track = document.querySelector(".carousel-slides");
  var dots = document.querySelectorAll(".carousel-dot");
  if (track && dots.length) {
    var i = 0;
    var n = dots.length;
    var autoplay = 5500;
    var timer;

    function go(index) {
      i = (index + n) % n;
      track.style.transform = "translateX(-" + i * 100 + "%)";
      dots.forEach(function (d, j) {
        d.classList.toggle("is-active", j === i);
      });
    }

    function nextSlide() {
      go(i + 1);
    }

    dots.forEach(function (dot, j) {
      dot.addEventListener("click", function () {
        go(j);
        resetTimer();
      });
    });

    function resetTimer() {
      clearInterval(timer);
      timer = setInterval(nextSlide, autoplay);
    }
    resetTimer();
  }

  /* Reveal on scroll */
  var reveals = document.querySelectorAll(".reveal");
  if (reveals.length && "IntersectionObserver" in window) {
    var obs = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) e.target.classList.add("is-visible");
        });
      },
      { rootMargin: "0px 0px -8% 0px", threshold: 0.08 }
    );
    reveals.forEach(function (el) {
      obs.observe(el);
    });
  } else {
    reveals.forEach(function (el) {
      el.classList.add("is-visible");
    });
  }
})();
