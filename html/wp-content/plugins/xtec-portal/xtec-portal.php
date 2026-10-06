<?php

/**
 * Plugin Name:       XTEC Portal
 * Description:       Descriptors, favorite blogs, latest posts and latest blogs for the XTECBlocs portal theme.
 * Version:           1.0
 * Requires at least: 5.1
 * Requires PHP:      7.4
 * Network:           true
 * Author:            Albert Pérez Monfort, Francesc Bassas i Bullich, Germán Antolin Priotto, Toni Ginard
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Combines the former XTEC API, XTEC Descriptors, XTEC Favorites and XTEC Latest Posts plugins.
 */

defined('ABSPATH') || exit;

const XTEC_PORTAL_FILE = __FILE__;

// Network tables of the plugin, named like the tables of WordPress core (e.g. $wpdb->blogs)
global $wpdb;
$wpdb->descriptors = $wpdb->base_prefix . 'descriptors';
$wpdb->descriptors_pre = $wpdb->base_prefix . 'descriptors_pre';
$wpdb->globalposts = $wpdb->base_prefix . 'globalposts';
$wpdb->user_blogs = $wpdb->base_prefix . 'user_blogs';

require_once __DIR__ . '/includes/api.php';
require_once __DIR__ . '/includes/descriptors.php';
require_once __DIR__ . '/includes/favorites.php';
require_once __DIR__ . '/includes/latest-posts.php';

/**
 * Creates the database tables of descriptors, favorites and latest posts.
 */
function xtec_portal_activation_hook(): void
{
    xtec_descriptors_activation_hook();
    xtec_favorites_activation_hook();
    xtec_latest_posts_activation_hook();
}

register_activation_hook(__FILE__, 'xtec_portal_activation_hook');
