jQuery(document).on('click', '.mebounce_delete', function () {
    //alert('Working');
    var id = this.id;
    //alert(id);
    var $tr = jQuery(this).closest('tr'); //here we hold a reference to the clicked tr which will be later used to delete the row
    
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {"action": "mebounce_delete_row", "element_id": id},
        success: function (response) {
            //run stuff on success here.  You can use `data` var in the 
           //return so you could post a message.  
            //alert(response);
            jQuery('#mebounce_response').html(response).show().delay(10000).fadeOut('fast');
            $tr.addClass('red');
            $tr.find('td').fadeOut(1000,function(){ 
                $tr.remove();
                //jQuery('#mepopup_response').delay(10000).fadeOut('fast');
            });
        }
    });
});