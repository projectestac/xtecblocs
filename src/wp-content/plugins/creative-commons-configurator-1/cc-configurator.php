<?php
/*
Plugin Name: Creative Commons Configurator
Plugin URI: http://www.g-loaded.eu/2006/01/14/creative-commons-configurator-wordpress-plugin/
Description: Adds a Creative Commons license to your blog pages and feeds. Also, provides some <em>Template Tags</em> for use in your theme templates.
Version: 1.4.0
Author: George Notaras
Author URI: http://www.g-loaded.eu/
License: Apache License v2
*/

/**
 *  Copyright 2008-2012 George Notaras <gnot@g-loaded.eu>, CodeTRAX.org
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */


/*
Creative Commons Icon Selection.
"0" : 88x31.png
"1" : somerights20.png
"2" : 80x15.png
*/
$default_button = "0";


/*
 * Translation Domain
 *
 * Translation files are searched in: wp-content/plugins
 */
load_plugin_textdomain('cc-configurator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');


/**
 * Settings Link in the ``Installed Plugins`` page
 */
function bccl_plugin_actions( $links, $file ) {
    // if( $file == 'creative-commons-configurator-1/cc-configurator.php' && function_exists( "admin_url" ) ) {
    if( $file == plugin_basename(__FILE__) && function_exists( "admin_url" ) ) {
        $settings_link = '<a href="' . admin_url( 'options-general.php?page=cc-configurator-options' ) . '">' . __('Settings') . '</a>';
        // Add the settings link before other links
        array_unshift( $links, $settings_link );
    }
    return $links;
}
add_filter( 'plugin_action_links', 'bccl_plugin_actions', 10, 2 );


function bccl_add_pages() {
    add_options_page(__('License Settings', 'cc-configurator'), __('License', 'cc-configurator'), 'manage_options', 'cc-configurator-options', 'bccl_license_options');
}
add_action('admin_menu', 'bccl_add_pages');


function bccl_show_info_msg($msg) {
    echo '<div id="message" class="updated fade"><p>' . $msg . '</p></div>';
}

function bccl_license_options () {
    // Permission Check
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    // Default CC-Configurator Settings
    $default_cc_settings = array(
        "license_url"   => "",
        "license_name"  => "",
        "license_button"=> "",
        "deed_url"      => "",
        "options"       => array(
            "cc_head"       => "0",
            "cc_feed"       => "0",
            "cc_body"       => "0",
            "cc_body_pages" => "0",
            "cc_body_attachments"   => "0",
            "cc_body_img"   => "0",
            "cc_extended"   => "0",
            "cc_creator"    => "blogname",
            "cc_perm_url"   => "",
            "cc_color"      => "#000000",
            "cc_bgcolor"    => "#eef6e6",
            "cc_brdr_color" => "#cccccc",
            "cc_no_style"   => "0",
            "cc_i_have_donated" => "0",
        )
    );

    /*
    It is checked if a specific form (options update, reset license) has been
    submitted or if a new license is available in a GET request.
    
    Then, it is determined which page should be displayed to the user by
    checking whether the license_url exists in the cc_settings or not.
    license_url is a mandatory attribute of the CC license.
    */
    if (isset($_POST["options_update"])) {
        /*
         * Updates the CC License options only.
         * It will never enter here if a license has not been set, so it is
         * taken for granted that "cc_settings" exist in the database.
         */
        $cc_settings = get_option("cc_settings");
        $cc_settings["options"] = array(
            "cc_head"       => $_POST["cc_head"],
            "cc_feed"       => $_POST["cc_feed"],
            "cc_body"       => $_POST["cc_body"],
            "cc_body_pages" => $_POST["cc_body_pages"],
            "cc_body_attachments" => $_POST["cc_body_attachments"],
            "cc_body_img"   => $_POST["cc_body_img"],
            "cc_extended"   => $_POST["cc_extended"],
            "cc_creator"    => $_POST["cc_creator"],
            "cc_perm_url"   => $_POST["cc_perm_url"],
            "cc_color"      => $_POST["cc_color"],
            "cc_bgcolor"    => $_POST["cc_bgcolor"],
            "cc_brdr_color" => $_POST["cc_brdr_color"],
            "cc_no_style"   => $_POST["cc_no_style"],
            "cc_i_have_donated" => $_POST["cc_i_have_donated"],
            );
        
        update_option("cc_settings", $cc_settings);
        bccl_show_info_msg(__('Creative Commons license options saved.', 'cc-configurator'));

    } elseif (isset($_POST["license_reset"])) {
        /*
         * Reset all options to the defaults.
         */
        delete_option("cc_settings");
        update_option("cc_settings", $default_cc_settings);
        bccl_show_info_msg(__("Creative Commons license options deleted from the WordPress database.", 'cc-configurator'));

    } elseif (isset($_GET["new_license"])) {
        /*
         * Saves the new license settings to database.
         * The ``new_license`` query argument must exist in the GET request.
         *
         * Also, saves the default colors to the options.
         */
        $cc_settings = $default_cc_settings;
        // Replace the base CC license settings
        $cc_settings["license_url"] = htmlspecialchars(rawurldecode($_GET["license_url"]));
        $cc_settings["license_name"] = htmlspecialchars(rawurldecode($_GET["license_name"]));
        $cc_settings["license_button"] = htmlspecialchars(rawurldecode($_GET["license_button"]));
        $cc_settings["deed_url"] = htmlspecialchars(rawurldecode($_GET["deed_url"]));
        
        update_option("cc_settings", $cc_settings);
        bccl_show_info_msg(__('Creative Commons license saved.', 'cc-configurator'));

    } elseif (!get_option("cc_settings")) {

        // CC-Configurator settings do not exist in the database.
        // This is the first run, so set our defaults.
        update_option("cc_settings", $default_cc_settings);
    }
    
    /*
    Decide if the license selection frame will be shown or the license options page.
    */
    $cc_settings = get_option("cc_settings");

    //var_dump($cc_settings);

    if (empty($cc_settings["license_url"])) {
        bccl_select_license();
    } else {
        bccl_set_license_options($cc_settings);
    }

}


