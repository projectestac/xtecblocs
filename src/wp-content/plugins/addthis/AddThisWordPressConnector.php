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

require_once('AddThisCmsConnectorInterface.php');

if (!class_exists('AddThisWordPressConnector')) {
    Class AddThisWordPressConnector {
        // implements AddThisCmsConnectorInterface {

        static $settingsVariableName = 'addthis_settings';
        public $plugin = null;
        static $anonymousProfileIdPrefix = 'wp';
        static $cmsName = "WordPress";
        protected $configs = null;
        protected $sharedConfigVariableName = 'addthis_shared_settings';
        protected $migratedConfigSettingsName = null;

        protected $defaultConfigs = array(
            'addthis_plugin_controls'      => 'WordPress',
        );

        static $simpleSharedConfigUpgradeMappings = array(
            array(
                'current' => array('addthis_profile'),
                'deprecated' => array('profile', 'pubid'),
            ),
        );

        static $sharedVariables = array(
            // general
            'addthis_anonymous_profile',
            'addthis_asynchronous_loading',
            'addthis_environment',
            'addthis_per_post_enabled',
            'addthis_plugin_controls',
            'addthis_profile',
            'api_key',
            'credential_validation_status',
            'debug_enable',
            'debug_profile_level',
            'debug_profile_type',
            'script_location',
            'wpfooter',
            'follow_buttons_feature_enabled',
            'recommended_content_feature_enabled',
            'sharing_buttons_feature_enabled',
            'trending_content_feature_enabled',
            // addthis_share
            'addthis_twitter_template',
            'addthis_bitly',
            'addthis_share_json',
            'addthis_share_follow_json',
            'addthis_share_recommended_json',
            'addthis_share_trending_json',
            'addthis_share_welcome_json',
            // addthis_layers
            'addthis_layers_json',
            'addthis_layers_follow_json',
            'addthis_layers_recommended_json',
            'addthis_layers_trending_json',
            'addthis_layers_welcome_json',
            // addthis_config
            'data_ga_property',
            'addthis_language',
            'atversion',
            'addthis_append_data',
            'addthis_addressbar',
            'addthis_508',
            'addthis_config_json',
            'addthis_config_follow_json',
            'addthis_config_recommended_json',
            'addthis_config_trending_json',
            'addthis_config_welcome_json',
            'addthis_plugin_controls',

        );

        static $deprecatedSharedVariables = array(
            'addthis_bitly_key',
            'addthis_bitly_login',
            'addthis_brand',
            'addthis_copytracking1',
            'addthis_copytracking2',
            'addthis_copytrackingremove',
            'addthis_fallback_username',
            'addthis_for_wordpress',
            'addthis_header_background',
            'addthis_header_color',
            'addthis_nag_username_ignore',
            'addthis_options',
            'addthis_password',
            'addthis_show_stats',
            'addthis_username',
            'options',
            'password',
            'profile',
            'username',
        );

        public function __construct($plugin) {
            $this->plugin = $plugin;
        }

        public function getPluginVersion() {
            return $this->plugin->getVersion();
        }

        static function getCmsName() {
            return self::$cmsName;
        }

        public function getDeprecatedVariables() {
            $sharedVars = self::$deprecatedSharedVariables;
            $pluginVars = $this->plugin->getDeprecatedVariables();
            $variables = array_merge($sharedVars, $pluginVars);
            return $variables;
        }

        public function getSimpleConfigUpgradeMapping() {
            $sharedVars = self::$simpleSharedConfigUpgradeMappings;
            $pluginVars = $this->plugin->getSimpleConfigUpgradeMapping();
            $mapping = array_merge($sharedVars, $pluginVars);
            return $mapping;
        }

        static function getCmsVersion() {
            $version =  get_bloginfo('version');
            return $version;
        }

        static function getCmsMinorVersion() {
            $version =  (float)substr(self::getCmsVersion(),0,3);
            return $version;
        }

        static function getAnonymousProfileIdPrefix() {
            return self::$anonymousProfileIdPrefix;
        }

        public function getProductVersion() {
            return $this->plugin->getProductVersion();
        }

        /**
         * the folder name for the AddThis plugin - OMG why is this hard coded?!?
         * @return string
         */
        public function getPluginFolder(){
            return 'addthis';
        }

        /**
         * gives you the base URL for our plugin
         * @return string
         */
        public function getPluginUrl(){
            $url = apply_filters(
                'addthis_files_uri',
                plugins_url()
            );
            $url .= '/' . $this->getPluginFolder();
            return $url;
        }

        /**
         * gives you the base URL for our plugin's JavaScript
         * @return string
         */
        public function getPluginJsFolderUrl() {
            $url = $this->getPluginUrl() . '/js/';
            return $url;
        }

        /**
         * gives you the base URL for our plugin's CSS
         * @return string
         */
        public function getPluginCssFolderUrl() {
            $url = $this->getPluginUrl() . '/css/';
            return $url;
        }

        /**
         * gives you the base URL for our plugin's images
         * @return string
         */
        public function getPluginImageFolderUrl() {
            $url = $this->getPluginUrl() . '/img/';
            return $url;
        }

        public function getSettingsPageUrl() {
            $url = admin_url("options-general.php?page=" . $this->plugin->getSettingsPageId());
            return $url;
        }

        public function getDefaultConfigs() {
            return $this->defaultConfigs;
        }

        public function getConfigs($cache = false) {
            if ($cache && is_array($this->configs)) {
                return $this->configs;
            }

            $plugin = $this->getPluginConfigVariables();
            $shared = $this->getSharedConfigVariables();

            if (is_array($plugin) && is_array($shared)) {
                $this->configs = array_merge($plugin, $shared);
            } else if (is_array($plugin)) {
                $this->configs = $plugin;
            } else if (is_array($shared)) {
                $this->configs = $shared;
            } else {
                $this->configs = null;
            }

            return $this->configs;
        }

        public function getPluginConfigVariables() {
            if ($this->isPreviewMode()) {
                $plugin = get_transient($this->plugin->getConfigVariableName());
            } else {
                $plugin = get_option($this->plugin->getConfigVariableName());
            }

            return $plugin;
        }

        public function getSharedConfigVariables() {
            if ($this->isPreviewMode()) {
                $shared = get_transient($this->sharedConfigVariableName);
            } else {
                $shared = get_option($this->sharedConfigVariableName);
            }

            return $shared;
        }

        public function saveConfigs($configs = null) {
            if (!is_array($configs)) {
                $configs = $this->configs;
            }

            if (is_array($configs)) {
                $this->saveSharedConfigs($configs);
                $this->savePluginConfigs($configs);
                $this->configs = $this->getConfigs();
            }

            return $this->configs;
        }

        protected function saveSharedConfigs($configs) {
            $newSharedConfigs = array();
            foreach (self::$sharedVariables as $variable) {
                if(isset($configs[$variable])) {
                    $newSharedConfigs[$variable] = $configs[$variable];
                }
            }

            update_option($this->sharedConfigVariableName, $newSharedConfigs);
        }

        protected function savePluginConfigs($configs) {
            foreach (self::$sharedVariables as $variable) {
                if(isset($configs[$variable])) {
                    unset($configs[$variable]);
                }
            }

            update_option($this->plugin->getConfigVariableName(), $configs);
        }

        /**
         * checks if you're in preview mode
         * @return boolean true if in preview, false otherwise
         */
        public function isPreviewMode() {
            if (isset($_GET['preview']) && $_GET['preview'] == 1) {
                return true;
            }

            return false;
        }

        public function getSharingButtonLocations() {
            $types = array(
                'above',
                'below',
                'sidebar',
            );
            return $types;
        }

        /**
         * Returns an array of template options generlized without location info
         * @return array[] an array of associative arrays
         */

        public function getContentTypes() {
//XTEC ************ MODIFICAT - Localization support
//2015.09.18 @dgras
            $options = array(
                array(
                    'fieldName'    => 'home',
                    'displayName'  => __('Homepage','addthis_trans_domain'),
                    'explanation'  => __('Includes both the blog post index page (home.php or index.php) and any static page set to be your front page under Settings->Reading->Front page displays.','addthis_trans_domain'),
                ),
                array(
                    'fieldName'    => 'posts',
                    'displayName'  => __('Posts','addthis_trans_domain'),
                    'explanation'  => __('Also known as articles or blog posts.','addthis_trans_domain'),
                ),
                array(
                    'fieldName'    => 'pages',
                    'displayName'  => __('Pages','addthis_trans_domain'),
                    'explanation'  => __('Often used to present static information about yourself or your site where the date published is less revelant than with posts.','addthis_trans_domain'),
                ),
                array(
                    'fieldName'    => 'archives',
                    'displayName'  => __('Archives','addthis_trans_domain'),
                    'explanation'  => __('A Category, Tag, Author or Date based view.','addthis_trans_domain'),
                ),
                array(
                    'fieldName'    => 'categories',
                    'displayName'  => __('Categories','addthis_trans_domain'),
                    'explanation'  => __('A view that displays costs filled under a specific category.','addthis_trans_domain'),
                ),
                array(
                    'fieldName'    => 'excerpts',
                    'displayName'  => __('Excerpts','addthis_trans_domain'),
                    'explanation'  => __('A condensed description of your post or page. These are often displayed in search results, RSS feeds, and sometimes on Archive or Category views. Important: Excerpts will only work some of the time with some themes, depending on how that theme retrieves your content.','addthis_trans_domain'),
                ),
            );
//************ ORIGINAL
//            $options = array(
//                array(
//                    'fieldName'    => 'home',
//                    'displayName'  => 'Homepage',
//                    'explanation'  => 'Includes both the blog post index page (home.php or index.php) and any static page set to be your front page under Settings->Reading->Front page displays.',
//                ),
//                array(
//                    'fieldName'    => 'posts',
//                    'displayName'  => 'Posts',
//                    'explanation'  => 'Also known as articles or blog posts.',
//                ),
//                array(
//                    'fieldName'    => 'pages',
//                    'displayName'  => 'Pages',
//                    'explanation'  => 'Often used to present static information about yourself or your site where the date published is less revelant than with posts.',
//                ),
//                array(
//                    'fieldName'    => 'archives',
//                    'displayName'  => 'Archives',
//                    'explanation'  => 'A Category, Tag, Author or Date based view.',
//                ),
//                array(
//                    'fieldName'    => 'categories',
//                    'displayName'  => 'Categories',
//                    'explanation'  => 'A view that displays costs filled under a specific category.',
//                ),
//                array(
//                    'fieldName'    => 'excerpts',
//                    'displayName'  => 'Excerpts',
//                    'explanation'  => 'A condensed description of your post or page. These are often displayed in search results, RSS feeds, and sometimes on Archive or Category views. Important: Excerpts will only work some of the time with some themes, depending on how that theme retrieves your content.',
//                ),
//            );
//************ FI
            return $options;
        }

        public function isUpgrade() {
            $this->getConfigs(true);
            if (   !isset($this->configs['addthis_plugin_version'])
                || $this->configs['addthis_plugin_version'] != $this->getPluginVersion()
            ) {
                return true;
            }

            return false;
        }

        private function migrationStatusVariable() {
            $migrationVariable = $this->plugin->getSettingsPageId() . "_migrated_to";
            return $migrationVariable;
        }

        private function recurseForOldConfigs($name) {
            $configs = get_option($name);
            if (!is_array($configs)) {
                return null;
            }
            $this->migratedConfigSettingsName = $name;

            $migrationVariable = $this->migrationStatusVariable();
            if (   isset($configs[$migrationVariable])
                && $configs[$migrationVariable] !== $this->plugin->getConfigVariableName()
            ) {
               $migratedConfigs = recurseForOldConfigs($configs[$migrationVariable]);
               if (is_array($migrationVariable)) {
                   $config = $migrationVariable;
               }
            }

            return $configs;
        }

        private function getOldConfigs() {
            $this->getConfigs(true);
            $migrationVariable = $this->migrationStatusVariable();

            $oldConfigs = $this->recurseForOldConfigs($this->plugin->getOldConfigVariableName());

            if (   !isset($oldConfigs[$migrationVariable])
                || $oldConfigs[$migrationVariable] !== $this->plugin->getConfigVariableName()
            ) {
                if (is_array($this->configs) && is_array($oldConfigs)) {
                    $this->configs = array_merge($oldConfigs, $this->configs);
                } else if (is_array($oldConfigs)) {
                    $this->configs = $oldConfigs;
                }

                $updatedOldConfigs[$migrationVariable] = $this->plugin->getConfigVariableName();
                update_option($this->migratedConfigSettingsName, $updatedOldConfigs);
            }

            $badUpgradeVersions = array('5.0.9', '5.0.10', '5.0.11');
            if (!empty($this->configs['addthis_plugin_version'])) {
                $oldVersion = $this->configs['addthis_plugin_version'];
            } else {
                $oldVersion = 'unknown';
            }

            if (in_array($oldVersion, $badUpgradeVersions)) {
                $this->configs = $oldConfigs;
            }

            return $this->configs;
        }

        public function upgradeConfigs() {
            $this->getOldConfigs();

            if (is_null($this->configs)) {
                return $this->configs;
            }

            $this->configs['addthis_plugin_version'] = $this->getPluginVersion();

            $upgradeMapping = $this->getSimpleConfigUpgradeMapping();
            foreach ($upgradeMapping as $configUpgradeMapping) {
                foreach ($configUpgradeMapping['current'] as $currentFieldName) {
                    foreach ($configUpgradeMapping['deprecated'] as $deprecatedFieldName) {
                        $this->getFromPreviousConfig($deprecatedFieldName, $currentFieldName);
                    }
                }
            }

            // if AddThis above button was enabled
            if (   !isset($this->configs['addthis_above_enabled'])
                && isset($this->configs['above'])
            ) {
                if ($this->configs['above'] == 'none' || $this->configs['above'] == 'disable') {
                    $this->configs['addthis_above_enabled'] = false;
                } else {
                    $this->configs['addthis_above_enabled'] = true;
                }
            }

            // if AddThis below button was enabled
            if (   !isset($this->configs['addthis_below_enabled'])
                && isset($this->configs['below'])
            ) {
                if ($this->configs['below'] == 'none' || $this->configs['below'] == 'disable') {
                    $this->configs['addthis_below_enabled'] = false;
                } else {
                    $this->configs['addthis_below_enabled'] = true;
                }
            }

            if (   isset($this->configs['addthis_for_wordpress'])
                && $this->configs['addthis_for_wordpress']
                && !isset($this->configs['addthis_plugin_controls'])
            ) {
                $this->configs['addthis_plugin_controls'] = "AddThis";
            }

            if (   isset($this->configs['above_sharing'])
                && !isset($this->configs['above_auto_services'])
            ) {
                if($this->configs['above_sharing'] == 'above-disable-smart-sharing') {
                    $this->configs['above_auto_services'] = false;
                }
                if($this->configs['above_sharing'] == 'above-enabled-smart-sharing') {
                    $this->configs['above_auto_services'] = true;
                }
            }

            if (   isset($this->configs['below_sharing'])
                && !isset($this->configs['below_auto_services'])
            ) {
                if($this->configs['below_sharing'] == 'below-disable-smart-sharing') {
                    $this->configs['below_auto_services'] = false;
                }
                if($this->configs['below_sharing'] == 'below-enabled-smart-sharing') {
                    $this->configs['below_auto_services'] = true;
                }
            }

            $deprecatedVariables = $this->getDeprecatedVariables();
            foreach ($deprecatedVariables as $field) {
                if (isset($this->configs[$field])) {
                    unset($this->configs[$field]);
                }
            }

            $this->saveConfigs();
            return $this->configs;
        }

        private function getFromPreviousConfig($deprecatedFieldName, $currentFieldName) {
            // if we don't have this value, get from a the depricated field
            if (   is_array($this->configs)
                && isset($this->configs[$deprecatedFieldName])
                && !isset($this->configs[$currentFieldName])
            ) {
                $deprecatedValue = $this->configs[$deprecatedFieldName];
                $this->configs[$currentFieldName] = $deprecatedValue;
            }
        }

        /**
         * Evaluates a handle and its source to determine if we should keep it.
         * We want to keep stuff from out plugin, from themes and from core
         * WordPress, but not stuff from other plugins as it can conflict with
         * our code.
         *
         * @param string  $handle     The name given to an enqueued script or
         * @param mixed   $src        style.  This is usually a string with the
         *                            the location of the enqueued script or
         *                            style, relative or absolute. Sometimes
         *                            this is not a string, and it adds CSS code
         *                            to a WordPress generated CSS file.
         * @param string[] $whitelist We will inevitably run into code from
         *                            other plugins that should be included on
         *                            our settings page. For those, their
         *                            handles can be added to this array of
         *                            strings. We've decided to whitelist
         *                            instead of blacklist, as we are likely to
         *                            encounter fewer plugins that add
         *                            functionality to our settings page than
         *                            plugins that behave badly and add unwanted
         *                            code to our page. This also keeps our code
         *                            working (though perhaps without the added
         *                            functionality from another plugin that may
         *                            be desired by the user) instead of
         *                            breaking the page outright.
         *                            Troubleshooting should also be easier, as
         *                            a user is more likely to be aware of which
         *                            of their plugins add functionality on
         *                            their settings pages, rather than which
         *                            ones doesn't play nicely with how they
         *                            enqueue their scripts and styles.
         * @return boolean true when a particular script or style should be
         *                 killed from our settings page, false when it should
         *                 not be killed
         */
        public function evalKillEnqueue($handle, $src, $whitelist = array()) {
            $regex = "/\/[^\/]+\/plugins$/";
            preg_match($regex, plugins_url(), $matches);
            if (isset($matches[0])) {
                $pluginsFolder = $matches[0] . '/';
            } else {
                $pluginsFolder = '/wp-content/plugins/';
            }

            $partialPathToOurPlugin = $pluginsFolder . $this->getPluginFolder();
            $fullUrlToOurPlugin = $this->getPluginUrl();

            if (!is_string($src)) {
                return false;
            }

            if (   !is_string($src) // is the source location a string? keep css if not, cause, for some reason it breaks otherwise
                || in_array($handle, $whitelist) // keep stuff that's in the whitelist
                || strpos($handle, 'addthis') !== false  // handle has our name
                || strpos($partialPathToOurPlugin, $src) !== false // keep relative path stuff from this plugin
                || strpos($fullUrlToOurPlugin, $src) !== false // full urls for this plugin
                || strpos($src, $pluginsFolder) == false // keep enqueued stuff for non-plugins
            ) {
                return false;
            }

            return true;
        }

        /**
         * Dequeues unwanted scripts from the HTML page generated by WordPress.
         * This should only be used for our settings page. See the documentation
         * for the evalKillEnqueue function for more details, secifically for
         * more information on the $whitespace variable.
         */
        public function killUnwantedScripts() {
            global $wp_scripts;
            $whitelist = array();

            foreach ($wp_scripts->queue as $handle) {
                $obj = $wp_scripts->registered[$handle];
                $src = $obj->src;
                $kill = $this->evalKillEnqueue($handle, $src, $whitelist);
                if ($kill) {
                    wp_dequeue_script($handle);
                }
            }
        }

        /**
         * Dequeues unwanted styles from the HTML page generated by WordPress.
         * This should only be used for our settings page. See the documentation
         * for the evalKillEnqueue function for more details, secifically for
         * more information on the $whitespace variable.
         */
        public function killUnwantedStyles() {
            global $wp_styles;
            $whitelist = array();

            foreach ($wp_styles->queue as $handle) {
                $obj = $wp_styles->registered[$handle];
                $src = $obj->src;
                $kill = $this->evalKillEnqueue($handle, $src, $whitelist);
                if ($kill) {
                    wp_dequeue_style($handle);
                }
            }
        }

        public function addSettingsPageScripts() {
            $this->getConfigs(true);
            $this->killUnwantedScripts();

            $jsRootUrl = $this->getPluginJsFolderUrl();
            $imgRootUrl = $this->getPluginImageFolderUrl();

            if (   $this->getCmsMinorVersion() >= 3.2
                || $this->assumeLatest()
            ) {
                $optionsJsUrl = $jsRootUrl . 'options-page.32.js';
            } else {
                $optionsJsUrl = $jsRootUrl . 'options-page.js';
            }

            $dependencies = array(
                'jquery-ui-tabs',
                'thickbox',
            );

            wp_enqueue_script('addthis_options_page_script',$optionsJsUrl, $dependencies);

            if ($this->configs['addthis_plugin_controls'] == 'AddThis') {
                wp_enqueue_script(
                    'addThisScript',
                    $jsRootUrl . 'addthis-for-wordpress.js'
                );

                return;
            }

            wp_enqueue_script('jquery-core');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-mouse');
            wp_enqueue_script('jquery-ui-position');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-droppable');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-tooltip');

            wp_enqueue_script('addthis_lr', $jsRootUrl . 'lr.js');
            wp_enqueue_script('addthis_qtip_script', $jsRootUrl . 'jquery.qtip.min.js');
            wp_enqueue_script('addthis_selectbox', $jsRootUrl . 'jquery.selectBoxIt.min.js');
            wp_enqueue_script('addthis_jquery_messagebox', $jsRootUrl . 'jquery.messagebox.js');
            wp_enqueue_script('addthis_jquery_atjax', $jsRootUrl . 'jquery.atjax.js');
            wp_enqueue_script('addthis_lodash_script', $jsRootUrl . 'lodash-0.10.0.js');

            wp_enqueue_script('addthis_services_script', $jsRootUrl . 'gtc-sharing-personalize.js');
            wp_enqueue_script('addthis_service_script', $jsRootUrl . 'gtc.cover.js');

            wp_localize_script(
                'addthis_services_script',
                'addthis_params',
                array('img_base' => $imgRootUrl)
            );
            wp_localize_script(
                'addthis_options_page_script',
                'addthis_option_params',
                array(
                    'wp_ajax_url'=> admin_url('admin-ajax.php'),
                    'addthis_validate_action' => 'validate_addthis_api_credentials',
                    'img_base' => $imgRootUrl
                )
            );
        }

        public function addSettingsPageStyles() {
            $this->getConfigs(true);
            $this->killUnwantedStyles();
            $cssRootUrl = $this->getPluginCssFolderUrl();

            wp_enqueue_style('addthis_options_page_style', $cssRootUrl . 'options-page.css');
            wp_enqueue_style('addthis_general_style', $cssRootUrl . 'style.css');

            if ($this->configs['addthis_plugin_controls'] == 'AddThis') {
                return;
            }

            wp_enqueue_style('thickbox');
            wp_enqueue_style('addthis_services_style', $cssRootUrl . 'gtc.sharing-personalize.css');
            wp_enqueue_style('addthis_bootstrap_style', $cssRootUrl . 'bootstrap.css');
            wp_enqueue_style('addthis_widget', 'https://ct1.addthis.com/static/r07/widget114.css');
            wp_enqueue_style('addthis_widget_big', 'https://ct1.addthis.com/static/r07/widgetbig056.css');
        }

        public function addSettingsPage($htmlGeneratingFunction) {
            $hook_suffix = add_options_page(
                $this->plugin->getName(),
                $this->plugin->getName(),
                'manage_options',
                $this->plugin->getSettingsPageId(),
                $htmlGeneratingFunction
            );

            $print_scripts_hook = 'admin_print_scripts-' . $hook_suffix;
            $print_styles_hook = 'admin_print_styles-' . $hook_suffix;

            add_action(
                $print_scripts_hook,
                array($this, 'addSettingsPageScripts')
            );
            add_action(
                $print_styles_hook,
                array($this, 'addSettingsPageStyles')
            );

        }

        public function assumeLatest() {
            if (   apply_filters('at_assume_latest', __return_false())
                || apply_filters('addthis_assume_latest', __return_false())
            ) {
                return true;
            }

            return false;
        }

        public function getHomepageUrl() {
            $url = get_option('home');
            return $url;
        }

        public function prepareSubmittedConfigs($input) {

            if (isset($input['addthis_rate_us'])) {
                $output['addthis_rate_us_timestamp'] = time();
            }

            $checkAndSanitize = array(
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
                'pubid'
            );

            foreach ($checkAndSanitize as $field) {
                if (isset($input[$field])) {
                    $output[$field] = sanitize_text_field($input[$field]);
                }
            }

            // All the checkbox fields
            $checkboxFields = array(
                'addthis_508',
                'addthis_addressbar',
                'addthis_append_data',
                'addthis_asynchronous_loading',
                'addthis_bitly',
                'addthis_per_post_enabled',
            );

            foreach ($checkboxFields as $field) {
                if (!empty($input[$field])) {
                    $output[$field] = true;
                } else {
                    $output[$field] = false;
                }
            }

            return $output;
        }

        public function prepareCmsModeSubmittedConfigs($input, $output) {
            return $output;
        }

        public function deactivate() {
            $configs = $this->getConfigs(true);

            if (isset($configs['sharing_buttons_feature_enabled'])) {
                unset($configs['sharing_buttons_feature_enabled']);
                $this->saveSharedConfigs($configs);
            }
        }
    }
}
