function openPopup(id,time=0){
    show(id);
    document.documentElement.style.overflowY ="hidden";
    if(time!=0){
        setTimeout(function(){closePopup(id)},time);
    }
}

function closePopup(id){
    hide(id);
    document.documentElement.style.overflowY ="scroll";
}