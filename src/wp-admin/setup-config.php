<?php
/**
 * Retrieves and creates the wp-config.php file.
 *
 * The permissions for the base directory must allow for writing files in order
 * for the wp-config.php to be created using this page.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * We are installing.
 *
 * @package WordPress
 */
define('WP_INSTALLING', true);

/**
 * We are blissfully unaware of anything.
 */
define('WP_SETUP_CONFIG', true);

/**
 * Disable error reporting
 *
 * Set this to error_reporting( E_ALL ) or error_reporting( E_ALL | E_STRICT ) for debugging
 */
error_reporting(0);

/**#@+
 * These three defines are required to allow us to use require_wp_db() to load
 * the database class while being wp-content/db.php aware.
 * @ignore
 */
define('ABSPATH', dirname(dirname(__FILE__)).'/');
define('WPINC', 'wp-includes');
define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
define('WP_DEBUG', false);
/**#@-*/

require_once(ABSPATH . WPINC . '/load.php');
require_once(ABSPATH . WPINC . '/compat.php');
require_once(ABSPATH . WPINC . '/functions.php');
require_once(ABSPATH . WPINC . '/class-wp-error.php');
require_once(ABSPATH . WPINC . '/version.php');

if (!file_exists(ABSPATH . 'wp-config-sample.php'))
	wp_die('Cal un fitxer wp-config-sample.php per a procedir. Pugeu aquest fitxer altre cop des de la instal&middot;aci&oacute; del WordPress.');

$configFile = file(ABSPATH . 'wp-config-sample.php');

// Check if wp-config.php has been created
if (file_exists(ABSPATH . 'wp-config.php'))
	wp_die("<p>El fitxer 'wp-config.php' ja existeix. Si necessiteu reiniciar qualsevol opció de la configuració que hi ha en aquest fitxer, esborreu-lo primer. Podeu intentar <a href='install.php'>instal·lar ara</a>.</p>");

// Check if wp-config.php exists above the root directory but is not part of another install
if (file_exists(ABSPATH . '../wp-config.php') && ! file_exists(ABSPATH . '../wp-settings.php'))
	wp_die("<p>El fitxer 'wp-config.php' ja existeix un nivell per sobre de la instal·lació del WordPress. Si necessiteu reiniciar qualsevol opció de la configuració que hi ha en aquest fitxer, esborreu-lo primer. Podeu intentar <a href='install.php'>instal·lar ara</a>.</p>");

if ( version_compare( $required_php_version, phpversion(), '>' ) )
	wp_die( sprintf( /*WP_I18N_OLD_PHP*/'El servidor està executant la versió %1$s del PHP però el WordPress necessita almenys la versió %2$s.'/*/WP_I18N_OLD_PHP*/, phpversion(), $required_php_version ) );

if ( !extension_loaded('mysql') && !file_exists(ABSPATH . 'wp-content/db.php') )
	wp_die( /*WP_I18N_OLD_MYSQL*/'A la vostra instal·lació del PHP sembla que li falta el connector MySQL que necessita el WordPress.'/*/WP_I18N_OLD_MYSQL*/ );

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;

/**
 * Display setup wp-config.php file header.
 *
 * @ignore
 * @since 2.3.0
 * @package WordPress
 * @subpackage Installer_WP_Config
 */
function display_header() {
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WordPress &rsaquo; Setup Configuration File</title>
<link rel="stylesheet" href="css/install.css" type="text/css" />

</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
<?php
}//end function display_header();

