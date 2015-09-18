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

if (!class_exists('AddThisWordPressSharingButtonsPlugin')) {
    Class AddThisWordPressSharingButtonsPlugin {
        // implements AddThisWordPressPluginInterface {

        static $version = '5.1.1';
        static $settingsPageId = 'addthis_social_widget';
        static $name = "AddThis Sharing Buttons";
        static $productPrefix = 'wpp';
        static $oldConfigVariableName = 'addthis_settings';
        static $configVariableName = 'addthis_sharing_buttons_settings';

        static $simpleConfigUpgradeMappings = array(
            array(
                'current' => array('addthis_above_showon_home', 'addthis_below_showon_home'),
                'deprecated' => array('addthis_showonhome'),
            ),
            array(
                'current' => array('addthis_above_showon_pages', 'addthis_below_showon_pages'),
                'deprecated' => array('addthis_showonpages'),
            ),
            array(
                'current' => array('addthis_above_showon_categories', 'addthis_below_showon_categories'),
                'deprecated' => array('addthis_showoncats'),
            ),
            array(
                'current' => array('addthis_above_showon_archives', 'addthis_below_showon_archives'),
                'deprecated' => array('addthis_showonarchives'),
            ),
            array(
                'current' => array('addthis_above_showon_posts', 'addthis_below_showon_posts'),
                'deprecated' => array('addthis_showonposts'),
            ),
        );

        static $deprecatedVariables = array(
            'above_sharing',
            'addthis_showonarchives',
            'addthis_showoncats',
            'addthis_showonhome',
            'addthis_showonpages',
            'addthis_showonposts',
            'addthis_sidebar_only',
            'below_sharing',
            'show_above',
            'show_below',
        );

        static function getVersion() {
            return self::$version;
        }

        static function getDeprecatedVariables() {
            return self::$deprecatedVariables;
        }

        static function getSimpleConfigUpgradeMapping() {
            return self::$simpleConfigUpgradeMappings;
        }

        static function getName() {
            return self::$name;
        }

        static function getSettingsPageId() {
            return self::$settingsPageId;
        }

        static function getOldConfigVariableName() {
            return self::$oldConfigVariableName;
        }

        static function getConfigVariableName() {
            return self::$configVariableName;
        }

        static function getProductVersion() {
            $productVersion = self::$productPrefix . '-' . self::getVersion();
            return $productVersion;
        }

        /**
         * the folder name for the AddThis plugin - OMG why is this hard coded?!?
         * @return string
         */
        static function getPluginFolder(){
            return 'addthis';
        }
    }
}