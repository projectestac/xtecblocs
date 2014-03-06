jQuery(document).ready(function($) {   
	
	$( "#config-error" ).hide();
	$( "#share-error" ).hide();
    $( "#tabs" ).tabs();
    
    var thickDims, tbWidth, tbHeight, img = abvimg = belimg = ''; 
    
    thickDims = function() {
        var tbWindow = $('#TB_window'), H = $(window).height(), W = $(window).width(), w, h;
    
        w = (tbWidth && tbWidth < W - 90) ? tbWidth : W - 90;
        h = (tbHeight && tbHeight < H - 60) ? tbHeight : H - 60;
        if ( tbWindow.size() ) {
            tbWindow.width(w).height(h);
            $('#TB_iframeContent').width(w).height(h - 27);
            tbWindow.css({'margin-left': '-' + parseInt((w / 2),10) + 'px'});
            if ( typeof document.body.style.maxWidth != 'undefined' )
                tbWindow.css({'top':'30px','margin-top':'0'});
        }
    };

       
    $('a.thickbox-preview').click( function() {
        
        var previewLink = this;
        
        var $inputs = $('#addthis_settings :input');

        var values = {};
        $.each($('#addthis_settings').serializeArray(), function(i, field) {
            
            var thisName = field.name
            if (thisName.indexOf("addthis_settings[") != -1 )
            {
                thisName = thisName.replace("addthis_settings[", '');
                thisName = thisName.replace("]", '');
            }
            
            values[thisName] = field.value;
        });

        var stuff = $.param(values, true);

        var data = {
            action: 'at_save_transient',
            value : stuff
        };
        

        jQuery.post(ajaxurl, data, function(response) {

            // Fix for WP 2.9's version of lightbox 
            if ( typeof tb_click != 'undefined' &&  $.isFunction(tb_click.call))
            {
               tb_click.call(previewLink); 
            }
            var href = $(previewLink).attr('href');
            var link = '';


        if ( tbWidth = href.match(/&tbWidth=[0-9]+/) ) 
            tbWidth = parseInt(tbWidth[0].replace(/[^0-9]+/g, ''), 10); 
        else 
            tbWidth = $(window).width() - 90; 

        if ( tbHeight = href.match(/&tbHeight=[0-9]+/) ) 
            tbHeight = parseInt(tbHeight[0].replace(/[^0-9]+/g, ''), 10);
        else
            tbHeight = $(window).height() - 60;
            
        $('#TB_title').css({'background-color':'#222','color':'#dfdfdf'}); 
        $('#TB_closeAjaxWindow').css({'float':'left'}); 
        $('#TB_ajaxWindowTitle').css({'float':'right'}).html(link); 

        $('#TB_iframeContent').width('100%'); 

        thickDims(); 

        });
        return false;
    });

    var aboveCustom = $('#above_custom_button'); 
    var aboveCustomShow = function(){
        if ( aboveCustom.is(':checked'))
        {
            $('.above_option_custom').removeClass('hidden');
            $('#above .personalizedMessage').addClass('hidden');
        }
        else
        {
            $('.above_option_custom').addClass('hidden');
            $('#above .personalizedMessage').removeClass('hidden');
        }
    };

    var belowCustom = $('#below_custom_button'); 
    var belowCustomShow = function(){
        if ( belowCustom.is(':checked'))
        {
            $('.below_option_custom').removeClass('hidden');
            $('#below .personalizedMessage').addClass('hidden');
        }
        else
        {
            $('.below_option_custom').addClass('hidden');
            $('#below .personalizedMessage').removeClass('hidden');
        }
    };

    var show_above =  $('input[name="addthis_settings[show_above]"]');
    var show_below = $('input[name="addthis_settings[show_below]"]');
    if ( $('#input[name="addthis_settings[show_above]"]').is(':checked'))
    {
        $('.above_option').toggleClass('hide');
    }
   
    if ( $('#input[name="addthis_settings[show_below]"]').is(':checked'))
    {
        $('.below_option').toggleClass('hide');
    }
   
    $('input[name="addthis_settings[show_above]"]').change( function() {
        $('.above_option').toggleClass('hide');
    });

    $('input[name="addthis_settings[show_below]"]').change( function() {
        $('.below_option').toggleClass('hide');
    });

    var aboveCustomString = $('#above_custom_string'); 
    var aboveCustomStringShow = function(){
        if ( aboveCustomString.is(':checked'))
        {
            $('.above_custom_string_input').removeClass('hidden');
            $('#above .personalizedMessage').addClass('hidden');
        }
        else
        {
            $('.above_custom_string_input').addClass('hidden');
            $('#above .personalizedMessage').removeClass('hidden');
        }
    };

    var belowCustomString = $('#below_custom_string'); 
    var belowCustomStringShow = function(){
        if ( belowCustomString.is(':checked'))
        {
            $('.below_custom_string_input').removeClass('hidden');
            $('#below .personalizedMessage').addClass('hidden');
        }
        else
        {
            $('.below_custom_string_input').addClass('hidden');
            $('#below .personalizedMessage').removeClass('hidden');
        }
    };

    aboveCustomShow();
    belowCustomShow();
    aboveCustomStringShow();
    belowCustomStringShow();

    $('input[name="addthis_settings[above]"]').change( function(){aboveCustomShow(); aboveCustomStringShow();} );
    $('input[name="addthis_settings[below]"]').change( function(){belowCustomStringShow();} );

    /**
     * Hide Theming and branding options when user selects version 3.0 or above
     */   
    var ATVERSION_250 = 250;
    var AT_VERSION_300 = 300;
    var MANUAL_UPDATE = -1;
    var AUTO_UPDATE = 0;
    var REVERTED = 1;
    var atVersionUpdateStatus = $("#addthis_atversion_update_status").val();
    if (atVersionUpdateStatus == REVERTED) {
        $(".classicFeature").show();
    } else {
        $(".classicFeature").hide();
    }
    
    /**
     * Revert to older version after the user upgrades
     */
    $(".addthis-revert-atversion").click(function(){
       $("#addthis_atversion_update_status").val(REVERTED);
       $("#addthis_atversion_hidden").val(ATVERSION_250);
       $(this).closest("form").submit();
       return false;
    });
   /**
    * Update to a newer version
    */ 
   $(".addthis-update-atversion").click(function(){
       $("#addthis_atversion_update_status").val(MANUAL_UPDATE);
       $("#addthis_atversion_hidden").val(AT_VERSION_300);
       $(this).closest("form").submit();
       return false;
   });

   var addthis_credential_validation_status = $("#addthis_credential_validation_status");
   var addthis_validation_message = $("#addthis-credential-validation-message");
   var addthis_profile_validation_message = $("#addthis-profile-validation-message");
   //Validate the Addthis credentials
   window.skipValidationInternalError = false;
   function validate_addthis_credentials() {
        $.ajax(
            {"url" : addthis_option_params.wp_ajax_url,
             "type" : "post",
             "data" : {"action" : addthis_option_params.addthis_validate_action,
                      "addthis_profile" : $("#addthis_profile").val(),
                      "addthis_username" : $("#addthis_username").val(),
                      "addthis_password" : $("#addthis_password").val()
                  },
             "dataType" : "json",
             "beforeSend" : function() {
                 $(".addthis-admin-loader").show();
                 addthis_validation_message.html("").next().hide();
                 addthis_profile_validation_message.html("").next().hide();
             },
             "success": function(data) {
                 addthis_validation_message.show();
                 addthis_profile_validation_message.show();

                 if (data.credentialmessage == "error" || (data.profileerror == "false" && data.credentialerror == "false")) {
                     if (data.credentialmessage != "error") {
                         addthis_credential_validation_status.val(1);
                     } else {
                         window.skipValidationInternalError = true;
                     }
                     $("#addthis_settings").submit();
                 } else {
                     addthis_validation_message.html(data.credentialmessage);
                     addthis_profile_validation_message.html(data.profilemessage);
                     if (data.profilemessage != "") {
                         $('html, body').animate({"scrollTop":0}, 'slow');
                     }
                 }

             },
             "complete" :function(data) {
                 $(".addthis-admin-loader").hide();
             },
             "error" : function(jqXHR, textStatus, errorThrown) {
                 console.log(textStatus, errorThrown);
             }
         });
    }
    //Prevent default form submission
    $("#addthis_settings").submit(function(){
        if(window.skipValidationInternalError) {
            return true;
        }
        var isProfileEmpty = $.trim($("#addthis_profile").val()) == "";
        var isUsernameEmpty = $.trim($("#addthis_username").val()) == "";
        var isPasswordEmpty = $.trim($("#addthis_password").val()) == "";
        var isAnyFieldEmpty = isProfileEmpty || isUsernameEmpty || isPasswordEmpty;
        var validationRequired = addthis_credential_validation_status.val() == 0;
        
        if(isUsernameEmpty != isPasswordEmpty) {
            var emptyLabel = isUsernameEmpty ? "username" : "password";
            addthis_validation_message.html("&#x2716; AddThis " + emptyLabel + " is required to view analytics").next().hide();
            return false;
        } else if (isProfileEmpty && !isUsernameEmpty && !isPasswordEmpty) {
            addthis_profile_validation_message.html("&#x2716; AddThis profile ID is required to view analytics").next().hide();
            $('html, body').animate({"scrollTop":0}, 'slow');
            return false;
        } else if (!validationRequired || isAnyFieldEmpty) {
            return true;
        } else if(!isAnyFieldEmpty && validationRequired) {
            validate_addthis_credentials();
            return false;
        }
    });
    
    $("#addthis_username, #addthis_password, #addthis_profile").change(function(){
       addthis_credential_validation_status.val(0);
       if($.trim($("#addthis_profile").val()) == "") {
            addthis_profile_validation_message.next().hide();
       }
       if(($.trim($("#addthis_username").val()) == "") || ($.trim($("#addthis_password").val()) == "")) {
            addthis_validation_message.next().hide();
       }
    });

    $('#addthis-config-json').focusout(function() {
    	var error = 0;
		if ($('#addthis-config-json').val() != " ") {
		    try {
		    	var addthis_config_json = jQuery.parseJSON($('#addthis-config-json').val());
		    }
	      	    catch (e) {
					$('#config-error').show();
					error = 1;
			    }
		}
		if (error == 0) {
			$('#config-error').hide();
		    return true;
		} else {
			$('#config-error').show();
		    return false;
		}
    });
    
    $('#addthis-share-json').focusout(function() {
    	var error = 0;
    	if ($('#addthis-share-json').val() != " ") {
		    try {
		    	var addthis_share_json = jQuery.parseJSON($('#addthis-share-json').val());
		    }
	        catch (e) {
	        	$('#share-error').show();
	        	error = 1;
		    }
		}
    	if (error == 0) {
    		$('#share-error').hide();
		    return true;
		} else {
			$('#share-error').show();
		    return false;
		}
    });
    
    $('#submit-button').click(function() {
    	$('#config-error').hide();
		$('#share-error').hide();
		var error = 0;
		if ($('#addthis-config-json').val() != " ") {
		    try {
		    	var addthis_config_json = jQuery.parseJSON($('#addthis-config-json').val());
		    }
	      	catch (e) {
				$('#config-error').show();
				error = 1;
		    }
		}
		if ($('#addthis-share-json').val() != " ") {
		    try {
		    	var addthis_share_json = jQuery.parseJSON($('#addthis-share-json').val());
		    }
	        catch (e) {
				$('#share-error').show();
				error = 1;
		    }
		}
	    if (error == 0) {
		    return true;
		} else {
		    return false;
		}
     });
    
   
  //preview box
    function rewriteServices(posn) {
    	var services = $('#'+posn+'-chosen-list').val();
    	var service = services.split(', ');
	    var i;
	    var newservice = '';
	    for (i = 0; i < (service.length); ++i) {
			if(service[i] == 'linkedin') {
				newservice += 'linkedin_counter, ';
		    }
		    else if(service[i] == 'facebook') {
		    	newservice += 'facebook_like, ';
		    }
		    else if(service[i] == 'twitter') {
		    	newservice += 'tweet, ';
		    }
		    else if(service[i] == 'pinterest_share') {
		    	newservice += 'pinterest_pinit, ';
		    }
		    else if(service[i] == 'hyves') {
		    	newservice += 'hyves_respect, ';
		    }
		    else if(service[i] == 'google_plusone_share') {
		    	newservice += 'google_plusone, ';
		    }
		    else if(service[i] == 'counter' || service[i] == 'compact') {
		    	newservice += service[i]+', ';
		    }
	    }
	    var newservices = newservice.slice(0,-2);
	    return newservices;
    }
    
    function revertServices(posn) {
    	var services = $('#'+posn+'-chosen-list').val();
    	var service = services.split(', ');
	    var i;
	    var newservice = '';
	    for (i = 0; i < (service.length); ++i) {
			if(service[i] == 'facebook_like') {
				newservice += 'facebook, ';
		    }
		    else if(service[i] == 'linkedin_counter') {
		    	newservice += 'linkedin, ';
		    }
		    else if(service[i] == 'hyves_respect') {
		    	newservice += 'hyves, ';
		    }
		    else if(service[i] == 'google_plusone') {
		    	newservice += 'google_plusone_share, ';
		    }
		    else if(service[i] == 'tweet') {
		    	newservice += 'twitter, ';
		    }
		    else if(service[i] == 'pinterest_pinit') {
		    	newservice += 'pinterest_share, ';
		    }
		    else {
		    	newservice += service[i]+', ';
		    }
	    }
	    var newservices = newservice.slice(0,-2);
	    return newservices;
    }
    
    function updatePreview(posn, style, option) {
    	var preview = '';
    	var service = option.split(', ');
		var i;
    	
    	if(style == 'large_toolbox') {
    	   preview  += '<div class="addthis_toolbox addthis_default_style addthis_32x32_style">';
  		   for (i = 0; i < (service.length); ++i) {
  			   if(service[i] == 'counter') {
  				 preview += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block; float: left;" href="#" tabindex="0"></a>';
  			   } else {
  				 preview += '<span class="at300bs at15nc at15t_'+service[i]+' at16t_'+service[i]+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
  			   }
  		   }
  		   preview += '</div>';
    	}
    	else if(style == 'fb_tw_p1_sc') {
    		for (i = 0; i < (service.length); ++i) {
	    		if(service[i] == 'compact') {
	    			preview += '<img src="'+addthis_option_params.img_base+'addthis_pill_style.png">';
	    		}
			    else if(service[i] != 'counter') {
			    	preview += '<img src="'+addthis_option_params.img_base+service[i]+'.png">';
			    }
    		}
    	}
    	else if(style == 'small_toolbox') {
    		for (i = 0; i < (service.length); ++i) {
  			   if(service[i] == 'counter') {
  				   preview += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block; float:left;" href="#" tabindex="0"></a>';
  			   } else {
  				   preview += '<span class="at300bs at15nc at15t_'+service[i]+' at16t_'+service[i]+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
  			   }
  		   }
    	}
    	$('#'+posn+'_previewContainer').html(preview);
    }
    if($('#large_toolbox_above').is(':checked')) {
 	   if($('#above-chosen-list').val() == "") {
 		   abvimg = '<img alt="large_toolbox" src="'+addthis_option_params.img_base+'toolbox-large.png">';
 	   } else {
 		   abvimg += '<div class="addthis_toolbox addthis_default_style addthis_32x32_style">';
 		   var aboveserv = $('#above-chosen-list').val();
 		   var aservice = aboveserv.split(', ');
 		   var i;
 		   for (i = 0; i < (aservice.length); ++i) {
 			   if(aservice[i] == 'counter') {
 				   abvimg += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block; float: left;" href="#" tabindex="0"></a>';
 			   } else {
 	 			   abvimg += '<span class="at300bs at15nc at15t_'+aservice[i]+' at16t_'+aservice[i]+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
 			   }
 		   }
 		   abvimg += '</div>';
 	   }
 	   $('.above_button_set').css('opacity', 1);
 	   $('.above-customize-sharing-link').css('opacity', 1);
 	   $('.above-smart-sharing-container').show();
 	   $('#above_previewContainer').html(abvimg);
    } else if($('#fb_tw_p1_sc_above').is(':checked')) {
 		if($('#above-chosen-list').val() == "") {
 			abvimg = '<img alt="large_toolbox" src="'+addthis_option_params.img_base+'horizontal_share_rect.png">';
 		} else {
 			   var aboveserv = $('#above-chosen-list').val();
 			   var aservice = aboveserv.split(', ');
 			   var i;
 			   for (i = 0; i < (aservice.length); ++i) {
 				  if(aservice[i] == 'compact') {
 					 abvimg += '<img src="'+addthis_option_params.img_base+'addthis_pill_style.png">';
 				  }
 				  else if(aservice[i] != 'counter') {
 					  abvimg += '<img src="'+addthis_option_params.img_base+aservice[i]+'.png">';
 				  }
 			   }
 		   }
 		$('.above_button_set').css('opacity', 1);
 		$('.above-customize-sharing-link').css('opacity', 1);
 		$('.above-smart-sharing-container').show();
 		$('#above_previewContainer').html(abvimg);
 	} else if($('#small_toolbox_above').is(':checked')) {
 		if($('#above-chosen-list').val() == "") {
 			abvimg = '<img alt="large_toolbox" src="'+addthis_option_params.img_base+'toolbox-small.png">';
 		} else {
 		   var aboveserv = $('#above-chosen-list').val();
 		   var aservice = aboveserv.split(', ');
 		   var i;
 		   for (i = 0; i < (aservice.length); ++i) {
 			   if(aservice[i] == 'counter') {
 				  abvimg += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block; float:left;" href="#" tabindex="0"></a>';
 			   } else {
 	 			   abvimg += '<span class="at300bs at15nc at15t_'+aservice[i]+' at16t_'+aservice[i]+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
 			   }
 		   }
 		}
 		$('.above_button_set').css('opacity', 1);
 		$('.above-customize-sharing-link').css('opacity', 1);
 		$('.above-smart-sharing-container').show();
 		$('#above_previewContainer').html(abvimg);
 	} else if($('#button_above').is(':checked')) {
 		abvimg = '<img alt="large_toolbox" src="'+addthis_option_params.img_base+'horizontal_share.png">';
 		$('.above_button_set').css('opacity', 1);
 		$('.above-customize-sharing-link').css('opacity', 0);
 		$('.above-smart-sharing-container').hide();
 		$('#above_previewContainer').html(abvimg);
 	} else if($('#disable_above').is(':checked')) {
 		abvimg = '';
 		$('.above_button_set').css('opacity', 0.4);
 		$('.above-customize-sharing-link').css('opacity', 0);
 		$('.above-smart-sharing-container').hide();
 		$('#above_previewContainer').html(abvimg);
 	}
 	
 	if($('#large_toolbox_below').is(':checked')) {
 		if($('#below-chosen-list').val() == "") { 
 			belimg = '<img alt="large_toolbox" src="'+addthis_option_params.img_base+'toolbox-large.png">';
 		} else {
 			belimg += '<div class="addthis_toolbox addthis_default_style addthis_32x32_style">';
 		   var belowserv = $('#below-chosen-list').val();
 		   var bservice = belowserv.split(', ');
 		   var i;
 		   for (i = 0; i < (bservice.length); ++i) {
 			   if(bservice[i] == 'counter') {
 				  belimg +='<a class="addthis_counter addthis_bubble_style" style="display: inline-block; float: left;" href="#" tabindex="0"></a>';
 			   } else {
 	 			   belimg += '<span class="at300bs at15nc at15t_'+bservice[i]+' at16t_'+bservice[i]+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
 			   }
 		   }
 		   belimg += '</div>';

 		}
 		$('.below_button_set').css('opacity', 1);
 		$('.below-customize-sharing-link').css('opacity', 1);
 		$('.below-smart-sharing-container').show();
 		$('#below_previewContainer').html(belimg);
 	} else if($('#fb_tw_p1_sc_below').is(':checked')) {
 		if($('#below-chosen-list').val() == "") { 
 			belimg = '<img alt="large_toolbox" src="'+addthis_option_params.img_base+'horizontal_share_rect.png">';
 		} else {
 		   var belowserv = $('#below-chosen-list').val();
 		   var bservice = belowserv.split(', ');
 		   var i;
 		   for (i = 0; i < (bservice.length-1); ++i) {
 			  if(bservice[i] == 'compact') {
 				 belimg += '<img src="'+addthis_option_params.img_base+'addthis_pill_style.png">';
			  } else if(bservice[i] != 'counter') {
				  belimg += '<img src="'+addthis_option_params.img_base+bservice[i]+'.png">';
			  }
 		   }
 		}
 		$('.below_button_set').css('opacity', 1);
 		$('.below-customize-sharing-link').css('opacity', 1);
 		$('.below-smart-sharing-container').show();
 		$('#below_previewContainer').html(belimg);
 	} else if($('#small_toolbox_below').is(':checked')) {
 		if($('#below-chosen-list').val() == "") { 
 			belimg = '<img alt="large_toolbox" src="'+addthis_option_params.img_base+'toolbox-small.png">';
 		} else {
 			 var belowserv = $('#below-chosen-list').val();
	 		 var bservice = belowserv.split(', ');
	 		 var i;
	 		 for (i = 0; i < (bservice.length); ++i) {
	 			 if(bservice[i] == 'counter') {
	 				 belimg += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block; float: left;" href="#" tabindex="0"></a>';
	 			 } else {
	 				 belimg += '<span class="at300bs at15nc at15t_'+bservice[i]+' at16t_'+bservice[i]+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
	 			 }
	 		}
	 	}
 		$('.below_button_set').css('opacity', 1);
 		$('.below-customize-sharing-link').css('opacity', 1);
 		$('.below-smart-sharing-container').show();
 		$('#below_previewContainer').html(belimg);
 	} else if($('#button_below').is(':checked')) {
 		belimg = '<img alt="large_toolbox" src="'+addthis_option_params.img_base+'horizontal_share.png">';
 		$('.below_button_set').css('opacity', 1);
 		$('.below-customize-sharing-link').css('opacity', 0);
 		$('.below-smart-sharing-container').hide();
 		$('#below_previewContainer').html(belimg);
 	} else if($('#disable_below').is(':checked')) {
 		belimg = '';
 		$('.below_button_set').css('opacity', 0.4);
 		$('.below-customize-sharing-link').css('opacity', 0);
 		$('.below-smart-sharing-container').hide();
 		$('#below_previewContainer').html(belimg);
 	}

    $("#large_toolbox_above").click( function() {
    	if($('#above-chosen-list').val() != '') {
    		var newserv = revertServices('above');
    		updatePreview('above', 'large_toolbox', newserv);
    		$('#above-chosen-list').val(newserv);
    	}
    	else {
    		$('#above_previewContainer').html('<img alt="large_toolbox" src="'+addthis_option_params.img_base+'toolbox-large.png">');
    	}
    	$('.above_button_set').css('opacity', 1);
    	$('.above-customize-sharing-link').css('opacity', 1);
    	$('.above-smart-sharing-container').show();
    });
   
    $("#large_toolbox_below").click( function() {
    	if($('#below-chosen-list').val() != '') {
    		var newserv = revertServices('below');
    		updatePreview('below', 'large_toolbox', newserv);
    		$('#below-chosen-list').val(newserv);
    	}
    	else {
    		$('#below_previewContainer').html('<img alt="large_toolbox" src="'+addthis_option_params.img_base+'toolbox-large.png">');
    	}
    	$('.below_button_set').css('opacity', 1);
    	$('.below-customize-sharing-link').css('opacity', 1);
    	$('.below-smart-sharing-container').show();
    });

    $("#fb_tw_p1_sc_above").click( function() {
    	if($('#above-chosen-list').val() != '') {
    		var newserv = rewriteServices('above');
    		updatePreview('above', 'fb_tw_p1_sc', newserv);
    		$('#above-chosen-list').val(newserv);
    	}
    	else {
    		$('#above_previewContainer').html('<img alt="large_toolbox" src="'+addthis_option_params.img_base+'horizontal_share_rect.png">');
    	}
    	$('.above_button_set').css('opacity', 1);
    	$('.above-customize-sharing-link').css('opacity', 1);
    	$('.above-smart-sharing-container').show();
    });
    
    $("#fb_tw_p1_sc_below").click( function() {
    	if($('#below-chosen-list').val() != '') {
    		var newserv = rewriteServices('below');
    		updatePreview('below', 'fb_tw_p1_sc', newserv);
    		$('#below-chosen-list').val(newserv);
    	}
    	else {
    		$('#below_previewContainer').html('<img alt="large_toolbox" src="'+addthis_option_params.img_base+'horizontal_share_rect.png">');
    	}
    	$('.below_button_set').css('opacity', 1);
    	$('.below-customize-sharing-link').css('opacity', 1);
    	$('.below-smart-sharing-container').show();
    });
    
    $("#small_toolbox_above").click( function() {
    	if($('#above-chosen-list').val() != '') {
    		var newserv = revertServices('above');
    		updatePreview('above', 'small_toolbox', newserv);
    		$('#above-chosen-list').val(newserv);
    	}
    	else {
    		$('#above_previewContainer').html('<img alt="large_toolbox" src="'+addthis_option_params.img_base+'toolbox-small.png">');
    	}
    	$('.above_button_set').css('opacity', 1);
    	$('.above-customize-sharing-link').css('opacity', 1);
    	$('.above-smart-sharing-container').show();
    });
    
    $("#small_toolbox_below").click( function() {
    	if($('#below-chosen-list').val() != '') {
    		var newserv = revertServices('below');
    		updatePreview('below', 'small_toolbox', newserv);
    		$('#below-chosen-list').val(newserv);
    	}
    	else {
    		$('#below_previewContainer').html('<img alt="large_toolbox" src="'+addthis_option_params.img_base+'toolbox-small.png">');
    	}
    	$('.below_button_set').css('opacity', 1);
    	$('.below-customize-sharing-link').css('opacity', 1);
    	$('.below-smart-sharing-container').show();
    });
    
    $("#button_above").click( function() {
    	if($(this).is(':checked')){
    		$('#above_previewContainer').html('<img alt="large_toolbox" src="'+addthis_option_params.img_base+'horizontal_share.png">');
    	}
    	$('.above_button_set').css('opacity', 1);
    	$('.above-customize-sharing-link').css('opacity', 1);
    	$('.above-smart-sharing-container').show();
    });
    
    $("#button_below").click( function() {
    	if($(this).is(':checked')){
    		$('#below_previewContainer').html('<img alt="large_toolbox" src="'+addthis_option_params.img_base+'horizontal_share.png">');
    	}
    	$('.below_button_set').css('opacity', 1);
    	$('.below-customize-sharing-link').css('opacity', 1);
    	$('.below-smart-sharing-container').show();
    });
    
    $("#disable_above").click( function() {
    	if($(this).is(':checked')){
    		$('#above_previewContainer').html('');
    		$('.above_button_set').css('opacity', 0.4);
    		$('.above-customize-sharing-link').css('opacity', 0);
    		$('.above-smart-sharing-container').hide();
    	}
    });
    
    $("#disable_below").click( function() {
    	if($(this).is(':checked')){
    		$('#below_previewContainer').html('');
    		$('.below_button_set').css('opacity', 0.4);
    		$('.below-customize-sharing-link').css('opacity', 0);
    		$('.below-smart-sharing-container').hide();
    	}
    });
 
	$('#submit-button').click(function() {
		if($('#above-disable-smart-sharing').is(':checked')) { 
			if($('#button_above').is(':checked')) { 
				$('#above-chosen-list').val('');
			} else if($('#disable_above').is(':checked')) { 
				$('#above-chosen-list').val('');
			} else {
				var list = [];
				$('.above-smart-sharing-container .selected-services .ui-sortable').each(function(){
					var service = '';
			        $(this).find('li').each(function(){
			        	if($(this).hasClass('enabled')) {
			        		list.push($(this).attr('data-service'));
			        		if($(this).attr('data-service') == 'compact') {
			        			list.push('counter');
			        		}
			        	}
			        });
				});
				var aboveservices = list.join(', ');
				$('#above-chosen-list').val(aboveservices);
			}
		}
		if($('#button_above').is(':checked')) { 
			$('#above-chosen-list').val('');
		}
		if($('#disable_above').is(':checked')) { 
			$('#above-chosen-list').val('');
		}
		
		if($('#below-disable-smart-sharing').is(':checked')) { 
			if($('#button_below').is(':checked')) { 
				$('#below-chosen-list').val('');
			} else if($('#disable_below').is(':checked')) { 
				$('#below-chosen-list').val('');
			} else {
				var list = [];
				$('.below-smart-sharing-container .selected-services .ui-sortable').each(function(){
					var service = '';
			        $(this).find('li').each(function(){
			        	if($(this).hasClass('enabled')) {
			        		list.push($(this).attr('data-service'));
			        		if($(this).attr('data-service') == 'compact') {
			        			list.push('counter');
			        		}
			        	}
			        });
				});
				var belowservices = list.join(', ');
				$('#below-chosen-list').val(belowservices);
			}
		}
		if($('#button_below').is(':checked')) { 
			$('#below-chosen-list').val('');
		}
		if($('#disable_below').is(':checked')) { 
			$('#below-chosen-list').val('');
		}

	});
	
    var s = $("#preview_floater");
    var pos = s.position();                    
    $(window).scroll(function() {
        var windowpos = $(window).scrollTop();
        
        if (windowpos >= pos.top) {
            s.addClass("stick");
        } else {
            s.removeClass("stick"); 
        }
    });
	
    $('.folded #collapse-menu').click(function(){
    	$('#preview_floater').removeClass();
    	var s = $("#preview_floater");
        var pos = s.position();                    
        $(window).scroll(function() {
	        var windowpos = $(window).scrollTop();
	            
	        if (windowpos >= pos.top) {
	        	setTimeout(function(){$('#preview_floater').addClass('stick');},1);
	        }
        });
    }); 
    
    var dataContent = '';
	var dataTitle = '';
	var innerContent = '';
	var left = 0;
	var top = 0;
	var popoverHeight = 0;
	var parent;
	var me;
	$('.row-right a').mouseover(function(){
		me = $(this);
		parent = $(me).parent();

		dataContent = $(parent).attr('data-content');
		dataTitle = $(parent).attr('data-original-title');
		innerContent = "<div class='popover fade right in' style='display: block;'><div class='arrow'></div><h3 class='popover-title'>";
		innerContent =  innerContent + dataTitle;
		innerContent = innerContent + "</h3><div class='popover-content'>";
		innerContent = innerContent + dataContent;
		innerContent = innerContent + "</div></div>";
		$(parent).append(innerContent);

		popoverHeight = $(parent).find('.popover').height();
		left = $(me).position().left + 15;
		top = $(me).position().top - (popoverHeight/2) + 8;

		$(parent).find('.popover').css({
			'left': left+'px',
			'top': top+'px'
		});
	});
	$('.row-right a').mouseout(function(){ 
		$('.popover').remove();
	});
	
});

