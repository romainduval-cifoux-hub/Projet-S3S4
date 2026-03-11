document.addEventListener("DOMContentLoaded", () => {

const burger = document.getElementById("burger");
const nav = document.getElementById("nav-menu");
const links = document.querySelectorAll("#nav-menu a");

if (burger && nav) {

    // ouvrir / fermer le menu
    burger.addEventListener("click", () => {
        nav.classList.toggle("active");
    });

    // fermer le menu quand on clique sur un lien
    links.forEach(link => {
        link.addEventListener("click", () => {
            nav.classList.remove("active");
        });
    });

}

});