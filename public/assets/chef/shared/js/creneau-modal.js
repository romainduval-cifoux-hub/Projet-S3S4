document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('creneauModal');
  const modalTitle = document.getElementById('creneauModalTitle');
  const modalBody  = document.getElementById('creneauModalBody');
  const modalClose = document.getElementById('creneauModalClose');

  if (!modal) return; 


  document.querySelectorAll('.slot').forEach(slot => {

    

    slot.addEventListener('click', () => {
      const heures = slot.dataset.heures || '';
      const label  = slot.dataset.label  || '';
      const info   = slot.dataset.info   || '';

      modalTitle.textContent = label || 'Détail du créneau';
      modalBody.innerHTML = `
        <p><strong>Heures :</strong> ${heures}</p>
        <p><strong>Détail :</strong> ${info || label}</p>
      `;

      modal.classList.add('open');
      console.log("oui")
    });
  });


  modalClose.addEventListener('click', () => {
    modal.classList.remove('open');

  });

  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('open');
    
  });
});