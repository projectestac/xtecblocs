/**
 * Created by mireiachaler on 26/09/2017.
 */
(function( $ ) {
    $(document).ready(function () {

        $(".read-button").click(function(){
            if($(this).hasClass("read-more")){
                $("#project-description .description").css("height", "auto");
                $(".read-button").removeClass("read-more");
                $(".read-button").addClass("read-less");
                $(".read-button").text("Amagar text");
                return true;
            }
            if($(this).hasClass("read-less")){
                $("#project-description .description").css("height", "150px");
                $(".read-button").removeClass("read-less");
                $(".read-button").addClass("read-more");
                $(".read-button").text("Lllegir més");
                return true;
            }

        });

    });

})( jQuery );