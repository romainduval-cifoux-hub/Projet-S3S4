document.addEventListener('DOMContentLoaded', () => {

  // 1er clic : afficher la zone confirmer/annuler
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-ask-delete');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation(); // IMPORTANT : empêche la modale de s'ouvrir

    const form = btn.closest('.js-double-confirm');
    if (!form) return;

    const confirmBox = form.querySelector('.slot-confirm');
    if (!confirmBox) return;

    confirmBox.hidden = false;
    btn.hidden = true;
  });

  // Annuler
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-cancel-delete');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    const form = btn.closest('.js-double-confirm');
    if (!form) return;

    const confirmBox = form.querySelector('.slot-confirm');
    const askBtn = form.querySelector('.js-ask-delete');

    if (confirmBox) confirmBox.hidden = true;
    if (askBtn) askBtn.hidden = false;
  });

  // Optionnel : empêcher que "Confirmer" ouvre la modale (normalement déjà bloqué)
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.slot-confirm button[type="submit"]');
    if (!btn) return;
    e.stopPropagation();
  });

});
