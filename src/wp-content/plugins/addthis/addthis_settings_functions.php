<?php
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

/**
 * Get the list of styles
 */
function _get_style_options()
{
    global $addthis_new_styles;
    return $addthis_new_styles;
}

/**
 * AddThis replacement for kses
 *
 */
function addthis_kses($string, $customstyles)
{
    global $allowedposttags;
    $mytags = $allowedposttags;
    $mytags['a'][ 'gplusonesize' ] = array();
    $mytags['a'][ 'gplusonecount' ]= array();
    $mytags['a'][ 'gplusoneannotation' ]= array();
    $mytags['a'][ 'fblikelayout' ]= array();
    $mytags['a'][ 'fblikesend' ]= array();
    $mytags['a'][ 'fblikeshowfaces' ]= array();
    $mytags['a'][ 'fblikewidth' ]= array();
    $mytags['a'][ 'fblikeaction' ]= array();
    $mytags['a'][ 'fblikefont' ]= array();
    $mytags['a'][ 'fblikecolorscheme' ]= array();
    $mytags['a'][ 'fblikeref' ]= array();
    $mytags['a'][ 'fblikehref' ]= array();
    $mytags['a'][ 'fbsharelayout' ]= array();
    $mytags['a'][ 'fblikelocale' ]= array();
    $mytags['a'][ 'twcount' ]= array();
    $mytags['a'][ 'twurl' ]= array();
    $mytags['a'][ 'twvia' ]= array();
    $mytags['a'][ 'twtext' ]= array();
    $mytags['a'][ 'twrelated' ]= array();
    $mytags['a'][ 'twlang' ]= array();
    $mytags['a'][ 'twhashtags' ]= array();
    $mytags['a'][ 'twcounturl' ]= array();
    $mytags['a'][ 'twscreenname' ]= array();
    $mytags['a'][ 'pipinitlayout' ]= array();
    $mytags['a'][ 'pipiniturl' ]= array();
    $mytags['a'][ 'pipinitmedia' ]= array();
    $mytags['a'][ 'pipinitdescription' ]= array();

    $pretags = array( 'g:plusone:', 'fb:like:', 'tw:', 'pi:pinit:', 'fb:share:layout', 'fb:like:locale');
    $posttags = array('gplusone', 'fblike', 'tw', 'pipinit', 'fbsharelayout', 'fblikelocale');

    foreach($pretags as $i => $attr)
    {
        $pre_pattern[] = '/'.$attr.'/';
        $pretags[$i] = ' '.$attr;
    }
    foreach($posttags as $i => $attr)
    {
        $post_pattern[] = '/[^_]'.$attr.'/';
        $posttags[$i] = ' '.$attr;
    }

    $temp_string = preg_replace( $pre_pattern, $posttags, $string);
    if (strpos($temp_string, "twscreen_name") != false) {
        $temp_string = str_replace('twscreen_name', 'twscreenname', $temp_string);
    }
    if (strpos($temp_string, "fblikeshow_faces") != false) {
        $temp_string = str_replace('fblikeshow_faces', 'fblikeshowfaces', $temp_string);
    }

    $new_temp_string = wp_kses($temp_string, $mytags);

    // Add in our %s so that the url and title get added properly
    if (!preg_match('/(<img[^>]+>)/i', $string, $matches)) {
        $new_string = preg_replace( $post_pattern, $pretags, $new_temp_string);
        $new_string = substr_replace($new_string, $customstyles, 4, 0);
    }
    else {
        $new_string = substr_replace($new_temp_string, $customstyles, 4, 0);
    }

    if (strpos($new_string, "tw:screenname") != false) {
        $new_string = str_replace('tw:screenname', 'tw:screen_name', $new_string);
    }
    if (strpos($new_string, "fb:like:showfaces") != false) {
        $new_string = str_replace('fb:like:showfaces', 'fb:like:show_faces', $new_string);
    }

    return $new_string;
}
/**
 * Add this version notification message
 * @param int $atversion_update_status
 * @param int $atversion
 */
