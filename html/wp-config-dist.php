<?php
/**
 * The base configuration for WordPress.
 *
 * This file contains the following configurations: Database settings, Secret keys,
 * Database table prefix, LudicrousDB settings, Multisite settings and ABSPATH.
 *
 * This file is copied to "wp-config.php" during the provisioning of the VM.
 * Fill in the values according to the environment.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

/** WordPress environment type: local, development, staging, production */
const WP_ENVIRONMENT_TYPE = 'local';

/** Google API key */
const WP_GOOGLE_API_KEY = '';

/** Used in xtec-mail extension */
const XTEC_MAIL_IDAPP = 'XTECBLOCS';

/** The name of the database for WordPress */
const DB_NAME = 'xtec_blocs_global';

/** Database username */
const DB_USER = 'root';

/** Database password */
const DB_PASSWORD = 'agora';

/** Database hostname */
const DB_HOST = 'localhost';

/** Database charset to use in creating database tables. */
const DB_CHARSET = 'utf8mb4';

/** The database collate type. Don't change this if in doubt. */
const DB_COLLATE = '';

/** Proxy configuration */
//const WP_PROXY_HOST = '';
//const WP_PROXY_PORT = '';

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
const AUTH_KEY = 'put your unique phrase here';
const SECURE_AUTH_KEY = 'put your unique phrase here';
const LOGGED_IN_KEY = 'put your unique phrase here';
const NONCE_KEY = 'put your unique phrase here';
const AUTH_SALT = 'put your unique phrase here';
const SECURE_AUTH_SALT = 'put your unique phrase here';
const LOGGED_IN_SALT = 'put your unique phrase here';
const NONCE_SALT = 'put your unique phrase here';

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/** LudicrousDB settings (see db-config.php) */
const DB_PREFIX = 'xtec_blocs_'; // LudicrousDB databases prefix
const DB_NUMS = 3; // LudicrousDB additional databases

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
const WP_DEBUG = false;
const WP_DEBUG_DISPLAY = false;

const AUTOMATIC_UPDATER_DISABLED = true;

const WP_ALLOW_MULTISITE = true;

const MULTISITE = true; // If tables wp_1_xxxx are NOT present
// const MULTISITE = false; // If tables wp_1_xxxx ARE present
const SUBDOMAIN_INSTALL = false;
const DOMAIN_CURRENT_SITE = 'blocs-aws.xtec.cat';
const PATH_CURRENT_SITE = '/';
const SITE_ID_CURRENT_SITE = 1;
const BLOG_ID_CURRENT_SITE = 1;

/**
 * Default blog creation theme.
 */
const WP_DEFAULT_THEME = 'twentytwentyfive';

/**
 * HTTPS config.
 */
const FORCE_SSL_ADMIN = true;
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
    $_SERVER['HTTPS'] = 'on';
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
