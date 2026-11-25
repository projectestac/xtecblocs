<?php

/**
 * Plugin Name:       Eduhack Projects Widget
 * Plugin URI:        http://mschools.mobileworldcapital.com/es/iniciativas/edu_hack/
 * Description:       Crea un giny sobre un projecte d'Edu_Hack
 * Version:           2.0
 * Requires at least: 6.1
 * Requires PHP:      8.0
 * Author:            Artesans
 * Author URI:        https://www.artesans.eu/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       eduhack-projects-widget
 * Domain Path:       /languages
 *
 * Rewritten for WordPress 7.x by the Departament d'Educació i Formació Professional, keeping the post type, the
 * metadata, the widget and its HTML, so that the EduHack blogs don't change. The blogs are no longer created.
 */

defined('ABSPATH') || exit;

const EDUHACK_FILE = __FILE__;
const EDUHACK_VERSION = '2.0';

require_once __DIR__ . '/includes/meta.php';
require_once __DIR__ . '/includes/project.php';
require_once __DIR__ . '/includes/widget.php';

add_action('init', static fn() => load_plugin_textdomain(
    'eduhack-projects-widget',
    false,
    dirname(plugin_basename(EDUHACK_FILE)) . '/languages'
));