function _addthis_version_notification($atversion_update_status, $atversion)
{
    //Fresh install Scenario. ie., atversion = 300 without reverting back. 
    if($atversion_update_status == ADDTHIS_ATVERSION_AUTO_UPDATE && $atversion >= ADDTHIS_ATVERSION) {
            return;
    }
    $imgLocationBase = apply_filters( 'addthis_files_uri',  plugins_url( '' , basename(dirname(__FILE__)))) . '/addthis/img/'  ;
    ob_start();
    // In the automatic update by the system the $atversion_update_status is 0
    // On subsequent update using notification link the  $atversion_update_status = -1
    // In both cases display the revert link
    if ($atversion_update_status == ADDTHIS_ATVERSION_AUTO_UPDATE || $atversion_update_status == ADDTHIS_ATVERSION_MANUAL_UPDATE) {
        ?>
        <div class="addthis-notification addthis-success-message">
            <div style="float:left">Your AddThis sharing plugin has been updated.</div>
            <div style="float:right">
<!--XTEC ************ MODIFICAT - Localization support
//2013.05.21 @jmiro227 -->
                <a href="#" class="addthis-revert-atversion"><?php _e('Revert back to previous version', 'addthis_trans_domain' ); ?></a>
<!--************ ORIGINAL
               <a href="#" class="addthis-revert-atversion">Revert back to previous version</a>
************ FI -->
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="addthis-notification addthis-warning-message">
<!--XTEC ************ MODIFICAT - Localization support
//2013.05.21 @jmiro227 -->
            <div style="float:left"><?php _e('Update AddThis to activate new features that will make sharing even easier.', 'addthis_trans_domain' ); ?></div>
<!--************ ORIGINAL
            <div style="float:left">Update AddThis to activate new features that will make sharing even easier.</div>
************ FI -->
            <div style="float:right">
                <a href="#" class="addthis-update-atversion"><img src="<?php echo $imgLocationBase . 'update.png';?>" /></a>
            </div>
        </div>       
        <?php
    }
    $notification_content = ob_get_contents();
    ob_end_clean();
    return $notification_content;
}
/**
 * Swap the order of occurrence of two keys in an associative array
 * @param type $array
 * @param type $key
 */
function _addthis_swap_first_two_elements (&$array, $key)
{
   $temp        = array($key => $array[$key]);
   unset($array[$key]);
   $array = $temp + $array;
}

