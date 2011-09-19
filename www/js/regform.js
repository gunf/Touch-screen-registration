$(function(){
    $("#regform").validate();
    $.extend($.validator.messages, errorMessages);    
    if ($("#country").val() != ""){
        loadTowns ($("#country").val());
    }
    $("#town").attr("disabled", "disabled");
    $(".input").focus(function(){
        kbdFor(this, "right");
    });   
    
    $("#country").focus(function(){
        $("#country" ).autocomplete( "option", "disabled", false);
    });
    $("#town").focus(function(){
        $("#town" ).autocomplete( "option", "disabled", false);
    });


    $("#country").autocomplete({
        source:"export.php?type=countries",
        select: function( event, ui ) {    
            $("#country" ).autocomplete( "option", "disabled", true );
            $("#country").val (ui.item.label);                          
            $("#town").attr("disabled", null);
            
            $("#town").autocomplete ({
                source:"export.php?type=towns&country="+ui.item.value,
                select:function(event, ui){
                    $("#town").val(ui.item.label);
                    $("#town").autocomplete( "option", "disabled", true );
                    return false;
                }
            });
            return false;
        }
    });

        
    setInterval (function(){
        var country = $("#country");
        var town = $("#town");

        if (previousCountry != country.val()) {
            previousCountry = country.val();
            country.autocomplete("search", country.val());
        }
            
        if (previousTown != town.val()) {
            previousTown = town.val();
            town.autocomplete("search", town.val());
        }
    }, 800);    
    
        $(".input").blur(function(){
	   var value = this.value;
	   value = value[0].toUpperCase() + value.substr(1);
           this.value = value;
        });
});
