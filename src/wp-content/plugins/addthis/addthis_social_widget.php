<?php
/**
 * Plugin Name: AddThis Sharing Buttons
 * Plugin URI: http://www.addthis.com
 * Description: Use the AddThis suite of website tools which includes sharing, following, recommended content, and conversion tools to help you make your website smarter. With AddThis, you can see how your users are engaging with your content, provide a personalized experience for each user and encourage them to share, subscribe or follow.
 * Version: 5.1.1
 * Author: The AddThis Team
 * Author URI: http://www.addthis.com/
 * License: GPL2
 *
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

if (!defined('ADDTHIS_INIT')) define('ADDTHIS_INIT', 1);
else return;

define( 'addthis_style_default' , 'fb_tw_p1_sc');
define( 'ENABLE_ADDITIONAL_PLACEMENT_OPTION', 0);


require_once('AddThisWordPressSharingButtonsPlugin.php');
require_once('AddThisWordPressConnector.php');
require_once('AddThisConfigs.php');
require_once('addthis_post_metabox.php');


$addThisSharingButtonsPluginObject = new AddThisWordPressSharingButtonsPlugin();
$cmsConnector = new AddThisWordPressConnector($addThisSharingButtonsPluginObject);
$addThisConfigs = new AddThisConfigs($cmsConnector);
$addthis_options = $addThisConfigs->getConfigs();

require_once('addthis_settings_functions.php');

//XTEC ************ AFEGIT - Localization support
//2015.10.29 @dgras
load_plugin_textdomain( 'addthis_trans_domain', null, dirname( plugin_basename( __FILE__ )) . '/languages' );
//************ FI

$addthis_languages = array(
    ''   => 'Automatic',
    'af' => 'Afrikaaner',
    'ar' => 'Arabic',
    'zh' => 'Chinese',
    'cs' => 'Czech',
    'da' => 'Danish',
    'nl' => 'Dutch',
    'en' => 'English',
    'fa' => 'Farsi',
    'fi' => 'Finnish',
    'fr' => 'French',
    'ga' => 'Gaelic',
    'de' => 'German',
    'el' => 'Greek',
    'he' => 'Hebrew',
    'hi' => 'Hindi',
    'it' => 'Italian',
    'ja' => 'Japanese',
    'ko' => 'Korean',
    'lv' => 'Latvian',
    'lt' => 'Lithuanian',
    'no' => 'Norwegian',
    'pl' => 'Polish',
    'pt' => 'Portugese',
    'ro' => 'Romanian',
    'ru' => 'Russian',
    'sk' => 'Slovakian',
    'sl' => 'Slovenian',
    'es' => 'Spanish',
    'sv' => 'Swedish',
    'th' => 'Thai',
    'ur' => 'Urdu',
    'cy' => 'Welsh',
    'vi' => 'Vietnamese',
);

/**
 * Show Plugin activation notice on first installation*
 */
function pluginActivationNotice()
{
    $run_once = get_option('addthis_run_once');
    global $cmsConnector;

    if (!$run_once) {
        wp_enqueue_style(
            'addThisStylesheet',
            plugins_url('css/style.css', __FILE__)
        );
        $html = '<div class="addthis_updated wrap">';
        $html .= '<span>'.
                    'Congrats! You\'ve Installed AddThis Sharing Buttons'.
                  '</span>';
        $html .= '<span><a class="addthis_configure" href="'
                . $cmsConnector->getSettingsPageUrl() .
                '">Configure it now</a> >></span>';
        $html .= '</div><!-- /.updated -->';
        echo '<style>div#message.updated{ display: none; }</style>';
        echo $html;

        update_option('addthis_run_once', true);
    }
}

register_deactivation_hook(__FILE__, array($cmsConnector, 'deactivate'));

/**
 * Make sure the option gets added on registration
 * @since 2.0.6
 */
add_action('admin_notices', 'pluginActivationNotice');
function addthis_activation_hook() {
    $addThisSharingButtonsPluginObject = new AddThisWordPressSharingButtonsPlugin();
    $cmsConnector = new AddThisWordPressConnector($addThisSharingButtonsPluginObject);
    $addThisConfigs = new AddThisConfigs($cmsConnector);
    $options = $addThisConfigs->getConfigs();
    $addThisConfigs->saveConfigs($options);
}

register_activation_hook( __FILE__, 'addthis_activation_hook' );

if (isset($_POST['addthis_plugin_controls'])) {
    $newModeValue = $_POST['addthis_plugin_controls'];
} else if (isset($_POST['addthis_settings']['addthis_plugin_controls'])) {
    $newModeValue = $_POST['addthis_settings']['addthis_plugin_controls'];
}

if (   isset($newModeValue)
    && $newModeValue != $addthis_options['addthis_plugin_controls']
) {
    if($newModeValue == 'AddThis') {
        // the WordPress mode magically doesn't need this to switch modes appropriately
        // probably because it handles settings correctly (registering them and then adding a hook for their sanitization)
        // $addthis_options['addthis_plugin_controls'] = 'AddThis';
    } else {
        $addthis_options['addthis_plugin_controls'] = 'WordPress';
    }
    $addthis_options = $addThisConfigs->saveConfigs($addthis_options);
}

add_action('wp_head', 'addthis_minimal_css');
function addthis_minimal_css() {
    global $cmsConnector;
    wp_enqueue_style( 'addthis_output', $cmsConnector->getPluginCssFolderUrl() . 'output.css' );
}

