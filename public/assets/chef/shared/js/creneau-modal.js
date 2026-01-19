document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('creneauModal');
  const modalTitle = document.getElementById('creneauModalTitle');
  const modalBody  = document.getElementById('creneauModalBody');
  const modalClose = document.getElementById('creneauModalClose');

  if (!modal) return;


  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-open-creneau-modal');
    if (!btn) return;


    e.preventDefault();
    e.stopPropagation();

    const heures = btn.dataset.heures || '';
    const label  = btn.dataset.label  || '';
    const info   = btn.dataset.info   || '';

    modalTitle.textContent = 'Détail du créneau';
    modalBody.innerHTML = `
      <p><strong>Heures :</strong> ${heures}</p>
      <p><strong>Détail :</strong> ${info || label}</p>
    `;

    modal.classList.add('open');
  }, true); 


  modalClose?.addEventListener('click', () => {
    modal.classList.remove('open');
  });

  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.classList.remove('open');
    }
  });
});