/**
 * The icon choser row.  Should be made to look a bit prettier
 */
 function _addthis_choose_icons($name, $options)
 {
    global $addThisConfigs;
    global $cmsConnector;

    $addthis_new_styles = _get_style_options();
    $addthis_default_options = $addThisConfigs->getConfigs();

    extract($options);
    if ($name == 'above') {
        _addthis_swap_first_two_elements($addthis_new_styles, 'large_toolbox');
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
        $titleAdjective = __('Above','addthis_trans_domain');
//************ ORIGINAL
//        $titleAdjective = 'Above';
//************ FI
        $option = $above;
        $custom_size = $above_custom_size;
        $do_custom_services  = ( isset( $above_do_custom_services ) && $above_do_custom_services  ) ? 'checked="checked"' : '';
        $do_custom_preferred = ( isset( $above_do_custom_preferred ) &&  $above_do_custom_preferred ) ? 'checked="checked"' : '';
        $custom_services = $above_custom_services;
        $custom_preferred  = $above_custom_preferred;
        $custom_more = $above_custom_more;
        $custom_string = $above_custom_string;
    } else {
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
        $titleAdjective = __('Below','addthis_trans_domain');
//************ ORIGINAL
//        $titleAdjective = 'Below';
//************ FI
        $option = $below;
        $custom_size =  $below_custom_size;
        $do_custom_services  = ( isset( $below_do_custom_services ) && $below_do_custom_services  ) ? 'checked="checked"' : '';
        $do_custom_preferred = ( isset( $below_do_custom_preferred ) &&  $below_do_custom_preferred ) ? 'checked="checked"' : '';
        $custom_services = $below_custom_services;
        $custom_preferred  = $below_custom_preferred;
        $custom_more = $below_custom_more;
        $custom_string = $below_custom_string;
    }
    $titleAdjectiveLower = strtolower($titleAdjective);
    ?>
    <table>
        <tr>
            <td id="<?php echo $name ?>" colspan="2">
              <fieldset>
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.18 @dgras-->
                  <legend class="hidden">
<!--                      <strong>--><?php //__(sprintf("Sharing %s Post", $titleAdjective),'addthis_trans_domain') ?><!--</strong>-->
                      <strong><?php echo sprintf(__("Sharing %s Post",'addthis_trans_domain'),'addthis_trans_domain')  ?></strong>
                  </legend>
                  <div class="Sharing-Icons-options-container">
<!--                      <p>--><?php //_e(sprintf("These buttons will appear %s your blog posts.",$titleAdjectiveLower), 'addthis_trans_domain') ?><!--</p>-->
                      <p><?php echo sprintf(__("These buttons will appear %s your blog posts.",'addthis_trans_domain'),$titleAdjectiveLower) ?></p>
<!--************ ORIGINAL-->
<!--                  <legend class="hidden">-->
<!--                      <strong>--><?php //_e("Sharing $titleAdjective Post", 'addthis_trans_domain') ?><!--</strong>-->
<!--                  </legend>-->
<!--                  <div class="Sharing-Icons-options-container">-->
<!--                  <p>--><?php //_e("These buttons will appear $titleAdjectiveLower your blog posts.", 'addthis_trans_domain') ?><!--</p>-->
<!--************ FI-->
                <?php

                foreach ($addthis_new_styles as $k => $v)
                {
                    $checked = '';
                    if ($option == $k || ($option == 'none' && $k == $addthis_default_options[$name]  ) ){
                        $checked = 'checked="checked"';
                    }
                    if ($checked === '' && isset($v['defaultHide']) && $v['defaultHide'] == true)
                        continue;
                    echo "
                        <div class='$name"."_option select_row'>
                            <span class='radio'>
                                <input
                                    $checked
                                    type='radio'
                                    value='".$k."'
                                    id='{$k}_{$name}'
                                    name='addthis_settings[$name]'
                                />
                            </span>
                            <label for='{$k}_{$name}'>
                                <span class=\"addthis-checkbox-label\">
                                    <img alt='".$k."' src='". $cmsConnector->getPluginImageFolderUrl() . $v['img'] ."' />
                                </span>
                            </label>
                        </div>";
                }

                $checked = '';
                if ($option == 'custom_string' || $option == 'none' && 'custom_strin' == $addthis_default_options[$name] )
                {
                    $checked = 'checked="checked"';
                }
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
                echo "
                    <div class='$name"."_option select_row'>
                        <span class='radio mt4'>
                            <input
                                $checked
                                type='radio'
                                value='custom_string'
                                name='addthis_settings[$name]'
                                id='$name"."_custom_string'
                            />
                        </span>
                        <label for='{$name}_custom_string'>
                            <span class=\"addthis-checkbox-label\">
                                ". __('Advanced API button configuration','addthis_trans_domain')."
                            </span>
                        </label>
                    </div>";
                printf("<div style='max-width: 555px;margin-left:20px' class='%s_custom_string_input'>", $name );
                _e("This text box allows you to enter any AddThis markup that you wish. To see examples of what you can do, visit <a href='https://www.addthis.com/get/sharing'>AddThis.com Sharing Tools</a> and select any sharing tool. You can also check out our <a href='http://support.addthis.com/customer/portal/articles/1365467-preferred-services-personalization'>Client API</a>. For any help you may need, please visit <a href='http://support.addthis.com'>AddThis Support</a>",'addthis_trans_domain');
                echo "</div>";
//************ ORIGINAL
//                echo "
//                    <div class='$name"."_option select_row'>
//                        <span class='radio mt4'>
//                            <input
//                                $checked
//                                type='radio'
//                                value='custom_string'
//                                name='addthis_settings[$name]'
//                                id='$name"."_custom_string'
//                            />
//                        </span>
//                        <label for='{$name}_custom_string'>
//                            <span class=\"addthis-checkbox-label\">
//                                Advanced API button configuration
//                            </span>
//                        </label>
//                    </div>";
//                _e( sprintf("<div style='max-width: 555px;margin-left:20px' class='%s_custom_string_input'> This text box allows you to enter any AddThis markup that you wish. To see examples of what you can do, visit <a href='https://www.addthis.com/get/sharing'>AddThis.com Sharing Tools</a> and select any sharing tool. You can also check out our <a href='http://support.addthis.com/customer/portal/articles/1365467-preferred-services-personalization'>Client API</a>. For any help you may need, please visit <a href='http://support.addthis.com'>AddThis Support</a></div>", $name ),'addthis_trans_domain');
//************ FI

                echo "<textarea rows='5' name='addthis_settings[$name"."_custom_string]' class='$name"."_custom_string_input' />".esc_textarea($custom_string)."</textarea>";

                $class = 'hidden';
                $checked = '';
                if ($option == 'custom' || ($option == 'none' && 'custom' == $addthis_default_options[$name]  ) ) {
                    $checked = 'checked="checked"';
                    $class = '';

                    echo "<div class='$name"."_option select_row $class mt20'><span class='radio mt4'><input $checked type='radio' value='custom' name='addthis_settings[$name]' id='$name"."_custom_button' /></span> Build your own</div>";

                    echo "<ul class='$name"."_option_custom hidden'>";
                    $custom_16 = ($custom_size == 16) ? 'selected="selected"' : '' ;
                    $custom_32 = ($custom_size == 32) ? 'selected="selected"' : '' ;

                    echo "<li class='nocheck'><span class='at_custom_label'>Size:</span><select name='addthis_settings[$name"."_custom_size]'><option value='16' $custom_16 >16x16</option><option value='32' $custom_32 >32x32</option></select><br/><span class='description'>The size of the icons to display</span></li>";
                    echo "<li><input $do_custom_services class='at_do_custom'  type='checkbox' name='addthis_settings[$name"."_do_custom_services]' value='true' /><span class='at_custom_label'>Services to always show:</span><input class='at_custom_input' name='addthis_settings[$name"."_custom_services]' value='$custom_services'/><br/><span class='description'>Enter a comma-separated list of <a href='//addthis.com/services'>service codes</a> </span></li>";
                    echo "<li><input type='checkbox' $do_custom_preferred class='at_do_custom'  name='addthis_settings[$name"."_do_custom_preferred]' value='true' /><span class='at_custom_label'>Automatically personalized:</span>
                        <select name='addthis_settings[$name"."_custom_preferred]' class='at_custom_input'>";
                        for($i=0; $i <= 11; $i++)
                        {
                            $selected = '';
                            if ($custom_preferred == $i)
                                $selected = 'selected="selected"';
                            echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';

                        }
                    echo "</select><br/><span class='description'>Enter the number of automatically user-personalized items you want displayed</span></li>";
                    $custom_more = ( $custom_more ) ? 'checked="checked"' : '';

                    echo "<li><input $custom_more type='checkbox' class='at_do_custom' name='addthis_settings[$name"."_custom_more]' value='true' /><span class='at_custom_label'>More</span><br/><span class='description'>Display our iconic logo that offers sharing to over 330 destinations</span></li>";
                    echo "</ul></div>";

                }
                echo '</div>';
                ?>
              </div>
                  <?php
                    _addthis_print_services_picker($name, $options);
                  ?>

        </fieldset>
    </td>
</tr>
</table>
<?php
 }

