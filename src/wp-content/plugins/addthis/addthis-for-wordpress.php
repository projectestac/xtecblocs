<?php
/**
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2008-2012 Add This, LLC                                    |
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
 *
 * PHP version 5.3.6
 * 
 * @category Class
 * @package  Wordpress_Plugin
 * @author   The AddThis Team <srijith@addthis.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  SVN: 1.0
 * @link     http://www.addthis.com/blog
 */
$pathParts = pathinfo(__FILE__);

$path = $pathParts['dirname'];

if (!defined('ADDTHIS_PLUGIN_VERSION')) {
    define('ADDTHIS_PLUGIN_VERSION', '4.0.1');
}

if (!defined('ADDTHIS_ATVERSION')) {
    define('ADDTHIS_ATVERSION', '300');
}

define('ADDTHIS_CSS_PATH', 'css/style.css');
define('ADDTHIS_JS_PATH', 'js/addthis-for-wordpress.js');
define('ADDTHIS_SETTINGS_PAGE_ID', 'addthis_social_widget');
define('ADDTHIS_PLUGIN_FILE', $path.'/addthis_social_widget.php');
define('ADDTHIS_PUBNAME_LIMIT', 255);

/**
 * Class for Addthis wordpress
 *
 * @category Class
 * @package  Wordpress_Plugin
 * @author   The AddThis Team <srijith@addthis.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: 1.0
 * @link     http://www.addthis.com/blog
 */
class Addthis_Wordpress
{
    const ADDTHIS_PROFILE_SETTINGS_PAGE = 'https://www.addthis.com/settings/publisher';
    const ADDTHIS_SITE_URL = 'https://www.addthis.com/settings/plugin-pubs';
    const ADDTHIS_SITE_URL_WITH_PUB = 'https://www.addthis.com/dashboard#gallery';
    const ADDTHIS_REFERER  = 'www.addthis.com';
    
    /** PHP $_GET Variables * */
    private $_getVariables;

    /** PHP $_POST Variables * */
    private $_postVariables;

    /** check upgrade or fresh installation **/
    private $_upgrade;
    
    /** Addthis Profile id **/
    private $_pubid;

    /**
     * Initializes the plugin.
     *
     * @param boolean $upgrade check upgrade or fresh installation
     *
     * @return null
     * */
    public function __construct($upgrade)
    {
        $this->_upgrade = $upgrade;
        $this->_getVariables = $_GET;
        $this->_postVariables = $_POST;
        
        $this->_pubid = self::getPubid();

        include_once 'addthis-toolbox.php';
        new Addthis_ToolBox;
        
        add_action('admin_menu', array($this, 'addthisWordpressMenu'));

        // Deactivation
        register_deactivation_hook(
            ADDTHIS_PLUGIN_FILE,
            array($this, 'pluginDeactivation')
        );
        
        // Settings link in plugins page
        $plugin = 'addthis/addthis_social_widget.php';
        add_filter(
            "plugin_action_links_$plugin", 
            array($this, 'addSettingsLink')
        );
    }
    
    /*
     * Function to add settings link in plugins page
     * 
     * @return null
     */
    public function addSettingsLink($links)
    {
        $settingsLink = '<a href="'.self::getSettingsPageUrl().'">Settings</a>';
        array_push($links, $settingsLink);
        return $links; 
    }

    /**
     * Functions to execute on plugin deactivation
     * 
     * @return null
     */
    public function pluginDeactivation()
    {
        if (get_option('addthis_run_once')) {
            delete_option('addthis_run_once');
        }
    }

    /**
     * Adds sub menu page to the WP settings menu
     * 
     * @return null
     */
    public function addthisWordpressMenu()
    {
        add_options_page(
            'AddThis for Wordpress', 'AddThis for Wordpress',
            'manage_options', ADDTHIS_SETTINGS_PAGE_ID,
            array($this, 'addthisWordpressOptions')
        );
    }

    /**
     * Manages the WP settings page
     * 
     * @return null
     */
    public function addthisWordpressOptions()
    {
        if (!current_user_can('manage_options')) {
            wp_die(
                __('You do not have sufficient permissions to access this page.')
            );
        }

        $updateResult = null;

        if ($this->_checkAddPubid()) {
            $updateResult = $this->updatePubid($this->_postVariables['pubid']);
        }
        wp_enqueue_script(
            'addThisScript',
            plugins_url(ADDTHIS_JS_PATH, __FILE__)
        );
        wp_enqueue_style(
            'addThisStylesheet',
            plugins_url(ADDTHIS_CSS_PATH, __FILE__)
        );
        echo $this->_getHTML($updateResult);
    }