switch($step) {
	case 0:
		display_header();
?>

<p>Benvinguda, benvingut al WordPress. Abans de comen&ccedil;ar, necessitem alguna informaci&oacute; sobre la base de dades. Haureu de con&egrave;ixer aix&ograve; per tal de procedir.</p>
<ol>
	<li>Nom de la base de dades</li>
	<li>Nom d'usuari en la base de dades</li>
	<li>Contrasenya de l'usuari en la base de dades</li>
	<li>Nom del host de la base de dades</li>
	<li>Prefix de les taules (si voleu tenir m&eacute;s d'un WordPress a la mateixa base de dades) </li>
</ol>
<p><strong>Si per algun motiu la creaci&oacute; autom&agrave;tica del fitxer no funcion&eacute;s, no us preocupeu. Tot el que fem aqu&iacute; &eacute;s escriure les informacions de la base de dades en el fitxer de configuraci&oacute;. Tamb&eacute; podeu simplement obrir <code>wp-config-sample.php</code> amb un editor de textos, escriure les dades, i desar-ho sota el nom <code>wp-config.php</code>. </strong></p>
<p>Molt probablement heu rebut aquestes informacions del vostre prove&iuml;dor d'hostatge. Si no teniu les dades haureu de contactar amb ell abans de continuar. Si esteu a punt&hellip;</p>

<p class="step"><a href="setup-config.php?step=1<?php if ( isset( $_GET['noapi'] ) ) echo '&amp;noapi'; ?>" class="button">Som-hi!</a></p>
<?php
	break;

	case 1:
		display_header();
	?>
<form method="post" action="setup-config.php?step=2">
	<p>Aqu&iacute; sota haur&iacute;eu d'introduir els detalls de connexi&oacute; amb la base de dades. Si no n'esteu segurs, contacteu amb el vostre prove&iuml;dor d'hostatge. </p>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="dbname">Nom de la base de dades</label></th>
			<td><input name="dbname" id="dbname" type="text" size="25" value="wordpress" /></td>
			<td>El nom de la base de dades en la qual voleu usar el WordPress. </td>
		</tr>
		<tr>
			<th scope="row"><label for="uname">Nom d'usuari</label></th>
			<td><input name="uname" id="uname" type="text" size="25" value="nom_usuari" /></td>
			<td>El nom d'usuari al MySQL</td>
		</tr>
		<tr>
			<th scope="row"><label for="pwd">Contrasenya</label></th>
			<td><input name="pwd" id="pwd" type="text" size="25" value="contrasenya" /></td>
			<td>...i la contrasenya del MySQL.</td>
		</tr>
		<tr>
			<th scope="row"><label for="dbhost">Nom del host de la base de dades</label></th>
			<td><input name="dbhost" id="dbhost" type="text" size="25" value="localhost" /></td>
			<td>Haur&iacute;eu de poder aconseguir aquesta informació del vostre servidor web, si <code>localhost</code> no funciona.</td>
		</tr>
		<tr>
			<th scope="row"><label for="prefix">Prefix de les taules</label></th>
			<td><input name="prefix" id="prefix" type="text" id="prefix" value="wp_" size="25" /></td>
			<td>Si voleu tenir m&uacute;ltiples instal&middot;lacions del WordPress en una &uacute;nica base de dades, canvieu-ho.</td>
		</tr>
	</table>
	<?php if ( isset( $_GET['noapi'] ) ) { ?><input name="noapi" type="hidden" value="true" /><?php } ?>
	<p class="step"><input name="submit" type="submit" value="Envia" class="button" /></p>
</form>
<?php
	break;

	case 2:
	$dbname  = trim($_POST['dbname']);
	$uname   = trim($_POST['uname']);
	$passwrd = trim($_POST['pwd']);
	$dbhost  = trim($_POST['dbhost']);
	$prefix  = trim($_POST['prefix']);
	if ( empty($prefix) )
		$prefix = 'wp_';

	// Validate $prefix: it can only contain letters, numbers and underscores
	if ( preg_match( '|[^a-z0-9_]|i', $prefix ) )
		wp_die( /*WP_I18N_BAD_PREFIX*/'<strong>ERROR</strong>: "El prefix de les taules" solament poden contindre números, lletres i guions baixos.'/*/WP_I18N_BAD_PREFIX*/ );

	// Test the db connection.
	/**#@+
	 * @ignore
	 */
	define('DB_NAME', $dbname);
	define('DB_USER', $uname);
	define('DB_PASSWORD', $passwrd);
	define('DB_HOST', $dbhost);
	/**#@-*/

	// We'll fail here if the values are no good.
	require_wp_db();
	if ( ! empty( $wpdb->error ) ) {
		$back = '<p class="step"><a href="setup-config.php?step=1" onclick="javascript:history.go(-1);return false;" class="button">Torneu-ho a provar</a></p>';
		wp_die( $wpdb->error->get_error_message() . $back );
	}

	// Fetch or generate keys and salts.
	$no_api = isset( $_POST['noapi'] );
	require_once( ABSPATH . WPINC . '/plugin.php' );
	require_once( ABSPATH . WPINC . '/l10n.php' );
	require_once( ABSPATH . WPINC . '/pomo/translations.php' );
	if ( ! $no_api ) {
		require_once( ABSPATH . WPINC . '/class-http.php' );
		require_once( ABSPATH . WPINC . '/http.php' );
		wp_fix_server_vars();
		/**#@+
		 * @ignore
		 */
		function get_bloginfo() {
			return ( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . str_replace( $_SERVER['PHP_SELF'], '/wp-admin/setup-config.php', '' ) );
		}
		/**#@-*/
		$secret_keys = wp_remote_get( 'https://api.wordpress.org/secret-key/1.1/salt/' );
	}

	if ( $no_api || is_wp_error( $secret_keys ) ) {
		$secret_keys = array();
		require_once( ABSPATH . WPINC . '/pluggable.php' );
		for ( $i = 0; $i < 8; $i++ ) {
			$secret_keys[] = wp_generate_password( 64, true, true );
		}
	} else {
		$secret_keys = explode( "\n", wp_remote_retrieve_body( $secret_keys ) );
		foreach ( $secret_keys as $k => $v ) {
			$secret_keys[$k] = substr( $v, 28, 64 );
		}
	}
	$key = 0;

	foreach ($configFile as $line_num => $line) {
		switch (substr($line,0,16)) {
			case "define('DB_NAME'":
				$configFile[$line_num] = str_replace("elnomdelabasededades", $dbname, $line);
				break;
			case "define('DB_USER'":
				$configFile[$line_num] = str_replace("'elnomdusuari'", "'$uname'", $line);
				break;
			case "define('DB_PASSW":
				$configFile[$line_num] = str_replace("'latevacontrasenya'", "'$passwrd'", $line);
				break;
			case "define('DB_HOST'":
				$configFile[$line_num] = str_replace("localhost", $dbhost, $line);
				break;
			case '$table_prefix  =':
				$configFile[$line_num] = str_replace('wp_', $prefix, $line);
				break;
			case "define('AUTH_KEY":
			case "define('SECURE_A":
			case "define('LOGGED_I":
			case "define('NONCE_KE":
			case "define('AUTH_SAL":
			case "define('SECURE_A":
			case "define('LOGGED_I":
			case "define('NONCE_SA":
				$configFile[$line_num] = str_replace('put your unique phrase here', $secret_keys[$key++], $line );
				break;
		}
	}
	if ( ! is_writable(ABSPATH) ) :
		display_header();
?>
<p>No es pot escriure en el fitxer <code>wp-config.php</code>.</p>
<p>Podeu crear un fitxer <code>wp-config.php</code> manualment i enganxar el seg&uuml; codi en ell.</p>
<textarea cols="98" rows="15" class="code"><?php
		foreach( $configFile as $line ) {
			echo htmlentities($line, ENT_COMPAT, 'UTF-8');
		}
?></textarea>
<p>Despr&eacute;s de fer aix&ograve;, feu clic en "Instal·la""</p>
<p class="step"><a href="install.php" class="button">Instal·la</a></p>
<?php
	else :
		$handle = fopen(ABSPATH . 'wp-config.php', 'w');
		foreach( $configFile as $line ) {
			fwrite($handle, $line);
		}
		fclose($handle);
		chmod(ABSPATH . 'wp-config.php', 0666);
		display_header();
?>
<p>Molt b&eacute;! Ja heu fet aquesta part de la instal&middot;laci&oacute;. Ara el WordPress pot comunicar-se amb la base de dades. Si esteu a punt, passeu al seg&uuml;ent pas&hellip;</p>

<p class="step"><a href="install.php" class="button">Executa la instal&middot;laci&oacute;</a></p>
<?php
	endif;
	break;
}
?>
</body>
</html>
