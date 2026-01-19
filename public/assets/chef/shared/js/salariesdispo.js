document.addEventListener("DOMContentLoaded", () => {
  const combo = document.getElementById("comboSalarie");
  if (!combo) return;

  const input = combo.querySelector("#salarie_search");
  const panel = combo.querySelector(".combo-panel");
  const hiddenId = document.getElementById("id_salarie");

  // ouvrir/fermer
  input.addEventListener("focus", () => panel.hidden = false);
  document.addEventListener("click", (e) => {
    if (!combo.contains(e.target)) panel.hidden = true;
  });

  // filtre
  input.addEventListener("input", () => {
    const q = input.value.trim().toLowerCase();
    combo.querySelectorAll(".combo-item").forEach(btn => {
      const label = (btn.dataset.label || "").toLowerCase();
      btn.style.display = label.includes(q) ? "" : "none";
    });
  });

  // sélection
  combo.querySelectorAll(".combo-item").forEach(btn => {
    btn.addEventListener("click", () => {
      if (btn.disabled) return;
      hiddenId.value = btn.dataset.id || "";
      input.value = btn.dataset.label || "";
      panel.hidden = true;
    });
  });
});
