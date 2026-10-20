<?php

/**
 * Plugin Name:       XTEC Users
 * Description:       XTEC users and local users in XTECBlocs, and limits of the creation of blogs.
 * Version:           2.0
 * Requires at least: 5.5
 * Requires PHP:      8.0
 * Network:           true
 * Text Domain:       xtec-users
 * Domain Path:       /languages
 * Author:            Francesc Bassas i Bullich, Toni Ginard
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Depends on the plugin XTEC LDAP Login, which identifies the XTEC users.
 */

defined('ABSPATH') || exit;

const XTEC_USERS_FILE = __FILE__;

// Minimum length of the passwords of the local users
const XTEC_USERS_MIN_PASSWORD_LENGTH = 8;

// Minimum length of the usernames of the local users. Shorter usernames belong to XTEC users.
const XTEC_USERS_MIN_LOCAL_USERNAME_LENGTH = 9;

// User support service of XTEC, where XTEC users can recover their password
const XTEC_USERS_SUPPORT_URL = 'https://xtec.gencat.cat/ca/at_usuari/';

require_once __DIR__ . '/includes/admin.php';
require_once __DIR__ . '/includes/add-users.php';
require_once __DIR__ . '/includes/manage-users.php';
require_once __DIR__ . '/includes/passwords.php';
require_once __DIR__ . '/includes/permissions.php';
require_once __DIR__ . '/includes/signup.php';
