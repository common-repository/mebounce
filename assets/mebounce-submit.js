jQuery(document).ready(function($) {
    
    $('#mebounce_submit').click(function(){
        var error = false;
        
        var name = $('input#mebounce_name').val();

        if( name.length < 3) {
            $('#mebounce_name_error').show(500);
            $('#mebounce_name_error').delay(4000);
            $('#mebounce_name_error').animate({
                height: 'toggle'
            }, 500, function () {
                // Animation complete.
            });
            error = true; // change the error state to true
        }
        
        var email = $('input#mebounce_email').val().toLowerCase();
        var emailCompare = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/; // Syntax to compare against input
        if(!emailCompare.test(email)) {
            $('#mebounce_email_error').show(500);
            $('#mebounce_email_error').delay(4000);
            $('#mebounce_email_error').animate({
                height: 'toggle'
            }, 500, function () {
                // Animation complete.
            });
            error = true; // change the error state to true
        }
        
        var mobile = $('input#mebounce_mobile').val();
        var numCheck = /^\d+$/;
        if($('input#mebounce_mobile').val().length > 0) {
            if (!numCheck.test(mobile)) { 
                $('#mebounce_mobile_error').show(500);
                $('#mebounce_mobile_error').delay(4000);
                $('#mebounce_mobile_error').animate({
                    height: 'toggle'
                }, 500, function () {
                    // Animation complete.
                });
                error = true; // change the error state to true
            }
        }
        
        var new_mebounce_nonce = $('#new_mebounce_nonce').val();
        
        if(error===false) {
            $.ajax({
                type: 'POST',
                url: $('#mebounce_form').attr('action'),
                action : 'mebounce_ajax_submit',
                data: {
                    action : 'mebounce_ajax_submit',
                    name : name,
                    email : email,
                    mobile : mobile,
                    new_mebounce_nonce : new_mebounce_nonce,
                },
                beforeSend : function() {
                    $('#mebounce_loader').show();
                },
                success : function( response ) {
                    //alert(response);
                    $('#mebounce_success').html(response);
                    $('#mebounce_success').show();
                    /*$('#mebounce_success').delay(10000);
                    $('#mebounce_success').animate({
                        height: 'toggle'
                    }, 500, function () {
                        // Animation complete.
                    });*/
                    $('#mebounce_name').val('');
                    $('#mebounce_email').val('');
                    $('#mebounce_mobile').val('');
                    $('#mebounce_loader').hide();
                },
                error: function() { alert("Error posting data."); }
            });
            //alert('perfect');
        }
        
        return false;
    });
});
