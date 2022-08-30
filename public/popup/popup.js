function popup(){
    let popUpWindow = document.getElementById('popup');
    let getPopup = document.getElementById('deleteButton');
    let cancel = document.getElementById('no');
    
    // OPEN THE POPUP
    getPopup.addEventListener('click', function(){
        popUpWindow.classList.remove('displayNone');
    })
    
    //CLOSE THE POPUP
    cancel.addEventListener('click', function(){
        popUpWindow.classList.add('displayNone');
    })
};
