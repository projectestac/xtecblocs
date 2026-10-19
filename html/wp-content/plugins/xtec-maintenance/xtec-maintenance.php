<?php

/**
 * Plugin Name:       XTEC Maintenance
 * Description:       Deletion of inactive blogs of XTECBlocs and history of the deleted blogs.
 * Version:           2.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Network:           true
 * Text Domain:       xtec-maintenance
 * Domain Path:       /languages
 * Author:            Francesc Bassas i Bullich, Toni Ginard
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

defined('ABSPATH') || exit;

const XTEC_MAINTENANCE_FILE = __FILE__;

const XTEC_MAINTENANCE_DB_VERSION = '1.0';

// Value of the column status of the history for the blogs deleted for inactivity
const XTEC_MAINTENANCE_STATUS_INACTIVITY = 2;

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/admin.php';
require_once __DIR__ . '/includes/inactive-blogs.php';
require_once __DIR__ . '/includes/deleted-blogs.php';

register_activation_hook(__FILE__, 'xtec_maintenance_activate');
