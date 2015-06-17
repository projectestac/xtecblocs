<?php

/*
Plugin Name: Xtec Settings
Plugin URI:
Description: This plugin adds various site modifications.
Version: 1.1
Author: Francesc Bassas i Bullich
Author URI:
*/

add_filter('allow_password_reset', 'xtec_settings_allow_password_reset', 10, 2);
add_filter('show_password_fields', 'xtec_settings_show_password_fields');
add_filter('pre_comment_user_ip', 'xtec_settings_pre_comment_user_ip');
add_action('wp_head', 'xtec_settings_wp_head');
add_filter('vvq_defaultsettings', 'xtec_settings_vvq_defaultsettings');

/**
 * Deny password reset.
 *
 * @param bool $b
 * @param int $userid The ID of a user.
 * @return WP_Error.
 */
function xtec_settings_allow_password_reset($b, $userid)
{
        // XTEC ********** Afegit -> Let no ldap users to restore their passwords
        // 2015.06.17 @jcaballero
        //NEW
        //**********FI
        $user = get_user_by( 'id', $userid );
        // XTEC ********** Modificat -> Let no ldap users to restore their passwords
        // 2015.06.17 @jcaballero
        if(strlen($user->user_login)<9){
            $error = new WP_Error('no_password_reset',"<strong>No és possible reinicialitzar la contrasenya.</strong>");
            $error->add('no_password_reset',"Si sou un usuari/ària de la XTEC i heu perdut la vostra contrasenya podeu visitar el següent <a href='http://xtec.cat/at_usuari/gestusu/identificacio/'>enllaç</a>.");
            $error->add('no_password_reset',"En cas que no sigueu un usuari/ària de la XTEC i hàgiu perdut la vostra contrasenya, us haureu de posar en contacte amb l'usuari/ària que us va donar d'alta al servei de blocs.");
	return $error;
        }else{
            return true;
        }
        //************ ORIGINAL
	/*$error = new WP_Error('no_password_reset',"<strong>No és possible reinicialitzar la contrasenya.</strong>");
            $error->add('no_password_reset',"Si sou un usuari/ària de la XTEC i heu perdut la vostra contrasenya podeu visitar el següent <a href='http://xtec.cat/at_usuari/gestusu/identificacio/'>enllaç</a>.");
            $error->add('no_password_reset',"En cas que no sigueu un usuari/ària de la XTEC i hàgiu perdut la vostra contrasenya, us haureu de posar en contacte amb l'usuari/ària que us va donar d'alta al servei de blocs.");
	return $error;*/
        //**********FI
}

/**
 * Determine if user can view the password fields.
 *
 * @return bool True if the user can view the password fields.
 */
function xtec_settings_show_password_fields()
{
	if (!is_super_admin()) { return false; }
	else { return true; }
}

/**
 *  Applied to the comment author's IP address prior to saving the comment in the database.
 *
 * @return string The comment author's IP address.
 */
function xtec_settings_pre_comment_user_ip()
{
	if ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) )
	{
		$X_FORWARDED_FOR=explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
		$REMOTE_ADDR=trim($X_FORWARDED_FOR[0]); //take the first element in the array
	} else {
		$REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
	}
	return $REMOTE_ADDR;
}

/**
 * Prints meta info.
 */
function xtec_settings_wp_head()
{
	echo sprintf("<meta name=\"DC.Title\" content=\"%s\"/>\n", get_bloginfo('title'));
	echo sprintf("<meta name=\"DC.Creator\" content=\"%s\"/>\n", get_user_by_email(get_bloginfo('admin_email'))->user_login);
	echo sprintf("<meta name=\"DC.Subject\" scheme=\"eo\" content=\"%s\"/>\n", get_bloginfo('description'));
	echo sprintf("<meta name=\"DC.Language\" content=\"%s\"/>\n", get_bloginfo('language'));
}

/**
 * Customize default Viper's Video Quicktags settings .
 *
 * @param array $defaults Default settings array.
 * @return array Default settings array modified.
 */
function xtec_settings_vvq_defaultsettings($defaults)
{
	$defaults['dailymotion']['button'] = 0;
	$defaults['veoh']['button'] = 0;
	$defaults['bliptv']['button'] = 0;
	$defaults['flv']['button'] = 1;
	return $defaults;
}