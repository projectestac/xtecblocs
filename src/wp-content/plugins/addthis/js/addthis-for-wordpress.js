/* 
 * Javascript for Addthis for Wordpress plugin
 */

jQuery(document).ready(function($) {  
    
    jQuery('#addthis-pubid').focus();
    
    jQuery('#addthis-form').submit(function(){
       if (jQuery('#addthis-pubid').val() === '') {
           jQuery('#addthis-pubid').css('border', '1px solid red');
           jQuery('#addthis-pubid').attr('title', 'Please fill Profile Id');
           return false;
       } 
    });
    
    jQuery('#addthis-pubid').keyup(function(){
        if(jQuery(this).val().length > 0) {
            jQuery(this).css('border', 'none');
            jQuery(this).attr('title', '');
        } else {
            jQuery(this).css('border', '1px solid red');
            jQuery(this).attr('title', 'Please fill Profile Id');
        }
    });
});