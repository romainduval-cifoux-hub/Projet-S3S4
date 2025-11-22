<div id="main-avis" class="avis_container">
    <h2>Vous pouvez nous faire <span class="highlight">CONFIANCE</span> </h2>
    <h3>EXCELLENT</h3>
    <img id="note" 
     src="<?= BASE_URL ?>/public/assets/vitrine/img/<?= $moyenne ?>stars-google.png" 
     alt="note moyenne">
    <p>Basé sur <strong><?= count($avis) ?> avis</strong></p>
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
                                <img
                                    src="<?= BASE_URL ?>/public/assets/vitrine/img/<?= (int)$a['note'] ?>stars-google.png"
                                    alt="note <?= (int)$a['note'] ?> étoiles"
                                    class="etoiles">
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
<div class="avis-leave-review">
    <button type="button" id="btn-laisser-avis">Laisser un avis</button>
</div>

<!-- Popup (modal) -->
<div id="avis-modal" class="avis-modal-overlay">
    <div class="avis-modal">
        <button type="button" class="avis-modal-close">&times;</button>
        <h3>Laisser un avis</h3>
        <form action="index.php?page=avis_add" method="post">
            <div class="form-group">
                <label for="note-avis">Note :</label>
<select name="note" id="note-avis" required>
                    <option value="">Choisir…</option>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Très bien</option>
                    <option value="3">3 - Moyen</option>
                    <option value="2">2 - Passable</option>
                    <option value="1">1 - Mauvais</option>
                </select>
            </div>

            <div class="form-group">
                <label for="commentaire">Commentaire :</label>
                <textarea name="commentaire" id="commentaire" rows="4" required></textarea>
            </div>

            <button type="button" id="submit-avis-btn">Envoyer mon avis</button>
        </form>
    </div>
</div>
<!-- Popup (modal) de connexion requise -->
<div id="login-modal" class="avis-modal-overlay">
    <div class="avis-modal">
        <button type="button" class="avis-modal-close">&times;</button>
        <h3>Connexion requise</h3>
        <p>Vous devez être connecté pour laisser un avis.</p>
        <button type="button" id="btn-go-login">Se connecter</button>
    </div>
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
            }, {
                once: true
            });
        }

        btnRight.addEventListener("click", () => slideTo(index + 1));
        btnLeft.addEventListener("click", () => slideTo(index - 1));
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    // ---- Infos de session passées depuis PHP ----
    const IS_LOGGED_IN = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    const IS_CLIENT    = <?= (isset($_SESSION['user_id']) && (isset($_SESSION['role']) && $_SESSION['role'] === 'client')) ? 'true' : 'false' ?>;
    const USER_ROLE    = '<?= $_SESSION['role'] ?? '' ?>';

    const btnOpenAvis   = document.getElementById("btn-laisser-avis");
    const avisModal     = document.getElementById("avis-modal");
    const loginModal    = document.getElementById("login-modal");
    const closeButtons  = document.querySelectorAll(".avis-modal-close");
    const avisForm      = document.querySelector("#avis-modal form");
    const submitAvisBtn = document.getElementById("submit-avis-btn");
    const goLoginBtn    = document.getElementById("btn-go-login");

    const noteSelect    = document.getElementById("note-avis");
    const commentaireEl = document.getElementById("commentaire");

    // Ouvrir la popup "laisser un avis"
    if (btnOpenAvis && avisModal) {
        btnOpenAvis.addEventListener("click", () => {
            avisModal.style.display = "flex";
        });
    }

    // Boutons de fermeture (croix dans les popups)
    closeButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            if (avisModal) avisModal.style.display = "none";
            if (loginModal) loginModal.style.display = "none";
        });
    });

    // Fermer en cliquant sur l'overlay sombre
    [avisModal, loginModal].forEach(modal => {
        if (!modal) return;
        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });
    });

    // Clic sur "Envoyer mon avis"
    if (submitAvisBtn && avisForm) {
        submitAvisBtn.addEventListener("click", () => {

            if (IS_CLIENT) {
                // ✅ Connecté ET client : on envoie en BDD
                avisForm.submit();
                return;
            }

            // ⛔ PAS client : soit pas connecté, soit admin/salarie

            // On récupère ce que l'utilisateur a saisi
            const pendingReview = {
                note: noteSelect ? noteSelect.value : "",
                commentaire: commentaireEl ? commentaireEl.value : ""
            };

            // Si pas connecté : on sauvegarde pour le retrouver après login
            if (!IS_LOGGED_IN) {
                localStorage.setItem("pendingReview", JSON.stringify(pendingReview));

                if (avisModal) avisModal.style.display = "none";
                if (loginModal) {
                    const title = loginModal.querySelector('h3');
                    const text  = loginModal.querySelector('p');
                    const loginBtn = document.getElementById('btn-go-login');

                    if (title) title.textContent = 'Connexion requise';
                    if (text)  text.textContent  = 'Vous devez être connecté pour laisser un avis.';
                    if (loginBtn) loginBtn.style.display = 'inline-block';

                    loginModal.style.display = "flex";
                }
            } else {
                // Connecté mais pas client (admin ou salarié)
                // -> on ne sauvegarde pas, on affiche juste un message
                if (avisModal) avisModal.style.display = "none";
                if (loginModal) {
                    const title = loginModal.querySelector('h3');
                    const text  = loginModal.querySelector('p');
                    const loginBtn = document.getElementById('btn-go-login');

                    if (title) title.textContent = 'Action non autorisée';
                    if (text)  text.textContent  = 'Seuls les clients peuvent laisser un avis.';
                    if (loginBtn) loginBtn.style.display = 'none';

                    loginModal.style.display = "flex";
                }
            }
        });
    }

    // Clic sur "Se connecter" dans la popup login (cas non connecté)
    if (goLoginBtn) {
        goLoginBtn.addEventListener("click", () => {
            window.location.href = "<?= BASE_URL ?>/public/index.php?page=login&redirect=home";
        });
    }

    // 🔁 Au retour après connexion client : si un avis était en attente, on le restaure
    if (IS_CLIENT) {
        const stored = localStorage.getItem("pendingReview");
        if (stored) {
            try {
                const data = JSON.parse(stored);
                if (data && (data.note || data.commentaire)) {
                    if (avisModal) {
                        avisModal.style.display = "flex";
                    }
                    if (noteSelect && data.note) {
                        noteSelect.value = data.note;
                    }
                    if (commentaireEl && data.commentaire) {
                        commentaireEl.value = data.commentaire;
                    }
                }
            } catch (e) {
                console.error("Erreur lecture pendingReview :", e);
            }
            localStorage.removeItem("pendingReview");
        }
    }
});
</script>


