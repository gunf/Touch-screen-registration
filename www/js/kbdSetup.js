var keyboardShown = false; 
var kbdBottom = 750;
    
function switchLang(lang){
    $("#kbd_"+lang).attr("disable", "disable");
    if (lang == "en") {
        $("#kbd_en").hide();
	$("#kbd_ru").show();
        VirtualKeyboard.switchLayout("US United States-International");
    } else {
        $("#kbd_ru").hide();
	$("#kbd_en").show();
        VirtualKeyboard.switchLayout("RU Russian");
    }
}

function kbdFor(obj, position) {
    if (arguments.length == 1) {
        position = "right";
    }

    if (!keyboardShown){
        keyboardShown = true;        
        VirtualKeyboard.toggle($(obj).attr("id"),'kbd_holder');
        $("#kb_langselector").hide();
        $("#kb_mappingselector").hide();
        $("#copyrights").hide();
    }
    VirtualKeyboard.attachInput(obj);
    
    offset = $(obj).offset();       
    if (position == "right"){
        offset.left += $(obj).width()+10;       
    } else if (position == "bottom") {
        offset.top += $(obj).height()+10;               
        offset.left += ($(obj).width()/2 - $("#keyboard").width()/2);  
    }
    if ($(obj).offset().top + $("#keyboard").height() > kbdBottom){
        offset.top = kbdBottom - $("#keyboard").height();
    } 
    
    $("#keyboard").offset(offset);
}
