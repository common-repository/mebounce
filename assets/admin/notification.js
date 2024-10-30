jQuery(document).ready(function($) {
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {"action": "mebounce_update_notificatio", },
        success: function (response) {
            //run stuff on success here.  You can use `data` var in the 
           //return so you could post a message.  
            //alert(response);
            
        }
    });
});