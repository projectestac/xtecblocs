<?php

/*
Plugin Name: XTEC LDAP Login
Plugin URI:
Description: Overrides the core WordPress authentication method to allow the user authentication and registration through LDAP Server. It also changes the login screen logo and it adds an API function for a web service authentication.
Version: 1.1
Author: Francesc Bassas i Bullich
Author URI:
*/

require_once(ABSPATH . WPINC . '/registration.php');

add_action('network_admin_menu', 'xtec_ldap_login_network_admin_menu');
add_action('login_head','xtec_ldap_login_css');
add_filter('authenticate', 'xtec_ldap_authenticate', 10, 3);

/**
 * Adds plugin network admin menu.
 */
function xtec_ldap_login_network_admin_menu()
{
    add_submenu_page('settings.php', 'LDAP Login', 'LDAP Login', 'manage_network_options', 'ms-ldap-login', 'xtec_ldap_login_network_options');
}

/**
 * Add plugin options form to network administration options form.
 */
function xtec_ldap_login_network_options()
{	
	switch ( $_GET['action'] ) {
		case 'siteoptions':
			if ( $_POST['xtec_ldap_host'] ) {
				$xtec_ldap_host = $_POST['xtec_ldap_host'];
				update_site_option('xtec_ldap_host',$xtec_ldap_host);
			}
			if ( $_POST['xtec_ldap_port'] ) {
				$xtec_ldap_port = $_POST['xtec_ldap_port'];
				update_site_option('xtec_ldap_port',$xtec_ldap_port);
			}
			if ( $_POST['xtec_ldap_version'] ) {
				$xtec_ldap_version = $_POST['xtec_ldap_version'];
				update_site_option('xtec_ldap_version',$xtec_ldap_version);
			}
			if ( $_POST['xtec_ldap_base_dn'] ) {
				$xtec_ldap_base_dn = $_POST['xtec_ldap_base_dn'];
				update_site_option('xtec_ldap_base_dn',$xtec_ldap_base_dn);
			}
			if ( $_POST['xtec_ldap_login_type'] ) {
				$xtec_ldap_login_type = $_POST['xtec_ldap_login_type'];
				update_site_option('xtec_ldap_login_type',$xtec_ldap_login_type);
			}
			?>
			<div id="message" class="updated"><p><?php _e( 'Options saved.' ) ?></p></div>
			<?php
		break;
	}
	?>
	<div class="wrap">
		<form method="post" action="?page=ms-ldap-login&action=siteoptions">
			<h2><?php _e('XTEC LDAP Login') ?></h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
			        	<th scope="row"><?php _e('LDAP Host')?></th>
			        	<td><input type="text" name="xtec_ldap_host" value="<?php echo get_site_option('xtec_ldap_host'); ?>" /></td>
			        </tr>         
			        <tr valign="top">
				        <th scope="row"><?php _e('LDAP Port')?></th>
				        <td><input type="text" name="xtec_ldap_port" value="<?php echo get_site_option('xtec_ldap_port'); ?>" /></td>
			        </tr>        
			        <tr valign="top">
				        <th scope="row"><?php _e('LDAP Version')?></th>
				        <td><input type="text" name="xtec_ldap_version" value="<?php echo get_site_option('xtec_ldap_version'); ?>" /></td>
			        </tr>        
			        <tr valign="top">
				        <th scope="row"><?php _e('Base DN')?></th>
				        <td><input type="text" name="xtec_ldap_base_dn" value="<?php echo get_site_option('xtec_ldap_base_dn'); ?>" /></td>
			        </tr>        
			        <tr valign="top">
				        <th scope="row"><?php _e('Validation Type')?></th>
				        <?php
							if( !get_site_option('xtec_ldap_login_type') ) {
								update_site_option( 'xtec_ldap_login_type', 'LDAP' );
							}
						?>
				        <td><input type="radio" name="xtec_ldap_login_type" value="LDAP" <?php if(get_site_option('xtec_ldap_login_type')=="LDAP"){echo 'checked="checked"';}?>" /> LDAP
				        <br>
				        The user is validated through LDAP server. If the user enters for the first time and validates, the application registers it. First attempt to validate as user of LDAP server and then if fails attempt to validate as user of the application.  
				        <br>
				        <br><input type="radio" name="xtec_ldap_login_type" value="Application Data Base" <?php if(get_site_option('xtec_ldap_login_type')=="Application Data Base"){echo 'checked="checked"';}?>" /> Application Data Base
				        <br>
				        The user is validated through Application Data Base.
				        <br>
				        </td>
			        </tr>        
				</tbody>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Desa els canvis"></p>
		</form>
	</div>
<?php
}

/**
 * Include CSS stylesheet.
 */

