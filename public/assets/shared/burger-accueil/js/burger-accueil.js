document.addEventListener('DOMContentLoaded', () => {
    const btn      = document.getElementById('quickMenuToggle');
    const menu     = document.getElementById('quickMenu');
    const backdrop = document.getElementById('quickMenuBackdrop');

    if (!btn || !menu || !backdrop) return;

    function openMenu() {
        menu.classList.add('open');
        backdrop.classList.add('open');
    }

    function closeMenu() {
        menu.classList.remove('open');
        backdrop.classList.remove('open');
    }

    btn.addEventListener('click', () => {
        menu.classList.contains('open') ? closeMenu() : openMenu();
    });

    backdrop.addEventListener('click', closeMenu);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeMenu();
    });
});