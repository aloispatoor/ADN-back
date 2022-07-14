let popUpWindow = document.getElementById('popup');
let button = document.getElementById('deleteButton');
let cancel = document.getElementById('no');

// OPEN THE POPUP
button.addEventListener('click', function(){
    popUpWindow.classList.remove('displayNone');
})

//CLOSE THE POPUP
cancel.addEventListener('click', function(){
    popUpWindow.classList.add('displayNone');
})