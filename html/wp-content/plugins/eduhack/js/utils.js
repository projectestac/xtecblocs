/**
 * Created by mireiachaler on 10/08/2017.
 */
(function( $ ) {
    $(document).ready(function () {

        var max_fields      = 100;
        var wrapper         = $(".team-container");
        var add_button      = $(".add_form_field");

        var x = 1;
        $(add_button).click(function(e){
            e.preventDefault();

            var count = $(this).attr("data-count");
            x = count;
            if(x < max_fields){
                $(wrapper).append('<div>'+
                    ' <label for="meta-text" class="team-label">Nom </label>'+
                    ' <input type="text" name="team[name][]">'+
                    ' <label for="meta-text" class="team-label">Centre </label>'+
                    ' <input type="text" name="team[school][]">'+
                    ' <label for="meta-text" class="team-label">Imatge  </label>'+
                    ' <input class="image-url-'+x+'" type="hidden" name="team[image][]" />'+
                    ' <input type="button" class="button upload-button-'+x+'" value="Pujar imatge" data-buttonid="'+x+'" data-att-image="image-url-" data-img-src="image-src-"/>'+
                    ' <img src="" class="team-img image-src-'+x+'"/><a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>'+
                    '</div>'); //add input box

                setButtonClick(x);
                x++;
                $(this).attr("data-count", x);

            }
            else
            {
                alert('You Reached the limits')
            }
        });

        $(wrapper).on("click",".delete", function(e){
            e.preventDefault(); $(this).parent('div').remove(); x--;
        })

        var x = 1;
        $(".add_facilitador_field").click(function(e){
            e.preventDefault();
            if(x < max_fields){

                $(".facilitador-container").append('<div>'+
                    ' <label for="meta-text" class="facilitador-label">Nom </label>'+
                    ' <input type="text" name="facilitador[name][]">'+
                    ' <label for="meta-text" class="facilitador-label">Centre </label>'+
                    ' <input type="text" name="facilitador[school][]">'+
                    ' <label for="meta-text" class="facilitador-label">Imatge  </label>'+
                    ' <input class="fac-image-url-'+x+'" type="hidden" name="facilitador[image][]" />'+
                    ' <input type="button" class="button upload-button-'+x+'" value="Pujar imatge" data-buttonid="'+x+'" data-att-image="fac-image-url-" data-img-src="fac-image-src-"/>'+
                    ' <img src="" class="team-img facilitador-img fac-image-src-'+x+'"/>'+
                    '<a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>'+
                    '</div>'); //add input box
                setButtonClick(x);
                x++;

            }
            else
            {
                alert('You Reached the limits')
            }
        });

        $('.facilitador-container').on("click",".delete", function(e){
            e.preventDefault(); $(this).parent('div').remove(); x--;
        })

        var x = 1;
        $(".add_form_tag1").click(function(e){
            e.preventDefault();
            if(x < max_fields){

                $(".config-tags1-container").append('<div>'+
                    '<input type="text" name="config1[tag-text][]" value="">'+
                    '<label for="meta-text" class="team-label">Imatge</label>'+
                    '<input class="config-image-url-'+x+'" type="hidden" name="config1[image][]" value="" />'+
                    '<input type="button" class="button upload-button-'+x+' button-'+x+'" value="Pujar imatge" data-buttonid="'+x+'" data-att-image="config-image-url-" data-img-src="config-image-src-"/>'+
                    '<img src="" class="team-img config-image-src-'+x+'"/>'+
                    '<a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>'+
                    '</div>'); //add input box
                setButtonClick(x);
                x++;

            }
            else
            {
                alert('You Reached the limits')
            }
        });

        $(".config-tags1-container").on("click",".delete", function(e){
            e.preventDefault(); $(this).parent('div').remove(); x--;
        })


        var y = 1;
        $(".add_form_tag2").click(function(e){
            e.preventDefault();

            if(y < max_fields){
                y++;
                $(".config-tags2-container").append('<div>'+
                    '<input type="text" name="config2[tag-text][]">'+
                    '<label for="meta-text" class="config-tag-label">Color  </label>'+
                    '<input id="color2-'+y+'" value="123456" name="config2[tag-color][]">'+
                    '<a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>'+
                    '</div>'); //add input box

                var picker = new jscolor("color2-"+y);
            }
            else
            {
                alert('You Reached the limits')
            }
        });

        $(".config-tags2-container").on("click",".delete", function(e){
            e.preventDefault(); $(this).parent('div').remove(); y--;
        })

        $(".tags1 select").change(function(){
            $(".tags1 select option:first-child").attr("disabled", true);
            var selected_value = $(".tags1 select option:selected").val();
            var selected_text = $(".tags1 select option:selected").text();
            var selected_color = $(".tags1 select option:selected").attr("data-color");
            var selected_img = $(".tags1 select option:selected").attr("data-img");
            var selected_id = $(".tags1 select option:selected").attr("data-id");

            var tag = '<div class="choosen-tag" style="background-color:'+selected_color+'" data-option="'+selected_id+'">'+
                '<span class="color-tag" style="background-color:'+selected_color+'"></span>'+
                '<input type="text" readonly name="selected_tags_text1[]" value="'+selected_text+'">'+
                '<input type="hidden" name="selected_tags_color1[]" value="'+selected_color+'">'+
                '<input type="hidden" name="selected_tags_id1[]" value="'+selected_id+'">'+
                '<img class="tag-image-url" src="'+selected_img+'" />'+
                '<a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>'+
                '<input class="tag-image-url-'+x+'" type="hidden" name="selected_tags_img1[]" value="'+selected_img+'" />'+
                '</div>';
            $(".selected-tags1").append(tag);
            $(".tags1 select option:selected").attr("disabled", true);
        });


        $(".selected-tags1").on("click",".delete", function(e){
            var option_value = $(this).parent().attr("data-option");
            e.preventDefault(); $(this).parent('div').remove();
            $(".tags1 select option").each(function(){
                if($(this).val() == option_value) $(this).attr("disabled", false);
            })
        })

        $(".tags2 select").change(function(){
            $(".tags2 select option:first-child").attr("disabled", true);
            var selected_value = $(".tags2 select option:selected").val();
            var selected_text = $(".tags2 select option:selected").text();
            var selected_color = $(".tags2 select option:selected").attr("data-color");
            var selected_id = $(".tags2 select option:selected").attr("data-id");

            var tag = '<div class="choosen-tag" data-option="'+selected_id+'" >'+
                '<span class="color-tag" style="background-color:'+selected_color+'"></span>'+
                '<input type="text" readonly name="selected_tags_text2[]" value="'+selected_text+'">'+
                '<input type="hidden" name="selected_tags_color2[]" value="'+selected_color+'">'+
                '<input type="hidden" name="selected_tags_id2[]" value="'+selected_id+'">'+
                '<a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>'+
                '</div>';
            $(".selected-tags2").append(tag);
            $(".tags2 select option:selected").attr("disabled", true);
        });


        $(".selected-tags2").on("click",".delete", function(e){
            var option_value = $(this).parent().attr("data-option");
            e.preventDefault(); $(this).parent('div').remove();
            $(".tags2 select option").each(function(){
                if($(this).val() == option_value) {
                    $(this).attr("disabled", false);
                }
            })
        })


        $( function() {
            $( "#tabs" ).tabs();
        } );


        setButtonClick('team0');
        setButtonClick('fac0');
        setButtonClick('proj0');

    });


    function setButtonClick(img_id){
        var mediaUploader, this_id, image, img_src;

        $('.upload-button-'+img_id).click(function(e) {
            e.preventDefault();

            this_id = $(this).attr("data-buttonid");
            image = $(this).attr("data-att-image");
            img_src = $(this).attr("data-img-src");

            // If the uploader object has already been created, reopen the dialog
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            // Extend the wp.media object
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                }, multiple: false });

            // When a file is selected, grab the URL and set it as the text field's value
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('.'+image+this_id).val(attachment.id);
                $('.upload-button-'+img_id).val("Canviar imatge");
                $('.'+img_src+this_id).attr( 'src', attachment.url);

            });
            // Open the uploader dialog
            mediaUploader.open();
        });
    }

})( jQuery );