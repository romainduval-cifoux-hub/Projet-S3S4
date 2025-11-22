<div id="main-avis" class="avis_container">
    <h2>Vous pouvez nous faire <span class="highlight">CONFIANCE</span> </h2>
    <h3>EXCELLENT</h3>
    <img id="note" src="<?= BASE_URL ?>/public/assets/vitrine/img/stars-google.png" alt="rating">
    <p>Basé sur <strong>404 avis</strong></p>
    <img id="logo-google" src="<?= BASE_URL ?>/public/assets/vitrine/img/Google-logo.png" alt="logo-google">

<div class="avis-slider-wrapper">
    <button class="avis-arrow avis-arrow-left" type="button">‹</button>

    <div class="avis-slider-window">
        <div class="avis-slider">
            <?php foreach ($avis as $a): ?>
                <div class="avis-slide">
                    <div class="avis-google">
                        <div class="avis-header">
                            <img class="pp" src="<?= htmlspecialchars($a['photo']) ?>" alt="photo de profil">

                            <div class="avis-info">
                                <p class="nom-client">
                                    <?= htmlspecialchars($a['nom'] . ' ' . $a['prenom']) ?>
                                </p>
                                <p class="date-avis">
                                    <?= htmlspecialchars(date('d/m/Y', strtotime($a['date']))) ?>
                                </p>
                            </div>

                            <img class="logo-google"
                                 src="<?= BASE_URL ?>/public/assets/vitrine/img/google-logo-carre.png"
                                 alt="logo Google">
                        </div>

                        <div class="avis-note">
                            <img src="<?= BASE_URL ?>/public/assets/vitrine/img/stars-google.png" class="etoiles">
                            <img src="<?= BASE_URL ?>/public/assets/vitrine/img/certif.png" class="verifie">
                        </div>

                        <p class="commentaire-client">
                            <?= htmlspecialchars($a['commentaire']) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <button class="avis-arrow avis-arrow-right" type="button">›</button>
</div>


</div>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const slider = document.querySelector(".avis-slider");
    const slides = document.querySelectorAll(".avis-slide");
    const btnLeft = document.querySelector(".avis-arrow-left");
    const btnRight = document.querySelector(".avis-arrow-right");

    const visible = 3; // toujours 3 visibles
    const total = slides.length;

    // Largeur d’un slide
    const slideWidth = slides[0].offsetWidth + 20;

    // 🔁 On clone les premiers et derniers slides pour boucle infinie
    for (let i = 0; i < visible; i++) {
        slider.appendChild(slides[i].cloneNode(true));           // clones en fin
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

            // 🔄 si on dépasse le vrai dernier -> revenir au vrai premier
            if (newIndex >= total + visible) {
                newIndex = visible;
                slider.style.transform = `translateX(${-newIndex * slideWidth}px)`;
            }

            // 🔄 si on dépasse le vrai premier -> revenir au vrai dernier
            if (newIndex < visible) {
                newIndex = total + visible - 1;
                slider.style.transform = `translateX(${-newIndex * slideWidth}px)`;
            }

            index = newIndex;
            setTimeout(() => slider.style.transition = "transform 0.4s ease", 20);
            isAnimating = false;
        }, { once: true });
    }

    btnRight.addEventListener("click", () => slideTo(index + 1));
    btnLeft.addEventListener("click", () => slideTo(index - 1));
});
</script>