function bccl_select_license() {
    /*
     * License selection using the partner interface.
     * http://wiki.creativecommons.org/Partner_Interface
     */

    $cc_partner_interface_url = "http://creativecommons.org/license/";
    $partner = "WordPress/CC-Configurator Plugin";
    $partner_icon_url = get_bloginfo("url") . "/wp-admin/images/wordpress-logo.png";
    $jurisdiction_choose = "1";
    $lang = get_bloginfo('language');
    $exit_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "&license_url=[license_url]&license_name=[license_name]&license_button=[license_button]&deed_url=[deed_url]&new_license=1";

    // Not currently used. Could be utilized to present the partner interace in an iframe.
    $Partner_Interface_URI = htmlspecialchars("http://creativecommons.org/license/?partner=$partner&partner_icon_url=$partner_icon_url&jurisdiction_choose=$jurisdiction_choose&lang=$lang&exit_url=$exit_url");

    print('
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>
        <h2>'.__('License Settings', 'cc-configurator').'</h2>
        <p>'.__('Welcome to the administration panel of the Creative-Commons-Configurator plugin for WordPress.', 'cc-configurator').'</p>

        <h2>'.__('Select License', 'cc-configurator').'</h2>
        <p>'.__('A license has not been set for your content. By pressing the following link you will be taken to the license selection wizard hosted by the Creative Commons Corporation. Once you have completed the license selection process, you will be redirected back to this page.', 'cc-configurator').'</p>

        <form name="formnewlicense" id="bccl-new-license-form" method="get" action="' . $cc_partner_interface_url . '">
            <input type="hidden" name="partner" value="'.$partner.'" />
            <input type="hidden" name="partner_icon_url" value="'.$partner_icon_url.'" />
            <input type="hidden" name="jurisdiction_choose" value="'.$jurisdiction_choose.'" />
            <input type="hidden" name="lang" value="'.$lang.'" />
            <input type="hidden" name="exit_url" value="'.$exit_url.'" />
            <p class="submit">
                <input id="submit" class="button-primary" type="submit" value="'.__('New License', 'cc-configurator').'" name="new-license-button" />
            </p>
        </form>

    </div>');
}


function bccl_set_license_options($cc_settings) {
    /*
    CC License Options
    */
    global $wp_version;

    print('
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>
        <h2>'.__('License Settings', 'cc-configurator').'</h2>

        <p style="text-align: center;"><big>' . bccl_get_full_html_license() . '</big></p>
        <form name="formlicense" id="bccl_reset" method="post" action="' . $_SERVER['REQUEST_URI'] . '">
            <fieldset>
                <legend class="screen-reader-text"><span>'.__('Current License', 'cc-configurator').'</span></legend>
                <p>'.__('A license has been set and will be used to license your work.', 'cc-configurator').'</p>
                <p>'.__('If you need to set a different license, press the <em>Reset License</em> button below. By reseting the license, the saved plugin options are removed from the WordPress database.', 'cc-configurator').'</p>
            </fieldset>
            <p class="submit">
                <input type="submit" class="button-primary" name="license_reset" value="'.__('Reset License', 'cc-configurator').'" />
            </p>
        </form>
    </div>

    <div class="wrap" style="background: #EEF6E6; padding: 1em 2em; border: 1px solid #E4E4E4;' . (($cc_settings["options"]["cc_i_have_donated"]=="1") ? ' display: none;' : '') . '">
        <h2>'.__('Message from the author', 'cc-configurator').'</h2>
        <p style="font-size: 1.2em; padding-left: 2em;">'.__('<em>CC-Configurator</em> is released under the terms of the <a href="http://www.apache.org/licenses/LICENSE-2.0.html">Apache License version 2</a> and, therefore, is <strong>free software</strong>.', 'cc-configurator').'</p>
        <p style="font-size: 1.2em; padding-left: 2em;">'.__('However, a significant amount of <strong>time</strong> and <strong>energy</strong> has been put into developing this plugin, so, its production has not been free from cost. If you find this plugin useful, I would appreciate an <a href="http://www.g-loaded.eu/about/donate/">extra cup of coffee</a>.', 'cc-configurator').'</p>
        <p style="font-size: 1.2em; padding-left: 2em;">'.__('Thank you in advance', 'cc-configurator').'</p>
        <div style="text-align: right;"><small>'.__('This message can de deactivated in the settings below.', 'cc-configurator').'</small></div>
    </div>

    <div class="wrap">
        <h2>'.__('Configuration', 'cc-configurator').'</h2>
        <p>'.__('Here you can choose where and how license information should be added to your blog.', 'cc-configurator').'</p>

        <form name="formbccl" method="post" action="' . $_SERVER['REQUEST_URI'] . '">

        <table class="form-table">
        <tbody>

            <tr valign="top">
            <th scope="row">'.__('Syndicated Content', 'cc-configurator').'</th>
            <td>
            <fieldset>
                <legend class="screen-reader-text"><span>'.__('Syndicated Content', 'cc-configurator').'</span></legend>
                <input id="cc_feed" type="checkbox" value="1" name="cc_feed" '. (($cc_settings["options"]["cc_feed"]=="1") ? 'checked="checked"' : '') .'" />
                <label for="cc_feed">
                '.__('Include license information in the blog feeds. (<em>Recommended</em>)', 'cc-configurator').'
                </label>
                <br />
            </fieldset>
            </td>
            </tr>

            <tr valign="top">
            <th scope="row">'.__('Page Head HTML', 'cc-configurator').'</th>
            <td>
            <fieldset>
                <legend class="screen-reader-text"><span>'.__('Page Head HTML', 'cc-configurator').'</span></legend>
                <input id="cc_head" type="checkbox" value="1" name="cc_head" '. (($cc_settings["options"]["cc_head"]=="1") ? 'checked="checked"' : '') .'" />
                <label for="cc_head">
                '.__('Include license information in the page\'s HTML head. This will not be visible to human visitors, but search engine bots will be able to read it. Note that the insertion of license information in the HTML head is done in relation to the content types (posts, pages or attachment pages) on which the license text block is displayed (see the <em>text block</em> settings below). (<em>Recommended</em>)', 'cc-configurator').'
                </label>
                <br />
            </fieldset>
            </td>
            </tr>

            <tr valign="top">
            <th scope="row">'.__('Text Block', 'cc-configurator').'</th>
            <td>
            <fieldset>
                <legend class="screen-reader-text"><span>'.__('Text Block', 'cc-configurator').'</span></legend>

                <p>'.__('By enabling the following options, a small block of text, which contains links to the author, the work and the used license, is appended to the published content.', 'cc-configurator').'</p>

                <input id="cc_body" type="checkbox" value="1" name="cc_body" '. (($cc_settings["options"]["cc_body"]=="1") ? 'checked="checked"' : '') .'" />
                <label for="cc_body">
                '.__('Posts: Add the text block with license information under the published posts. (<em>Recommended</em>)', 'cc-configurator').'
                </label>
                <br />

                <input id="cc_body_pages" type="checkbox" value="1" name="cc_body_pages" '. (($cc_settings["options"]["cc_body_pages"]=="1") ? 'checked="checked"' : '') .'" />
                <label for="cc_body_pages">
                '.__('Pages: Add the text block with license information under the published pages.', 'cc-configurator').'
                </label>
                <br />

                <input id="cc_body_attachments" type="checkbox" value="1" name="cc_body_attachments" '. (($cc_settings["options"]["cc_body_attachments"]=="1") ? 'checked="checked"' : '') .'" />
                <label for="cc_body_attachments">
                '.__('Attachments: Add the text block with license information under the attached content in attachment pages.', 'cc-configurator').'
                </label>
                <br />

                <p>'.__('By enabling the following option, the license image is also included in the license text block.', 'cc-configurator').'</p>

                <input id="cc_body_img" type="checkbox" value="1" name="cc_body_img" '. (($cc_settings["options"]["cc_body_img"]=="1") ? 'checked="checked"' : '') .'" />
                <label for="cc_body_img">
                '.__('Include the license image in the text block.', 'cc-configurator').'
                </label>
                <br />
            </fieldset>
            </td>
            </tr>

            <tr valign="top">
            <th scope="row">'.__('Extra Text Block Customization', 'cc-configurator').'</th>
            <td>
            <p>'.__('The following settings have an effect only if the text block containing licensing information has been enabled above.', 'cc-configurator').'</p>
            <fieldset>
                <legend class="screen-reader-text"><span>'.__('Extra Text Block Customization', 'cc-configurator').'</span></legend>

                <input id="cc_extended" type="checkbox" value="1" name="cc_extended" '. (($cc_settings["options"]["cc_extended"]=="1") ? 'checked="checked"' : '') .'" />
                <label for="cc_extended">
                '.__('Include extended information about the published work and its creator. By enabling this option, hyperlinks to the published content and its creator/publisher are also included into the license statement inside the block. This, by being an attribution example itself, will generally help others to attribute the work to you.', 'cc-configurator').'
                </label>
                <br />
                <br />

                <select name="cc_creator" id="cc_creator">');
                $creator_arr = bccl_get_creator_pool();
                foreach ($creator_arr as $internal => $creator) {
                    if ($cc_settings["options"]["cc_creator"] == $internal) {
                        $selected = ' selected="selected"';
                    } else {
                        $selected = '';
                    }
                    printf('<option value="%s"%s>%s</option>', $internal, $selected, $creator);
                }
                print('</select>
                <br />
                <label for="cc_creator">
                '.__('If extended information about the published work has been enabled, then you can choose which name will indicate the creator of the work. By default, the blog name is used.', 'cc-configurator').'
                </label>
                <br />
                <br />

                <input name="cc_perm_url" type="text" id="cc_perm_url" class="code" value="' . $cc_settings["options"]["cc_perm_url"] . '" size="100" maxlength="1024" />
                <br />
                <label for="cc_perm_url">
                '.__('If you have added any extra permissions to your license, provide the URL to the webpage that contains them. It is highly recommended to use absolute URLs.', 'cc-configurator').'
                <br />
                <strong>'.__('Example', 'cc-configurator').'</strong>: <code>http://www.example.org/ExtendedPermissions</code>
                </label>
                <br />

            </fieldset>
            </td>
            </tr>

            
            <tr valign="top">
            <th scope="row">'.__('Colors of the text block', 'cc-configurator').'</th>
            <td>
            <p>'.__('The following settings have an effect only if the text block containing licensing information has been enabled above.', 'cc-configurator').'</p>
            <fieldset>
                <legend class="screen-reader-text"><span>'.__('Colors of the text block', 'cc-configurator').'</span></legend>

                <input name="cc_color" type="text" id="cc_color" class="code" value="' . $cc_settings["options"]["cc_color"] . '" size="7" maxlength="7" />
                <label for="cc_color">
                '.__('Set a color for the text that appears within the block (does not affect hyperlinks).', 'cc-configurator').'
                <br />
                <strong>'.__('Default', 'cc-configurator').'</strong>: <code>#000000</code>
                </label>
                <br />
                <br />

                <input name="cc_bgcolor" type="text" id="cc_bgcolor" class="code" value="' . $cc_settings["options"]["cc_bgcolor"] . '" size="7" maxlength="7" />
                <label for="cc_bgcolor">
                '.__('Set a background color for the block.', 'cc-configurator').'
                <br />
                <strong>'.__('Default', 'cc-configurator').'</strong>: <code>#eef6e6</code>
                </label>
                <br />
                <br />

                <input name="cc_brdr_color" type="text" id="cc_brdr_color" class="code" value="' . $cc_settings["options"]["cc_brdr_color"] . '" size="7" maxlength="7" />
                <label for="cc_brdr_color">
                '.__('Set a color for the border of the block.', 'cc-configurator').'
                <br />
                <strong>'.__('Default', 'cc-configurator').'</strong>: <code>#cccccc</code>
                </label>
                <br />
                <br />

                <input id="cc_no_style" type="checkbox" value="1" name="cc_no_style" '. (($cc_settings["options"]["cc_no_style"]=="1") ? 'checked="checked"' : '') .'" />
                <label for="cc_no_style">
                '.__('Disable the internal formatting of the license block. If the internal formatting is disabled, then the color selections above have no effect any more. You can still format the license block via your own CSS. The <em>cc-block</em> and <em>cc-button</em> classes have been reserved for formatting the license block and the license button respectively.', 'cc-configurator').'
                </label>
                <br />

            </fieldset>
            </td>
            </tr>

            <tr valign="top">
            <th scope="row">'.__('Donations', 'cc-configurator').'</th>
            <td>
            <fieldset>
                <legend class="screen-reader-text"><span>'.__('Donations', 'cc-configurator').'</span></legend>
                <input id="cc_i_have_donated" type="checkbox" value="1" name="cc_i_have_donated" '. (($cc_settings["options"]["cc_i_have_donated"]=="1") ? 'checked="checked"' : '') .'" />
                <label for="cc_i_have_donated">
                '.__('By checking this, the <em>message from the author</em> above goes away. Thanks for <a href="http://www.g-loaded.eu/about/donate/">donating</a>!', 'cc-configurator').'
                </label>
                <br />

            </fieldset>
            </td>
            </tr>


        </tbody>
        </table>

        <p class="submit">
            <input id="submit" class="button-primary" type="submit" value="'.__('Save Changes', 'cc-configurator').'" name="options_update" />
        </p>

        </form>

    </div>

    <div class="wrap">

        <h2>'.__('Advanced Info', 'cc-configurator').'</h2>
        <p>'.__('Apart from the options above for the inclusion of licensing information in your blog, this plugin provides some <em>Template Tags</em>, which can be used in your theme templates. These are the following:', 'cc-configurator').'
        </p>
        
        <table class="form-table">
        <tbody>

            <tr valign="top">
            <th scope="row">'.__('Text Hyperlink', 'cc-configurator').'</th>
            <td>
                <code>bccl_get_license_text_hyperlink()</code> - '.__('Returns the text hyperlink of your current license for use in the PHP code.', 'cc-configurator').'
                <br />
                <code>bccl_license_text_hyperlink()</code> - '.__('Displays the text hyperlink.', 'cc-configurator').'
            </td>
            </tr>

            <tr valign="top">
            <th scope="row">'.__('Image Hyperlink', 'cc-configurator').'</th>
            <td>
                <code>bccl_get_license_image_hyperlink()</code> - '.__('Returns the image hyperlink of the current license.', 'cc-configurator').'
                <br />
                <code>bccl_license_image_hyperlink()</code> - '.__('Displays the image hyperlink of the current license.', 'cc-configurator').'
            </td>
            </tr>

            <tr valign="top">
            <th scope="row">'.__('License URIs', 'cc-configurator').'</th>
            <td>
                <code>bccl_get_license_url()</code> - '.__('Returns the license\'s URL.', 'cc-configurator').'
                <br />
                <code>bccl_get_license_deed_url()</code> - '.__('Returns the license\'s Deed URL. Usually this is the same URI as returned by the bccl_get_license_url() function.', 'cc-configurator').'
            </td>
            </tr>

            <tr valign="top">
            <th scope="row">'.__('Full HTML Code', 'cc-configurator').'</th>
            <td>
                <code>bccl_get_full_html_license()</code> - '.__('Returns the full HTML code of the license. This includes the text and the image hyperlinks.', 'cc-configurator').'
                <br />
                <code>bccl_full_html_license()</code> - '.__('Displays the full HTML code of the license. This includes the text and the image hyperlinks.', 'cc-configurator').'
            </td>
            </tr>

            <tr valign="top">
            <th scope="row">'.__('Complete License Block', 'cc-configurator').'</th>
            <td>
                <code>bccl_license_block($work, $css_class, $show_button)</code> - '.__('Displays a complete license block. This template tag can be used to publish specific original work under the current license or in order to display the license block at custom locations on your website. This function supports the following arguments', 'cc-configurator').':
                <ol>
                    <li><code>$work</code> ('.__('alphanumeric', 'cc-configurator').') : '.__('This argument is used to define the work to be licensed. Its use is optional, when the template tag is used in single-post view. If not defined, the user-defined settings for the default license block are used.', 'cc-configurator').'</li>
                    <li><code>$css_class</code> ('.__('alphanumeric', 'cc-configurator').') : '.__('This argument sets the name of the CSS class that will be used to format the license block. It is optional. If not defined, then the default class <em>cc-block</em> is used.', 'cc-configurator').'</li>
                    <li><code>$show_button</code> ('.__('alphanumeric', 'cc-configurator').') - ("default", "yes", "no") : '.__('This argument is optional. It can be used in order to control the appearance of the license icon.', 'cc-configurator').'</li>
                </ol>
            </td>
            </tr>

            <tr valign="top">
            <th scope="row">'.__('Licence Documents', 'cc-configurator').'</th>
            <td>
                <code>bccl_license_summary($width, $height, $css_class)</code> - '.__('Displays the license\'s summary document in an <em>iframe</em>.', 'cc-configurator').'
                <br />
                <code>bccl_license_legalcode($width, $height, $css_class)</code> - '.__('Displays the license\'s full legal code in an <em>iframe</em>.', 'cc-configurator').'
            </td>
            </tr>

        </tbody>
        </table>

    </div>

    ');
}


