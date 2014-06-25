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
define( 'WPCACHEHOME', '/srv/www/blocs/src/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'xtec_blocs_global');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'agora');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         'V *EE(.5^J1Q(RD3X,dA8:O!p[9sID_:yKec+~QKmHKqWV2*&3~qm5`/7aa8<S{ ');
define('SECURE_AUTH_KEY',  'q>$H/e1)b?hM**GH~pmieUwMH@%b9ktp8+..A C1/@KxG?Da6d;5qB*`)f>on$Cu');
define('LOGGED_IN_KEY',    '-p8]1f)EC9&H(j_Cb`-N}Da+rt;Vt)*3f>U_yXXfmngb<IXQKdvIradb`]=G]G;c');
define('NONCE_KEY',        'H$!voDN%o:68i8m2Y^](aDIXj,nzQ)YqMZI^-28UuJF3&DfGh[_l#1;X*ScTRU b');
define('AUTH_SALT',        '8%3?:BlMuVZT__AkBSQ#mR)?p]<njwt(0U`W&wcnVv.!0*L(]4g9Ocix/`~W Nj4');
define('SECURE_AUTH_SALT', ';1xgm?A}B.L>;2.4FRF0<0PQYR>t/v{GD}1i2G M~RU[#iE6.(NNP}d%X<xodA0(');
define('LOGGED_IN_SALT',   '=-yzXm=}C9@eG9;V_f4g{NMv5z,J+5]ZtbBqN?P~?aK:WG7%vdf7BESxcIGkSz9l');
define('NONCE_SALT',       '> k~>vto-|mBWdp3l!Uwfomp8A?PKk2+6FmF,>;|HO[l!gH.v13pKb~$AP/ _c(/');

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

//XTEC ************ AFEGIT - Constants de configuració d'XTECBlocs

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

define('WP_ALLOW_MULTISITE', true);

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE','agora');
define('PATH_CURRENT_SITE', '/blocs/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/**
 * True if FRM environment, false or undefined otherwise.
 */
//define('IS_FRM', true);

/**
 * HTTPS config.
 */
//define('FORCE_SSL_LOGIN', true);
//define('FORCE_SSL_ADMIN', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

