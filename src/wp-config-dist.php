<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

include_once dirname(__FILE__) . '/wp-includes/xtec/lib.php';

global $isAgora, $isBlocs;

$isAgora = false;
$isBlocs = true;

// ** DB settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true); //Added by WP-Cache Manager
define('WPCACHEHOME', '/dades/blocs/src/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'xtec_blocs_global');

/**
 * DES, INT, ACC, PRO, FRM.
 */
define('ENVIRONMENT', 'DES');


/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', 'agora');

/** Databbase hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**
 * Proxy configuration.
 */
//define('WP_PROXY_HOST', '');
//define('WP_PROXY_PORT', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/** HyperDB settings */
// HyperDB databases prefix
define('DB_PREFIX','xtec_blocs_');
define('DB_NUMS',3); /** HyperDB additional databases */

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

define('AUTOMATIC_UPDATER_DISABLED', true);

define('WP_ALLOW_MULTISITE', true);

define('MULTISITE', true);  // If tables wp_1_xxxx are NOT present
// define('MULTISITE', false);  // If tables wp_1_xxxx ARE present
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE','agora');
define('PATH_CURRENT_SITE', '/blocs/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/**
 * Default blog creation theme.
 */
define('WP_DEFAULT_THEME', 'twentyfourteen');

/**
 * HTTPS config.
 */
define('FORCE_SSL_ADMIN', true);
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && ( $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
    $_SERVER['HTTPS'] = 'on';
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
