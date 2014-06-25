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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true); //Added by WP-Cache Manager
define('WPCACHEHOME', '/dades/xtec_blocs/html/wp-content/plugins/wp-super-cache/'); //Added by WP-Cache Manager
define('DB_NAME', 'xtec_blocs');

/** MySQL database username */
define('DB_USER', 'xtec_blocs');

/** MySQL database password */
define('DB_PASSWORD', 'ixtecb');

/** MySQL hostname */
define('DB_HOST', 'pdb-int:3311');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */

define('AUTH_KEY',         'c90e74854e02bb6e9bd8caff6ae7237208a40f46947f07760b9fada9ba79ed6c');
define('SECURE_AUTH_KEY',  'd568e966636062194c30acd92502c32b5b4dbc3908d69ebd0ac2b5d9bf290717');
define('LOGGED_IN_KEY',    '745216b626336c083fb354589130301f15d430400c80725462d41fc5e3f77bd6');
define('NONCE_KEY',        '3739ac1292b96e91beef28eaeffc69aa58926ddf4580d0a8c3e98fcd182eeb63');
define('AUTH_SALT',        'e016f9d3b10a2cde55385a17b3f9a076432221ab15046b29815162ee131a20b9');
define('SECURE_AUTH_SALT', '60f5125208d29cd21e3e859c2477ea2577357af37c9c9323eaa98212fa02d53c');
define('LOGGED_IN_SALT',   '81263e85d701df603308b9e3bba4057dadd69270e5ef9aaf97b82636c64677dd');
define('NONCE_SALT',       '+u/w5/`5nL2u9uSBR:*55OSp.RyS,~z{y-} Mp!6M;#%hd/Dj(xwEp9L]>`8`?Y');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', 'ca');

/** HyperDB settings */
// HyperDB databases prefix
define('DB_PREFIX','xtec_blocs_');
define('DB_NUMS',10); /** HyperDB additional databases */

/** Sets RSS fetch time out */
define('MAGPIE_FETCH_TIME_OUT', 10); // WordPress default value is 2

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

define('WP_ALLOW_MULTISITE', true);

//define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'pwc-int.educacio.intranet');
define('PATH_CURRENT_SITE', '/xtec_blocs/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/**
 * True if FRM environment, false or undefined otherwise.
 */
//define('IS_FRM', true);

/**
 * HTTPS config.
 */
define('FORCE_SSL_LOGIN', true);
//define('FORCE_SSL_ADMIN', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