/**
 * Prints the header and list of checkboxes of templates on which AddThis
 * buttons can be seen.
 * @param string $type 'above', 'below', 'sidebar'
 * @return bool true on success, false on failure
 */
function _addthis_print_template_checkboxes($type) {
    global $addThisConfigs;
    global $cmsConnector;

    $types = $cmsConnector->getSharingButtonLocations();
    if (!in_array($type, $types)) {
        return false;
    }
    $addthis_options = $addThisConfigs->getConfigs();

    $template_checkboxes_config = $addThisConfigs->getFieldsForContentTypeSharingLocations(
        null,
        $type
    );

    ?>
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.18 @dgras-->
    <?php echo "<h4>". __('Templates','addthis_trans_domain')."</h4>"; ?>
    <?php echo "<p>". __('These are the page templates on which your buttons will appear.','addthis_trans_domain')."</p>" ?>
<!--************ ORIGINAL-->
<!--            <h4>Templates</h4>-->
<!--            <p>These are the page templates on which your buttons will appear.</p>-->
<!--************ FI-->

    <ul>
        <?php
        foreach ($template_checkboxes_config as $checkbox) {
            $fieldName = $checkbox['fieldName'];
            $displayName = $checkbox['displayName'];
            $explanation = $checkbox['explanation'];

            $checked = '';
            if ($addthis_options[$fieldName]) {
                $checked = 'checked="checked"';
            }

            $listItemStart = "
                <li>
                    <input
                        type=\"checkbox\"
                        id=\"$fieldName\"
                        name=\"addthis_settings[$fieldName]\"
                        value=\"true\" $checked
                    />
                        <label for=\"$fieldName\">
                        <span class=\"addthis-checkbox-label\">
            ";

            $listItemEnd = "
                        </span>
                    </label>
                    <span class=\"at-wp-tooltip\" tooltip=\"$explanation\">?</span>
                </li>\n";

            echo $listItemStart;
            _e($displayName, 'addthis_trans_domain');
            echo $listItemEnd;
        }
        ?>
    </ul>
    <?php
    return true;
}

