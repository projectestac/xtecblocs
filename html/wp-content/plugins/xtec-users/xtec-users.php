<?php

/**
 * Plugin Name:       XTEC Users
 * Description:       Adds XTECBlocs user system with two types of users: XTEC users, validated through LDAP or OAuth,
 *                    and local users. It also limits the creation of blogs to XTEC users and to a maximum per day.
 * Version:           1.2
 * Requires at least: 5.5
 * Requires PHP:      8.0
 * Network:           true
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

require_once __DIR__ . '/includes/admin.php';
require_once __DIR__ . '/includes/add-users.php';
require_once __DIR__ . '/includes/manage-users.php';
require_once __DIR__ . '/includes/permissions.php';
require_once __DIR__ . '/includes/signup.php';
