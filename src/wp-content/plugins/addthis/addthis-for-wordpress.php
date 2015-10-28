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

$pathParts = pathinfo(__FILE__);

$path = $pathParts['dirname'];

define('ADDTHIS_PLUGIN_FILE', $path.'/addthis_social_widget.php');
define('ADDTHIS_PUBNAME_LIMIT', 255);

require_once('addthis_settings_functions.php');

class Addthis_Wordpress
{
    const ADDTHIS_REFERER  = 'www.addthis.com';

    /** PHP $_GET Variables * */
    private $_getVariables;

    /** PHP $_POST Variables * */
    private $_postVariables;

    /** check upgrade or fresh installation **/
    private $_upgrade;

    /** Addthis Settings **/
    private $_options;

    private $addThisConfigs;
    private $cmsConnector;

    public $addThisToolBox;

    /**
     * Initializes the plugin.
     *
     * @param boolean $upgrade check upgrade or fresh installation
     *
     * @return null
     * */
    public function __construct($upgrade, $addThisConfigs, $cmsConnector)
    {
        $this->addThisConfigs = $addThisConfigs;
        $this->cmsConnector = $cmsConnector;
        // Save async load settings via ajax request
        add_action( 'wp_ajax_at_async_loading', array($this, 'addthisAsyncLoading'));
        $this->_upgrade = $upgrade;
        $this->_getVariables = $_GET;
        $this->_postVariables = $_POST;
        $this->_options = $this->addThisConfigs->getConfigs();

        include_once 'addthis-toolbox.php';
        $this->addThisToolBox = new Addthis_ToolBox($addThisConfigs, $cmsConnector);

        add_action('admin_menu', array($this, 'addToWordpressMenu'));

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
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
        $settingsLink = '<a href="'.$this->cmsConnector->getSettingsPageUrl().'">'. __("Settings",'addthis_trans_domain') .'</a>';
//************ ORIGINAL
//      $settingsLink = '<a href="'.$this->cmsConnector->getSettingsPageUrl().'">Settings</a>';
//************ FI


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
    public function addToWordpressMenu()
    {
        $htmlGeneratingFunction = array($this, 'addthisWordpressOptions');
        $this->cmsConnector->addSettingsPage($htmlGeneratingFunction);
    }

    /**
     * Manages the WP settings page
     *
     * @return null
     */
    public function addthisWordpressOptions()
    {
        $updateResult = null;

        if ($this->_checkAddPubid()) {
            $updateResult = $this->updateSettings($this->_postVariables);
        }
        echo $this->_getHTML($updateResult);
    }

    /**
     *  Updates addthis profile id
     *
     *  @param string $pubId Addthis public id
     *
     *  @return string
     */
    public function updateSettings($settings)
    {
        if (!empty($settings['addthis_settings'])) {
            $this->_options = $this->addThisConfigs->saveSubmittedConfigs($settings['addthis_settings']);
        }

        return '
            <div class="addthis_updated wrap" style="margin-top:50px;width:95%">
<!-- XTEC ************ MODIFICAT - Localization support -->
<!-- 2015.09.18 @dgras -->
                        '. __("AddThis Profile Settings updated successfully!!!",'addthis_trans_domain').'
<!-- ************ ORIGINAL -->
<!--      AddThis Profile Settings updated successfully!!! -->
<!-- ************ FI -->
            </div>
        ';
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
        $successReturn = isset ($this->_postVariables['addthis_settings']['addthis_profile'])
                         && isset ($this->_postVariables['submit'])
                         && isset( $this->_postVariables['pubid_nonce'] )
                         && wp_verify_nonce( $this->_postVariables['pubid_nonce'], 'update_pubid' );

        return $successReturn;
    }

    /**
     *  Check if there is request to update async loading
     *
     *  @return boolean
     */
    private function _checkAsyncLoading()
    {
        $successReturn = isset ($this->_postVariables['async_loading']);

        return $successReturn;
    }

    public function addthisAsyncLoading()
    {
        if (current_user_can( 'manage_options' ) && $this->_checkAsyncLoading()) {
            $updateResult = $this->updateSettings($this->_postVariables);
        }
        die; //exit from the ajax request
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
        $html = '
            <div class="wrap">
                <form
                    id="addthis-settings"
                    method="post"
                    action="'.$this->cmsConnector->getSettingsPageUrl().'"
                >
                    <div class="Header">
<!-- XTEC ************ MODIFICAT - Localization support -->
<!-- 2015.09.18 @dgras -->
                        <h1>'. __("<em>AddThis</em> Sharing Buttons",'addthis_trans_domain').'</h1>
<!-- ************ ORIGINAL -->
<!--       <h1><em>AddThis</em> Sharing Buttons</h1> -->';
//************ FI

        if (!_addthis_is_csr_form()) {
            $html .= '<span class="preview-save-btns">' . _addthis_settings_buttons(false) . '</span>';
        }

        $html .= '</div>';

        if ($this->_upgrade && !$this->addThisConfigs->getProfileId()) {
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
            $html .= addthis_profile_id_csr_confirmation();
        } else {
            $html .= $this->_getAddThisLinkButton();
        }

        if (!_addthis_is_csr_form()) {
            $html .= '
                    <div class="Btn-container-end">
                        ' . _addthis_settings_buttons(false) . '
                    </div>
                </form>';
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

//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
        ". __('Failed to add AddThis Profile ID','addthis_trans_domain').";
//************ ORIGINAL
//        "Failed to add AddThis Profile ID".
//************ FI
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
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
        ". __('Click on the link below to finish setting up your AddThis tools.','addthis_trans_domain').";
//************ ORIGINAL
//        "Click on the link below to finish setting up your AddThis tools.".
//************ FI
               "</div>";
    }

    /**
     * Get Link to addthis site
     *
     * @return string
     */
    private function _getAddThisLinkButton()
    {
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
        $noPubIdDescription = __('To configure sharing tools for your site, use the button below to set up an AddThis account at addthis.com, create a profile for your site and begin adding sharing tools. This process will require an email address.','addthis_trans_domain');
        $noPubIdButtonText = __("AddThis profile setup",'addthis_trans_domain');
        $noPubIdCardTitle = __('You\'re almost done!','addthis_trans_domain');

        $pubIdDescription = __('To configure sharing tools for your site, use the button below. It will take you to Tools on addthis.com','addthis_trans_domain');
        $pubIdCardTitle = __('Setup AddThis Tools','addthis_trans_domain');
        $pubIdButtonText = __("Configure AddThis Tools",'addthis_trans_domain');
//************ ORIGINAL
//        $noPubIdDescription = 'To configure sharing tools for your site, use the button below to set up an AddThis account at addthis.com, create a profile for your site and begin adding sharing tools. This process will require an email address.';
//        $noPubIdButtonText = "AddThis profile setup";
//        $noPubIdCardTitle = 'You\'re almost done!';
//
//        $pubIdDescription = 'To configure sharing tools for your site, use the button below. It will take you to Tools on addthis.com';
//        $pubIdCardTitle = 'Setup AddThis Tools';
//        $pubIdButtonText = "Configure AddThis Tools";
//************ FI

        if (!$this->addThisConfigs->getProfileId()) {
            // if they don't have a profile yet, default to setup
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
            $tabOrder = array(
                'tabs-1' => __('Setup','addthis_trans_domain'),
                'tabs-2' => __('Advanced Options','addthis_trans_domain'),
            );
//************ ORIGINAL
//            $tabOrder = array(
//                'tabs-1' => 'Setup',
//                'tabs-2' => 'Advanced Options',
//            );
//************ FI

            $sharingToolsCardTitle = $noPubIdCardTitle;
            $sharingToolsDescription = $noPubIdDescription;
            $sharingToolsButtonUrl = _addthis_profile_setup_url();
            $sharingToolsButtonText = $noPubIdButtonText;
            $target = '';
        } else {
            // else default to profile
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
            $tabOrder = array(
                'tabs-1' => __('Sharing Tools','addthis_trans_domain'),
                'tabs-2' => __('Advanced Options','addthis_trans_domain'),
            );
//************ ORIGINAL
//            $tabOrder = array(
//                'tabs-1' => 'Sharing Tools',
//                'tabs-2' => 'Advanced Options',
//            );
//************ FI

            $sharingToolsCardTitle = $pubIdCardTitle;
            $sharingToolsDescription = $pubIdDescription;
            $sharingToolsButtonUrl = _addthis_tools_url();
            $sharingToolsButtonText = $pubIdButtonText;
            $target = 'target="_blank"';
        }

        $tabsHtml = '';
        foreach ($tabOrder as $href => $title) {
            $tabsHtml .= '<li class="Tabbed-nav-item"><a href="#' . $href . '">' . $title . '</a></li>';
        }

        $html = '
            <div class="Main-content" id="tabs">
                <ul class="Tabbed-nav">
                    ' . $tabsHtml . '
                </ul>
                <div id="tabs-1">
                    <div class="Card" id="Card-side-sharing">
                        <div>
                            <h3 class="Card-hd-title">
                                ' . $sharingToolsCardTitle . '
                            </h3>
                        </div>
                        <div class="addthis_seperator">&nbsp;</div>
                        <div class="Card-bd">
                            <div class="addthis_description">
<!--XTEC ************ MODIFICAT - Localization support -->
<!--2015.09.18 @dgras -->
            '. __ ("Beautiful simple website tools designed to help you get likes, get shares, get follows and get discovered.",'addthis_trans_domain') .'
<!--************ ORIGINAL -->
<!--            Beautiful simple website tools designed to help you get likes, get shares, get follows and get discovered. -->
<!--************ FI -->

                            </div>
                            <p>' . $sharingToolsDescription . '</p>
                            <a
                                class="Btn Btn-blue"
                                ' . $target . '
                                href="' . $sharingToolsButtonUrl . '">' . $sharingToolsButtonText . ' &#8594;
                            </a>
                            <p class="addthis_support">
<!--XTEC ************ MODIFICAT - Localization support -->
<!--2015.09.18 @dgras -->
            '. __ ("If you don't see your tools after configuring them in the dashboard, please contact",'addthis_trans_domain') .'
            <a href="http://support.addthis.com/">'. __ ("AddThis Support") .'</a>
<!--************ ORIGINAL -->
<!--            If you don\'t see your tools after configuring them in the dashboard, please contact -->
<!--            <a href="http://support.addthis.com/">AddThis Support</a> -->
<!--************ FI -->
                            </p>
                        </div>
                    </div>
                   ' . _addthis_rate_us_card() . '
                </div>
                <div id="tabs-2">
                    ' . _addthis_tracking_card() . '
                    ' . _addthis_display_options_card() . '
                    ' . _addthis_additional_options_card() . '
                    ' . _addthis_profile_id_card() . '
                    ' . _addthis_mode_card() . '
                </div>
            </div>';

        return $html;
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
 * @global AddThis_addjs_sharing_button_plugin $AddThis_addjs_sharing_button_plugin
 * @return null
 */
function Addthis_Wordpress_early()
{
    global $AddThis_addjs_sharing_button_plugin;
    global $addThisConfigs;
    global $cmsConnector;

    if (!isset($AddThis_addjs_sharing_button_plugin)) {
        include 'addthis_addjs_new.php';
        $AddThis_addjs_sharing_button_plugin = new AddThis_addjs_sharing_button_plugin($addThisConfigs, $cmsConnector);
    }
}