    /**
     *  Updates addthis profile id
     * 
     *  @param string $pubId Addthis public id
     * 
     *  @return string
     */
    public function updatePubid($pubId)
    {
        global $addthis_addjs;
        $addthis_addjs->setProfileId($pubId);
        $this->_pubid = $pubId;
        return "<div class='addthis_updated wrap'>".
                    "AddThis Profile ID updated successfully!!!".
               "</div>";
    }

    /**
     *  Get addthis profile id
     * 
     *  @return string
     */
    public static function getPubid()
    {
        $settings = get_option('addthis_settings');
        if (isset($settings) && isset($settings['profile'])) {
            return $settings['profile'];
        } else {
            return null;
        }
    }

    /**
     *  Get referer url
     * 
     *  @return string
     */
    private function _getReferelUrl()
    {
        $referer = ''; 
        if (isset($_SERVER['HTTP_REFERER'])) {
            $parse   = parse_url($_SERVER['HTTP_REFERER']);
            $referer = $parse['host'];
        }
        
//        return $referer;
        return self::ADDTHIS_REFERER;
    }

    /**
     *  Check if there is an addthis profile id return from addthis.com
     * 
     *  @return boolean
     */
    private function _checkPubidFromAddThis()
    {
        $referer = $this->_getReferelUrl();
        $successReturn = isset ($this->_getVariables['pubid']) &&
                         isset ($this->_getVariables['complete']) &&
                         $this->_getVariables['complete'] == 'true' &&
                         $referer == self::ADDTHIS_REFERER;

        return $successReturn;
    }

    /**
     *  Check if there is request to add addthis profile id
     *
     *  @return boolean
     */
    private function _checkAddPubid()
    {
        $successReturn = isset ($this->_postVariables['pubid'])
                         && isset ($this->_postVariables['submit']);

        return $successReturn;
    }

    /**
     *  Check pubid from addthis failure
     *
     *  @return boolean
     */
    private function _checkAddPubidFailure()
    {
        $referer = $this->_getReferelUrl();
        $successReturn = (isset ($this->_getVariables['complete']) &&
                         $this->_getVariables['complete'] != 'true') ||
                         (isset ($this->_getVariables['complete']) &&
                         $referer !== self::ADDTHIS_REFERER);

        return $successReturn;
    }

    /**
     * Get the HTML for addthis settings page
     * 
     * @param string $updateResult Updated message
     * 
     * @return string
     */
    private function _getHTML($updateResult)
    {
        $html = '<div class="addthis_wrap">'.
                '<p>'.
                    '<img class="header-img" '.
                    'src="//cache.addthis.com/icons/v1/thumbs/32x32/more.png" '.
                    'alt="Addthis">'.
                    '<span class="addthis-title">AddThis <sup>*</sup></span>'.
                    '<span class="addthis-name">for WordPress</span>'.
                '</p>';
        if ($this->_upgrade && !$this->_pubid) {
            $html .= $this->_getupdateSuccessMessage();
        }

        if ($this->_checkAddPubidFailure()) {
            $html .= $this->_getPubIdFromAddthisFailureMessage();
        }

        if ($updateResult) {
            $html .= $updateResult;
        }

        if ($this->_checkPubidFromAddThis()
            || (isset($this->_getVariables['advanced_settings'])
            && ($this->_getVariables['advanced_settings'] == 'true'))
        ) {
            // Get Confirmation form
            $html .= $this->_getConfirmationForm();
        } else {
            $html .= $this->_getAddThisLinkButton();
            $html .= "</div>";
        }

        return $html;
    }

    /**
     * Get pubid failure message
     *
     * @return <string>
     */
    private static function _getPubIdFromAddthisFailureMessage()
    {
        return "<div class='addthis_error wrap'>".
                        "Failed to add AddThis Profile ID".
                   "</div>";
    }

    /**
     * Get Update Success Message when updating from old plugin
     *
     * @return null
     */
    private function _getupdateSuccessMessage()
    {
        return "<div class='addthis_updated wrap'>".
                    "Click on the link below to finish setting up your AddThis tools.".
               "</div>";
    }

    /**
     * Get Link to addthis site
     *
     * @return string
     */
    private function _getAddThisLinkButton()
    {
        $html = '';
        if (!$this->_pubid) {
            $html  = "<h2>You're almost done!</h2>";
        }
        $html .= "<div class='addthis_description'>".
                 "Beautiful simple website tools designed to help you get ".
                 "likes, get shares, get follows and get discovered. </div>";
        
        if (!$this->_pubid) {
            // Get pub name
            $pubName = self::_getPubName();
            $html .= "<a class='addthis_button next' ".
                     "href='".self::ADDTHIS_SITE_URL.
                     "?cms=wp&pubname=".urlencode($pubName)."&wp_redirect=".
                     str_replace('.', '%2E', urlencode(self::getSettingsPageUrl())).
                     "'>Next</a>";
        } else {

            $html .= "<a class='addthis_button' target='_blank'".
                     "href='".self::ADDTHIS_SITE_URL."?cms=wp&pubid=".$this->_pubid.
                     "'>".
                     "To control your AddThis plugins, click here &#8594;".
                     "</a>";          
        }

       $html .="<p class='addthis_support'> If you don’t see your tools after configuring them in the dashboard, please contact ".
		"<a href='http://support.addthis.com/'>AddThis Support</a></p>";


        $html .= "<div class='addthis_seperator'>&nbsp;</div>";
        $html .= "<a href = '".
                  self::getSettingsPageUrl()."&advanced_settings=true'".
                  " class='addthis_reset_button'>Edit Profile Settings</a>";

        return $html;
    }

