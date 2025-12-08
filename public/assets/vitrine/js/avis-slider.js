document.addEventListener("DOMContentLoaded", () => {

    const slider = document.querySelector(".avis-slider");
    const slides = document.querySelectorAll(".avis-slide");
    const btnLeft = document.querySelector(".avis-arrow-left");
    const btnRight = document.querySelector(".avis-arrow-right");

    const visible = 3; // toujours 3 visibles
    const total = slides.length;

    // Largeur d’un slide
    const slideWidth = slides[0].offsetWidth + 20;

    // On clone les premiers et derniers slides pour boucle infinie
    for (let i = 0; i < visible; i++) {
        slider.appendChild(slides[i].cloneNode(true)); // clones en fin
        slider.insertBefore(slides[total - 1 - i].cloneNode(true), slider.firstChild); // clones en début
    }

    let index = visible; // on commence après les clones du début
    slider.style.transform = `translateX(${-index * slideWidth}px)`;

    let isAnimating = false;

    function slideTo(newIndex) {
        if (isAnimating) return;
        isAnimating = true;

        slider.style.transition = "transform 0.4s ease";
        slider.style.transform = `translateX(${-newIndex * slideWidth}px)`;

        slider.addEventListener("transitionend", () => {
            slider.style.transition = "none";

            // si on dépasse le vrai dernier -> revenir au vrai premier
            if (newIndex >= total + visible) {
                newIndex = visible;
                slider.style.transform = `translateX(${-newIndex * slideWidth}px)`;
            }

            // si on dépasse le vrai premier -> revenir au vrai dernier
            if (newIndex < visible) {
                newIndex = total + visible - 1;
                slider.style.transform = `translateX(${-newIndex * slideWidth}px)`;
            }

            index = newIndex;
            setTimeout(() => slider.style.transition = "transform 0.4s ease", 20);
            isAnimating = false;
        }, {
            once: true
        });
    }

    btnRight.addEventListener("click", () => slideTo(index + 1));
    btnLeft.addEventListener("click", () => slideTo(index - 1));
});
