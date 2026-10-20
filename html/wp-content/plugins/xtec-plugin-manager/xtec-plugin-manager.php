<?php

/**
 * Plugin Name:       XTEC Plugin Manager
 * Description:       Chooses the plugins that blog administrators can activate and the ones of the new blogs.
 * Version:           1.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Network:           true
 * Text Domain:       xtec-plugin-manager
 * Domain Path:       /languages
 * Author:            Toni Ginard
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Replaces the plugin Multisite Plugin Manager and keeps its options, so that the settings are kept when migrating.
 * The administrators of the blogs need the menu Plugins, which is enabled in the settings of the network.
 */

defined('ABSPATH') || exit;

const XTEC_PLUGIN_MANAGER_FILE = __FILE__;

require_once __DIR__ . '/includes/access.php';
require_once __DIR__ . '/includes/admin.php';
