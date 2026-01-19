function initCombo(comboId, inputId, panelSelector, hiddenInputId) {
  const combo = document.getElementById(comboId);
  if (!combo) return;

  const input = document.getElementById(inputId);
  const hidden = document.getElementById(hiddenInputId);
  const panel = combo.querySelector(panelSelector);
  const items = Array.from(panel.querySelectorAll(".combo-item"));

  function openPanel() { panel.hidden = false; }
  function closePanel() { panel.hidden = true; }

  input.addEventListener("focus", () => openPanel());

  input.addEventListener("input", () => {
    const q = input.value.trim().toLowerCase();
    openPanel();
    items.forEach(btn => {
      const label = (btn.dataset.label || "").toLowerCase();
      btn.style.display = label.includes(q) ? "" : "none";
    });
  });

  items.forEach(btn => {
    btn.addEventListener("click", () => {
      input.value = btn.dataset.label || "";
      hidden.value = btn.dataset.id || "";
      closePanel();
    });
  });

  document.addEventListener("click", (e) => {
    if (!combo.contains(e.target)) closePanel();
  });
}

document.addEventListener("DOMContentLoaded", () => {
  // combo salariés (tu l’as déjà sûrement, sinon garde)
  initCombo("comboSalarie", "salarie_search", ".combo-panel", "id_salarie");

  // combo clients (nouveau)
  initCombo("comboClient", "client_search", ".combo-panel", "id_client");
});
