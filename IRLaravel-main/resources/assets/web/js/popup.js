var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopup() {
    //loads popup only if it is disabled    
    if (popupStatus == 0 && $("#popupContact".length)) {
        $("#backgroundPopup").fadeIn("slow");
        $("#popupContact").fadeIn("slow");
        popupStatus = 1;
    }
}

//disabling popup with jQuery magic!
function disablePopup() {
    //disables popup only if it is enabled
    if (popupStatus == 1) {
        $("#backgroundPopup").fadeOut("slow");
        $("#popupContact").fadeOut("slow");
        popupStatus = 0;
    }
}

$(function () {
    var s = $.session.get('p')
    if (!$.session.get('p') || s < 1) {
        loadPopup();
    }

    //Click the button event!
    $("#button").click(function () {
        loadPopup();
        $.session.set('p', '1');
    });
    //CLOSING POPUP
    //Click the x event!
    $(".popupContactClose, .close-popup").click(function () {
        disablePopup();
        $.session.set('p', '1');
        //$.cookie("showpopup",null); 
    });
    //Click out event!
    $("#backgroundPopup").click(function () {
        disablePopup();
        $.session.set('p', '1');
    });
    //Press Escape event!
    $(document).keypress(function (e) {
        if (e.keyCode == 27 && popupStatus == 1) {
            disablePopup();
            //$.cookie("showpopup",null); 
            $.session.set('p', '1');
        }
    });
});