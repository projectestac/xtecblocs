<?php

/**
 * Plugin Name:       XTEC Site Cloner
 * Description:       Creates blogs from a template blog, copying its tables and its files.
 * Version:           1.0
 * Requires at least: 6.1
 * Requires PHP:      8.0
 * Network:           true
 * Text Domain:       xtec-site-cloner
 * Domain Path:       /languages
 * Author:            Toni Ginard
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Replaces Multisite Clone Duplicator. The copy is done in the same request, table by table and with queries that
 * name a single table, so that it works when the tables of the blogs are distributed in several databases.
 */

defined('ABSPATH') || exit;

const XTEC_SITE_CLONER_FILE = __FILE__;

require_once __DIR__ . '/includes/copy.php';
require_once __DIR__ . '/includes/api.php';

if (is_admin()) {
    require_once __DIR__ . '/includes/admin.php';
}