function xtec_ldap_login_css() {	
	$cssPath = plugin_dir_url(__FILE__) . "xtec-ldap-login-logo.css";
	echo "<link rel=\"stylesheet\" href=\"" . $cssPath	. "\" type=\"text/css\" />";
}

/**
 * Checks a user's login information and it tries to logs them in through LDAP Server or through application database depending on plugin configuration.
 *
 * @param WP_User $user
 * @param string $username User's username
 * @param string $password User's password
 * @return WP_Error|WP_User WP_User object if login successful, otherwise WP_Error object.
 */
function xtec_ldap_authenticate($user, $username, $password)
{
	if ( is_a($user, 'WP_User') ) { return $user; }
	
	remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
	
	if ( empty($username) || empty($password) ) {
		$error = new WP_Error();

		if ( empty($username) )
			$error->add('empty_username', __('<strong>ERROR</strong>: The username field is empty.'));

		if ( empty($password) )
			$error->add('empty_password', __('<strong>ERROR</strong>: The password field is empty.'));

		return $error;
	}
	
	$userdata = get_user_by('login', $username);

	if ( !$userdata || (strtolower($userdata->user_login) != strtolower($username)) ) 
	{
		//No user, we attempt to create one
		$ldap = ldap_connect(get_site_option('xtec_ldap_host'), get_site_option('xtec_ldap_port')) 
		or die("Can't connect to LDAP server.");
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, get_site_option('xtec_ldap_version'));
		
// XTEC *********** MODIFICAT -> Addaptation to the new ldap server
// 2012.06.13 @mmartinez
		$ldapbind = @ldap_bind($ldap, 'cn=' . $username . ',' . get_site_option('xtec_ldap_base_dn'), $password);
// ************* ORIGINAL
		//$ldapbind = @ldap_bind($ldap, 'uid=' . $username . ',' . get_site_option('xtec_ldap_base_dn'), $password);
// ************* FI

		if ($ldapbind == true) {
// XTEC *********** MODIFICAT -> Addaptation to the new ldap server
// 2012.06.13 @mmartinez
			$result = ldap_search($ldap, get_site_option('xtec_ldap_base_dn'), '(cn=' . $username . ')', array(LOGIN, 'sn', 'givenname', 'mail'));
// ************ ORIGINAL
			//$result = ldap_search($ldap, get_site_option('xtec_ldap_base_dn'), '(uid=' . $username . ')', array(LOGIN, 'sn', 'givenname', 'mail'));
// ************ FI
			$ldapuser = ldap_get_entries($ldap, $result);
			if ($ldapuser['count'] == 1) {
			//Create user using wp standard include
			$userData = array(
				'user_pass'     => $password,
				'user_login'    => $username,
				'user_nicename' => $ldapuser[0]['givenname'][0].' '.$ldapuser[0]['sn'][0],
				'user_email'    => $ldapuser[0]['mail'][0],
				'display_name'  => $ldapuser[0]['givenname'][0].' '.$ldapuser[0]['sn'][0],
				'first_name'    => $ldapuser[0]['givenname'][0],
				'last_name'     => $ldapuser[0]['sn'][0],
				'role'		=> strtolower('subscriber')
				);
					
				//Get ID of new user
				wp_insert_user($userData);
			}
		}
		else {
			do_action( 'wp_login_failed', $username );				
			return new WP_Error('invalid_username', '<strong>ERROR</strong>: Aquest nom d\'usuari i contrasenya no corresponen a cap usuari XTEC.');
		}
	}

	$userdata = get_user_by('login', $username);

	if ( is_multisite() ) {
		// Is user marked as spam?
		if ( 1 == $userdata->spam)
			return new WP_Error('invalid_username', __('<strong>ERROR</strong>: Your account has been marked as a spammer.'));

		// Is a user's blog marked as spam?
		if ( !is_super_admin( $userdata->ID ) && isset($userdata->primary_blog) ) {
			$details = get_blog_details( $userdata->primary_blog );
			if ( is_object( $details ) && $details->spam == 1 )
				return new WP_Error('blog_suspended', __('Site Suspended.'));
		}
	}
	
	$userdata = apply_filters('wp_authenticate_user', $userdata, $password);
	if ( is_wp_error($userdata) )
		return $userdata;
	
	if ( get_site_option('xtec_ldap_login_type') == "LDAP" ) {
		// Attempt to validate through LDAP
		$ldap = ldap_connect(get_site_option('xtec_ldap_host'), get_site_option('xtec_ldap_port')) 
		or die("Can't connect to LDAP server.");
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, get_site_option('xtec_ldap_version'));
// XTEC *********** MODIFICAT -> Addaptation to the new ldap server
// 2012.06.13 @mmartinez
		$ldapbind = @ldap_bind($ldap, 'cn=' . $username . ',' . get_site_option('xtec_ldap_base_dn'), $password);
