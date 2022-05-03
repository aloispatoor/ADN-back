// MENUBURGER
let getBurgerBtn = document.getElementById('menuBurger');
let getBurger = document.getElementById('link');
let getNavBar = document.getElementsByTagName('nav')[0];
let supportBtn = document.getElementById('supportBtn');

getBurgerBtn.addEventListener('click', function(){
    // getBurger.classList.toggle('hide-nav');
    // getNavBar.classList.toggle('hide-nav');
    supportBtn.classList.remove('button');
    getBurger.classList.toggle('show-nav');
    getNavBar.classList.toggle('show-nav');
});
// ANIMATION BARS => CROIX
getBurgerBtn.addEventListener('click', function(e) {
    e.preventDefault()
    bars.classList.toggle('open');
});