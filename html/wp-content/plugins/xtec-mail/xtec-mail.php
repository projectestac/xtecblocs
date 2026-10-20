<?php

/**
 * Plugin Name:       XTEC Mail
 * Description:       Sends the mails of WordPress through the service EnviaCorreu of the Departament d'Educació.
 * Version:           4.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Network:           true
 * Text Domain:       xtec-mail
 * Domain Path:       /languages
 * Author:            Francesc Bassas i Bullich, Toni Ginard
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * The plugin is configured in wp-config.php with the constants XTEC_MAIL_IDAPP (identifier of the application in the
 * service) and XTEC_MAIL_ENVIRONMENT (INT, PRE or PRO). Without them, WordPress sends the mails itself.
 */

defined('ABSPATH') || exit;

const XTEC_MAIL_FILE = __FILE__;

require_once __DIR__ . '/includes/mail.php';
require_once __DIR__ . '/includes/log.php';
require_once __DIR__ . '/includes/admin.php';