function bccl_add_placeholders($data, $what = "html") {
    if (!(trim($data))) { return ""; }
    if ($what = "html") {
        return sprintf("\n<!-- Creative Commons License -->\n%s\n<!-- /Creative Commons License -->\n", trim($data));
    } else {
        return sprintf("\n<!--\n%s\n-->\n", trim($data));
    }
}


function bccl_get_license_text_hyperlink() {
    /*
    Returns Full TEXT hyperlink to License <a href=...>...</a>
    */
    $cc_settings = get_option("cc_settings");
    if (!$cc_settings) { return ""; }
    $license_url = $cc_settings["license_url"];
    $license_name = $cc_settings["license_name"];
    
    $text_link_format = '<a rel="license" href="%s">%s %s %s</a>';
    return sprintf($text_link_format, $license_url, __('Creative Commons', 'cc-configurator'), trim($license_name), __('License', 'cc-configurator'));
}


function bccl_license_text_hyperlink() {
    /*
    Displays Full TEXT hyperlink to License <a href=...>...</a>
    */
    echo bccl_add_placeholders(bccl_get_license_text_hyperlink());
}


function bccl_get_license_image_hyperlink($button = "default") {
    /*
    Returns Full IMAGE hyperlink to License <a href=...><img.../></a>
    
    Creative Commons Icon Selection
    "0" : 88x31.png
    "1" : http://creativecommons.org/images/public/somerights20.png
    "2" : 80x15.png

    CSS customization via "cc-button" class.
    */
    
    global $default_button;
    
    $cc_settings = get_option("cc_settings");
    if (!$cc_settings) { return ""; }
    $license_url = $cc_settings["license_url"];
    $license_name = $cc_settings["license_name"];
    $license_button = $cc_settings["license_button"];
    
    // Available buttons
    $buttons = array(
        "0" => dirname($license_button) . "/88x31.png",
        "1" => "http://creativecommons.org/images/public/somerights20.png",
        "2" => dirname($license_button) . "/80x15.png"
        );
    
    // Modify button
    if ($button == "default") {
        if (array_key_exists($default_button, $buttons)) {
            $license_button = $buttons[$default_button];
        }
    } elseif (array_key_exists($button, $buttons)){
        $license_button = $buttons[$button];
    }
    
    // Finally check whether the WordPress site is served over the HTTPS protocol
    // so as to use https in the image source. Creative Commons makes license
    // images available over HTTPS as well.
    if (is_ssl()) {
        $license_button = str_replace('http://', 'https://', $license_button);
    }

    $image_link_format = "<a rel=\"license\" href=\"%s\"><img alt=\"%s\" src=\"%s\" class=\"cc-button\" /></a>";
    return sprintf($image_link_format, $license_url, __('Creative Commons License', 'cc-configurator'), $license_button);

}


