let getNavBar = document.getElementsByTagName('nav')[0];
let supportBtn = document.getElementById('supportBtn');
// si jamais la resolution change alors que la page est déjà chargé
window.addEventListener("resize", function() {
    if (window.matchMedia("(max-width: 1100px)").matches) {
        getNavBar.classList.remove("special-anim");
    }else{
        getNavBar.classList.add("special-anim");
        getNavBar.classList.add("hide-nav");
        getNavBar.classList.remove('show-nav');
        supportBtn.classList.add("button");
    };
});