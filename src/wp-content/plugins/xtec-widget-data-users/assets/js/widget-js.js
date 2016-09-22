jQuery(document).ready( function($) {

    $('.widget-mail-content').on('mouseenter',function(e){
        content = $(e.target).attr('data-large');
        $(e.target).text(content);
    });

    $('.widget-mail-content').on('mouseleave',function(e){
        content = $(e.target).attr('data-small');
        $(e.target).text(content);
    });

});