<?php
/** 
 * Les configuracions bàsiques del WordPress.
 *
 * Aquest fitxer té les següents configuracions: configuració de MySQL, prefix de taules,
 * claus secretes, idioma del WordPress i ABSPATH. Trobaràs més informació 
 * al Còdex (en anglès): {@link http://codex.wordpress.org/Editing_wp-config.php Editant
 * el wp-config.php}. Les dades per a configurar MySQL les pots obtenir del
 * teu proveïdor d'hostatjament de web.
 *
 * Aquest fitxer és usat per l'script de creació de wp-config.php durant la
 * instal·lació. No cal que usis el web, pots simplement copiar aquest fitxer
 * sota el nom "wp-config.php" i omplir-lo amb els teus valors.
 *
 * @package WordPress
 */

// ** Configuració de MySQL - Pots obtenir aquestes informacions del teu proveïdor de web ** //
/** El nom de la base de dades per al WordPress */
define('DB_NAME', 'elnomdelabasededades');

/** El teu nom d'usuari a MySQL */
define('DB_USER', 'elnomdusuari');

/** La teva contrasenya a MySQL */
define('DB_PASSWORD', 'latevacontrasenya');

/** Nom del host de MySQL */
define('DB_HOST', 'localhost');

/** Joc de caràcters usat en crear taules a la base de dades. */
define('DB_CHARSET', 'utf8');

/** Tipus d'ordenació en la base de dades. No ho canvïis si tens cap dubte. */
define('DB_COLLATE', '');

/**#@+
 * Claus úniques d'autentificació.
 *
 * Canvia-les per frases úniques diferents!
 * Les pots generar usant el {@link http://api.wordpress.org/secret-key/1.1/ servei de claus secretes de WordPress.org}
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'escriu una frase única teva aquí');
define('SECURE_AUTH_KEY', 'escriu una frase única teva aquí');
define('LOGGED_IN_KEY', 'escriu una frase única teva aquí');
define('NONCE_KEY', 'escriu una frase única teva aquí');
/**#@-*/

/**
 * Prefix de taules per a la base de dades del WordPress.
 *
 * Pots tenir múltiples instal·lacions en una única base de dades usant prefixos
 * diferents. Només xifres, lletres i subratllats!
 */
$table_prefix  = 'wp_';

/**
 * Idioma de localització del WordPress.
 *
 * Modifica això per a canviar l'idioma del WordPress, si no el vols tenir en català.
 * Un fitxer MO corresponent a l'idioma escollit ha de ser present al subdirectori wp-content/languages.
 * Per exemple, copia el fitxer de_DE.mo a wp-content/languages i escriu define ('WPLANG', 'de_DE');
 * per a obtenir la traducció alemanya. O escriu define ('WPLANG', 'ca'); per a
 * veure la versió original americana.
 */
define ('WPLANG', 'ca');

// Això és tot, prou d'editar - que bloguis de gust!

/** Ruta absoluta del directori del Wordpress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Assigna les variables del WordPress vars i fitxers inclosos. */
require_once(ABSPATH . 'wp-settings.php');
?>