if ($addthis_options['addthis_plugin_controls'] == "AddThis") {
    require_once 'addthis-for-wordpress.php';
    $addThisWordPress = new Addthis_Wordpress(isset($upgraded), $addThisConfigs, $cmsConnector);
} else {

    // Show old version of the plugin till upgrade button is clicked

    // Add settings link on plugin page
    function addthis_plugin_settings_link($links) {
      global $cmsConnector;
      $settings_link = '<a href="'.$cmsConnector->getSettingsPageUrl().'">Settings</a>';
      array_push($links, $settings_link);
      return $links;
    }

    $plugin = plugin_basename(__FILE__);
    add_filter("plugin_action_links_$plugin", 'addthis_plugin_settings_link' );


    // Setup our shared resources early
    // addthis_addjs.php is a standard class shared by the various AddThis plugins to make it easy for us to include our bootstrapping JavaScript only once. Priority should be lowest for Share plugin.
    add_action('init', 'addthis_early', 0);
    function addthis_early(){
        global $AddThis_addjs_sharing_button_plugin;
        global $addThisConfigs;
        global $cmsConnector;
        if (!isset($AddThis_addjs_sharing_button_plugin)){
            require('addthis_addjs_new.php');
            $AddThis_addjs_sharing_button_plugin = new AddThis_addjs_sharing_button_plugin($addThisConfigs, $cmsConnector);
        }
    }

    $addthis_settings = array();
    $addthis_settings['isdropdown'] = 'true';
    $addthis_settings['customization'] = '';
    $addthis_settings['menu_type'] = 'dropdown';
    $addthis_settings['language'] = 'en';
    $addthis_settings['fallback_username'] = '';
    $addthis_settings['style'] = 'share';
    $addthis_settings['placement'] = ENABLE_ADDITIONAL_PLACEMENT_OPTION;

    $addthis_menu_types = array('static', 'dropdown', 'toolbox');

    $addthis_styles = array(
        'share'          => array('img'=>'lg-share-%lang%.gif', 'w'=>125, 'h'=>16),
        'bookmark'       => array('img'=>'lg-bookmark-en.gif',  'w'=>125, 'h'=>16),
        'addthis'        => array('img'=>'lg-addthis-en.gif',   'w'=>125, 'h'=>16),
        'share-small'    => array('img'=>'sm-share-%lang%.gif', 'w'=>83,  'h'=>16),
        'bookmark-small' => array('img'=>'sm-bookmark-en.gif',  'w'=>83,  'h'=>16),
        'plus'           => array('img'=>'sm-plus.gif',         'w'=>16,  'h'=>16)
    );

    $addthis_options = get_option('addthis_settings');
    $atversion = $addThisConfigs->getAddThisVersion();

    $addthis_new_styles = array(
        'large_toolbox' => array(
            'src' =>  '
                <div class="addthis_toolbox addthis_default_style addthis_32x32_style" %1$s >
                    <a class="addthis_button_preferred_1"></a>
                    <a class="addthis_button_preferred_2"></a>
                    <a class="addthis_button_preferred_3"></a>
                    <a class="addthis_button_preferred_4"></a>
                    <a class="addthis_button_compact"></a>
                    <a class="addthis_counter addthis_bubble_style"></a>
                </div>',
            'img' => 'toolbox-large.png',
            'name' => 'Large Toolbox',
            'above' => 'hidden ',
            'below' => 'hidden'
        ),
        'small_toolbox' => array(
            'src' => '
                <div class="addthis_toolbox addthis_default_style addthis_" %1$s >
                    <a class="addthis_button_preferred_1"></a>
                    <a class="addthis_button_preferred_2"></a>
                    <a class="addthis_button_preferred_3"></a>
                    <a class="addthis_button_preferred_4"></a>
                    <a class="addthis_button_compact"></a>
                    <a class="addthis_counter addthis_bubble_style"></a>
                </div>',
            'img' => 'toolbox-small.png',
            'name' => 'Small Toolbox',
            'above' => 'hidden ',
            'below' => ''
        ),
        'fb_tw_p1_sc' => array(
            'src' => '
                <div class="addthis_toolbox addthis_default_style " %1$s  >
                    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                    <a class="addthis_button_tweet"></a>
                    <a class="addthis_button_pinterest_pinit"></a>
                    <a class="addthis_counter addthis_pill_style"></a>
                </div>',
            'img' => 'horizontal_share_rect.png',
            'name' => 'Like, Tweet, +1, Share',
            'above' => '',
            'below' => '',
        ),
        'button' => array(
            'src' => '
                <div>
                    <a class="addthis_button" href="//addthis.com/bookmark.php?v='.$atversion.'" %1$s>
                        <img src="//cache.addthis.com/cachefly/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="Bookmark and Share" style="border:0"/>
                    </a>
                </div>',
            'img' => 'horizontal_share.png',
            'name' => 'Classic Share Button',
            'above' => 'hidden ',
            'below' => 'hidden'
        )
    );


    //add_filter('the_title', 'at_title_check');
    /**
     * @deprecated
     * @todo Add _deprecated_function notice.
     */
    function at_title_check($title)
    {

        global $addthis_did_filters_added;

        if (!isset ($addthis_did_filters_added) || $addthis_did_filters_added != true)
        {
            addthis_add_content_filters();
            add_filter('the_content', 'addthis_script_to_content');
        }

        return $title;
    }


    /**
     * @deprecated
     * @todo Add _deprecated_function notice.
     */
    function addthis_script_to_content($content)
    {
        global $addthis_did_script_output;

        if (!isset($addthis_did_script_output) )
        {
            $addthis_did_script_output = true;
            $content .= addthis_output_script(true);
        }
        return $content ;
    }


    add_filter('language_attributes', 'addthis_language_attributes');
    function addthis_language_attributes($input)
    {
        return $input . ' xmlns:fb="http://ogp.me/ns/fb#" xmlns:addthis="http://www.addthis.com/help/api-spec" ';
    }


    /**
     * Converts our old many options in to one beautiful array
     *
     */

     // Caution:  Using this filter to disable upgrades may have unexpected consequences.
    if ( apply_filters( 'at_do_options_upgrades', '__return_true') || apply_filters( 'addthis_do_options_upgrades', '__return_true')   )
    {
        function addthis_options_200()
        {
            global $current_user;
            global $addThisConfigs;
            global $cmsConnector;

            $user_id = $current_user->ID;
            $addthis_new_options = array();

            if ($asynchronous_loading = get_option('addthis_asynchronous_loading'))
                $addthis_new_options['addthis_asynchronous_loading'] = $asynchronous_loading;

            if ($addthis_per_post_enabled = get_option('addthis_per_post_enabled'))
                $addthis_new_options['addthis_per_post_enabled'] = $addthis_per_post_enabled;

            if ($append_data = get_option('addthis_append_data'))
                $addthis_new_options['addthis_append_data'] = $append_data;

            // populate variables for share button location template settings
            $locationTemplateFields = $addThisConfigs->getFieldsForContentTypeSharingLocations();
            foreach ($locationTemplateFields as $field) {
                $optionName = $field['fieldName'];
                $variableName = $field['variableName'];

                if ($$variableName = get_option($optionName)) {
                    $addthis_new_options[$optionName] = $$variableName;
                }
            }

            if ($aftertitle = get_option('addthis_aftertitle'))
                $addthis_new_options['addthis_aftertitle'] = $aftertitle;

            if ($beforecomments = get_option('addthis_beforecomments'))
                $addthis_new_options['addthis_beforecomments'] = $beforecomments;

            $addthis_new_options['below'] = 'none';

            if ($language = get_option('addthis_language'))
                $addthis_new_options['addthis_language'] = $language;

            //version check
            if ($atversion = get_option('atversion'))
                $addthis_new_options['atversion'] = $atversion;


            // Above is new, set it to none
            $addthis_new_options['above'] = 'none';

            // Save option
            add_option('addthis_settings', $addthis_new_options);

            // if the option saved, delete the old options

            delete_option('addthis_asynchronous_loading');
            delete_option('addthis_product');
            delete_option('addthis_isdropdown');
            delete_option('addthis_menu_type');
            delete_option('addthis_append_data');
            delete_option('addthis_aftertitle');
            delete_option('addthis_beforecomments');
            delete_option('addthis_style');
            delete_option('addthis_language');
            delete_option('atversion');

            // delete each share button location template settings
            foreach ($locationTemplateFields as $field) {
                $optionName = $field['fieldName'];
                delete_option($optionName);
            }

            // old options that are no longer used, to clean up after ourshelves
            if (false) {
                $deprecatedFields = $cmsConnector->getDeprecatedVariables();
                foreach ($deprecatedFields as $field) {
                    delete_option($field);
                }
            }

            global $current_user;
            $user_id = $current_user->ID;

            add_user_meta($user_id, 'addthis_nag_updated_options', 'true', true);
        }

        function addthis_options_240()
        {
            global $addThisConfigs;
            $options = $addThisConfigs->getConfigs();

            //$options['wpfooter'] = false;
            $addThisConfigs->saveConfigs($options);
        }
    }

    function addthis_add_for_check_footer() {

    }

    function addthis_check_footer() {

    }

    /**
    * For templates, we need a wrapper for printing out the code on demand.
    */
    function addthis_print_widget($url = null, $title = null, $style = addthis_style_default) {
        global $addthis_styles, $addthis_new_styles;
        global $addThisConfigs;
        $styles = array_merge($addthis_styles, $addthis_new_styles);

        $options = $addThisConfigs->getConfigs();

        $identifier = addthis_get_identifier($url, $title);

        if (!is_array($style) && isset($addthis_new_styles[$style])) {
            $template = $addthis_new_styles[$style]['src'];
        } elseif ($style == 'above') {
            $template = addthis_display_widget_above($styles, $options);
        } elseif ($style == 'below') {
            $template = addthis_display_widget_below($styles, $options);
        } elseif (is_array($style)) {
            $template = addthis_custom_toolbox($style);
        }

        echo "\n<!-- AddThis Custom -->\n";
        echo sprintf($template, $identifier);
        echo "\n<!-- End AddThis Custom -->\n";
    }

    /*
    * Generates the addthis:url and addthis:title attributes
    */

    function addthis_get_identifier($url = null, $title = null)
    {

        if (is_null($url)) {
            $url = get_permalink();
        }

        if (is_null($title)) {
            $title = esc_attr(get_the_title());
        }

        if (!is_null($url)) {
            $identifier =  "addthis:url='$url' ";
        }

        if (!is_null($title)) {
            $identifier .= "addthis:title='$title'";
        }

        if (!isset($identifier)) {
            $identifier = '';
        }

        return $identifier;
    }

    /**
    * Options is an array that contains
    * size - either 16 or 32.  Defaults to 16
    * services - comma sepperated list of services
    * preferred - number of Prefered services to be displayed after listed services
    * more - bool to show or not show the more icon at the end
    *
    * @param $options array
    */

    function addthis_custom_toolbox($options)
    {
        $outerClasses = 'addthis_toolbox addthis_default_style';

        if (isset($options['size']) && $options['size'] == '32')
            $outerClasses .= ' addthis_32x32_style';

        if (isset($options['type']) && $options['type'] != 'custom_string') {
            $button = '<div class="'.$outerClasses.'" %1$s>';

            if (isset($options['services']) ) {
                $services = explode(',', $options['services']);
                foreach ($services as $service)
                {
                    $service = trim($service);
                    if ($service == 'more' || $service == 'compact') {
                        if (isset($options['type']) && $options['type'] != 'fb_tw_p1_sc') {
                            $button .= '<a class="addthis_button_compact"></a>';
                        }
                    }
                    else if ($service == 'counter') {
                        if (isset($options['type']) && $options['type'] == 'fb_tw_p1_sc') {
                            $button .= '<a class="addthis_counter addthis_pill_style"></a>';
                        }
                        else {
                            $button .= '<a class="addthis_counter addthis_bubble_style"></a>';
                        }
                    }
                    else if ($service == 'google_plusone') {
                        $button .= '<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>';
                    }
                    else
                        $button .= '<a class="addthis_button_'.strtolower($service).'"></a>';
                }
            }

            if (isset($options['preferred']) && is_numeric($options['preferred']))
            {
                for ($a = 1; $a <= $options['preferred']; $a++)
                {
                    $button .= '<a class="addthis_button_preferred_'.$a.'"></a>';
                }
            }

            if (isset($options['more']) && $options['more'] == true)
            {
                    $button .= '<a class="addthis_button_compact"></a>';
            }

            if (isset($options['counter']) && ($options['counter'] != "") && ($options['counter'] !== false))
            {
                if ($options['counter'] === true)
                {  //no style was specified
                   $button .= '<a class="addthis_counter"></a>';
                }
                else
                {  //a specific style was specified such as pill_style or bubble_style
                    $button .= '<a class="addthis_counter addthis_'.$options['counter'].'"></a>';
                }
            }

            $button .= '</div>';
        }

        return $button;
    }

    /**
    * Adds AddThis CSS to page. Only used for admin dashboard in WP 2.7 and higher.
    */
    function addthis_print_style() {
        wp_enqueue_style( 'addthis' );
    }

    /**
    * Adds AddThis script to page. Only used for admin dashboard in WP 2.7 and higher.
    */
    function addthis_print_script() {
        wp_enqueue_script( 'addthis' );
    }

    add_action('admin_notices', 'addthis_admin_notices');

    function addthis_admin_notices(){
        if (   !current_user_can('manage_options')
            || (   defined('ADDTHIS_NO_NOTICES')
                && ADDTHIS_NO_NOTICES)
        ) {
            return;
        }

        global $cmsConnector;
        global $current_user;

        $user_id = $current_user->ID;
        $options = $cmsConnector->getConfigs();
        $message = '';

        $setupMessage = '
            <div class="updated addthis_setup_nag">
                <p>
                    Configure the AddThis Sharing Buttons plugin to enable users to share your content around the web.
                    <br />
                    <a href="' . $cmsConnector->getSettingsPageUrl() . '">Configuration options</a>
                    |
                    <a href="?addthis_nag_ignore=0" id="php_below_min_nag-no">Ignore this notice</a>
                </p>
            </div>
        ';

        $updatedMessage = '
            <div class="updated addthis_setup_nag">
                <p>
                    We have updated the options for the AddThis Sharing Buttons plugin. Check out the
                    <a href="' . $cmsConnector->getSettingsPageUrl() . '">AddThis settings page</a> to see the new styles and options.
                    <br />
                    <a href="' . $cmsConnector->getSettingsPageUrl() . '">See New Options</a>
                    |
                    <a href="?addthis_nag_updated_ignore=0">Ignore this notice</a>
                </p>
            </div>
        ';

        if (    !isset($options['addthis_plugin_controls'])
            && !get_user_meta($user_id, 'addthis_ignore_notices')
        ) {
            // nothing set up
            $message = $setupMessage;
        } else if(   $options['addthis_plugin_controls'] == "AddThis"
                  && empty($options['addthis_profile'])
                  && !get_user_meta($user_id, 'addthis_ignore_notices')
        ) {
            // AddThis mode but no pubid
            $message = $setupMessage;
        } else if(   $options['addthis_plugin_controls'] != "AddThis"
                  && empty($options['addthis_above_enabled'])
                  && empty($options['addthis_below_enabled'])
                  && empty($options['addthis_sidebar_enabled'])
                  && !get_user_meta($user_id, 'addthis_ignore_notices')
        ) {
            // WordPress mode but no tools enabled
            $message = $setupMessage;
        } elseif (get_user_meta($user_id, 'addthis_nag_updated_options')) {
            // upgrade
            $message = $updatedMessage;
        }

        echo $message;
    }
    add_action('admin_init', 'addthis_nag_ignore');

    function addthis_nag_ignore()
    {
        global $current_user;
        $user_id = $current_user->ID;

        if (isset($_GET['addthis_nag_ignore']) && '0' == $_GET['addthis_nag_ignore'])
            add_user_meta($user_id, 'addthis_ignore_notices', 'true', true);
        if (isset($_GET['addthis_nag_updated_ignore']) && '0' == $_GET['addthis_nag_updated_ignore'])
            delete_user_meta($user_id, 'addthis_nag_updated_options', 'true');
    }

    function addthis_plugin_useragent($userAgent)
    {
        global $cmsConnector;
        return $userAgent . 'ATV/' . $cmsConnector->getPluginVersion();
    }

    function addthis_render_dashboard_widget_holder()
    {
         echo '<p class="widget-loading hide-if-no-js">' . __( 'Loading&#8230;','addthis_trans_domain' ) . '</p><p class="describe hide-if-js">' . __('This widget requires JavaScript.','addthis_trans_domain') . '</p>';
    }

    add_action('wp_ajax_at_save_transient', 'addthis_save_transient');

    function addthis_save_transient() {
        global $wpdb; // this is how you get access to the database


        parse_str($_POST['value'], $values);

        // verify nonce (or die).
        $nonce = $values['_wpnonce'];
        if (!wp_verify_nonce($nonce, 'addthis-options')) {
            die('Security check');
        }

        // Parse Post data
        $option_array = addthis_parse_options($values);

        // Set Transient
        if (get_transient('addthis_settings') !==  false) {
            delete_transient('addthis_settings');
        }

        $eh = set_transient('addthis_settings', $option_array, 120);

        print_r($option_array);

        die();
    }

    function addthis_save_settings($input)
    {
        global $addThisConfigs;

        // if special, do special, else
        if (   isset($input['addthis_csr_confirmation'])
            && $input['addthis_csr_confirmation'] == 'true'
        ) {
            if (   isset($input['addthis_profile'])
                && wp_verify_nonce($_POST['pubid_nonce'], 'update_pubid')
            ) {
                $configs['addthis_profile'] = $input['addthis_profile'];
            }
        } elseif(count($input) > 2) {
            // there should be more than two entires in forms posted from this
            // plugin
            $configs = addthis_parse_options($input);
        }

        if (isset($configs)) {
            $addThisConfigs->saveConfigs($configs);
        }
    }


    /**
     * goes through all the options, sanitizing, verifying and returning for storage what needs to be there
     */
    function addthis_parse_options($data)
    {
        global $addthis_styles, $addthis_new_styles;
        global $addThisConfigs;

        $styles = array_merge($addthis_styles, $addthis_new_styles);
        $below_custom_styles = $above_custom_styles = '';
        $options = $addThisConfigs->getConfigs();

        if (!is_array($data)) {
          return $options;
        }

        if (isset($data['show_below'])) {
            $options['below'] = 'none';
        } elseif (isset($data['below'], $styles[$data['below']])) {
            $options['below'] = $data['below'];
        } elseif ($data['below'] == 'disable') {
            $options['below'] = $data['below'];
        } elseif ($data['below'] == 'none') {
            $options['below'] = 'none';
        } elseif ($data['below'] == 'custom') {
            $options['below_do_custom_services'] = isset($data['below_do_custom_services']);
            $options['below_do_custom_preferred'] = isset($data['below_do_custom_preferred']);

            $options['below'] = 'custom';

            if (   $data['below_custom_size'] == 16
                || $data['below_custom_size'] == 32
            ) {
                $options['below_custom_size'] = $data['below_custom_size'];
            } else {
                $options['below_custom_size'] = '';
            }
            $options['below_custom_services'] = sanitize_text_field($data['below_custom_services']);
            $options['below_custom_preferred'] = sanitize_text_field($data['below_custom_preferred']);
            $options['below_custom_more'] = isset($data['below_custom_more']);
        } elseif ($data['below'] == 'custom_string') {
            $options['below'] = 'custom_string';
            if (strpos($data['below_custom_string'], "style=") != false) {
                $custom_style = explode('style=', $data['below_custom_string']);
                $custom_style = explode('>', $custom_style[1]);
                $custom_style = explode(' ', $custom_style[0]);
                $below_custom_styles = " style=$custom_style[0]";
            }
            $options['below_custom_string'] = addthis_kses(
                $data['below_custom_string'],
                $below_custom_styles
            );
        }

        if (isset($data['wpfooter'])) {
            $options['wpfooter'] = (bool) $data['wpfooter'];
        }

        if (isset($styles[$data['above']])) {
            $options['above'] = $data['above'];
        } elseif ($data['above'] == 'disable') {
            $options['above'] = $data['above'];
        } elseif ($data['above'] == 'none') {
            $options['above'] = 'none';
        } elseif ($data['above'] == 'custom') {
            $options['above_do_custom_services'] = isset($data['above_do_custom_services']) ;
            $options['above_do_custom_preferred'] = isset($data['above_do_custom_preferred']) ;
            $options['above'] = 'custom';

            if (    $data['above_custom_size'] == 16
                ||  $data['above_custom_size'] == 32
            ) {
                $options['above_custom_size'] = $data['above_custom_size'];
            } else {
                $options['above_custom_size'] = '';
            }
            $options['above_custom_services'] = sanitize_text_field($data['above_custom_services']);
            $options['above_custom_preferred'] = (int) $data['above_custom_preferred'] ;
            $options['above_custom_more'] = isset($data['above_custom_more']);
        } elseif ($data['above'] == 'custom_string') {
            //[addthis_twitter_template]
            if (   isset($data['addthis_twitter_template'])
                && strlen($data['addthis_twitter_template']) != 0
            ) {
                 //Parse the first twitter username to be used with via
                 $options['addthis_twitter_template'] = sanitize_text_field($data['addthis_twitter_template']);
            }

            $options['above'] = 'custom_string';
            if (strpos($data['above_custom_string'], "style=")) {
                $custom_style = explode('style=', $data['above_custom_string']);
                $custom_style = explode('>', $custom_style[1]);
                $custom_style = explode(' ', $custom_style[0]);
                $above_custom_styles = " style=$custom_style[0]";
            }
            $options['above_custom_string'] = addthis_kses(
                $data['above_custom_string'],
                $above_custom_styles
            );
        }

        if (isset($data['addthis_profile'])) {
            $options['addthis_profile'] = sanitize_text_field($data['addthis_profile']);
        }

        if (isset($styles[$data['below']])) {
            $options['below'] = $data['below'];
        } elseif ($data['below'] == 'disable') {
            $options['below'] = $data['below'];
        } elseif ($data['below'] == 'none') {
            $options['below'] = 'none';
        } elseif ($data['below'] == 'custom') {
            $options['below_do_custom_services'] = isset($data['below_do_custom_services']) ;
            $options['below_do_custom_preferred'] = isset($data['below_do_custom_preferred']) ;

            $options['below'] = 'custom';
            if (   $data['below_custom_size'] == 16
                || $data['below_custom_size'] == 32
            ) {
                $options['below_custom_size'] = $data['below_custom_size'];
            } else {
                $options['below_custom_size'] = '';
            }
            $options['below_custom_services'] = sanitize_text_field($data['below_custom_services']);
            $options['below_custom_preferred'] = sanitize_text_field($data['below_custom_preferred']);
            $options['below_custom_more'] = isset($data['below_custom_more']);
        } elseif ($data['below'] == 'custom_string') {
            $options['below'] = 'custom_string';
            if (strpos($data['below_custom_string'], "style=")) {
                $custom_style = explode('style=', $data['below_custom_string']);
                $custom_style = explode('>', $custom_style[1]);
                $custom_style = explode(' ', $custom_style[0]);
                $below_custom_styles = " style=$custom_style[0]";
            }
            $options['below_custom_string'] = addthis_kses(
                $data['below_custom_string'],
                $below_custom_styles
            );
        }

        // All the checkbox fields
        $checkboxFields = array(
            // general
            'addthis_508',
            'addthis_addressbar',
            'addthis_append_data',
            'addthis_asynchronous_loading',
            'addthis_bitly',
            'addthis_per_post_enabled',
            // WordPress mode only
            'above_auto_services',
            'addthis_above_enabled',
            'addthis_aftertitle' ,
            'addthis_beforecomments',
            'addthis_below_enabled',
            'addthis_sidebar_enabled',
            'below_auto_services',
        );

        // add all share button location template settings to list of checkbox fields
        $locationTemplateFields = $addThisConfigs->getFieldsForContentTypeSharingLocations();
        foreach ($locationTemplateFields as $field) {
            $optionName = $field['fieldName'];
            $checkboxFields[] = $optionName;
        }

        foreach ($checkboxFields as $field) {
            if (isset($data[$field]) && $data[$field]) {
                $options[$field] = true;
            } else {
                $options[$field] = false;
            }
        }

        $checkAndSanitize = array(
            // general
            'addthis_config_json',
            'addthis_environment',
            'addthis_language',
            'addthis_layers_json',
            'addthis_plugin_controls',
            'addthis_profile',
            'addthis_rate_us',
            'addthis_share_json',
            'addthis_twitter_template',
            'atversion',
            'atversion_update_status',
            'credential_validation_status',
            'data_ga_property',
            // WordPress mode only
            'addthis_sidebar_count',
            'addthis_sidebar_position',
            'addthis_sidebar_theme',
        );

        foreach ($checkAndSanitize as $field) {
            if (isset($data[$field])) {
                $options[$field] = sanitize_text_field($data[$field]);
            }
        }

        if (!empty($data['above_chosen_list'])) {
            $options['above_chosen_list'] = sanitize_text_field($data['above_chosen_list']);
        } else {
            $options['above_chosen_list'] = "";
        }

        if (!empty($data['below_chosen_list'])) {
            $options['below_chosen_list'] = sanitize_text_field($data['below_chosen_list']);
        } else {
            $options['below_chosen_list'] = "";
        }

        if (isset($data['addthis_rate_us'])
            && $options['addthis_rate_us'] != $data['addthis_rate_us']
        ) {
                $options['addthis_rate_us_timestamp'] = time();
        }

        return $options;
    }


    /**
    * Formally registers AddThis settings. Only called in WP 2.7+.
    */
    function register_addthis_settings() {
        register_setting('addthis', 'addthis_settings', 'addthis_save_settings');
    }

    /*
     * Used to make sure excerpts above the head aren't displayed wrong
    */
    function addthis_add_content_filters()
    {
        global $addthis_did_filters_added;
        global $addThisConfigs;
        $addthis_did_filters_added = true;

        $options = $addThisConfigs->getConfigs();

        if ( ! empty( $options) ){
            if (_addthis_excerpt_buttons_enabled()) {
                add_filter('get_the_excerpt', 'addthis_display_social_widget_excerpt', 11);
            }

            if (!empty($options['addthis_aftertitle'])) {
                add_filter('the_title', 'addthis_display_after_title', 11);
            }

            add_filter('the_content', 'addthis_display_social_widget', 15);

        }
    }

   /**
    * Adds WP filter so we can append the AddThis button to post content.
    */
    function addthis_init()
    {
        global $addThisConfigs;
        global $cmsConnector;

        $options = $addThisConfigs->getConfigs();
        load_plugin_textdomain( 'addthis_trans_domain', null, dirname( plugin_basename( __FILE__ )) . '/languages' );
        add_action('wp_head', 'addthis_add_content_filters');

        if (   (   $cmsConnector->getCmsMinorVersion() >= 2.7
                || $cmsConnector->assumeLatest())
            && is_admin()
        ) {
            add_action('admin_init', 'register_addthis_settings');
        }

        add_action('admin_print_styles-index.php', 'addthis_print_style');
        add_action('admin_print_scripts-index.php', 'addthis_print_script');

        add_filter('admin_menu', 'addToWordpressMenu');

        if ( apply_filters( 'at_do_options_upgrades', '__return_true') || apply_filters( 'addthis_do_options_upgrades', '__return_true')   )
        {
            if (   get_option('addthis_product') !== false
                && !is_array($options)
            ) {
                addthis_options_200();
            }

            // Upgrade to 240 and add at 300
            if (!isset($options['atversion']) || empty($options['atversion'])) {
                addthis_options_240();
            }
        }
        add_action( 'addthis_widget', 'addthis_print_widget', 10, 3);
    }

    /**
     * Places our options into a global associative array.
     * @refactor
     */
    function addthis_set_addthis_settings()
    {
        global $addthis_settings;
        $product = get_option('addthis_product');


        $style = get_option('addthis_style');
        if (strlen($style) == 0) $style = 'share';
        $addthis_settings['style'] = $style;

        $addthis_settings['menu_type'] = get_option('addthis_menu_type');

        $language = get_option('addthis_language');
        $addthis_settings['language'] = $language;

        $atversion = get_option('atversion');
        $addthis_settings['atversion'] = $atversion;

        $advopts = array('brand', 'append_data', 'language', 'header_background', 'header_color');
        $addthis_settings['customization'] = '';
        for ($i = 0; $i < count($advopts); $i++)
        {
            $opt = $advopts[$i];
            $val = get_option("addthis_$opt");
            if (isset($val) && strlen($val)) $addthis_settings['customization'] .= "var addthis_$opt = '$val';";
        }
    }

    add_action('widgets_init', 'addthis_widget_init');

    function addthis_widget_init()
    {
        require_once('addthis_sidebar_widget.php');
        //require_once('addthis_content_feed_widget.php');
        register_widget('AddThisSidebarWidget');
        //register_widget('AddThisContentFeedWidget');
    }

    function addthis_sidebar_widget($args)
    {
        extract($args);
        echo $before_widget;
        echo $before_title . $after_title . addthis_social_widget('', true);
        echo $after_widget;
    }

    // if there are suppose to be sharing buttons and they're not there, add them.
    function addthis_remove_tag($content)
    {
        global $addThisConfigs;

        if (!is_object($addThisConfigs)) {
            $addThisSharingButtonsPluginObject = new AddThisWordPressSharingButtonsPlugin();
            $cmsConnector = new AddThisWordPressConnector($addThisSharingButtonsPluginObject);
            $addThisConfigs = new AddThisConfigs($cmsConnector);
        }

        if(   !is_object($addThisConfigs)
           || !method_exists($addThisConfigs, 'getConfigs')
        ) {
            return $content;
        }

        $options = $addThisConfigs->getConfigs();

        $checkForToolbox = "addthis_toolbox";
        $checkForButton = "addthis_button";

        if (   _addthis_excerpt_buttons_enabled()
            && strpos($content, $checkForToolbox) === false
            && strpos($content, $checkForButton) === false
        ) {
          $content = addthis_display_social_widget($content, false, false);
        }

        return $content;
    }

    /**
     * so named because it is added "later then the standard filter and all the WP internal filters"
     */
    function addthis_late_widget($link_text)
    {
        global $addThisConfigs;
        global $addthis_styles;
        global $addthis_new_styles;

        remove_filter('get_the_excerpt', 'addthis_late_widget');
        if (!_addthis_excerpt_buttons_enabled()) {
            return $link_text;
        }

        $options = $addThisConfigs->getConfigs();
        $styles = array_merge($addthis_styles, $addthis_new_styles);

        if (   has_excerpt()
            && !is_attachment()
            && isset($options['below'])
            && $options['below'] == 'custom'
        ) {
            $parsedOptions['size'] = $options['below_custom_size'];
            $newOptions['more'] = $options['below_custom_more'];

            if ($options['below_do_custom_services']) {
                $parsedOptions['services'] = $options['below_custom_services'];
            }

            if ($options['below_do_custom_preferred']) {
                $parsedOptions['preferred'] = $options['below_custom_preferred'];
            }

            $template = addthis_custom_toolbox($parsedOptions);
        } elseif (   isset($styles[$options['below']])
                  && has_excerpt()
                  && !is_attachment()
        ) {
            $template = $styles[$options['below']]['src'];
        } else {
            $template = '';
        }

        $buttons = sprintf($template, addthis_get_identifier());

        return  $link_text . $buttons;
    }


    function addthis_display_social_widget_excerpt($content)
    {
        // I don't think has_excerpt() is always necessarily true when calling "get_the_excerpt()",
        // but since this function is only as a get_the_excerpt() filter, we should probably
        // not care whether or not an excerpt is there since the caller obviously wants one.
        // needs testing/understanding.

        if (has_excerpt() && _addthis_excerpt_buttons_enabled()) {
            return addthis_display_social_widget($content, true, true);
        } else {
            return $content;
        }
    }

    function addthis_display_widget_above($styles, $options) {
        $above = '';
        if ($options['addthis_above_enabled'] == true){
            if (isset($styles[$options['above']])) {
                if (   isset($options['above_chosen_list'], $options['above_auto_services'])
                    && !$options['above_auto_services']
                    && strlen($options['above_chosen_list']) != 0
                ) {
                    if (isset($options['above']) && $options['above'] == 'large_toolbox') {
                        $aboveOptions['size'] = '32';
                    } elseif (isset($options['above']) && $options['above'] == 'small_toolbox') {
                        $aboveOptions['size'] = '16';
                    }
                    $aboveOptions['type'] = $options['above'];
                    $aboveOptions['services'] = $options['above_chosen_list'];
                    $above = addthis_custom_toolbox($aboveOptions);
                } else {
                    $above = $styles[$options['above']]['src'];
                }
            } elseif ($options['above'] == 'custom') {
                $aboveOptions['size'] = $options['above_custom_size'];
                if ($options['above_do_custom_services'])
                    $aboveOptions['services'] = $options['above_custom_services'];
                if ($options['above_do_custom_preferred'])
                    $aboveOptions['preferred'] = $options['above_custom_preferred'];
                $aboveOptions['more'] = $options['above_custom_more'];
                $above = addthis_custom_toolbox($aboveOptions);
            } elseif ($options['above'] == 'custom_string') {
                $custom = preg_replace('/<\s*div\s*/', '<div %1$s ', $options['above_custom_string']);
                $above = $custom;
            }
        }
        return $above;
    }

    function addthis_display_widget_below($styles, $options) {
        $below = '';
        if ($options['addthis_below_enabled'] == true){
            if (isset($styles[$options['below']])) {
                if (   isset($options['below_chosen_list'], $options['below_auto_services'])
                    && !$options['below_auto_services']
                    && strlen($options['below_chosen_list']) != 0
                ) {
                    if (isset($options['below']) && $options['below'] == 'large_toolbox') {
                        $belowOptions['size'] = '32';
                    } elseif (isset($options['below']) && $options['below'] == 'small_toolbox') {
                        $belowOptions['size'] = '16';
                    }
                    $belowOptions['type'] = $options['below'];
                    $belowOptions['services'] = $options['below_chosen_list'];
                    $below = addthis_custom_toolbox($belowOptions);
                } else {
                    $below = $styles[$options['below']]['src'];
                }
            } elseif ($options['below'] == 'custom') {
                $belowOptions['size'] = $options['below_custom_size'];
                $belowOptions['services'] = $options['below_custom_services'];
                $belowOptions['preferred'] = $options['below_custom_preferred'];
                $belowOptions['more'] = $options['below_custom_more'];
                $below = addthis_custom_toolbox($belowOptions);
            } elseif ($options['below'] == 'custom_string') {
                $custom = preg_replace('/<\s*div\s*/', '<div %1$s ', $options['below_custom_string']);
                $below = $custom;
            }
        }
        return $below;
    }

    function addthis_display_social_widget($content, $filtered = true, $below_excerpt = false)
    {
        global $addthis_styles, $addthis_new_styles, $post;
        global $addThisConfigs;

        $styles = array_merge($addthis_styles, $addthis_new_styles);
        $options = $addThisConfigs->getConfigs();

        $templateType = _addthis_determine_template_type();

        // get configs for this template type
        if (is_string($templateType)) {
            $fieldList = $addThisConfigs->getFieldsForContentTypeSharingLocations($templateType);
            foreach ($fieldList as $key => $field) {
                $fieldList[$field['location']] = $field;
                unset($fieldList[$key]);
            }

            $aboveFieldName = $fieldList['above']['fieldName'];
            $belowFieldName = $fieldList['below']['fieldName'];
            $displayAbove = (isset($options[$aboveFieldName]) && $options[$aboveFieldName] == true ) ? true : false;
            $displayBelow = (isset($options[$belowFieldName]) && $options[$belowFieldName] == true ) ? true : false;
        } else {
            $displayAbove = false;
            $displayBelow = false;
        }

        if ( $templateType === 'home' ) {
            $templateIsAnExcerpt = (boolean)(strpos($post->post_content, '<!--more-->') != false);
            if ($templateIsAnExcerpt) {
                if ($displayAbove && !_addthis_excerpt_buttons_enabled_above()) {
                    $displayAbove = false;
                }

                if ($displayBelow && !_addthis_excerpt_buttons_enabled_below()) {
                    $displayBelow = false;
                }
            }
        }

        $custom_fields = get_post_custom($post->ID);
        if (   isset($custom_fields['addthis_exclude'])
            && $custom_fields['addthis_exclude'][0] ==  'true'
        ) {
            $displayAbove = false;
            $displayBelow = false;
        }

        remove_filter('wp_trim_excerpt', 'addthis_remove_tag', 9, 1);
        remove_filter('get_the_excerpt', 'addthis_late_widget');
        $identifier =  addthis_get_identifier();

        // Still here?  Well let's add some social goodness
        if (   isset($options['above'])
            && $options['above'] != 'none'
            && $options['above'] != 'disable'
            && $displayAbove
        ) {
            $above = addthis_display_widget_above($styles, $options);
        } elseif ($displayAbove) {
            $above = '';
        } else {
            $above = '';
        }

        if (   isset($options['below'])
            && $options['below'] != 'none'
            && $options['below'] != 'disable'
            && $displayBelow
            && ! $below_excerpt
        ) {
            $below = addthis_display_widget_below($styles, $options);
        } elseif (   $below_excerpt
                  && $displayBelow
                  && $options['below'] != 'none'
        ) {
            $below = '';

            if (_addthis_excerpt_buttons_enabled()) {
                add_filter('get_the_excerpt', 'addthis_late_widget', 14);
            }
        } else {
            $below = '';
        }

        $at_flag = get_post_meta( $post->ID, '_at_widget', TRUE );
        if (!$options['addthis_per_post_enabled']) {
            $at_flag = '1';
        }

        if ($at_flag !== '0') {
            if ($displayAbove && isset($above)) {
                $content = sprintf($above, $identifier) . $content;
            }
            if ($displayBelow && isset($below)) {
                $content = $content . sprintf($below, $identifier);
            }
        }

        if (($displayAbove || $displayBelow) && $filtered) {
            add_filter('wp_trim_excerpt', 'addthis_remove_tag', 11, 1);
        }

        return $content;
    }

    add_action('wp_head', 'addthis_register_script_in_addjs', 20);

    function addthis_register_script_in_addjs(){
        global $AddThis_addjs_sharing_button_plugin;
        $script = addthis_output_script(true, true);
        $AddThis_addjs_sharing_button_plugin->addToScript($script);
    }

    /**
     * Check to see if our Javascript has been outputted yet.  If it hasn't, return it.  Else, return false.
     *
     * @return mixed
    */
    function addthis_output_script($return = false, $justConfig = false )
    {
        global $addThisConfigs;
        global $cmsConnector;
        $options = $addThisConfigs->getConfigs();

        $addthis_config = $addThisConfigs->createAddThisConfigVariable();
        $addthis_config_js = 'var addthis_config = '. json_encode($addthis_config) .';';

        $addthis_share = $addThisConfigs->createAddThisShareVariable();
        $addthis_share_js = 'var addthis_share = '. json_encode($addthis_share) .';';

        $addthis_layers = $addThisConfigs->createAddThisLayersVariable();
        $addthis_layers_js = 'var addthis_layers = '. json_encode($addthis_layers) .';';

        if ($justConfig) {
            $script = "\n" . $addthis_config_js . "\n" . $addthis_share_js . "\n";
            return $script;
        }

        $async = '';
        if (!empty($addthis_config['addthis_asynchronous_loading'])) {
            $async = 'async="async"';
        }

        /**
         * Load client script based on the enviornment variable
         * Admin can enable debug mode in adv settings by adding url param debug=true
         */
        $script_domain = '//s7.addthis.com/js/';
        if (!empty($addthis_config['addthis_environment'])) {
            $at_env = $addthis_config['addthis_environment'];
            $script_domain = '//cache-'.$at_env.'.addthis.com/cachefly/js/';
        }

        $url = $script_domain .
            $addthis_config['atversion'] .
            '/addthis_widget.js#pubid=' .
            urlencode($addThisConfigs->getUsableProfileId());

        $script = '
            <!-- AddThis Settings Begin -->
            <script data-cfasync="false" type="text/javascript">
                var addthis_product = "'. $cmsConnector->getProductVersion() . ';
                var wp_product_version = "' . $this->cmsConnector->getProductVersion() . ';
                var wp_blog_version = "' . $this->cmsConnector->getCmsVersion() . ';
                var addthis_plugin_info = ' . $addThisConfigs->getAddThisPluginInfoJson() . ';
                if (typeof(addthis_config) == "undefined") {
                    ' . $addthis_config_js . '
                }
                if (typeof(addthis_share) == "undefined") {
                    ' . $addthis_share_js . '
                }
                if (typeof(addthis_layers) == "undefined") {
                    ' . $addthis_layers_js . '
                }
            </script>
            <script
                data-cfasync="false"
                type="text/javascript"
                src="' . $url . '"
                ' . $async . '
            >
            </script>
            <script data-cfasync="false" type="text/javascript">
                (function() {
                    var at_interval = setInterval(function () {
                        if(window.addthis) {
                            clearInterval(at_interval);
                            addthis.layers(addthis_layers);
                        }
                    },1000)
                }());
            </script>
            ';

        if (!is_admin() && !is_feed()) {
            echo $script;
        } elseif ($return && !is_admin() && !is_feed()) {
            return $script;
        }
    }

    /**
    * Appends AddThis button to post content.
    */
    function addthis_social_widget($content, $onSidebar = false, $url = null, $title = null)
    {
        addthis_set_addthis_settings();
        global $addthis_settings;
        global $addThisConfigs;
        global $cmsConnector;

        // add nothing to RSS feed or search results; control adding to static/archive/category pages
        if (!$onSidebar)
        {
            if (   $addthis_settings['sidebar_only']
                || is_feed()
                || is_search()
                || is_home()
                || is_page()
                || is_archive()
                || is_category()
            ) {
                return $content;
            }
        }

        $pub = urlencode($addThisConfigs->getUsableProfileId());

        $link  = !is_null($url) ? $url : ($onSidebar ? get_bloginfo('url') : get_permalink());
        $title = !is_null($title) ? $title : ($onSidebar ? get_bloginfo('title') : the_title('', '', false));

        $content .= '
            <!-- AddThis Button BEGIN -->
            <script data-cfasync="false" type="text/javascript">'
                ."\n//<!--\n"
                    ."var addthis_product = '". $cmsConnector->getProductVersion() ."';\n";


        if (strlen($addthis_settings['customization']))
        {
            $content .= ($addthis_settings['customization']) . "\n";
        }

        if ($addthis_settings['menu_type'] === 'dropdown') {
            $content .= '
                </script>
                    <div class="addthis_container">
                        <a
                            href="//www.addthis.com/bookmark.php?v='.$atversion.'&amp;username='.$pub.'"
                            class="addthis_button"
                            ' . addthis_get_identifier($link, $title) . '
                        >
                ';

            $content .= ($addthis_settings['language'] == '' ? '' /* no hardcoded image -- we'll choose the language automatically */ : addthis_get_button_img()) . '</a><script data-cfasync="false" type="text/javascript" src="//s7.addthis.com/js/'.$atversion.'/addthis_widget.js#username='.$pub.'"></script></div>';
        } else if ($addthis_settings['menu_type'] === 'toolbox') {
            $content .= "\n//-->\n</script>\n";
            $content .= '
                <div
                    class="addthis_container addthis_toolbox addthis_default_style"
                    ' . addthis_get_identifier($link, $title) . '
                >
                    <a
                        href="//www.addthis.com/bookmark.php?v='.$atversion.'&amp;username=$pub"
                        class="addthis_button_compact">
                        Share
                    </a>
                    <span class="addthis_separator">|</span>
                    <script data-cfasync="false" type="text/javascript" src="//s7.addthis.com/js/'.$atversion.'/addthis_widget.js#username='.$pub.'">
                    </script>
                </div>';
        } else {
            $link = urlencode($link);
            $title = urlencode($title);
            $content .= '//-->
                </script>
                <div class="addthis_container">
                <a
                    href="//www.addthis.com/bookmark.php?v='.$atversion.'&amp;username=$pub"
                    onclick="window.open(\'//www.addthis.com/bookmark.php?v='.$atversion.'&amp;username='.$pub.'&amp;url='.$link.'&amp;title='.$title.'\', \'ext_addthis\', \'scrollbars=yes,menubar=no,width=620,height=520,resizable=yes,toolbar=no,location=no,status=no\'); return false;"
                    title="Bookmark using any bookmark manager!"
                    target="_blank"
                >
            ';
            $content .= addthis_get_button_img() . '</a></div>';
        }
        $content .= "\n<!-- AddThis Button END -->";

        return $content;
    }

    /**
    * Generates img tag for share/bookmark button.
    */
    function addthis_get_button_img( $btnStyle = false )
    {
        global $addthis_settings;
        global $addthis_styles;
        global $addThisConfigs;
        $options = $addThisConfigs->getConfigs();

        $language = $options['language'];

        if ($btnStyle == false)
            $btnStyle = $addthis_settings['style'];
        if ($addthis_settings['language'] != 'en')
        {
            // We use a translation of the word 'share' for all verbal buttons
            switch ($btnStyle)
            {
                case 'bookmark':
                case 'addthis':
                case 'bookmark-sm':
                    $btnStyle = 'share';
            }
        }

        if (!isset($addthis_styles[$btnStyle])) $btnStyle = 'share';
        $btnRecord = $addthis_styles[$btnStyle];
        $btnUrl = (strpos(trim($btnRecord['img']), '//') !== 0 ? "//s7.addthis.com/static/btn/v2/" : "") . $btnRecord['img'];

        if (strpos($btnUrl, '%lang%') !== false)
        {
            $btnUrl = str_replace('%lang%', strlen($language) ? $language : 'en', $btnUrl);
        }
        $btnWidth = $btnRecord['w'];
        $btnHeight = $btnRecord['h'];
        return <<<EOF
<img src="$btnUrl" width="$btnWidth" height="$btnHeight" style="border:0" alt="Bookmark and Share"/>
EOF;
    }

    function addToWordpressMenu()
    {
        global $cmsConnector;
        $htmlGeneratingFunction = 'addthis_plugin_options_php4';
        $cmsConnector->addSettingsPage($htmlGeneratingFunction);
    }

    function addthis_plugin_options_php4() {
        global $current_user;
        $user_id = $current_user->ID;
        global $addThisConfigs;
        $options = $addThisConfigs->getConfigs();
        global $cmsConnector;

        if (get_user_meta($user_id, 'addthis_nag_updated_options') )
            delete_user_meta($user_id, 'addthis_nag_updated_options', 'true');

        ?>
        <div class="wrap">
            <h2 class='placeholder'>&nbsp;</h2>

            <form
                id="addthis-settings"
                method="post"
                action="options.php"
            >
                <?php
                    // use the old-school settings style in older versions of wordpress
                    if (   $cmsConnector->getCmsMinorVersion() >= 2.7
                        || $cmsConnector->assumeLatest()
                    ) {
                        settings_fields('addthis');
                    } else {
                        wp_nonce_field('update-options');
                    }
                ?>

                <div class="Header">
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.22 @dgras                    -->
                    <h1><?php echo __("<em>AddThis</em> Sharing Buttons",'addthis_trans_domain') ?></h1>
<!--************ ORIGINAL-->
<!--                    <h1><em>AddThis</em> Sharing Buttons</h1>-->
<!--************ FI-->
                    <span class="preview-save-btns">
                        <?php echo _addthis_settings_buttons(); ?>
                    </span>
                </div>

                <?php
                        if (_addthis_is_csr_form()) {
                            // Get Confirmation form
                            echo addthis_profile_id_csr_confirmation();
                        } else {
                            addthis_wordpress_mode_tabs();
                        }
                ?>

                <div class="Btn-container-end">
                    <?php echo _addthis_settings_buttons(); ?>
                </div>

            </form>
        </div>
    <?php
    }
    add_action('init', 'addthis_init');

    function addthis_wordpress_mode_tabs() {
        global $addThisConfigs;
        global $cmsConnector;

        $options = $addThisConfigs->getConfigs();

        ?>
        <div class="Main-content" id="tabs">
            <ul class="Tabbed-nav">
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.22 @dgras                    -->
                                <li class="Tabbed-nav-item"><a href="#tabs-1"><?php echo __("Sharing Tools",'addthis_trans_domain')?></a></li>
                                <li class="Tabbed-nav-item"><a href="#tabs-2"><?php echo __("Advanced Options",'addthis_trans_domain') ?></a></li>
<!--************ ORIGINAL-->
<!--                <li class="Tabbed-nav-item"><a href="#tabs-1">Sharing Tools</a></li>-->
<!--                <li class="Tabbed-nav-item"><a href="#tabs-2">Advanced Options</a></li>-->
<!--************ FI-->
            </ul>

            <div id="tabs-1">
                <input
                    type="hidden"
                    value="<?php echo $options['atversion']?>"
                    name="addthis_settings[atversion]"
                    id="addthis_atversion_hidden" />
                <input
                    type="hidden"
                    value="<?php echo $options['atversion_update_status']?>"
                    name="addthis_settings[atversion_update_status]"
                    id="addthis_atversion_update_status" />
                <input
                    type="hidden"
                    value="<?php echo $options['credential_validation_status']?>"
                    name="addthis_settings[credential_validation_status]"
                    id="addthis_credential_validation_status" />

                <div class="Card" id="Card-above-post">
                    <div class="Card-hd">
                        <div class="at-tool-toggle">
                            <input
                                type="checkbox"
                                value="true"
                                name="addthis_settings[addthis_above_enabled]"
                                class="addthis-toggle-cb"
                                id="addthis_above_enabled"
                                style="display:none;" <?php echo ( $options['addthis_above_enabled']  != false ? 'checked="checked"' : ''); ?>/>
                            <div
                                id="addthis_above_enabled_switch"
                                class="addthis-switch <?php echo ( $options['addthis_above_enabled']  != false ? 'addthis-switchOn' : ''); ?>">
                            </div>
                        </div>
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.22 @dgras                    -->
                        <h3 class="Card-hd-title"><?php echo __("Sharing Buttons Above Content",'addthis_trans_domain') ?></h3>
                        <ul class="Tabbed-nav">
                            <li class="Tabbed-nav-item"><a href="#top-1"><?php echo __("Style",'addthis_trans_domain')?></a></li>
                            <li class="Tabbed-nav-item"><a href="#top-2"><?php echo __("Options",'addthis_trans_domain')?></a></li>
                        </ul>
<!--************ ORIGINAL-->
<!--                        <h3 class="Card-hd-title">Sharing Buttons Above Content</h3>-->
<!--                        <ul class="Tabbed-nav">-->
<!--                            <li class="Tabbed-nav-item"><a href="#top-1">Style</a></li>-->
<!--                            <li class="Tabbed-nav-item"><a href="#top-2">Options</a></li>-->
<!--                        </ul>-->
<!--************ FI-->
                    </div>
                    <div class="addthis_above_enabled_overlay" >
                        <div  class="Card-bd">
                            <div id="top-1">
                                <?php _addthis_choose_icons('above', $options ); ?>
                            </div>
                            <div id="top-2">
                                <?php _addthis_print_template_checkboxes('above') ?>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="Card" id="Card-below-post">
                    <div class="Card-hd">
                        <div class="at-tool-toggle">
                            <input
                                type="checkbox"
                                value="true"
                                name="addthis_settings[addthis_below_enabled]"
                                class="addthis-toggle-cb"
                                id="addthis_below_enabled"
                                style="display:none;" <?php echo ( $options['addthis_below_enabled'] != false ? 'checked="checked"' : ''); ?>/>
                            <div
                                id="addthis_below_enabled_switch"
                                class="addthis-switch <?php echo ( $options['addthis_below_enabled'] != false ? 'addthis-switchOn' : ''); ?>">
                            </div>
                        </div>
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.22 @dgras                    -->
                        <h3 class="Card-hd-title"><?php echo __("Sharing Buttons Below Content",'addthis_trans_domain') ?></h3>
                        <ul class="Tabbed-nav">
                            <li class="Tabbed-nav-item"><a href="#bottom-1"><?php echo __("Style",'addthis_trans_domain')?></a></li>
                            <li class="Tabbed-nav-item"><a href="#bottom-2"><?php echo __("Options",'addthis_trans_domain')?></a></li>
                        </ul>
<!--************ ORIGINAL-->
<!--                       <h3 class="Card-hd-title">Sharing Buttons Below Content</h3>-->
<!--                        <ul class="Tabbed-nav">-->
<!--                            <li class="Tabbed-nav-item"><a href="#bottom-1">Style</a></li>-->
<!--                            <li class="Tabbed-nav-item"><a href="#bottom-2">Options</a></li>-->
<!--                        </ul>-->
<!--************ FI-->
                    </div>
                    <div class="addthis_below_enabled_overlay">
                        <div class="Card-bd">
                            <div id="bottom-1">
                                <?php _addthis_choose_icons('below', $options ); ?>
                            </div>
                            <div id="bottom-2">
                                <?php _addthis_print_template_checkboxes('below') ?>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="Card"  id="Card-side-sharing">
                    <div class="Card-hd">
                        <div class="at-tool-toggle">
                            <input
                                type="checkbox"
                                value="true"
                                name="addthis_settings[addthis_sidebar_enabled]"
                                class="addthis-toggle-cb" id="addthis_sidebar_enabled"
                                style="display:none;" <?php echo ( $options['addthis_sidebar_enabled'] != false ? 'checked="checked"' : ''); ?>/>
                            <div
                                id="addthis_sidebar_enabled_switch"
                                class="addthis-switch <?php echo ( $options['addthis_sidebar_enabled']  != false ? 'addthis-switchOn' : ''); ?>">
                            </div>
                        </div>
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.22 @dgras                    -->
                            <h3 class="Card-hd-title"><?php echo __("Sharing Sidebar",'addthis_trans_domain') ?></h3>
                            <ul class="Tabbed-nav">
                                <li class="Tabbed-nav-item"><a href="#side-1"><?php echo __("Style",'addthis_trans_domain')?></a></li>
                                <li class="Tabbed-nav-item"><a href="#side-2"><?php echo __("Options",'addthis_trans_domain')?></a></li>
                            </ul>
<!--************ ORIGINAL-->
<!--                        <h3 class="Card-hd-title">Sharing Sidebar</h3>-->
<!--                        <ul class="Tabbed-nav">-->
<!--                            <li class="Tabbed-nav-item"><a href="#side-1">Style</a></li>-->
<!--                            <li class="Tabbed-nav-item"><a href="#side-2">Options</a></li>-->
<!--                        </ul>-->
<!--************ FI-->
                    </div>
                    <div class="addthis_sidebar_enabled_overlay">
                        <div class="Card-bd">
                            <div id="side-1">

<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.22 @dgras                    -->
                                <p><?php echo __("These buttons will appear on the side of the page, along the edge.",'addthis_trans_domain') ?></p>
<!--************ ORIGINAL-->
<!--                                <p>These buttons will appear on the side of the page, along the edge.</p>-->
<!--************ FI-->

                                <img src="<?php echo $cmsConnector->getPluginImageFolderUrl() . 'sidebar_theme_light.png'; ?>" />
                                <ul>
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.22 @dgras                    -->
                                    <li>
                                        <strong><?php echo __("Position",'addthis_trans_domain') ?></strong>
                                        <label for="addthis_sidebar_position_left" class="addthis-sidebar-position-label">
                                            <input
                                                type="radio"
                                                id="addthis_sidebar_position_left"
                                                name="addthis_settings[addthis_sidebar_position]"
                                                value="left" <?php echo ( $options['addthis_sidebar_position'] == 'left' ? 'checked="checked"' : ''); ?>/>
                                            <span class="addthis-checkbox-label"><?php echo __("Left") ?></span>
                                        </label>
                                        <label for="addthis_sidebar_position_right" class="addthis-sidebar-position-label">
                                            <input
                                                type="radio"
                                                id="addthis_sidebar_position_right"
                                                name="addthis_settings[addthis_sidebar_position]"
                                                value="right" <?php echo ( $options['addthis_sidebar_position']  == 'right' ? 'checked="checked"' : ''); ?>/>
                                            <span class="addthis-checkbox-label"><?php echo __("Right") ?></span>
                                        </label>
                                    </li>
<!--************ ORIGINAL-->
<!--                                    <li>-->
<!--                                        <strong>Position</strong>-->
<!--                                        <label for="addthis_sidebar_position_left" class="addthis-sidebar-position-label">-->
<!--                                            <input-->
<!--                                                type="radio"-->
<!--                                                id="addthis_sidebar_position_left"-->
<!--                                                name="addthis_settings[addthis_sidebar_position]"-->
<!--                                                value="left" --><?php //echo ( $options['addthis_sidebar_position'] == 'left' ? 'checked="checked"' : ''); ?><!--/>-->
<!--                                            <span class="addthis-checkbox-label">Left</span>-->
<!--                                        </label>-->
<!--                                        <label for="addthis_sidebar_position_right" class="addthis-sidebar-position-label">-->
<!--                                            <input-->
<!--                                                type="radio"-->
<!--                                                id="addthis_sidebar_position_right"-->
<!--                                                name="addthis_settings[addthis_sidebar_position]"-->
<!--                                                value="right" --><?php //echo ( $options['addthis_sidebar_position']  == 'right' ? 'checked="checked"' : ''); ?><!--/>-->
<!--                                            <span class="addthis-checkbox-label">Right</span>-->
<!--                                        </label>-->
<!--                                    </li>-->
<!--************ FI-->
                                </ul>
                            </div>
                            <div id="side-2">
                                <?php _addthis_print_template_checkboxes('sidebar') ?>
                                <ul>
                                    <li>
                                        <label for="addthis_sidebar_count">
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.22 @dgras                    -->
                                            <strong><?php echo __("Buttons",'addthis_trans_domain') ?></strong>
                                            <span class="at-wp-tooltip" tooltip="<?php echo __("The number of social sharing buttons to show in the side sharing tool.",'addthis_trans_domain')?>">?</span>
<!--************ ORIGINAL-->
<!--                                            <strong>Buttons</strong>-->
<!--                                            <span class="at-wp-tooltip" tooltip="The number of social sharing buttons to show in the side sharing tool.">?</span>-->
<!--************ FI-->
                                        </label>
                                        <select id="addthis_sidebar_count" name="addthis_settings[addthis_sidebar_count]">
                                            <?php
                                                for($i=1;$i<7;$i++){
                                                    echo '<option value="'.$i.'"'.($options['addthis_sidebar_count'] == $i? " selected='selected'":"").'>'.$i.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </li>
                                    <li>
<!--XTEC ************ MODIFICAT - Localization support-->
<!--2015.09.22 @dgras                    -->
                                        <label for="addthis_sidebar_theme">
                                            <strong><?php echo __("Theme",'addthis_trans_domain') ?></strong>
                                            <span class="at-wp-tooltip" tooltip="<?php echo __("You can select the background color that better matches the look of your site for the expand/minimize arrow on the side sharing tool.",'addthis_trans_domain')?>">?</span>
                                        </label>
<!--************ ORIGINAL-->
<!--                                        <label for="addthis_sidebar_theme">-->
<!--                                            <strong>Theme</strong>-->
<!--                                            <span class="at-wp-tooltip" tooltip="You can select the background color that better matches the look of your site for the expand/minimize arrow on the side sharing tool.">?</span>-->
<!--                                        </label>-->
<!--************ FI-->
                                        <select id="addthis_sidebar_theme" name="addthis_settings[addthis_sidebar_theme]">
                                            <?php
                                                $themes = array("Transparent","Light","Gray","Dark");
                                                foreach ($themes as $theme) {
                                                    echo '<option value="'.$theme.'"'.($options['addthis_sidebar_theme'] == $theme ? " selected='selected'":"").'>'.$theme.'</option>';
                                                }
                                            ?>
                                        </select>
                                        <br />
                                        <img src="<?php echo $cmsConnector->getPluginImageFolderUrl() . 'sidebar_theme_light.png'; ?>" id="sbpreview_Light"/>
                                        <img src="<?php echo $cmsConnector->getPluginImageFolderUrl() . 'sidebar_theme_gray.png'; ?>" id="sbpreview_Gray"/>
                                        <img src="<?php echo $cmsConnector->getPluginImageFolderUrl() . 'sidebar_theme_dark.png'; ?>" id="sbpreview_Dark"/>
                                        <img src="<?php echo $cmsConnector->getPluginImageFolderUrl() . 'sidebar_theme_light.png'; ?>" id="sbpreview_Transparent"/>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo _addthis_rate_us_card(); ?>
            </div>
            <div id="tabs-2">
                <?php echo _addthis_tracking_card(); ?>

                <?php echo _addthis_display_options_card(); ?>

                <?php echo _addthis_additional_options_card(); ?>

                <?php echo _addthis_profile_id_card($options['credential_validation_status']); ?>

                <?php echo _addthis_mode_card(); ?>
            </div>
        </div>
        <?php
    }

    /* 2.9 compatability functions
     */

    if (! function_exists('get_user_meta'))
    {
        function get_user_meta($userid, $metakey, $ignored='')
        {
            $userdata = get_userdata($userid);
            if (isset($userdata->{$metakey}) )
                return $userdata->{$metakey};
            else
                return false;
        }

    }
    if (! function_exists('delete_user_meta'))
    {
        function delete_user_meta($userid, $metakey, $ignored = '')
        {
            return delete_usermeta($userid, $metakey);
        }
    }

    if (! function_exists('add_user_meta'))
    {
        function add_user_meta($userid, $metakey, $metavalue)
        {
            return update_usermeta($userid, $metakey, $metavalue);
        }
    }
    if (! function_exists('get_home_url'))
    {
        function get_home_url()
        {
            return get_option( 'home' );
        }
    }

    if (! function_exists('__return_false'))
    {
        function __return_false()
        {
            return false;
        }
    }

    if (! function_exists('__return_true'))
    {
        function __return_true()
        {
            return true;
        }
    }

    if (! function_exists('esc_textarea'))
    {
        function esc_textarea($text)
        {
             $safe_text = htmlspecialchars( $text, ENT_QUOTES );
             return $safe_text;
        }

    }

    // check for pro user
    function at_share_is_pro_user() {
        global $addThisConfigs;
        $isPro = false;

        if ($addThisConfigs->getProfileId()) {
            $request = wp_remote_get( "http://q.addthis.com/feeds/1.0/config.json?pubid=" . $addThisConfigs->getProfileId() );
            $server_output = wp_remote_retrieve_body( $request );
            $array = json_decode($server_output);
            // check for pro user
            if (is_array($array) && array_key_exists('_default',$array)) {
                $isPro = true;
            } else {
                $isPro = false;
            }
        }
        return $isPro;
    }

    function addthis_deactivation_hook()
    {
        if (get_option('addthis_run_once')) {
            delete_option('addthis_run_once');
        }
    }

    // Deactivation
    register_deactivation_hook(__FILE__, 'addthis_deactivation_hook');
}

function _addthis_profile_setup_url() {
    $pubName = get_bloginfo('name');
    global $cmsConnector;

    if (!preg_match('/^[A-Za-z0-9 _\-\(\)]*$/', $pubName)) {
        // if title not match, get domain
        $url     = get_option('siteurl');
        $urlobj  = parse_url($url);
        $domain  = $urlobj['host'];

        if (preg_match(
            '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i',
            $domain, $regs
        )) {
            $domainArray = explode(".", $regs['domain']);
            $pubName     = $domainArray[0];
        } else {
            $pubName = '';
        }
        $pubName  = str_replace('.', '', $pubName);
    }

    if (!preg_match('/^[A-Za-z0-9 _\-\(\)]*$/', $pubName) || $pubName == '') {
        // if domain not match, get loggedin username
        $currentUser = wp_get_current_user();
        $pubName = $currentUser->user_login;
    }

    $profileSetupUrl = 'https://www.addthis.com/settings/plugin-pubs'
        . '?cms=wp&pubname='
        . urlencode($pubName)
        . '&wp_redirect='
        . str_replace(
            '.',
            '%2E',
            urlencode($cmsConnector->getSettingsPageUrl())
        );

    return $profileSetupUrl;
}

function _addthis_analytics_url() {
    global $addThisConfigs;
    $analyticsUrl = 'https://www.addthis.com/dashboard#analytics/' . $addThisConfigs->getProfileId();
    return $analyticsUrl;
}

function _addthis_tools_url() {
    global $addThisConfigs;
    $addthis_options = $addThisConfigs->getConfigs();
    $toolsUrl = 'https://www.addthis.com/settings/plugin-pubs?cms=wp&pubid=' . $addThisConfigs->getProfileId();
    return $toolsUrl;
}

function _addthis_profile_id_card($credential_validation_status = false) {
    global $addThisConfigs;
    $addthis_options = $addThisConfigs->getConfigs();

    $fieldId = 'addthis_profile';
//XTEC ************ MODIFICAT - Localization support
//2015.09.22 @dgras
    $noPubIdDescription = __('To begin tracking analytics on social shares from your site, use the button below to set up an AddThis account at addthis.com and create a profile for your site. This process will require an email address.','addthis_trans_domain');
    $noPubIdButtonText = __("AddThis profile setup",'addthis_trans_domain');
    $pubIdDescription = __('To see analytics on social shares from your site, use the button below. It will take you to Analytics on addthis.com.','addthis_trans_domain');
    $pubIdButtonText = __("Your analytics on addthis.com",'addthis_trans_domain');
//************ ORIGINAL
//    $noPubIdDescription = 'To begin tracking analytics on social shares from your site, use the button below to set up an AddThis account at addthis.com and create a profile for your site. This process will require an email address.';
//    $noPubIdButtonText = "AddThis profile setup";
//    $pubIdDescription = 'To see analytics on social shares from your site, use the button below. It will take you to Analytics on addthis.com.';
//    $pubIdButtonText = "Your analytics on addthis.com";
//************ FI
    $fieldName = 'addthis_settings[addthis_profile]';

    $securitySnippet = '';
    if ($credential_validation_status == 1) {
//XTEC ************ MODIFICAT - Localization support
//2015.09.22 @dgras
        $securitySnippet = '<span class="success_text">&#10004; '. __("Valid AddThis Profile ID",'addthis_trans_domain').'</span>';
//************ ORIGINAL
//        $securitySnippet = '<span class="success_text">&#10004; Valid AddThis Profile ID</span>';
//************ FI
    }

    // because i can't figure out how to bring these two in line... :-(
    if ($addthis_options['addthis_plugin_controls'] != "AddThis") {
        $security = '';
    } else {
        $security = wp_nonce_field('update_pubid', 'pubid_nonce');
    }

    if ($addThisConfigs->getProfileId()) {
        $description = $pubIdDescription;
        $buttonUrl = _addthis_analytics_url();
        $buttonText = $pubIdButtonText;
        $alternatePath = '';
        $target = 'target="_blank"';
    } else {
        $description = $noPubIdDescription;
        $buttonUrl = _addthis_profile_setup_url();
        $buttonText = $noPubIdButtonText;
//XTEC ************ MODIFICAT - Localization support
//2015.09.22 @dgras
                $alternatePath = '<p>'. __("Alternately, you can input your profile id manually below",'addthis_trans_domain').'</p>';
//************ ORIGINAL
//        $alternatePath = '<p>Alternately, you can input your profile id manually below.</p>';
//************ FI
        $target = '';
    }

    $html = '
        <div class="Card">
            <div class="Card-hd">
<!--XTEC ************ MODIFICAT - Localization support -->
<!-- 2015.09.22 @dgras -->
                <h3 class="Card-hd-title">'. __("AddThis Analytics",'addthis_trans_domain').'</h3>
<!-- ************ ORIGINAL -->
<!--               <h3 class="Card-hd-title">AddThis Analytics</h3> -->
<!-- ************ FI -->
            </div>
            <div class="Card-bd">
                <div class="addthis_description">
                    ' . $description . '
                </div>
                <div class="Btn-container-card">
                    <a
                        class="Btn Btn-blue"
                        ' . $target . '
                        href="' . $buttonUrl . '"> ' . $buttonText . ' &#8594;
                    </a>
                </div>
                ' . $alternatePath . '
                <label for="' . $fieldId . '">
<!--XTEC ************ MODIFICAT - Localization support -->
<!-- 2015.10.02 @dgras -->
                <strong>'.__("AddThis Profile ID",'addthis_trans_domain').'</strong>
<!-- ************ ORIGINAL -->
                    <!-- <strong>AddThis Profile ID</strong> -->
<!-- ************ FI -->
                </label>
                <ul class="Profile-widget">
                    <li>
                        <input
                            id="' . $fieldId . '"
                            type="text"
                            name="' . $fieldName . '"
                            value="' . $addThisConfigs->getProfileId() . '"
                            autofill="off"
                            autocomplete="off"
                        />
                        '
                        . $security
                        . '
                    </li>
                    <li>
                        ' . $securitySnippet . '
                    </li>
                </ul>
            </div>
        </div>
    ';

    return $html;
}

function _addthis_mode_card() {
    global $addThisConfigs;
    $addthis_options = $addThisConfigs->getConfigs();

    $wordPressChecked = '';
    $addThisChecked = '';
    $fieldName = 'addthis_settings[addthis_plugin_controls]';
    $fieldId = 'addthis_plugin_controls';

    $checked = 'checked="checked"';
    if ($addthis_options['addthis_plugin_controls'] != 'AddThis') {
        $wordPressChecked = $checked;
        $tbody = '<tbody></tbody>';
    } else {
        $addThisChecked = $checked;
//XTEC ************ MODIFICAT - Localization support
//2015.09.22 @dgras
        $tbody = '
            <tbody>
                <tr>
                    <th role="rowheader" scope="row">'. __("Sharing buttons above content",'addthis_trans_domain').'</td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("Sharing buttons below content",'addthis_trans_domain').'</td>
                     <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                     <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("Sharing sidebar",'addthis_trans_domain').'</td>
                     <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                     <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("Mobile toolbar",'addthis_trans_domain').'</td>
                    <td>
                        <span class="at-hidden-cell-content">'. __("Does not have this feature",'addthis_trans_domain').'</span>
                    </td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("Newsletter sharing buttons",'addthis_trans_domain').'</td>
                    <td>
                        <span class="at-hidden-cell-content">'. __("Does not have this feature",'addthis_trans_domain').'</span>
                    </td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("Follow header",'addthis_trans_domain').'</td>
                    <td>
                        <span class="at-hidden-cell-content">'. __("Does not have this feature",'addthis_trans_domain').'</span>
                    </td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("Horizontal follow buttons",'addthis_trans_domain').'</td>
                    <td><span class="at-hidden-cell-content">'. __("Does not have this feature",'addthis_trans_domain').'</span></td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("Vertical follow buttons",'addthis_trans_domain').'</td>
                    <td><span class="at-hidden-cell-content">'. __("Does not have this feature",'addthis_trans_domain').'</span></td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("What's next",'addthis_trans_domain').'</td>
                    <td><span class="at-hidden-cell-content">'. __("Does not have this feature",'addthis_trans_domain').'</span></td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("Recommended content footer",'addthis_trans_domain').'</td>
                    <td><span class="at-hidden-cell-content">'. __("Does not have this feature",'addthis_trans_domain').'</span></td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("Horizontal recommended content",'addthis_trans_domain').'</td>
                    <td><span class="at-hidden-cell-content">'. __("Does not have this feature",'addthis_trans_domain').'</span></td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("Vertical recommended content",'addthis_trans_domain').'</td>
                    <td><span class="at-hidden-cell-content">'. __("Does not have this feature",'addthis_trans_domain').'</span></td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th role="rowheader" scope="row">'. __("Access to additional Pro tools for upgraded accounts",'addthis_trans_domain').'</td>
                    <td><span class="at-hidden-cell-content">'. __("Does not have this feature",'addthis_trans_domain').'</span></td>
                    <td>
                        <span class="at-icon-check">
                            <span class="at-icon-fallback-text">'. __("Has this Feature",'addthis_trans_domain').'</span>
                        </span>
                    </td>
                </tr>
            </tbody>
        ';
//************ ORIGINAL
//        $tbody = '
//            <tbody>
//                <tr>
//                    <th role="rowheader" scope="row">Sharing buttons above content</td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">Sharing buttons below content</td>
//                     <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                     <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">Sharing sidebar</td>
//                     <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                     <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">Mobile toolbar</td>
//                    <td>
//                        <span class="at-hidden-cell-content">Does not have this feature</span>
//                    </td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">Newsletter sharing buttons</td>
//                    <td>
//                        <span class="at-hidden-cell-content">Does not have this feature</span>
//                    </td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">Follow header</td>
//                    <td>
//                        <span class="at-hidden-cell-content">Does not have this feature</span>
//                    </td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">Horizontal follow buttons</td>
//                    <td><span class="at-hidden-cell-content">Does not have this feature</span></td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">Vertical follow buttons</td>
//                    <td><span class="at-hidden-cell-content">Does not have this feature</span></td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">What\'s next</td>
//                    <td><span class="at-hidden-cell-content">Does not have this feature</span></td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">Recommended content footer</td>
//                    <td><span class="at-hidden-cell-content">Does not have this feature</span></td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">Horizontal recommended content</td>
//                    <td><span class="at-hidden-cell-content">Does not have this feature</span></td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">Vertical recommended content</td>
//                    <td><span class="at-hidden-cell-content">Does not have this feature</span></td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//                <tr>
//                    <th role="rowheader" scope="row">Access to additional Pro tools for upgraded accounts</td>
//                    <td><span class="at-hidden-cell-content">Does not have this feature</span></td>
//                    <td>
//                        <span class="at-icon-check">
//                            <span class="at-icon-fallback-text">Has this Feature</span>
//                        </span>
//                    </td>
//                </tr>
//            </tbody>
//        ';
//************ FI

    }

    $debugHtml = '';
    if (isset($_GET['debug']) || !empty($addthis_options["addthis_environment"])) {
//XTEC ************ MODIFICAT - Localization support
//2015.09.22 @dgras
        $debugHtml = '
            <label for="addthis_environment"><strong>'.__("Environment (test/dev/local)",'addthis_trans_domain').'</strong></label>
            <input
                type="textbox"
                id="addthis_environment"
                name="addthis_settings[addthis_environment]"
                value="' . $addthis_options["addthis_environment"] . '"
            />
        ';
//************ ORIGINAL
//        $debugHtml = '
//            <label for="addthis_environment">
//                <strong>Environment (test/dev/local)</strong>
//            </label>
//            <input
//                type="textbox"
//                id="addthis_environment"
//                name="addthis_settings[addthis_environment]"
//                value="' . $addthis_options["addthis_environment"] . '"
//            />
//        ';
//************ FI
    }

//XTEC ************ MODIFICAT - Localization support
//2015.09.22 @dgras
    $html =  '
        <div class="Card">
            <div class="Card-hd">
                <h3 class="Card-hd-title">'. __("I want to control my plugin from...",'addthis_trans_domain').'</h3>
            </div>
            <div class="Card-bd">
                <p class="Card-description">'. __("Regardless of your choice, analytics for shares of your site will be available to you on the AddThis website after you set up your AddThis profile ID.",'addthis_trans_domain').'</p>
                <table class="at-comparison-table">
                    <thead>
                        <th role="columnheader" scope="col">
                            <span class="at-hidden-cell-content">'. __("Feature List Column",'addthis_trans_domain').'</span>
                        </th>
                        <th role="columnheader" scope="col">
                            <input type="radio" id="wordpress-option" name="' . $fieldName . '"  value="WordPress" ' . $wordPressChecked . ' />
                            <label for="wordpress-option">
                                <strong>WordPress</strong>
                            </label>
                        </th>
                        <th role="columnheader" scope="col">
                            <input type="radio" id="addthis-option" name="' . $fieldName . '" value="AddThis"' . $addThisChecked . '/>
                            <label for="addthis-option">
                                <strong>AddThis.com</strong>
                            </label>
                        </th>
                    </thead>
                    ' . $tbody . '
                </table>
                <p>'. __("To see your choice reflected on these screens, save your changes.",'addthis_trans_domain').'</p>
                ' . $debugHtml . '
            </div>
        </div>';
//************ ORIGINAL
//    $html =  '
//        <div class="Card">
//            <div class="Card-hd">
//                <h3 class="Card-hd-title">I want to control my plugin from...</h3>
//            </div>
//            <div class="Card-bd">
//                <p class="Card-description">Regardless of your choice, analytics for shares of your site will be available to you on the AddThis website after you set up your AddThis profile ID.</p>
//                <table class="at-comparison-table">
//                    <thead>
//                        <th role="columnheader" scope="col">
//                            <span class="at-hidden-cell-content">Feature List Column</span>
//                        </th>
//                        <th role="columnheader" scope="col">
//                            <input type="radio" id="wordpress-option" name="' . $fieldName . '"  value="WordPress" ' . $wordPressChecked . ' />
//                            <label for="wordpress-option">
//                                <strong>WordPress</strong>
//                            </label>
//                        </th>
//                        <th role="columnheader" scope="col">
//                            <input type="radio" id="addthis-option" name="' . $fieldName . '" value="AddThis"' . $addThisChecked . '/>
//                            <label for="addthis-option">
//                                <strong>AddThis.com</strong>
//                            </label>
//                        </th>
//                    </thead>
//                    ' . $tbody . '
//                </table>
//                <p>To see your choice reflected on these screens, save your changes.</p>
//                ' . $debugHtml . '
//            </div>
//        </div>';
//************ FI

    return $html;
}

function _addthis_tracking_card() {
    global $addThisConfigs;
    $options = $addThisConfigs->getConfigs();

    $checkedString = 'checked="checked"';
    $clickbacksChecked = "";
    $addressBarChecked = "";
    $bitlyChecked = "";
    if (!empty($options['addthis_append_data'])) {
        $clickbacksChecked = $checkedString;
    }
    if (!empty($options['addthis_addressbar'])) {
        $addressBarChecked = $checkedString;
    }
    if (!empty($options['addthis_bitly'])) {
        $bitlyChecked = $checkedString;
    }
//XTEC ************ MODIFICAT - Localization support
//2015.09.22 @dgras
    $html = '
        <div class="Card">
            <div class="Card-hd">
                <h3 class="Card-hd-title">'.__("Tracking",'addthis_trans_domain').'</h3>
            </div>
            <div class="Card-bd">
                <ul class="Card-option-list">
                    <li>
                        <ul>
                            <li>
                                <input
                                    id="addthis_append_data"
                                    type="checkbox"
                                    name="addthis_settings[addthis_append_data]"
                                    value="true"' . $clickbacksChecked . '/>
                                <label for="addthis_append_data">
                                    <span class="addthis-checkbox-label">
                                        <strong>' . __("Clickbacks", 'addthis_trans_domain') . '</strong>
                                        '.__("(Recommended)",'addthis_trans_domain').'
                                    </span>
                                </label>
                                <span class="at-wp-tooltip" tooltip="'.__("AddThis will use this to track how many people come back to your content via links shared with AddThis buttons. This data will be available to you at addthis.com.",'addthis_trans_domain').'">?</span>
                            </li>
                            <li>
                                <input
                                    type="checkbox"
                                    id="addthis_addressbar"
                                    name="addthis_settings[addthis_addressbar]"
                                    value="true"' . $addressBarChecked . '/>
                                <label for="addthis_addressbar">
                                    <span class="addthis-checkbox-label">
                                        <strong>' . __("Address bar shares", 'addthis_trans_domain') . '</strong>
                                    </span>
                                </label>
                                <span class="at-wp-tooltip" tooltip="'.__("AddThis will append a code to your site’s URLs (except for the homepage) to track when a visitor comes to your site from a link someone copied out of their browser's address bar.",'addthis_trans_domain').'">?</span>
                            </li>
                            <li>
                                <input
                                    id="addthis_bitly"
                                    type="checkbox"
                                    name="addthis_settings[addthis_bitly]"
                                    value="true" ' . $bitlyChecked . '/>
                                <label for="addthis_bitly">
                                    <span class="addthis-checkbox-label">
                                        <strong>' . __("Bitly URL shortening for Twitter", 'addthis_trans_domain','addthis_trans_domain') . '</strong>
                                    </span>
                                </label>
                                <span class="at-wp-tooltip" tooltip="'. __("Your Bitly login and key will need to be setup with your profile on addthis.com before Bitly will begin working with WordPress.",'addthis_trans_domain').'">?</span>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <label for="data_ga_property">
                            <strong>' . __("Google Analytics property ID", 'addthis_trans_domain','addthis_trans_domain') . '</strong>
                        </label>
                        <input
                            id="data_ga_property"
                            type="text"
                            name="addthis_settings[data_ga_property]"
                            value="' . $options['data_ga_property'] . '"/>
                    </li>
                </ul>
            </div>
        </div>
    ';
//************ ORIGINAL
//    $html = '
//        <div class="Card">
//            <div class="Card-hd">
//                <h3 class="Card-hd-title">Tracking</h3>
//            </div>
//            <div class="Card-bd">
//                <ul class="Card-option-list">
//                    <li>
//                        <ul>
//                            <li>
//                                <input
//                                    id="addthis_append_data"
//                                    type="checkbox"
//                                    name="addthis_settings[addthis_append_data]"
//                                    value="true"' . $clickbacksChecked . '/>
//                                <label for="addthis_append_data">
//                                    <span class="addthis-checkbox-label">
//                                        <strong>' . translate("Clickbacks", 'addthis_trans_domain') . '</strong>
//                                        (Recommended)
//                                    </span>
//                                </label>
//                                <span class="at-wp-tooltip" tooltip="AddThis will use this to track how many people come back to your content via links shared with AddThis buttons. This data will be available to you at addthis.com.">?</span>
//                            </li>
//                            <li>
//                                <input
//                                    type="checkbox"
//                                    id="addthis_addressbar"
//                                    name="addthis_settings[addthis_addressbar]"
//                                    value="true"' . $addressBarChecked . '/>
//                                <label for="addthis_addressbar">
//                                    <span class="addthis-checkbox-label">
//                                        <strong>' . translate("Address bar shares", 'addthis_trans_domain') . '</strong>
//                                    </span>
//                                </label>
//                                <span class="at-wp-tooltip" tooltip="AddThis will append a code to your site’s URLs (except for the homepage) to track when a visitor comes to your site from a link someone copied out of their browser\'s address bar.">?</span>
//                            </li>
//                            <li>
//                                <input
//                                    id="addthis_bitly"
//                                    type="checkbox"
//                                    name="addthis_settings[addthis_bitly]"
//                                    value="true" ' . $bitlyChecked . '/>
//                                <label for="addthis_bitly">
//                                    <span class="addthis-checkbox-label">
//                                        <strong>' . translate("Bitly URL shortening for Twitter", 'addthis_trans_domain') . '</strong>
//                                    </span>
//                                </label>
//                                <span class="at-wp-tooltip" tooltip="Your Bitly login and key will need to be setup with your profile on addthis.com before Bitly will begin working with WordPress.">?</span>
//                            </li>
//                        </ul>
//                    </li>
//                    <li>
//                        <label for="data_ga_property">
//                            <strong>' . translate("Google Analytics property ID", 'addthis_trans_domain') . '</strong>
//                        </label>
//                        <input
//                            id="data_ga_property"
//                            type="text"
//                            name="addthis_settings[data_ga_property]"
//                            value="' . $options['data_ga_property'] . '"/>
//                    </li>
//                </ul>
//            </div>
//        </div>
//    ';
//************ FI
    return $html;
}

function _addthis_display_options_card() {
    global $addThisConfigs;
    $options = $addThisConfigs->getConfigs();

    $checkedString = 'checked="checked"';
    $asyncChecked = "";
    $perPostEnableChecked = "";
    $a508Checked = "";
    if (!empty($options['addthis_asynchronous_loading'])) {
        $asyncChecked = $checkedString;
    }
    if (!empty($options['addthis_per_post_enabled'])) {
        $perPostEnableChecked = $checkedString;
    }
    if (!empty($options['addthis_508'])) {
        $a508Checked = $checkedString;
    }

//XTEC ************ MODIFICAT - Localization support
//2015.09.22 @dgras
    $html = '
        <div class="Card">
            <div class="Card-hd">
                <h3 class="Card-hd-title">'. __("Display Options",'addthis_trans_domain') .'</h3>
            </div>
            <div class="Card-bd">
                <ul class="Card-option-list">
                    <li>
                        <ul>
                            <li>
                                <input
                                    id="addthis_asynchronous_loading"
                                    type="checkbox"
                                    name="addthis_settings[addthis_asynchronous_loading]"
                                    value="true" ' . $asyncChecked . ' />
                                <label for="addthis_asynchronous_loading">
                                    <span class="addthis-checkbox-label">
                                        <strong>' . __("Asynchronous loading", 'addthis_trans_domain') . '</strong>
                                        '. __("(Recommended)",'addthis_trans_domain') .'
                                    </span>
                                </label>
                                <span class="at-wp-tooltip" tooltip="'.__("When checked, your site will load before AddThis sharing buttons are added. If unchecked, your site will not load until AddThis buttons (and AddThis JavaScript) have been loaded by your vistors.",'addthis_trans_domain').'">?</span>
                            </li>
                            <li>
                                <input
                                    id="addthis_per_post_enabled"
                                    type="checkbox"
                                    name="addthis_settings[addthis_per_post_enabled]"
                                    value="true" ' . $perPostEnableChecked . ' />
                                <label for="addthis_per_post_enabled">
                                    <span class="addthis-checkbox-label">
                                        <strong>' . __("Include an option to turn off sharing tools by post", 'addthis_trans_domain') . '</strong>
                                        '. __("(Recommended)",'addthis_trans_domain') .'
                                    </span>
                                </label>
                                <span class="at-wp-tooltip" tooltip="'.__("When checked, on the edit page of posts you will be able to turn off sharing buttons for that specific post.",'addthis_trans_domain').'">?</span>
                            </li>
                            <li>
                                <input
                                    id="addthis_508"
                                    type="checkbox"
                                    name="addthis_settings[addthis_508]"
                                    value="true" ' . $a508Checked . ' />
                                <label for="addthis_508">
                                    <span class="addthis-checkbox-label">
                                        <strong>' . __("Enhanced accessibility", 'addthis_trans_domain') . '</strong>
                                    </span>
                                </label>
                                <span class="at-wp-tooltip" tooltip="'.__("Also known as 508 compliance. If checked, clicking an AddThis sharing button will open a new window to a page that is keyboard navigable for people with disabilities.", 'addthis_trans_domain').'">?</span>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <label id="addthis_twitter_template">
                            <strong>' . __("Twitter via", 'addthis_trans_domain','addthis_trans_domain') . '</strong>
                            <span class="at-wp-tooltip" tooltip="'.__("When a visitor uses an AddThis button to send a tweet about your page, this will be used within Twitter to identify through whom they found your page. You would usually enter a twitter handle here. For example, Twitter could show a tweet came from jsmith via AddThis.",'addthis_trans_domain').'">?</span>
                        </label>
                        <input
                            id="addthis_twitter_template"
                            type="text"
                            name="addthis_settings[addthis_twitter_template]"
                            value="' . $options['addthis_twitter_template'] . '" />
                    </li>
                </ul>
            </div>
        </div>
    ';
//************ ORIGINAL
//    $html = '
//        <div class="Card">
//            <div class="Card-hd">
//                <h3 class="Card-hd-title">Display Options</h3>
//            </div>
//            <div class="Card-bd">
//                <ul class="Card-option-list">
//                    <li>
//                        <ul>
//                            <li>
//                                <input
//                                    id="addthis_asynchronous_loading"
//                                    type="checkbox"
//                                    name="addthis_settings[addthis_asynchronous_loading]"
//                                    value="true" ' . $asyncChecked . ' />
//                                <label for="addthis_asynchronous_loading">
//                                    <span class="addthis-checkbox-label">
//                                        <strong>' . translate("Asynchronous loading", 'addthis_trans_domain') . '</strong>
//                                        (Recommended)
//                                    </span>
//                                </label>
//                                <span class="at-wp-tooltip" tooltip="When checked, your site will load before AddThis sharing buttons are added. If unchecked, your site will not load until AddThis buttons (and AddThis JavaScript) have been loaded by your vistors.">?</span>
//                            </li>
//                            <li>
//                                <input
//                                    id="addthis_per_post_enabled"
//                                    type="checkbox"
//                                    name="addthis_settings[addthis_per_post_enabled]"
//                                    value="true" ' . $perPostEnableChecked . ' />
//                                <label for="addthis_per_post_enabled">
//                                    <span class="addthis-checkbox-label">
//                                        <strong>' . translate("Include an option to turn off sharing tools by post", 'addthis_trans_domain') . '</strong>
//                                        (Recommended)
//                                    </span>
//                                </label>
//                                <span class="at-wp-tooltip" tooltip="When checked, on the edit page of posts you will be able to turn off sharing buttons for that specific post.">?</span>
//                            </li>
//                            <li>
//                                <input
//                                    id="addthis_508"
//                                    type="checkbox"
//                                    name="addthis_settings[addthis_508]"
//                                    value="true" ' . $a508Checked . ' />
//                                <label for="addthis_508">
//                                    <span class="addthis-checkbox-label">
//                                        <strong>' . translate("Enhanced accessibility", 'addthis_trans_domain') . '</strong>
//                                    </span>
//                                </label>
//                                <span class="at-wp-tooltip" tooltip="Also known as 508 compliance. If checked, clicking an AddThis sharing button will open a new window to a page that is keyboard navigable for people with disabilities.">?</span>
//                            </li>
//                        </ul>
//                    </li>
//                    <li>
//                        <label id="addthis_twitter_template">
//                            <strong>' . translate("Twitter via", 'addthis_trans_domain') . '</strong>
//                            <span class="at-wp-tooltip" tooltip="When a visitor uses an AddThis button to send a tweet about your page, this will be used within Twitter to identify through whom they found your page. You would usually enter a twitter handle here. For example, Twitter could show a tweet came from jsmith via AddThis.">?</span>
//                        </label>
//                        <input
//                            id="addthis_twitter_template"
//                            type="text"
//                            name="addthis_settings[addthis_twitter_template]"
//                            value="' . $options['addthis_twitter_template'] . '" />
//                    </li>
//                </ul>
//            </div>
//        </div>
//    ';
//************ FI
    return $html;
}

function _addthis_additional_options_card() {
    global $addthis_languages;
    global $addThisConfigs;
    $options = $addThisConfigs->getConfigs();

    $curlng = $options['addthis_language'];
    $languageDropdown = '';
    foreach ($addthis_languages as $lng=>$name)
    {
        $languageDropdown .= '
            <option
                value="'. $lng . '"'
                . ($lng == $curlng ? ' selected="selected"':'') . '"
            >
            '.$name.'
            </option>'."\n";
    }

//XTEC ************ MODIFICAT - Localization support
//2015.09.22 @dgras
    $html = '
        <div class="Card">
            <div class="Card-hd">
                <h3 class="Card-hd-title">'.__("Additional Options",'addthis_trans_domain').'</h3>
            </div>
            <div class="Card-bd">
                <ul class="Card-option-list">
                    <li>
                        <p class="Card-description">
                            '.sprintf(__("For more details on the following options, see <a href=\"%s\">our customization documentation</a>.",'addthis_trans_domain'),'//support.addthis.com/customer/portal/articles/381263-addthis-client-api').'
                            '.__("Important: AddThis optimizes displayed services based on popularity and language, and personalizes the list for each user. You may decrease sharing by overriding these features.",'addthis_trans_domain').'
                        </p>
                    </li>
                    <li>
                        <label for="addthis_language">
                            <strong>' . __("Language", 'addthis_trans_domain') . '</strong>
                        </label>
                        <select id="addthis_language" name="addthis_settings[addthis_language]">
                            ' . $languageDropdown . '
                        </select>
                    </li>
                    <li>
                        <h4><strong>'.__("Global Advanced API Configuration",'addthis_trans_domain').'</strong></h4>
                        <ul>
                            <li>
                                <label for="addthis_config_json">
                                    <?php ' . __("addthis_config values (json format)", 'addthis_trans_domain','addthis_trans_domain') . '
                                </label>
                                <br/>
                                <small>ex:- <i>{ "services_exclude": "print" }</i></small>
                                <div><p>'.sprintf(__("For more information, please see the AddThis documentation on <a href=\"%s\">the addthis_config variable</a>",'addthis_trans_domain'),"http://support.addthis.com/customer/portal/articles/1337994-the-addthis_config-variable").'.</p></div>
                                <textarea
                                    id="addthis_config_json"
                                    rows="3"
                                    type="text"
                                    name="addthis_settings[addthis_config_json]"
                                    id="addthis-config-json"/>' . $options['addthis_config_json'] . '</textarea>
                                <span id="config-error" class="hidden error_text">'.__("Invalid JSON format",'addthis_trans_domain','addthis_trans_domain','addthis_trans_domain').'</span>
                            </li>
                            <li>
                                <label for="addthis_share_json">
                                    ' . __("addthis_share values (json format)", 'addthis_trans_domain','addthis_trans_domain') . '
                                </label>
                                <br/>
                                <small>ex:- <i>{ "url" : "http://www.yourdomain.com", "title" : "Custom Title" }</i></small>
                                <div><p>'.sprintf(__("For more information, please see the AddThis documentation on <a href=\"%s\">the addthis_share variable</a>",'addthis_trans_domain'),"http://support.addthis.com/customer/portal/articles/1337996-the-addthis_share-variable").'.</p></div>
                                <textarea
                                    id="addthis_share_json"
                                    rows="3"
                                    type="text"
                                    name="addthis_settings[addthis_share_json]"
                                    id="addthis-share-json"/>' . $options['addthis_share_json'] . '</textarea>
                                <span id="share-error" class="hidden error_text">'.__("Invalid JSON format",'addthis_trans_domain').'</span>
                            </li>';

    if ($options['addthis_plugin_controls'] != 'AddThis') {
        $html .= '
                            <li>
                                <label for="addthis_layers_json">
                                    ' . translate("addthis.layers() initial paramater (json format)", 'addthis_trans_domain','addthis_trans_domain') . '
                                </label>
                                <br/>
                                <small>ex:- <i>{ "share": { "services": "facebook,twitter,google_plusone_share,pinterest_share,print,more" } }</i></small>
                                <div><p>'.sprintf(__("For more information, please see the AddThis documentation on <a href=\"%s\">the addthis.layers() paramters</a>",'addthis_trans_domain'),"http://support.addthis.com/customer/portal/articles/1200473-smart-layers-api").'.</p></div>
                                <textarea
                                    id="addthis_layers_json"
                                    rows="3"
                                    type="text"
                                    name="addthis_settings[addthis_layers_json]"
                                    id="addthis-layers-json"/>' . $options['addthis_layers_json'] . '</textarea>
                                <span id="layers-error" class="hidden error_text">'.__("Invalid JSON format",'addthis_trans_domain').'</span>
                            </li>
        ';
    }
//************ ORIGINAL
//    $html = '
//        <div class="Card">
//            <div class="Card-hd">
//                <h3 class="Card-hd-title">Additional Options</h3>
//            </div>
//            <div class="Card-bd">
//                <ul class="Card-option-list">
//                    <li>
//                        <p class="Card-description">
//                            For more details on the following options, see <a href="//support.addthis.com/customer/portal/articles/381263-addthis-client-api">our customization documentation</a>.
//                            Important: AddThis optimizes displayed services based on popularity and language, and personalizes the list for
//                            each user. You may decrease sharing by overriding these features.
//                        </p>
//                    </li>
//                    <li>
//                        <label for="addthis_language">
//                            <strong>' . translate("Language", 'addthis_trans_domain') . '</strong>
//                        </label>
//                        <select id="addthis_language" name="addthis_settings[addthis_language]">
//                            ' . $languageDropdown . '
//                        </select>
//                    </li>
//                    <li>
//                        <h4><strong>Global Advanced API Configuration</strong></h4>
//                        <ul>
//                            <li>
//                                <label for="addthis_config_json">
//                                    <?php ' . translate("addthis_config values (json format)", 'addthis_trans_domain') . '
//                                </label>
//                                <br/>
//                                <small>ex:- <i>{ "services_exclude": "print" }</i></small>
//                                <div><p>For more information, please see the AddThis documentation on <a href="http://support.addthis.com/customer/portal/articles/1337994-the-addthis_config-variable">the addthis_config variable</a>.</p></div>
//                                <textarea
//                                    id="addthis_config_json"
//                                    rows="3"
//                                    type="text"
//                                    name="addthis_settings[addthis_config_json]"
//                                    id="addthis-config-json"/>' . $options['addthis_config_json'] . '</textarea>
//                                <span id="config-error" class="hidden error_text">Invalid JSON format</span>
//                            </li>
//                            <li>
//                                <label for="addthis_share_json">
//                                    ' . translate("addthis_share values (json format)", 'addthis_trans_domain') . '
//                                </label>
//                                <br/>
//                                <small>ex:- <i>{ "url" : "http://www.yourdomain.com", "title" : "Custom Title" }</i></small>
//                                <div><p>For more information, please see the AddThis documentation on <a href="http://support.addthis.com/customer/portal/articles/1337996-the-addthis_share-variable">the addthis_share variable</a>.</p></div>
//                                <textarea
//                                    id="addthis_share_json"
//                                    rows="3"
//                                    type="text"
//                                    name="addthis_settings[addthis_share_json]"
//                                    id="addthis-share-json"/>' . $options['addthis_share_json'] . '</textarea>
//                                <span id="share-error" class="hidden error_text">Invalid JSON format</span>
//                            </li>';
//
//    if ($options['addthis_plugin_controls'] != 'AddThis') {
//        $html .= '
//                            <li>
//                                <label for="addthis_layers_json">
//                                    ' . translate("addthis.layers() initial paramater (json format)", 'addthis_trans_domain') . '
//                                </label>
//                                <br/>
//                                <small>ex:- <i>{ "share": { "services": "facebook,twitter,google_plusone_share,pinterest_share,print,more" } }</i></small>
//                                <div><p>For more information, please see the AddThis documentation on <a href="http://support.addthis.com/customer/portal/articles/1200473-smart-layers-api">the addthis.layers() paramters</a>.</p></div>
//                                <textarea
//                                    id="addthis_layers_json"
//                                    rows="3"
//                                    type="text"
//                                    name="addthis_settings[addthis_layers_json]"
//                                    id="addthis-layers-json"/>' . $options['addthis_layers_json'] . '</textarea>
//                                <span id="layers-error" class="hidden error_text">Invalid JSON format</span>
//                            </li>
//        ';
//    }
//************ FI
    $html .= '

                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    ';

    return $html;
}

function _addthis_rate_us_card() {
    global $addThisConfigs;
    $options = $addThisConfigs->getConfigs();
//XTEC ************ MODIFICAT - Localization support
//2015.09.23 @dgras
    $html = '
        <div class="Card" id="addthis_do_you_like_us">
            <div class="Card-hd">
                <h3 class="Card-hd-title">'.__("Did you find this plugin useful?",'addthis_trans_domain'). '</h3>
            </div>

            <div class="Card-bd" id="addthis_like_us_answers">
                <div class="Btn-container-card">
                    <a class="Btn Btn-blue" id="addthis_dislike_confirm">'.__("Not really ",'addthis_trans_domain'). ' </a>
                    <a class="Btn Btn-blue" id="addthis_like_confirm"> '.__("Yes!",'addthis_trans_domain'). ' </a>
                </div>

                <input
                    type="hidden"
                    value="' . $options['addthis_rate_us'] . '"
                    id="addthis_rate_us"
                    name="addthis_settings[addthis_rate_us]" >
            </div>

            <div class="Card-bd" id="addthis_dislike">
                <div class="addthis_description">
                    <p class="Card-description">
                        '.sprintf(__("Let us know how we can improve our plugin through our support forum or by emailing <a href=\"%s\">help@addthis.com</a>"),"mailto:help@addthis.com").'
                    </p>
                </div>
                <div class="Btn-container-card">
                    <a
                        class="Btn Btn-blue"
                        target="_blank"
                        href="https://wordpress.org/support/plugin/addthis"> '.__("Support Forum",'addthis_trans_domain' ).' &#8594;
                    </a>
                    <a
                        class="Btn Btn-blue"
                        target="_blank"
                        href="mailto:help@addthis.com"> '.__("Email",'addthis_trans_domain' ).' &#8594;
                    </a>
                </div>
            </div>

            <div class="Card-bd" id="addthis_like">
                <div class="addthis_description">
                    <p class="Card-description">
                        '.__("How about rating us?",'addthis_trans_domain' ).'
                    </p>
                    <h3 id="addthis_rating_thank_you">'.__("Thank you!" ,'addthis_trans_domain').'</h3>
                </div>
                <div class="Btn-container-card">
                    <a
                        class="Btn Btn-blue" id="addthis_not_rating"> '.__("No, thanks.",'addthis_trans_domain' ).'
                    </a>
                    <a
                        class="Btn Btn-blue" id="addthis_rating"
                        target="_blank"
                        href="https://wordpress.org/support/view/plugin-reviews/addthis#postform">'.__(" Yes, I will rate this plugin" ,'addthis_trans_domain').' &#8594;
                    </a>
                </div>
            </div>

        </div>
    ';
//************ ORIGINAL
//    $html = '
//        <div class="Card" id="addthis_do_you_like_us">
//            <div class="Card-hd">
//                <h3 class="Card-hd-title">Did you find this plugin useful?</h3>
//            </div>
//
//            <div class="Card-bd" id="addthis_like_us_answers">
//                <div class="Btn-container-card">
//                    <a class="Btn Btn-blue" id="addthis_dislike_confirm"> Not really </a>
//                    <a class="Btn Btn-blue" id="addthis_like_confirm"> Yes! </a>
//                </div>
//
//                <input
//                    type="hidden"
//                    value="' . $options['addthis_rate_us'] . '"
//                    id="addthis_rate_us"
//                    name="addthis_settings[addthis_rate_us]" >
//            </div>
//
//            <div class="Card-bd" id="addthis_dislike">
//                <div class="addthis_description">
//                    <p class="Card-description">
//                        Let us know how we can improve our plugin through our support forum or by emailing <a href="mailto:help@addthis.com">help@addthis.com</a>.
//                    </p>
//                </div>
//                <div class="Btn-container-card">
//                    <a
//                        class="Btn Btn-blue"
//                        target="_blank"
//                        href="https://wordpress.org/support/plugin/addthis"> Support Forum &#8594;
//                    </a>
//                    <a
//                        class="Btn Btn-blue"
//                        target="_blank"
//                        href="mailto:help@addthis.com"> Email &#8594;
//                    </a>
//                </div>
//            </div>
//
//            <div class="Card-bd" id="addthis_like">
//                <div class="addthis_description">
//                    <p class="Card-description">
//                        How about rating us?
//                    </p>
//                    <h3 id="addthis_rating_thank_you">Thank you!</h3>
//                </div>
//                <div class="Btn-container-card">
//                    <a
//                        class="Btn Btn-blue" id="addthis_not_rating"> No, thanks.
//                    </a>
//                    <a
//                        class="Btn Btn-blue" id="addthis_rating"
//                        target="_blank"
//                        href="https://wordpress.org/support/view/plugin-reviews/addthis#postform"> Yes, I will rate this plugin &#8594;
//                    </a>
//                </div>
//            </div>
//
//        </div>
//    ';
//************ FI


    return $html;
}

function _addthis_is_csr_form() {
    global $addThisConfigs;

    if (   isset($_GET['complete'], $_GET['pubid'])
        && $_GET['complete'] == 'true'
        && $_GET['pubid'] != $addThisConfigs->getProfileId()
    ) {
        return true;
    }

    return false;
}

function _addthis_settings_buttons($includePreview = true) {
    $html = '';
    if (_addthis_is_csr_form()) {
        return $html;
    }

    if($includePreview) {
        $stylesheet = get_option('stylesheet');
        $template = get_option('template');
        $previewLink = esc_url(get_option('home') . '/');
        if (is_ssl()) {
            $previewLink = str_replace('http://', 'https://', $previewLink);
        }
        $queryArgs = array(
            'preview' => 1,
            'template' => $template,
            'stylesheet' => $stylesheet,
            'preview_iframe' => true,
            'TB_iframe' => 'true'
        );
        $previewLink = htmlspecialchars(add_query_arg($queryArgs, $previewLink));
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
        $previewHtml = '
        <a
            href="'.$previewLink.'"
            class="Btn thickbox thickbox-preview" >
                '.__("Preview",'addthis_trans_domain').'
        </a>';
//************ ORIGINAL
//        $previewHtml = '
//        <a
//            href="'.$previewLink.'"
//            class="Btn thickbox thickbox-preview" >
//                Preview
//        </a>';
//************ FI
        $html .= $previewHtml;
    }
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
    $saveHtml = '
    <input
        type="submit"
        name="submit"
        value="'.__("Save Changes",'addthis_trans_domain').'"
        class="Btn Btn-blue addthis-submit-button"
    />';

//************ ORIGINAL
//    $previewHtml = '
//        <a
//            href="'.$previewLink.'"
//            class="Btn thickbox thickbox-preview" >
//                Preview
//        </a>';
//    $html .= $previewHtml;
//    $saveHtml = '
//    <input
//        type="submit"
//        name="submit"
//        value="Save Changes"
//        class="Btn Btn-blue addthis-submit-button"
//    />';
//************ FI

    $html .= $saveHtml;

    return $html;
}

/**
 * Get HTML for new users with confirmation
 *
 * @return string
 */
function addthis_profile_id_csr_confirmation()
{
    global $cmsConnector;
    global $addThisConfigs;
    if (isset($_GET['pubid'])) {
        $pubId = $_GET['pubid'];
    } else {
        $pubId = $addThisConfigs->getProfileId();
    }
//XTEC ************ MODIFICAT - Localization support
    $submitButtonValue = __("Confirm and Save Changes",'addthis_trans_domain');
//2015.09.18 @dgras
//************ ORIGINAL
//    $submitButtonValue = "Confirm and Save Changes";
//************ FI
    $fieldName = 'addthis_settings[addthis_profile]';

    $html  = '<div class="Card">';

    if (isset($_GET['advanced_settings'])) {
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
        $html  .= '
                <div  class="Card-bd">
                    <p>
                        '. __("Here you can manually set your AddThis Profile ID - you can get this from your",'addthis_trans_domain') .'
                        <a target="_blank" href="https://www.addthis.com/settings/publisher">'. __("Profile Settings",'addthis_trans_domain'). '</a>
                    </p>
            ';
    } else {
        $html  .= '
                <div class="Card-hd">
                    <h3 class="Card-hd-title"> '.__("You're almost done!",'addthis_trans_domain').'</h3>
                </div>
                <div  class="Card-bd">
                    <p> '. __("It's time to connect your AddThis account with Wordpress.",'addthis_trans_domain').'</p>';
    }

    $html .= '
        <form id="addthis-settings" method="post" action="'.$cmsConnector->getSettingsPageUrl().'">
            <div class="addthis_pub_id">
                <ul class="addthis-csr-confirm-list">
                    <li class="addthis-csr-item wp_div">
                        <img src="'.$cmsConnector->getPluginImageFolderUrl().'wordpress.png">
                        <span>'.__("Your WordPress Site",'addthis_trans_domain').':</span>
                        <input
                            type="text"
                            value="' . get_bloginfo('name') . '"
                            name="pub_id"
                            readonly=true
                            onfocus="this.blur()"/>
                    </li>
                    <li class="addthis-csr-item arrow_div">
                        <img src="'.$cmsConnector->getPluginImageFolderUrl().'arrow_right.png">
                        <img src="'.$cmsConnector->getPluginImageFolderUrl().'arrow_left.png">
                    </li>
                    <li class="addthis-csr-item addthis_div">
                        <img src="'.$cmsConnector->getPluginImageFolderUrl().'addthis.png">
                        <span>'. __("AddThis Profile ID",'addthis_trans_domain').':</span>
                        <input
                            type="text"
                            value="'.esc_html($pubId).'"
                            name="'.$fieldName.'"
                            id="addthis_profile" >
                        <input
                            type="hidden"
                            value="true"
                            name="addthis_settings[addthis_csr_confirmation]" >
                    </li>
                </ul>
                <ul class="addthis-csr-button-list">
                    <li class="addthis-csr-button-item">
                        <button
                            class="Btn Btn-cancel"
                            type="button"
                            onclick="window.location=\''.$cmsConnector->getSettingsPageUrl().'\';return false;">
                            '.__("Cancel",'addthis_trans_domain').'
                        </button>
                        ' . wp_nonce_field( 'update_pubid', 'pubid_nonce' ) . '
                    </li>
                    <li class="addthis-csr-button-item">
                        <input
                            type="submit"
                            value="'.$submitButtonValue.'"
                            name="submit"
                            class="Btn Btn-blue addthis-submit-button">
                    </li>
                </ul>
            </div>
        </form>
    </div>';

//************ ORIGINAL
//        $html  .= '
//                <div  class="Card-bd">
//                    <p>
//                        Here you can manually set your AddThis Profile ID - you can get this from your
//                        <a target="_blank" href="https://www.addthis.com/settings/publisher">Profile Settings</a>
//                    </p>
//            ';
//    } else {
//        $html  .= '
//                <div class="Card-hd">
//                    <h3 class="Card-hd-title">You\'re almost done!</h3>
//                </div>
//                <div  class="Card-bd">
//                    <p>It\'s time to connect your AddThis account with Wordpress.</p>
//                ';
//    }
//    $html .= '
//        <form id="addthis-settings" method="post" action="'.$cmsConnector->getSettingsPageUrl().'">
//            <div class="addthis_pub_id">
//                <ul class="addthis-csr-confirm-list">
//                    <li class="addthis-csr-item wp_div">
//                        <img src="'.$cmsConnector->getPluginImageFolderUrl().'wordpress.png">
//                        <span>Your WordPress Site:</span>
//                        <input
//                            type="text"
//                            value="' . get_bloginfo('name') . '"
//                            name="pub_id"
//                            readonly=true
//                            onfocus="this.blur()"/>
//                    </li>
//                    <li class="addthis-csr-item arrow_div">
//                        <img src="'.$cmsConnector->getPluginImageFolderUrl().'arrow_right.png">
//                        <img src="'.$cmsConnector->getPluginImageFolderUrl().'arrow_left.png">
//                    </li>
//                    <li class="addthis-csr-item addthis_div">
//                        <img src="'.$cmsConnector->getPluginImageFolderUrl().'addthis.png">
//                        <span>AddThis Profile ID:</span>
//                        <input
//                            type="text"
//                            value="'.esc_html($pubId).'"
//                            name="'.$fieldName.'"
//                            id="addthis_profile" >
//                        <input
//                            type="hidden"
//                            value="true"
//                            name="addthis_settings[addthis_csr_confirmation]" >
//                    </li>
//                </ul>
//                <ul class="addthis-csr-button-list">
//                    <li class="addthis-csr-button-item">
//                        <button
//                            class="Btn Btn-cancel"
//                            type="button"
//                            onclick="window.location=\''.$cmsConnector->getSettingsPageUrl().'\';return false;">
//                            Cancel
//                        </button>
//                        ' . wp_nonce_field( 'update_pubid', 'pubid_nonce' ) . '
//                    </li>
//                    <li class="addthis-csr-button-item">
//                        <input
//                            type="submit"
//                            value="'.$submitButtonValue.'"
//                            name="submit"
//                            class="Btn Btn-blue addthis-submit-button">
//                    </li>
//                </ul>
//            </div>
//        </form>
//    </div>';
//************ FI

    return $html;
}
