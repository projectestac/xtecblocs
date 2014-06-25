<?php
/**
 * Get the list of styles
 */

function _get_style_options()
{
    global $addthis_new_styles;
    return apply_filters('addthis_style_options', $addthis_new_styles );
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
 * @param type $key1
 * @param type $key2
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
     $addthis_new_styles = _get_style_options();
     global $addthis_default_options;
     extract($options);
     if ($name == 'above')
     {
        _addthis_swap_first_two_elements($addthis_new_styles, 'large_toolbox');
        $legend = 'Top';
        $option = $above;
        $custom_size = $above_custom_size;
        $do_custom_services  = ( isset( $above_do_custom_services ) && $above_do_custom_services  ) ? 'checked="checked"' : '';
        $do_custom_preferred = ( isset( $above_do_custom_preferred ) &&  $above_do_custom_preferred ) ? 'checked="checked"' : '';
        $custom_services = $above_custom_services;
        $custom_preferred  = $above_custom_preferred;
        $custom_more = $above_custom_more;
        $custom_string = $above_custom_string;
     }
     else
     {
        $legend = 'Bottom';
        $option = $below;
        $custom_size =  $below_custom_size;
        $do_custom_services  = ( isset( $below_do_custom_services ) && $below_do_custom_services  ) ? 'checked="checked"' : '';
        $do_custom_preferred = ( isset( $below_do_custom_preferred ) &&  $below_do_custom_preferred ) ? 'checked="checked"' : '';
        $custom_services = $below_custom_services;
        $custom_preferred  = $below_custom_preferred;
        $custom_more = $below_custom_more;
        $custom_string = $below_custom_string;
     }
?>
        <tr>
            <td id="<?php echo $name ?>" colspan="2">
              <fieldset>  
		<legend>&nbsp;<strong><?php _e("$legend Sharing Tool", 'addthis_trans_domain') ?></strong> &nbsp;</legend>
                <div style="float: left; width: 395px;">
		<?php 
                    
                 $imgLocationBase = apply_filters( 'at_files_uri',  plugins_url( '' , basename(dirname(__FILE__)))) . '/addthis/img/'  ;
                 $imgLocationBase = apply_filters( 'addthis_files_uri',  plugins_url( '' , basename(dirname(__FILE__)))) . '/addthis/img/'  ;
                
                foreach ($addthis_new_styles as $k => $v)
                {
                    $checked = '';
                    if ($option == $k || ($option == 'none' && $k == $addthis_default_options[$name]  ) ){
                        $checked = 'checked="checked"';
                    }
                    if ($checked === '' && isset($v['defaultHide']) &&  $v['defaultHide'] == true)
                        continue;
                    echo "<div class='$name"."_option select_row'><span class='radio'><input $checked type='radio' value='".$k."' id='{$k}_{$name}' name='addthis_settings[$name]' /></span><label for='{$k}_{$name}'> <img alt='".$k."'  src='". $imgLocationBase  .  $v['img'] ."' align='left' /></label><div class='clear'></div></div>";
                }
                $ischecked = '';
                if ($option == 'disable' ){
                	$ischecked = 'checked="checked"';
                }

// ************ MODIFICAT - Localization support
//2014.03.28 @jmiro227
				echo "<div class='$name"."_option select_row'><span class='radio'><input type='radio' $ischecked value='disable' id='disable_{$name}' name='addthis_settings[$name]' /></span><label for='disable_{$name}'>";_e('Do not show a sharing tool at the', 'addthis_trans_domain' ); echo ' <strong>'; _e($legend, 'addthis_trans_domain' ); echo '</strong> ';_e('of posts', 'addthis_trans_domain' ); echo '</label></div>';
//************ ORIGINAL
//				echo "<div class='$name"."_option select_row'><span class='radio'><input type='radio' $ischecked value='disable' id='disable_{$name}' name='addthis_settings[$name]' /></span><label for='disable_{$name}'>Do not show a sharing tool at the <strong>$legend</strong> of posts</label></div>";
//************ FI -->
				
				$checked = '';
                if ($option == 'custom_string' || $option == 'none' && 'custom_strin' == $addthis_default_options[$name] )
                {
                    $checked = 'checked="checked"';
                }

// ************ MODIFICAT - Localization support
//2014.03.28 @jmiro227
                echo "<div class='$name"."_option select_row'><span class='radio mt4'><input $checked type='radio' value='custom_string' name='addthis_settings[$name]' id='$name"."_custom_string' /></span> <label for='{$name}_custom_string'>"; _e('Custom button', 'addthis_trans_domain' ); echo"</label><div class='clear'></div></div>";
//************ ORIGINAL
//                echo "<div class='$name"."_option select_row'><span class='radio mt4'><input $checked type='radio' value='custom_string' name='addthis_settings[$name]' id='$name"."_custom_string' /></span> <label for='{$name}_custom_string'>Custom button</label><div class='clear'></div></div>";
//************ FI -->

// ************ MODIFICAT - Localization support
//2014.05.06 @jmiro227
		echo "<div style='max-width: 555px;margin-left:20px' class='{$name}_custom_string_input'> "; _e('This text box allows you to enter any AddThis markup that you wish. To see examples of what you can do, visit', 'addthis_trans_domain');echo" <a href='https://www.addthis.com/get/sharing'>"; _e('AddThis.com Sharing Tools', 'addthis_trans_domain');echo"</a> "; _e('and select any sharing tool. You can also check out our', 'addthis_trans_domain');echo " <a href='http://support.addthis.com/customer/portal/articles/381263-addthis-client-api#rendering-decoration'>"; _e('Client API', 'addthis_trans_domain'); echo"</a>. "; _e('For any help you may need, please visit', 'addthis_trans_domain'); echo" <a href='http://support.addthis.com'>"; _e('AddThis Support', 'addthis_trans_domain'); echo "</a>.</div>";
//************ ORIGINAL
//                _e( sprintf("<div style='max-width: 555px;margin-left:20px' class='%s_custom_string_input'> This text box allows you to enter any AddThis markup that you wish. To see examples of what you can do, visit <a href='https://www.addthis.com/get/sharing'>AddThis.com Sharing Tools</a> and select any sharing tool. You can also check out our <a href='http://support.addthis.com/customer/portal/articles/381263-addthis-client-api#rendering-decoration'>Client API</a>. For any help you may need, please visit <a href='http://support.addthis.com'>AddThis Support</a></div>", $name ),'addthis_trans_domain');
//************ FI -->

                echo "<textarea style='max-width:555px;margin-left:20px'  rows='5' cols='100' name='addthis_settings[$name"."_custom_string]' class='$name"."_custom_string_input' />".esc_textarea($custom_string)."</textarea>";
				               
                $class = 'hidden';
                $checked = '';
                if ($option == 'custom' || ($option == 'none' && 'custom' == $addthis_default_options[$name]  ) ) {
                    $checked = 'checked="checked"';
                    $class = '';

                    echo "<div class='$name"."_option select_row $class mt20'><span class='radio mt4'><input $checked type='radio' value='custom' name='addthis_settings[$name]' id='$name"."_custom_button' /></span> Build your own<div class='clear'></div></div>";

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
              <div class="<?php echo $name;?>_button_set select_row" style="float: left; width: 480px;">
              	<div id="<?php echo $name;?>_custom_btns">
              	<?php //if ($name == "above") { ?>
              		<span class="<?php echo $name;?>-smart-sharing-container">
              			<p id="customizedMessage" class="mb40 personalizedMessage customize-message-section customize-your-buttons" style="display:none;">
	      					Your buttons are currently customized.  <a href="#" class="<?php echo $name;?>-customize-sharing-link customize-your-buttons">Show customization.</a>
	      				</p>
	      				<p id="personalizedMessage" class="mb40 personalizedMessage customize-message-section customize-your-buttons">

<!--XTEC ************ MODIFICAT - Localization support
//2013.05.21 @jmiro227 -->
<?php _e('AddThis boosts sharing by automatically showing the right buttons to each user based on their location and activity across the web.', 'addthis_trans_domain' ); ?>  <a href="#" class="<?php echo $name;?>-customize-sharing-link customize-your-buttons"><?php _e('Disable and select your own buttons.','addthis_trans_domain'); ?></a>

<!--************ ORIGINAL
	      					AddThis boosts sharing by automatically showing the right buttons to each user based on their location and activity across the web.  <a href="#" class="<?php echo $name;?>-customize-sharing-link customize-your-buttons">Disable and select your own buttons.</a>
************ FI -->
	      				</p>
						<p class="mb40 smart-sharing-link customize-message-section">Your buttons are currently customized. <a href="#" class="<?php echo $name;?>-customize-sharing-link smart-sharing-link">Let AddThis choose instead and boost sharing</a>
                            <span class="row-right" data-content="Smartest sharing buttons on the web. Automated to show each user the services that they use most based on their location and activity across the web." data-original-title="Smart Sharing."> (<a href="#">?</a>)</span>
                        </p>
						<span class="smart-sharing-inner-container">
		          			<p class="hide">
			            		<label>
			              			<input type="radio" checked="checked" name="<?php echo $name;?>-sharing" id="<?php echo $name;?>-enable-smart-sharing" value="<?php echo $name;?>-enable-smart-sharing"/> Use Smart Buttons <strong>(Recommended)</strong>
			            		</label>
			            		<label>
			              			<input type="radio" name="<?php echo $name;?>-sharing" id="<?php echo $name;?>-disable-smart-sharing"> Customize your buttons
			            		</label>
		          			</p>
		          			<div class="customize-buttons">
		            			<div class="sharing-buttons">
		              				<h4 class="sortable-heading">Button Options</h4>
		              				<input type="text" class="sharing-buttons-search" placeholder="Find a service" maxlength="20" size="30" style="width: 230px; height: 41px; margin: 0;">
		              				<ul class="sortable"></ul>
		            			</div>
					            <div class="selected-services">
		              				<h4 class="sortable-heading">Selected Buttons</h4>
		              				<ul class="sortable" data-type="addthisButtons"></ul>
		            			</div>
		          			</div>
		          			<div class="vertical-drag">
		            			<i class="icon-arrow-up"></i>
		            			<i class="icon-arrow-down"></i>
		            			<p>Drag up or down to reorder services</p>
		          			</div>
		          			<div class="horizontal-drag">
		            			<i class="icon-arrow-right"></i>
		            			<p>Drag across to add service</p>
		          			</div>
		          			<a href="#" class="restore-default-options" style="float: left; padding-left: 100px;">Restore default options</a>
		          			<?php $list = $name.'_chosen_list'; ?>
		          			<input type="hidden" id="<?php echo $name?>-chosen-list" name="addthis_settings[<?php echo $name;?>_chosen_list]" value="<?php echo $options[$list];?>"/>
	        			</span>
      				</span>
      				<script type="text/javascript">
      				window.page = 'sharing-buttons';
      				//$('.follow-tooltip').popover({ trigger: "hover" });
			      </script>
<!-- 			      <div id="atcode"></div> -->
			      <?php //} ?>
	           </div>  
             </div>        				
		</fieldset>	
    </td>
</tr>

<?php
 }
