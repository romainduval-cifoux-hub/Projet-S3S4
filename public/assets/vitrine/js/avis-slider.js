document.addEventListener("DOMContentLoaded", () => {
    const slider = document.querySelector(".avis-slider");
    const btnLeft = document.querySelector(".avis-arrow-left");
    const btnRight = document.querySelector(".avis-arrow-right");

    let originalSlides = Array.from(document.querySelectorAll(".avis-slide"));
    let total = originalSlides.length;
    let visible = getVisibleSlides();
    let index = visible;
    let slideWidth = 0;
    let isAnimating = false;

    function getVisibleSlides() {
        const w = window.innerWidth;
        if (w <= 600) return 1;
        if (w <= 900) return 2;
        return 3;
    }

    function buildSlider() {
        slider.innerHTML = "";

        visible = getVisibleSlides();

        for (let i = total - visible; i < total; i++) {
            slider.appendChild(originalSlides[i].cloneNode(true));
        }

        originalSlides.forEach(slide => {
            slider.appendChild(slide);
        });

        for (let i = 0; i < visible; i++) {
            slider.appendChild(originalSlides[i].cloneNode(true));
        }

        const allSlides = slider.querySelectorAll(".avis-slide");
        slideWidth = allSlides[0].offsetWidth + 20;
        index = visible;

        slider.style.transition = "none";
        slider.style.transform = `translateX(${-index * slideWidth}px)`;
    }

    function slideTo(newIndex) {
        if (isAnimating) return;
        isAnimating = true;

        slider.style.transition = "transform 0.4s ease";
        slider.style.transform = `translateX(${-newIndex * slideWidth}px)`;

        slider.addEventListener("transitionend", () => {
            slider.style.transition = "none";

            if (newIndex >= total + visible) {
                newIndex = visible;
                slider.style.transform = `translateX(${-newIndex * slideWidth}px)`;
            }

            if (newIndex < visible) {
                newIndex = total + visible - 1;
                slider.style.transform = `translateX(${-newIndex * slideWidth}px)`;
            }

            index = newIndex;
            isAnimating = false;
        }, { once: true });
    }

    btnRight.addEventListener("click", () => slideTo(index + 1));
    btnLeft.addEventListener("click", () => slideTo(index - 1));

    let resizeTimeout;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            buildSlider();
        }, 150);
    });

    buildSlider();
});