function _addthis_print_services_picker($name, $options) {

    $checkedValue = ' checked="checked"';
    $autoServicesChecked = '';
    $customServicesChecked = '';
    $autoServicesKey = $name.'_auto_services';
    if ($options[$autoServicesKey]) {
        $autoServicesChecked = $checkedValue;
    } else {
        $customServicesChecked = $checkedValue;
    }

    ?>

    <div class="<?php echo $name;?>_button_set select_row Sharing-Icons-options-container">
        <div id="<?php echo $name;?>_custom_btns">
            <span class="<?php echo $name;?>-smart-sharing-container">
                <span class="smart-sharing-inner-container">
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.18 @dgras-->
                    <?php echo "<h4>". __('Services','addthis_trans_domain').":</h4>"; ?>
                    <?php echo "<p>". __('AddThis boosts sharing by automatically showing the right buttons to each user based on their location and activity across the web.','addthis_trans_domain')."</p>" ?>
<!--************ ORIGINAL-->
<!--                    <h4>Services:</h4>-->
<!--                    <p>AddThis boosts sharing by automatically showing the right buttons to-->
<!--                        each user based on their location and activity across the web.</p>-->
<!--//************ FI-->
                    <div class="above_option select_row">
                        <span class="radio mt4">
                            <input
                                type="radio"
                                checked="checked"
                                name="addthis_settings[<?php echo $autoServicesKey; ?>]"
                                id="<?php echo $name;?>-enable-smart-sharing"
                                <?php echo $autoServicesChecked; ?>
                                value="1"
                            />
                            <span class="addthis-checkbox-label">
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.18 @dgras-->
                                <?php echo "<strong>". __('Auto Personalization','addthis_trans_domain').":</strong>"; ?>
                                <?php echo "<span>". __('(recommended)','addthis_trans_domain')."</span>" ?>
<!--************ ORIGINAL-->
<!--                        <strong>Auto Personalization</strong>-->
<!--                        <span> (recommended)</span>-->
<!--************ FI-->
                            </span>
                        </span>
                    </div>
                    <div class="above_option select_row">
                        <span class="radio mt4 addthis-checkbox-label">
                            <input
                                type="radio"
                                name="addthis_settings[<?php echo $autoServicesKey; ?>]"
                                id="<?php echo $name;?>-disable-smart-sharing"
                                value="0"
                                <?php echo $customServicesChecked; ?>
                            >
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.18 @dgras-->
                            <?php echo "<strong>". __('Select Your Own','addthis_trans_domain').":</strong>"; ?>
<!--************ ORIGINAL-->
<!--                        <strong>Select Your Own</strong>-->
<!--************ FI-->
                        </span>
                    </div>
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.18 @dgras-->
                    <div class="customize-buttons">
                        <div class="sharing-buttons">
                            <h4 class="sortable-heading"><?php echo __("Button Options",'addthis_trans_domain')?></h4>
                            <input type="text" class="sharing-buttons-search" placeholder="<?php echo __("Find a service",'addthis_trans_domain');?>" maxlength="20" size="30" style="width: 230px; height: 41px; margin: 0;">
                            <ul class="sortable"></ul>
                        </div>
                        <div class="selected-services">
                            <h4 class="sortable-heading"><?php echo __("Selected Buttons",'addthis_trans_domain'); ?></h4>
                            <ul class="sortable" data-type="addthisButtons"></ul>
                        </div>
                    </div>
                    <a href="#" class="restore-default-options" style="float: left; padding-left: 100px;"><?php echo __("Restore default options",'addthis_trans_domain');?></a>
                    <div class="vertical-drag">
                        <p><?php echo __("Drag up or down to reorder services",'addthis_trans_domain') ?></p>
                    </div>
                    <div class="horizontal-drag">
                        <p><?php echo __("Drag across to add service",'addthis_trans_domain')?></p>
                    </div>
<!--************ ORIGINAL-->
<!--<div class="customize-buttons">-->
<!--    <div class="sharing-buttons">-->
<!--        <h4 class="sortable-heading">Button Options</h4>-->
<!--        <input type="text" class="sharing-buttons-search" placeholder="Find a service" maxlength="20" size="30" style="width: 230px; height: 41px; margin: 0;">-->
<!--        <ul class="sortable"></ul>-->
<!--    </div>-->
<!--    <div class="selected-services">-->
<!--        <h4 class="sortable-heading">Selected Buttons</h4>-->
<!--        <ul class="sortable" data-type="addthisButtons"></ul>-->
<!--    </div>-->
<!--</div>-->
<!--                    <a href="#" class="restore-default-options" style="float: left; padding-left: 100px;">Restore default options</a>-->
<!--                    <div class="vertical-drag">-->
<!--                        <p>Drag up or down to reorder services</p>-->
<!--                    </div>-->
<!--                    <div class="horizontal-drag">-->
<!--                        <p>Drag across to add service</p>-->
<!--                    </div>-->
<!--************ FI-->
                    <?php $list = $name.'_chosen_list'; ?>
                    <input type="hidden" id="<?php echo $name?>-chosen-list" name="addthis_settings[<?php echo $name;?>_chosen_list]" value="<?php echo $options[$list];?>"/>
                </span>
            </span>
            <script type="text/javascript">
                window.page = 'sharing-buttons';
            </script>
        </div>
    </div>

    <?php
    return true;
}

