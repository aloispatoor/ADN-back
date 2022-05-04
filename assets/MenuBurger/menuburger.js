// MENUBURGER
let getBurgerBtn = document.getElementById('menuBurger');
let getBurger = document.getElementById('link');
let getNavBar = document.getElementsByTagName('nav')[0];
let supportBtn = document.getElementById('supportBtn');

getBurgerBtn.addEventListener('click', function(){
    getBurger.classList.toggle('dropdown');
    getNavBar.classList.toggle('dropdown');
    supportBtn.classList.remove('button');
    getBurger.classList.toggle('mobiledropdown');
    getNavBar.classList.toggle('mobiledropdown');
});
// ANIMATION BARS => CROIX
getBurgerBtn.addEventListener('click', function(e) {
    e.preventDefault()
    bars.classList.toggle('open');
});