    /**
     * Get the pubname for addthis
     *
     * @return string
     */
    private static function _getPubName()
    {
        $pubName = get_bloginfo('name');

        if (!preg_match('/^[A-Za-z0-9 _\-\(\)]*$/', $pubName)) {
            // if title not match, get domain
            $domain  = self::getDomain();
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

        $pubName = substr($pubName, 0, ADDTHIS_PUBNAME_LIMIT);
        return $pubName;
    }

    /**
     * Get HTML for new users with confirmation
     *
     * @return string
     */
    private function _getConfirmationForm()
    {
        if (isset($this->_getVariables['advanced_settings'])) {
            $html  = "<div>";
            $html .= "Here you can manually set your AddThis Profile ID - ".
                      "you can get this from your ".
                      "<a target='_blank' ".
                        "href='".self::ADDTHIS_PROFILE_SETTINGS_PAGE."'>".
                      "Profile Settings</a>";
            $html .= "</div>";
        } else {
            $html  = "<h2>You're almost done!</h2>";
            $html .= "<div>".
                     "It's time to connect your AddThis account with Wordpress.".
                     "</div>";
        }
        $html .= '<form id="addthis-form" method="post" action="'.
                    self::getSettingsPageUrl().'">';
        $html .= "<div class='addthis_pub_id'>".
                  "<div class='icons wp_div'>".
                    "<img src='".plugins_url('images/wordpress.png', __FILE__).
                     "'>".
                     "<span>Your WordPress Site:</span>".
                     "<input type='text' value='" . get_bloginfo('name') . "'".
                     "name='pub_id' readonly=true onfocus='this.blur()'/>".
                  "</div>".
                  "<div class='icons arrow_div'>".
                    "<img src='".plugins_url('images/arrow_right.png', __FILE__).
                    "'>".
                    "<img src='".plugins_url('images/arrow_left.png', __FILE__).
                    "'>".
                  "</div>".
                  "<div class='icons addthis_div'>".
                    "<img src='".plugins_url('images/addthis.png', __FILE__).
                    "'>".
                    "<span>AddThis Profile ID:</span>";
        
        if (isset($this->_getVariables['pubid'])) {
            $pubId = $this->_getVariables['pubid'];
        } else {
            $pubId = $this->_pubid;
        }
        
        $html .=  "<input type='text' value='".$pubId."' ".
                      "name='pubid' id='addthis-pubid'/>";
        $html .=  "</div></div>";
        $submitButtonValue = "Confirm and Save";
        
        if (isset($this->_getVariables['advanced_settings'])) {
            $submitButtonValue = "Update";
        }
        
        $html .= '<input type="submit" value="'.$submitButtonValue.'"'.
                     ' name="submit" class="addthis_confirm_button">';
        $html .= '<button class="addthis_cancel_button" type="button"'
                . ' onclick="window.location=\''.self::getSettingsPageUrl()
                .'\';return false;">Cancel</button>';
        $html .= "</form>";

        return $html;
    }

    /**
     * Get the plugin's settings page url
     * 
     * @return string
     */
    public static function getSettingsPageUrl()
    {
        return admin_url("options-general.php?page=" . ADDTHIS_SETTINGS_PAGE_ID);
    }

    /**
     * Get the wp domain
     * 
     * @return string
     */
    public static function getDomain()
    {
        $url     = get_option('siteurl');
        $urlobj  = parse_url($url);
        $domain  = $urlobj['host'];
        return $domain;
    }

}

// Setup our shared resources early
// addthis_addjs.php is a standard class shared by the various AddThis plugins
// to make it easy for us to include our bootstrapping JavaScript only once.
// Priority should be lowest for Share plugin.
add_action('init', 'Addthis_Wordpress_early', 0);

/**
 * Include addthis js widget
 *
 * @global AddThis_addjs $addthis_addjs
 * @return null
 */
function Addthis_Wordpress_early()
{
    global $addthis_addjs;
    if (!isset($addthis_addjs)) {
        include 'includes/addthis_addjs_new.php';
        $addthis_options = get_option('addthis_settings');
        $addthis_addjs = new AddThis_addjs($addthis_options);
    }
}