// ************ ORIGINAL
		//$ldapbind = @ldap_bind($ldap, 'uid=' . $username . ',' . get_site_option('xtec_ldap_base_dn'), $password);
// ************ FI
		if ($ldapbind == false) {
			// LDAP validation fails, check if it is an administrator who wants to validate
			//$admins = get_site_option('site_admins');
			//foreach ($admins as $admin){
			//	if ($admin == $username) {
			//		if ( !wp_check_password($password, $user->user_pass, $user->ID) ) {
			//			do_action( 'wp_login_failed', $username );
			//			return new WP_Error('incorrect_password', __('<strong>ERROR</strong>: Incorrect password.'));
			//		}					
			//		return new WP_User($user->ID);					
			//	}
			//}
			// LDAP validation fails, check if it is a user of the application
			if ( !wp_check_password($password, $userdata->user_pass, $userdata->ID) ) {
				do_action( 'wp_login_failed', $username );
				return new WP_Error('incorrect_password', __('<strong>ERROR</strong>: Incorrect password.'));
			}

// XTEC *********** MODIFICAT -> Els usuaris xtec (<=8) que no hagin validat per LDAP no poden entrar (excepte 'admin' i @edu365.cat)
// 2013.02.26 @jmiro227

                        $user_info = get_userdatabylogin($username);

			if ( ( strlen($username) > 8 ) || ( $username == 'admin' ) || preg_match( "/^.+@edu365\.cat$/", $user_info -> user_email ) )  {return new WP_User($userdata->ID);}
                        else {return new WP_Error('incorrect_password', __('<strong>ERROR</strong>: Incorrect password.'));}

// *********** ORIGINAL
			//return new WP_User($userdata->ID);
// *********** FI
		}
		else { // $ldapbind == true
			// Check if the password has changed
			if ( !wp_check_password($password, $userdata->user_pass, $userdata->ID) ) {
				wp_update_user(array("ID" => $userdata->ID,"user_pass" => $password));
			}
// XTEC *********** MODIFICAT -> Addaptation to the new ldap server
// 2012.06.13 @mmartinez
			$result = ldap_search($ldap, get_site_option('xtec_ldap_base_dn'), '(cn=' . $username . ')', array('mail'));
// *********** ORIGINAL
			//$result = ldap_search($ldap, get_site_option('xtec_ldap_base_dn'), '(uid=' . $username . ')', array('mail'));
// *********** FI
			$ldapuser = ldap_get_entries($ldap, $result);
			if ($ldapuser['count'] == 1) {
				$domain = strstr($ldapuser[0]['mail'][0], '@');
				if ($domain == '@xtec.cat') {
					// it's an XTEC user
					update_user_meta($userdata->ID,'xtec_user_creator','LDAP_XTEC');			
				}
			}
			
			return new WP_User($userdata->ID);			
		}
	}
	
	else { // get_site_option('xtec_ldap_login_type') == "Application Data Base")
		if ( !wp_check_password($password, $userdata->user_pass, $userdata->ID) )
			return new WP_Error( 'incorrect_password', sprintf( __( '<strong>ERROR</strong>: The password you entered for the username <strong>%1$s</strong> is incorrect. <a href="%2$s" title="Password Lost and Found">Lost your password</a>?' ),
			$username, site_url( 'wp-login.php?action=lostpassword', 'login' ) ) );
	
		$user =  new WP_User($userdata->ID);
		return $user;
	}
}

/**
 * Checks a user's login information and it tries to authenticate them in through LDAP Server or through application database if it checks out.
 *
 * @param string $username User's username
 * @param string $password User's password
 * @return '1$$usermail' if user's XTEC user, '2$$usermail' if user's other, '101' if username's empty, '102' if password's empty, '103' if username's incorrect and '104' if password's incorrect.
 */
function xtec_authenticate($username, $password)
{
	// 1	XTEC USER
	// 2	OTHER USER
	
	// 101	Empty username
	// 102	Empty password
	// 103	Incorrect username
	// 104	Incorrect password 
	
	if ( '' == $username ) { return 101; }

	if ( '' == $password ) { return 102; }
	
	$user = get_userdatabylogin($username);

	if ( !$user || (strtolower($user->user_login) != strtolower($username) ) ) { return 103; }

	if ( !wp_check_password($password, $user->user_pass, $user->ID) ) { return 104; }
	else { 
		if ( get_user_meta($user->ID,'xtec_user_creator',true) == 'LDAP_XTEC' ) { return 1 . '$$' . $user->user_email; }
		else { return 2 . '$$' . $user->user_email; }
	}			
}
