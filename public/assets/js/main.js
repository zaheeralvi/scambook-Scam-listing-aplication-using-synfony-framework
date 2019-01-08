
(function ($) {
    "use strict";

    
    /*==================================================================
         
    [ Validate ]*/
    var Damages = $('.validate-input input[name="Damages"]');
    var location = $('.validate-input input[name="location"]');
    var detail = $('.validate-input input[name="detail"]');
    var date = $('.validate-input textarea[name="date"]');
    var company = $('.validate-input select .company');


    $('.validate-form').on('submit',function(){
        var check = true;

        if($(Damages).val().trim() == ''){
            showValidate(Damages);
            check=false;
        }

        if($(location).val().trim() == ''){
            showValidate(location);
            check=false;
        }

        if($(detail).val().trim() == ''){
            showValidate(detail);
            check=false;
        }

        if($(date).val().trim() == ''){
            showValidate(date);
            check=false;
        }

        if($(company).val().trim() == ''){
            showValidate(company);
            check=false;
        }

        return check;
    });


    $('.validate-form .input1').each(function(){
        $(this).focus(function(){
           hideValidate(this);
       });
    });

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');
    }
    
    

})(jQuery);