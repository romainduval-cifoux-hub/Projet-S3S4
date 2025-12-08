document.addEventListener("DOMContentLoaded", () => {

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

            if (window.IS_CLIENT) {
                // Connecté ET client : on envoie en BDD
                avisForm.submit();
                return;
            }

            // PAS client : soit pas connecté, soit admin/salarie

            // On récupère ce que l'utilisateur a saisi
            const pendingReview = {
                note: noteSelect ? noteSelect.value : "",
                commentaire: commentaireEl ? commentaireEl.value : ""
            };

            // Si pas connecté : on sauvegarde pour le retrouver après login
            if (!window.IS_LOGGED_IN) {
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
            window.location.href = window.LOGIN_URL;
        });
    }

    // Au retour après connexion client : si un avis était en attente, on le restaure
    if (window.IS_CLIENT) {
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
