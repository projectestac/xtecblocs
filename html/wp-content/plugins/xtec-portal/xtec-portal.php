<?php

/*
Plugin Name: XTEC Portal
Description: Functions used by the XTECBlocs portal theme: descriptors of the blogs, favorite blogs of the users, lastest posts and lastest blogs. Combines the former XTEC API, XTEC Descriptors, XTEC Favorites and XTEC Lastest Posts plugins.
Version: 1.0
Network: true
Author: Albert Pérez Monfort, Francesc Bassas i Bullich & Germán Antolin Priotto
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
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
require_once __DIR__ . '/includes/lastest-posts.php';

/**
 * Creates the database tables of descriptors, favorites and lastest posts.
 */
function xtec_portal_activation_hook(): void
{
    xtec_descriptors_activation_hook();
    xtec_favorites_activation_hook();
    xtec_lastest_posts_activation_hook();
}

register_activation_hook(__FILE__, 'xtec_portal_activation_hook');
