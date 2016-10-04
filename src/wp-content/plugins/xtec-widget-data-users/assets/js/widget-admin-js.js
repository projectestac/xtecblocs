function widgetRemoveImage(id){
    jQuery('input[name="widget-users_data_widget['+id+'][image_uri]"').val('').trigger('change');
    jQuery('img[name="widget-users_data_widget['+id+'][image_uri]"').attr('src','');
    jQuery('img[name="widget-users_data_widget['+id+'][image_uri]').css('display','none');
    jQuery('#widget-users_data_widget-'+id+'-image_uri').css('display','none');
}

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

function xtecCheckMail(name){
    content = jQuery('[name="'+name+'"]').val();
    result = isEmail(content);
    if( result == false ){
        jQuery('[name="'+name+'"]').css('border','1px solid red');
    } else {
        jQuery('[name="'+name+'"]').css('border','1px solid #ddd');
    }
}

jQuery(document).ready( function($) {
    function media_upload(button_class) {
        var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;

        $('body').on('click', button_class, function(e) {
            var button_id ='#'+$(this).attr('id');
            var self = $(button_id);
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = $(button_id);
            var id = button.attr('id').replace('_button', '');
            _custom_media = true;
            wp.media.editor.send.attachment = function(props, attachment){
                if ( _custom_media  ) {
                    id = e.target.name;
                    id = id.replace('button_widget-users_data_widget','');
                    cross = id.replace('[','');
                    cross = cross.replace('][image_uri]','');
                    $('input[name="widget-users_data_widget'+id+'"]').val(attachment.url).trigger('change');
                    $('img[name="widget-users_data_widget'+id+'"]').attr('src',attachment.url).css('display','block');
                    $('#widget-users_data_widget-'+cross+'-image_uri').css('display','block');
                } else {
                    return _orig_send_attachment.apply( button_id, [props, attachment] );
                }
            }
            wp.media.editor.open(button);
                return false;
        });
    }
    media_upload('.custom_media_button.button');

});