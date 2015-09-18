/**
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2008-2015 AddThis, LLC                                     |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 2 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program; if not, write to the Free Software              |
 * | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
 * +--------------------------------------------------------------------------+
 */

jQuery(document).ready(function(jQuery) {
    jQuery( "#config-error" ).hide();
    jQuery( "#share-error" ).hide();
    jQuery( "#tabs" ).tabs();

    var thickDims, tbWidth, tbHeight, img = '';

    thickDims = function() {
        var tbWindow = jQuery('#TB_window'), H = jQuery(window).height(), W = jQuery(window).width(), w, h;

        w = (tbWidth && tbWidth < W - 90) ? tbWidth : W - 90;
        h = (tbHeight && tbHeight < H - 60) ? tbHeight : H - 60;
        if ( tbWindow.size() ) {
            tbWindow.width(w).height(h);
            jQuery('#TB_iframeContent').width(w).height(h - 27);
            tbWindow.css({'margin-left': '-' + parseInt((w / 2),10) + 'px'});
            if ( typeof document.body.style.maxWidth != 'undefined' )
                tbWindow.css({'top':'30px','margin-top':'0'});
        }
    };

    jQuery('#addthis_rating_thank_you').hide()

    switch(jQuery('#addthis_rate_us').val()) {
        case 'dislike':
            jQuery('#addthis_like_us_answers').hide();
            jQuery('#addthis_dislike').show();
            jQuery('#addthis_like').hide();
            break;
        case 'like':
            jQuery('#addthis_like_us_answers').hide();
            jQuery('#addthis_dislike').hide();
            jQuery('#addthis_like').show();
            break;
        case 'will not rate':
        case 'rated':
            jQuery('#addthis_do_you_like_us').hide()
            break;
        default:
            jQuery('#addthis_dislike').hide();
            jQuery('#addthis_like').hide();
    }

    jQuery('#addthis_dislike_confirm').click(function() {
        jQuery('#addthis_like_us_answers').hide();
        jQuery('#addthis_dislike').show();
        jQuery('#addthis_rate_us').val('dislike')
    });
    jQuery('#addthis_like_confirm').click(function() {
        jQuery('#addthis_like_us_answers').hide();
        jQuery('#addthis_like').show();
        jQuery('#addthis_rate_us').val('like')
    });
    jQuery('#addthis_not_rating').click(function() {
        jQuery('#addthis_do_you_like_us').hide()
        jQuery('#addthis_rate_us').val('will not rate')
    });
    jQuery('#addthis_rating').click(function() {
        jQuery('#addthis_rating_thank_you').show()
        jQuery('#addthis_rate_us').val('rated')
    });

    jQuery('a.thickbox-preview').click( function() {

        var previewLink = this;

        var values = {};
        jQuery.each(jQuery('#addthis-settings').serializeArray(), function(i, field) {

            var thisName = field.name
            if (thisName.indexOf("addthis_settings[") != -1 )
            {
                thisName = thisName.replace("addthis_settings[", '');
                thisName = thisName.replace("]", '');
            }

            values[thisName] = field.value;
        });

        var stuff = jQuery.param(values, true);

        var data = {
            action: 'at_save_transient',
            value : stuff
        };


        jQuery.post(ajaxurl, data, function(response) {

            // Fix for WP 2.9's version of lightbox
            if ( typeof tb_click != 'undefined' &&  jQuery.isFunction(tb_click.call))
            {
               tb_click.call(previewLink);
            }
            var href = jQuery(previewLink).attr('href');
            var link = '';


        if ( tbWidth = href.match(/&tbWidth=[0-9]+/) )
            tbWidth = parseInt(tbWidth[0].replace(/[^0-9]+/g, ''), 10);
        else
            tbWidth = jQuery(window).width() - 90;

        if ( tbHeight = href.match(/&tbHeight=[0-9]+/) )
            tbHeight = parseInt(tbHeight[0].replace(/[^0-9]+/g, ''), 10);
        else
            tbHeight = jQuery(window).height() - 60;

        jQuery('#TB_title').css({'background-color':'#222','color':'#dfdfdf'});
        jQuery('#TB_closeAjaxWindow').css({'float':'left'});
        jQuery('#TB_ajaxWindowTitle').css({'float':'right'}).html(link);
        jQuery('#TB_iframeContent').width('100%');
        thickDims();

        });
        return false;
    });

    var aboveCustom = jQuery('#above_custom_button');
    var aboveCustomShow = function(){
        if ( aboveCustom.is(':checked'))
        {
            jQuery('.above_option_custom').removeClass('hidden');
        }
        else
        {
            jQuery('.above_option_custom').addClass('hidden');
        }
    };

    var belowCustom = jQuery('#below_custom_button');
    var belowCustomShow = function(){
        if ( belowCustom.is(':checked'))
        {
            jQuery('.below_option_custom').removeClass('hidden');
        }
        else
        {
            jQuery('.below_option_custom').addClass('hidden');
        }
    };

    var aboveCustomString = jQuery('#above_custom_string');
    var aboveCustomStringShow = function(){
        if ( aboveCustomString.is(':checked'))
        {
            jQuery('.above_custom_string_input').removeClass('hidden');
        }
        else
        {
            jQuery('.above_custom_string_input').addClass('hidden');
        }
    };

    var belowCustomString = jQuery('#below_custom_string');
    var belowCustomStringShow = function(){
        if ( belowCustomString.is(':checked'))
        {
            jQuery('.below_custom_string_input').removeClass('hidden');
        }
        else
        {
            jQuery('.below_custom_string_input').addClass('hidden');
        }
    };

    aboveCustomShow();
    belowCustomShow();
    aboveCustomStringShow();
    belowCustomStringShow();

    jQuery('input[name="addthis_settings[above]"]').change( function(){aboveCustomShow(); aboveCustomStringShow();} );
    jQuery('input[name="addthis_settings[below]"]').change( function(){belowCustomStringShow();} );

    /**
     * Hide Theming and branding options when user selects version 3.0 or above
     */
    var ATVERSION_250 = 250;
    var AT_VERSION_300 = 300;
    var MANUAL_UPDATE = -1;
    var AUTO_UPDATE = 0;
    var REVERTED = 1;
    var atVersionUpdateStatus = jQuery("#addthis_atversion_update_status").val();
    if (atVersionUpdateStatus == REVERTED) {
        jQuery(".classicFeature").show();
    } else {
        jQuery(".classicFeature").hide();
    }

    /**
     * Revert to older version after the user upgrades
     */
    jQuery(".addthis-revert-atversion").click(function(){
       jQuery("#addthis_atversion_update_status").val(REVERTED);
       jQuery("#addthis_atversion_hidden").val(ATVERSION_250);
       jQuery(this).closest("form").submit();
       return false;
    });
   /**
    * Update to a newer version
    */
   jQuery(".addthis-update-atversion").click(function(){
       jQuery("#addthis_atversion_update_status").val(MANUAL_UPDATE);
       jQuery("#addthis_atversion_hidden").val(AT_VERSION_300);
       jQuery(this).closest("form").submit();
       return false;
   });

   var addthis_credential_validation_status = jQuery("#addthis_credential_validation_status");
   var addthis_validation_message = jQuery("#addthis-credential-validation-message");
   var addthis_profile_validation_message = jQuery("#addthis-profile-validation-message");
   //Validate the Addthis credentials
   window.skipValidationInternalError = false;
   function validate_addthis_credentials() {
        jQuery.ajax(
            {"url" : addthis_option_params.wp_ajax_url,
             "type" : "post",
             "data" : {"action" : addthis_option_params.addthis_validate_action,
                      "addthis_profile" : jQuery("#addthis_profile").val()
                  },
             "dataType" : "json",
             "beforeSend" : function() {
                 jQuery(".addthis-admin-loader").show();
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
                     jQuery("#addthis-settings").submit();
                 } else {
                     addthis_validation_message.html(data.credentialmessage);
                     addthis_profile_validation_message.html(data.profilemessage);
                     if (data.profilemessage != "") {
                         jQuery('html, body').animate({"scrollTop":0}, 'slow');
                     }
                 }

             },
             "complete" :function(data) {
                 jQuery(".addthis-admin-loader").hide();
             },
             "error" : function(jqXHR, textStatus, errorThrown) {
                 console.log(textStatus, errorThrown);
             }
         });
    }

    jQuery("#addthis_profile").change(function(){
       addthis_credential_validation_status.val(0);
       if(jQuery.trim(jQuery("#addthis_profile").val()) == "") {
            addthis_profile_validation_message.next().hide();
       }
    });

    jQuery('#addthis_config_json').focusout(function() {
        var error = 0;
        if (jQuery('#addthis_config_json').val() != " ") {
            try {
                var addthis_config_json = jQuery.parseJSON(jQuery('#addthis_config_json').val());
            }
                catch (e) {
                    jQuery('#config-error').show();
                    error = 1;
                }
        }
        if (error == 0) {
            jQuery('#config-error').hide();
            return true;
        } else {
            jQuery('#config-error').show();
            return false;
        }
    });

    jQuery('#addthis_share_json').focusout(function() {
        var error = 0;
        if (jQuery('#addthis_share_json').val() != " ") {
            try {
                var addthis_share_json = jQuery.parseJSON(jQuery('#addthis_share_json').val());
            }
            catch (e) {
                jQuery('#share-error').show();
                error = 1;
            }
        }
        if (error == 0) {
            jQuery('#share-error').hide();
            return true;
        } else {
            jQuery('#share-error').show();
            return false;
        }
    });

    jQuery('#addthis_layers_json').focusout(function() {
        var error = 0;
        if (jQuery('#addthis_layers_json').val() != " ") {
            try {
                var addthis_layers_json = jQuery.parseJSON(jQuery('#addthis_layers_json').val());
            }
            catch (e) {
                jQuery('#layers-error').show();
                error = 1;
            }
        }
        if (error == 0) {
            jQuery('#layers-error').hide();
            return true;
        } else {
            jQuery('#layers-error').show();
            return false;
        }
    });

    jQuery('.addthis-submit-button').click(function() {
        jQuery('#config-error').hide();
        jQuery('#share-error').hide();
        var error = 0;
        if (jQuery('#addthis_config_json').val() != " ") {
            try {
                var addthis_config_json = jQuery.parseJSON(jQuery('#addthis_config_json').val());
            }
            catch (e) {
                jQuery('#config-error').show();
                error = 1;
            }
        }
        if (jQuery('#addthis_share_json').val() != " ") {
            try {
                var addthis_share_json = jQuery.parseJSON(jQuery('#addthis_share_json').val());
            }
            catch (e) {
                jQuery('#share-error').show();
                error = 1;
            }
        }
        if (jQuery('#addthis_layers_json').val() != " ") {
            try {
                var addthis_layers_json = jQuery.parseJSON(jQuery('#addthis_layers_json').val());
            }
            catch (e) {
                jQuery('#layers-error').show();
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
        var services = jQuery('#'+posn+'-chosen-list').val();
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
        var services = jQuery('#'+posn+'-chosen-list').val();
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

    if(jQuery('#large_toolbox_above').is(':checked')) {
       jQuery('.above_button_set').show();
    } else if(jQuery('#fb_tw_p1_sc_above').is(':checked')) {
        jQuery('.above_button_set').show();
    } else if(jQuery('#small_toolbox_above').is(':checked')) {
        jQuery('.above_button_set').show();
    } else if(jQuery('#button_above').is(':checked')) {
        jQuery('.above_button_set').hide();
    } else if(jQuery('#above_custom_string').is(':checked')) {
        jQuery('.above_button_set').hide();
    }

    if(jQuery('#large_toolbox_below').is(':checked')) {
        jQuery('.below_button_set').show();
    } else if(jQuery('#fb_tw_p1_sc_below').is(':checked')) {
        jQuery('.below_button_set').show();
    } else if(jQuery('#small_toolbox_below').is(':checked')) {
        jQuery('.below_button_set').show();
    } else if(jQuery('#button_below').is(':checked')) {
        jQuery('.below_button_set').hide();
    } else if(jQuery('#below_custom_string').is(':checked')) {
        jQuery('.below_button_set').hide();
    }

    jQuery("#large_toolbox_above").click( function() {
        if(jQuery('#above-chosen-list').val() != '') {
            var newserv = revertServices('above');
            jQuery('#above-chosen-list').val(newserv);
        }
        jQuery('.above_button_set').show();
    });

    jQuery("#large_toolbox_below").click( function() {
        if(jQuery('#below-chosen-list').val() != '') {
            var newserv = revertServices('below');
            jQuery('#below-chosen-list').val(newserv);
        }
        jQuery('.below_button_set').show();
    });

    jQuery("#fb_tw_p1_sc_above").click( function() {
        if(jQuery('#above-chosen-list').val() != '') {
            var newserv = rewriteServices('above');
            jQuery('#above-chosen-list').val(newserv);
        }
        jQuery('.above_button_set').show();
    });

    jQuery("#fb_tw_p1_sc_below").click( function() {
        if(jQuery('#below-chosen-list').val() != '') {
            var newserv = rewriteServices('below');
            jQuery('#below-chosen-list').val(newserv);
        }
        jQuery('.below_button_set').show();
    });

    jQuery("#small_toolbox_above").click( function() {
        if(jQuery('#above-chosen-list').val() != '') {
            var newserv = revertServices('above');
            jQuery('#above-chosen-list').val(newserv);
        }
        jQuery('.above_button_set').show();
    });

    jQuery("#small_toolbox_below").click( function() {
        if(jQuery('#below-chosen-list').val() != '') {
            var newserv = revertServices('below');
            jQuery('#below-chosen-list').val(newserv);
        }
        jQuery('.below_button_set').show();
    });

    jQuery("#button_above").click( function() {
        jQuery('.above_button_set').show();
    });

    jQuery("#above_custom_string").click( function() {
        if(jQuery(this).is(':checked')){
            jQuery('.above_button_set').hide();
        } else {
            jQuery('.above_button_set').show();
        }
    });

    jQuery("#button_below").click( function() {
        jQuery('.below_button_set').show();
    });

    jQuery("#below_custom_string").click( function() {
        if(jQuery(this).is(':checked')){
            jQuery('.below_button_set').hide();
        } else {
            jQuery('.below_button_set').show();
        }
    });

    jQuery('.addthis-submit-button').click(function() {
        if(jQuery('#above-disable-smart-sharing').is(':checked')) {
            if(jQuery('#button_above').is(':checked')) {
                jQuery('#above-chosen-list').val('');
            } else {
                var list = [];
                jQuery('.above-smart-sharing-container .selected-services .ui-sortable').each(function(){
                    var service = '';
                    jQuery(this).find('li').each(function(){
                        if(jQuery(this).hasClass('enabled')) {
                            list.push(jQuery(this).attr('data-service'));
                            if(jQuery(this).attr('data-service') == 'compact') {
                                list.push('counter');
                            }
                        }
                    });
                });
                var aboveservices = list.join(', ');
                jQuery('#above-chosen-list').val(aboveservices);
            }
        }
        if(jQuery('#button_above').is(':checked')) {
            jQuery('#above-chosen-list').val('');
        }

        if(jQuery('#below-disable-smart-sharing').is(':checked')) {
            if(jQuery('#button_below').is(':checked')) {
                jQuery('#below-chosen-list').val('');
            } else {
                var list = [];
                jQuery('.below-smart-sharing-container .selected-services .ui-sortable').each(function(){
                    var service = '';
                    jQuery(this).find('li').each(function(){
                        if(jQuery(this).hasClass('enabled')) {
                            list.push(jQuery(this).attr('data-service'));
                            if(jQuery(this).attr('data-service') == 'compact') {
                                list.push('counter');
                            }
                        }
                    });
                });
                var belowservices = list.join(', ');
                jQuery('#below-chosen-list').val(belowservices);
            }
        }
        if(jQuery('#button_below').is(':checked')) {
            jQuery('#below-chosen-list').val('');
        }

    });

    var dataContent = '';
    var dataTitle = '';
    var innerContent = '';
    var left = 0;
    var top = 0;
    var popoverHeight = 0;
    var parent;
    var me;
    jQuery('.row-right a').mouseover(function(){
        me = jQuery(this);
        parent = jQuery(me).parent();

        dataContent = jQuery(parent).attr('data-content');
        dataTitle = jQuery(parent).attr('data-original-title');
        innerContent = "<div class='popover fade right in' style='display: block;'><div class='arrow'></div><h3 class='popover-title'>";
        innerContent =  innerContent + dataTitle;
        innerContent = innerContent + "</h3><div class='popover-content'>";
        innerContent = innerContent + dataContent;
        innerContent = innerContent + "</div></div>";
        jQuery(parent).append(innerContent);

        popoverHeight = jQuery(parent).find('.popover').height();
        left = jQuery(me).position().left + 15;
        top = jQuery(me).position().top - (popoverHeight/2) + 8;

        jQuery(parent).find('.popover').css({
            'left': left+'px',
            'top': top+'px'
        });
    });
    jQuery('.row-right a').mouseout(function(){
        jQuery('.popover').remove();
    });

});