function bccl_license_image_hyperlink($button = "default") {
    /*
    Displays Full IMAGE hyperlink to License <a href=...><img...</a>
    */
    echo bccl_add_placeholders(bccl_get_license_image_hyperlink($button));
}


function bccl_get_license_url() {
    /*
    Returns only the license URL.
    */
    $cc_settings = get_option("cc_settings");
    if (!$cc_settings) { return ""; }
    return $cc_settings["license_url"];
}

function bccl_get_license_deed_url() {
    /*
    Returns only the license deed URL.
    */
    $cc_settings = get_option("cc_settings");
    if (!$cc_settings) { return ""; }
    return $cc_settings["deed_url"];
}


function bccl_license_summary($width = "100%", $height = "600px", $css_class= "cc-frame") {
    /*
    Displays the licence summary page from creative commons in an iframe
    
    */
    printf('
        <iframe src="%s" frameborder="0" width="%s" height="%s" class="%s"></iframe>
        ', bccl_get_license_url(), $width, $height, $css_class);
}


function bccl_license_legalcode($width = "100%", $height = "600px", $css_class= "cc-frame") {
    /*
    Displays the licence summary page from creative commons in an iframe
    */
    printf('
        <iframe src="%slegalcode" frameborder="0" width="%s" height="%s" class="%s"></iframe>
        ', bccl_get_license_url(), $width, $height, $css_class);
}


function bccl_get_full_html_license($button = "default") {
    /*
    Returns the full HTML code of the license
    */    
    return bccl_get_license_image_hyperlink($button) . "<br />" . bccl_get_license_text_hyperlink();
}


function bccl_full_html_license($button = "default") {
    /*
    Displays the full HTML code of the license
    */    
    echo bccl_add_placeholders(bccl_get_full_html_license($button));
}


function bccl_get_license_block($work = "", $css_class = "", $show_button = "default", $button = "default") {
    /*
    This function should not be used in template tags.
    
    $work: The work that is licensed can be defined by the user.
    
    $show_button: (default, yes, no) - no explanation (TODO possibly define icon URL)
    
    $button: The user can se the desired button (hidden feature): "0", "1", "2"
    
    */
    $cc_block = "LICENSE BLOCK ERROR";
    $cc_settings = get_option("cc_settings");
    if (!$cc_settings) { return ""; }
    
    // Set CSS class
    if (empty($css_class)) {
        $css_class = "cc-block";
    }
    
    // License button inclusion
    if ($show_button == "default") {
        if ($cc_settings["options"]["cc_body_img"]) {
            $button_code = bccl_get_license_image_hyperlink($button) . "<br />";
        }
    } elseif ($show_button == "yes") {
        $button_code = bccl_get_license_image_hyperlink($button) . "<br />";
    } elseif ($show_button == "no") {
        $button_code = "";
    } else {
        $button_code = "ERROR";
    }
    
    // Work analysis
    if ( empty($work) ) {
        // Proceed only if the user has not defined the work.
        if ( $cc_settings["options"]["cc_extended"] ) {
            $creator = bccl_get_the_creator($cc_settings["options"]["cc_creator"]);
            $work = "<em><a href=\"" . get_permalink() . "\">" . get_the_title() . "</a></em>";
            $by = "<em><a href=\"" . get_bloginfo("url") . "\">" . $creator . "</a></em>";
            $work = sprintf("%s %s %s %s", __("The", 'cc-configurator'), $work, __("by", 'cc-configurator'), $by);
        } else {
            $work = __('This work', 'cc-configurator');
        }
    }
    $work .= sprintf(", ".__('unless otherwise expressly stated', 'cc-configurator').", ".__('is licensed under a', 'cc-configurator')." %s.", bccl_get_license_text_hyperlink());
    
    // Additional Permissions
    if ( $cc_settings["options"]["cc_perm_url"] ) {
        $additional_perms = " ".__('Terms and conditions beyond the scope of this license may be available at', 'cc-configurator')." <a href=\"" . $cc_settings["options"]["cc_perm_url"] . "\">" . $_SERVER["HTTP_HOST"] . "</a>.";
    } else {
        $additional_perms = "";
    }
    
    // $cc_block = sprintf("<div class=\"%s\">%s%s%s</div>", $css_class, $button_code, $work, $additional_perms);
    $cc_block = sprintf("<p class=\"%s\">%s%s%s</p>", $css_class, $button_code, $work, $additional_perms);
    return $cc_block;
}


function bccl_license_block($work = "", $css_class = "", $show_button = "default", $button = "default") {
    /*
    $work: The work that is licensed can be defined by the user.
    $css_class : The user can define the CSS class that will be used to
    $show_button: (default, yes, no)
    format the license block. (if empty, the default cc-block is used)
    */
    echo bccl_add_placeholders(bccl_get_license_block($work, $css_class, $show_button, $button));
}




function bccl_get_creator_pool() {
    $creator_arr = array(
        "blogname"    => __('Blog Name', 'cc-configurator'),
        "firstlast"    => __('First + Last Name', 'cc-configurator'),
        "lastfirst"    => __('Last + First Name', 'cc-configurator'),
        "nickname"    => __('Nickname', 'cc-configurator'),
        "displayedname"    => __('Displayed Name', 'cc-configurator'),
        );
    return $creator_arr;
}


function bccl_get_the_creator($who) {
    /*
    Return the creator/publisher of the licensed work according to the user-defined option (cc-creator)
    */
    if ($who == "blogname") {
        return get_bloginfo("name");
    } elseif ($who == "firstlast") {
        return get_the_author_firstname() . " " . get_the_author_lastname();
    } elseif ($who == "lastfirst") {
        return get_the_author_lastname() . " " . get_the_author_firstname();
    } elseif ($who == "nickname") {
        return get_the_author_nickname();
    } elseif ($who == "displayedname") {
        return get_the_author();
    } else {
        return "ERROR";
    }
}



// Action

function bccl_add_to_header() {
    /*
    Adds a link element with "license" relation in the web page HEAD area.
    
    Also, adds style for the license block, only if the user has:
     * enabled the display of such a block
     * not disabled internal license block styling
     * if it is single-post view
    */
    $cc_settings = get_option("cc_settings");
    if ( !is_singular() ) {
        return "";
    } elseif ( is_attachment() && ($cc_settings["options"]["cc_body_attachments"] != "1") ) {
        return "";
    } elseif ( is_single() && ($cc_settings["options"]["cc_body"] != "1") ) {
        return "";
    } elseif ( is_page() && ($cc_settings["options"]["cc_body_pages"] != "1") ) {
        return "";
    }
    
    if ( !empty($cc_settings["license_url"]) && $cc_settings["options"]["cc_head"] == "1" ) {
        echo "\n<!-- Creative Commons License added by Creative-Commons-Configurator plugin for WordPress\nGet the plugin at: http://www.g-loaded.eu/2006/01/14/creative-commons-configurator-wordpress-plugin/ -->\n";
        // Adds a link element with "license" relation in the web page HEAD area.
        echo "<link rel=\"license\" type=\"text/html\" href=\"" . bccl_get_license_url() . "\" />\n\n";
    }
    if ( $cc_settings["options"]["cc_no_style"] != "1" ) {
        // Adds style for the license block
        $color = $cc_settings["options"]["cc_color"];
        $bgcolor = $cc_settings["options"]["cc_bgcolor"];
        $brdrcolor = $cc_settings["options"]["cc_brdr_color"];
        $bccl_default_block_style = "width: 90%; margin: 8px auto; padding: 4px; text-align: center; border: 1px solid $brdrcolor; color: $color; background-color: $bgcolor;";
        $style = "<style type=\"text/css\"><!--\n.cc-block { $bccl_default_block_style }\n--></style>\n\n";
        echo $style;
    }
}


function bccl_add_cc_ns_feed() {
    /*
    Adds the CC RSS module namespace declaration.
    */
    $cc_settings = get_option("cc_settings");
    if (!$cc_settings) { return ""; }
    if ( $cc_settings["options"]["cc_feed"] == "1" ) {
        echo "xmlns:creativeCommons=\"http://backend.userland.com/creativeCommonsRssModule\"\n";
    }
}

function bccl_add_cc_element_feed() {
    /*
    Adds the CC URL to the feeds.
    */
    $cc_settings = get_option("cc_settings");
    if (!$cc_settings) { return ""; }
    if ( $cc_settings["license_url"] && $cc_settings["options"]["cc_feed"] == "1" ) {
        echo "<creativeCommons:license>" . bccl_get_license_url() . "</creativeCommons:license>\n";
    }
}


function bccl_append_to_post_body($PostBody) {
    /*
    Adds the license block under the published content.
    
    The check if the user has chosen to display a block under the published
    content is performed in bccl_get_license_block(), in order not to retrieve
    the saved settings two timesor pass them between functions.
    */
    $cc_settings = get_option("cc_settings");
    if ( !is_singular() ) { // Possibly not necessary
        return $PostBody;
    } elseif ( is_attachment() && ($cc_settings["options"]["cc_body_attachments"] != "1") ) {
        return $PostBody;
    } elseif ( is_single() && ($cc_settings["options"]["cc_body"] != "1") ) {
        return $PostBody;
    } elseif ( is_page() && ($cc_settings["options"]["cc_body_pages"] != "1") ) {
        return $PostBody;
    }
    // Append the license block to the content
    $cc_block = bccl_get_license_block("", "", "default", "default");
    if ( $cc_block ) {
        $PostBody .= bccl_add_placeholders($cc_block);
    }
    return $PostBody;
}

// ACTION

add_action('wp_head', 'bccl_add_to_header', 10);

add_filter('the_content', 'bccl_append_to_post_body', 250);

add_action('rdf_ns', 'bccl_add_cc_ns_feed');
add_action('rdf_header', 'bccl_add_cc_element_feed');
add_action('rdf_item', 'bccl_add_cc_element_feed');

add_action('rss2_ns', 'bccl_add_cc_ns_feed');
add_action('rss2_head', 'bccl_add_cc_element_feed');
add_action('rss2_item', 'bccl_add_cc_element_feed');

add_action('atom_ns', 'bccl_add_cc_ns_feed');
add_action('atom_head', 'bccl_add_cc_element_feed');
add_action('atom_entry', 'bccl_add_cc_element_feed');

?>