/**
 * Returns an array of template options
 * @return string[] an array of strings
 */
function _addthis_determine_template_type() {
    global $post;

    // determine page type
    if (is_home() || is_front_page()) {
        $type = 'home';
    } elseif (is_archive()) {
        $type = 'archives';
        if (is_category()) {
            $type = 'categories';
        }
    } elseif (is_page($post->ID)) {
        $type = 'pages';
    } elseif (is_single()) {
        $type = 'posts';
    } else {
        $type = false;
    }

    return $type;
}

/**
 * determines if the above buttons are enabled for exceprts
 * @return boolean true if enabled, false is disabled
 */
function _addthis_excerpt_buttons_enabled_above() {
    global $addThisConfigs;
    $options = $addThisConfigs->getConfigs();
    $enabled = (boolean)(isset($options['addthis_above_showon_excerpts']) && $options['addthis_above_showon_excerpts']);
    return $enabled;
}

/**
 * determines if the below buttons are enabled for exceprts
 * @return boolean true if enabled, false is disabled
 */
function _addthis_excerpt_buttons_enabled_below() {
    global $addThisConfigs;
    $options = $addThisConfigs->getConfigs();
    $enabled = (boolean)(isset($options['addthis_below_showon_excerpts']) && $options['addthis_below_showon_excerpts']);
    return $enabled;
}

/**
 * determines if the above or below buttons are enabled for exceprts
 * @return boolean true if enabled, false is disabled
 */
function _addthis_excerpt_buttons_enabled() {
    $addthis_above_showon_excerpts = _addthis_excerpt_buttons_enabled_above();
    $addthis_below_showon_excerpts = _addthis_excerpt_buttons_enabled_below();
    $enabled = (boolean)($addthis_above_showon_excerpts || $addthis_below_showon_excerpts);
    return $enabled;
}

if (!function_exists('array_replace_recursive')) {
  function array_replace_recursive($array, $array1) {
    if (!function_exists('addthis_recurse')) {
      function addthis_recurse($array, $array1) {
        foreach ($array1 as $key => $value) {
          if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
            $array[$key] = array();
          }

          if (is_array($value)) {
            $value = addthis_recurse($array[$key], $value);
          }

          $array[$key] = $value;
        }
      }

      return $array;
    }

    // handle the arguments, merge one by one
    $args = func_get_args();
    $array = $args[0];
    if (!is_array($array)) {
      return $array;
    }
    for ($i = 1; $i < count($args); $i++) {
      if (is_array($args[$i])) {
        $array = addthis_recurse($array, $args[$i]);
      }
    }

    return $array;
  }
}