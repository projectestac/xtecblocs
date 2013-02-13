<?php

/*
Plugin Name: XTEC Users
Plugin URI:
Description: Adds XTECBlocs user system with two types of users: LDAP users from XTEC and the others.
Dependencies: XTEC LDAP Login
Version: 1.1
Author: Francesc Bassas i Bullich
Author URI:
*/

add_action('admin_menu', 'xtec_users_menu');

/**
 * Adds plugin menu.
 */
function xtec_users_menu()
{   
	if ( xtec_current_user_can('add_users') ) {
		$add_users = add_submenu_page('users.php','Afegeix','Afegeix','administrator','xtec-add-users','xtec_add_users');
		remove_submenu_page('users.php','user-new.php');
		add_action( "admin_print_scripts-$add_users", 'xtec_users_js_admin_head' );
	}
	if ( xtec_current_user_can('manage_users') ) {
		add_submenu_page('users.php','Gestió d\'usuaris','Gestió d\'usuaris','administrator','xtec-manage-users','xtec_manage_users');
	}	
}

/**
 * Includes JavaScript file and CSS stylesheet.
 */
function xtec_users_js_admin_head()
{
	$plugindir = plugin_dir_url(__FILE__);
	wp_enqueue_script('xtec-users_js', $plugindir . 'xtec-users.js');
	echo "<link rel='stylesheet' href='$plugindir" . "xtec-users.css' type='text/css' />\n";	
}

function xtec_add_users()
{
	global $blog_id;
	
	$errors_step_2=$errors_step_3=$message='';
	$confirm_user_addition=false;
	$username_readonly='';
	
	$display_step_2="style='display:none'";
	$display_check_user=true;
	$display_username_info=true;
	
	$display_step_3_add=false;
	$display_step_3_create_add=false;
	
	$username_info='';
	$username_info_xtec='<small>Introdueix el nom d\'usuari/ària de la XTEC.</small>';
	$username_info_other='<small>El nom d\'usuari/ària ha de tenir un mínim de 9 caràcters.</small>';
	
	$administrator_tag=_x('Administrator', 'User role');
	$editor_tag=_x('Editor', 'User role');
	$author_tag=_x('Author', 'User role');
	$contributor_tag=_x('Contributor', 'User role');
	$subscriber_tag=_x('Subscriber', 'User role');

	if ( isset($_POST['User_Type']) ) {
		$user_type = $_POST['User_Type'];

		$display_step_2='';

		if ( $user_type == 'xtec' ) { $username_info=$username_info_xtec; }
		else if ( $user_type == 'other' ) {	$username_info=$username_info_other; }
	}
	
	if ( isset($_POST['user']) ) {
		$user = $_POST['user'];
	}
	
	// Action - Check user
	if ( isset($_POST['Check_user_x']) ) {
		
		if ( empty($user['username']) ) {
			$errors_step_2 .= "<p>El nom d'usuari/ària està buit. Cal que introduïu un nom d'usuari/ària per poder afegir-lo al vostre bloc.</p>";
		}
		
		else {
			
			$username = esc_html(strtolower($user['username']));
			
			if ( $user_type == 'xtec' ) {
				
				if ( strlen($username) >= 9 ) {
					$errors_step_2 .= "<p>El nom d'usuari/ària de la XTEC que heu introduit no és vàlid.</p>";
				}
				else if ( !username_exists($username) ) {
					$errors_step_2 .= "<p>No s'ha trobat cap usuari/ària XTEC amb aquest nom d'usuari/ària. Recordeu que l'usuari/ària ha d'haver entrat a XTECBlocs almenys una vegada.</p>";
				}
				
				if ( empty($errors_step_2) ) {					
					$user_id = get_userdatabylogin($username)->ID;
					if ( is_user_member_of_blog($user_id, $blog_id) ) {
						$info = "<p>L'usuari/ària <strong>$username</strong> ja és membre d'aquest bloc.</p>";
					}
					else {
						$username_readonly='readonly';
						$display_check_user=false;
						$display_username_info=false;						
						$display_step_3_add=true;
					}
				}
			}
			
			else if ( $user_type == 'other' ) {
				
				if ( strlen($username) < 9 ) {
					$errors_step_2 .= "<p>El nom d'usuari/ària que heu introduit no és vàlid. El nom d'usuari/ària ha de tenir un mínim de 9 caràcters.</p>";
				}
				
				if ( !xtec_is_valid_username($username) ) {
					$errors_step_2 .= "<p>Només s'admeten els caràcters següents: lletres, números, punts, guions i barres baixes.</p>";
				}
				
				if ( empty($errors_step_2) ) {
					
					$userdata = get_userdatabylogin($username);
					
					if ( !$userdata ) {
						// L'usuari no existeix
						$username_readonly='readonly';
						$display_check_user=false;
						$display_username_info=false;
						$display_step_3_create_add=true;
					}
					else {
						// L'usuari existeix
						$user_id = get_userdatabylogin($username)->ID;

						if ( is_user_member_of_blog($userdata->ID, $blog_id) ) { // OK
							$info = "<p>L'usuari/ària <strong>$username</strong> ja és membre d'aquest bloc.</p>";
						}
						else {
							// l'usuari no pertany a aquest blog i cal confirmar que vulgui afegir-lo
							$username_readonly='readonly';
							$display_check_user=false;
							$display_username_info=false;
							$confirm_user_addition=true;
						}
					}
				}
			}
		}
	}
	
	// Action - Confirm user addition
	if ( isset($_POST['Confirm_user_addition']) ) {
		
		$username = $user['username'];
		
		if ( !xtec_is_valid_username($username) ) {
			wp_die(__('Cheatin&#8217; uh?'));
		}
		
		$username_readonly='readonly';
		$display_check_user=false;
		$display_username_info=false;		
		$display_step_3_add=true;
	}
	
	// Action - Add user
	if ( isset($_POST['Add_user']) ) {
		
		$username = $user['username'];
		
		if ( ($user_type == 'other') && (!xtec_is_valid_username($username)) ) {
			wp_die(__('Cheatin&#8217; uh?'));
		}

		$role = $user['role'];
		switch ($role) {
			case 'administrator':
				$role_tag = $administrator_tag;
				break;
			case 'editor':
				$role_tag = $editor_tag;
				break;
			case 'author':
				$role_tag = $author_tag;
				break;
			case 'contributor':
				$role_tag = $contributor_tag;
				break;
			case 'subscriber':
				$role_tag = $subscriber_tag;
				break;
		}
		
		$user_data = get_userdatabylogin($username);		
		
		if ( $user_type == 'xtec' ) {
			$newuser_key = substr(md5($user_data->ID),0,5);
			add_option( 'new_user_' . $newuser_key, array( 'user_id' => $user_data->ID, 'email' => $user_data->user_email, 'role' => $role ) );
			$message = __("Hi,\n\nYou have been invited to join '%s' at\n%s as a %s.\nPlease click the following link to confirm the invite:\n%s\n");
			wp_mail( $user_data->user_email, sprintf( __( '[%s] Joining confirmation' ), get_option( 'blogname' ) ),  sprintf($message, get_option('blogname'), site_url(), $role_tag, site_url("/newbloguser/$newuser_key/")));
			$info .= "<p>S'ha enviat una invitació al correu electrònic de l'usuari/ària <strong>$username</strong> per afegir-se com a <strong>$role_tag</strong> d'aquest bloc.</p>";
			$info .= "<p>Quan l'usuari/ària confirmi la invitació s'afegirà com a membre del bloc.</p>";
		}
		
		else if ( $user_type == 'other' ) {			
			add_user_to_blog($blog_id,$user_data->ID,$role);
			$info .= "<p>Heu afegit l'usuari/ària <strong>$username</strong> com a <strong>$role_tag</strong> d'aquest bloc.</p>";			
		}
	}

	// Action - Create add user
	if ( isset($_POST['Create_add_user']) ) {
		
		$username = $user['username'];
		
		if ( !xtec_is_valid_username($username) ) {
			wp_die(__('Cheatin&#8217; uh?'));
		}
		
		$password = $user['password'];
		if (empty($user['email'])) { $email = $username . '@' . $username . '.blocs'; }
		else { $email = $user['email']; } 
		$role = $user['role'];
		switch ($role) {
			case 'administrator':
				$role_tag = $administrator_tag;
				break;
			case 'editor':
				$role_tag = $editor_tag;
				break;
			case 'author':
				$role_tag = $author_tag;
				break;
			case 'contributor':
				$role_tag = $contributor_tag;
				break;
			case 'subscriber':
				$role_tag = $subscriber_tag;
				break;
		}

		$username_readonly='readonly';
		$display_check_user=false;
		$display_username_info=false;	
		$display_step_3_create_add=true;		
		
		if ( empty($user['password']) ) {
			// Check if password is empty
			$errors_step_3 .= "<p>Heu d'introduir la contrasenya per afegir un nou usuari/ària.</p>";
		}
		
		if ( !is_email($email) ) { 
			// Check if the email address is valid
			$errors_step_3 .= '<p>El correu electrònic que heu introduit no és vàlid.</p>';
		}
		else if ( email_exists($email) ) {			
			// Check if the email address has been used already
			$errors_step_3 .= "<p>El correu electrònic que heu introduit ja s'està utilitzant.</p>";
		}

//XTEC ************ AFEGIT - Control de l'us d'@ xtec en funció del tipus d'usuari
//2011.02.13 @jmiro227

		else if ( !is_valid_email($email,$user_type) ) {			
			// Check if the email address is valid according user typology
			$errors_step_3 .= "<p>El correu electrònic que heu introduit no és vàlid.</p>";
		}

//************ FI
		
		if ( empty($errors_step_3) ) {
			// Create user
			$user_id = wpmu_create_user($username,$password,$email);

			if( !$user_id ) {
				// User already exist
				$info = "<p>Mentre intentàveu crear l'usuari/ària algú us ha passat al davant.</p>";
			}
			else {
				// User created, add creator
				global $current_user;
				wp_get_current_user();
				update_user_meta($user_id,'xtec_user_creator',$current_user->user_login);
				$info = "<p>Heu donat d'alta al servei de blocs l'usuari/ària <strong>$username</strong> amb contrasenya <strong>$password</strong>. Feu arribar a l'usuari/ària el nom d'usuari/ària i la contrasenya per a què pugui accedir al servei de blocs.</p>";
				// Add user to blog
				add_user_to_blog($blog_id,$user_id,$role);
				$info .= "<p>Heu afegit l'usuari/ària <strong>$username</strong> com a <strong>$role_tag</strong> d'aquest bloc.</p>";
			}
		}
		else {
			if (empty($user['email'])) { $email = ''; }
		}
	}

	// Action - Discard
	if (isset($_POST['Discard']) ) {}
	
	?>
	
	<div class="wrap">
	
		<?php screen_icon(); ?>
	
		<h2>Afegeix un nou usuari/ària</h2>

		<?php
		if (!empty($info)) {
			echo "<div id='info' class='updated fade' style='background-color: rgb(255, 251, 204); '>";
				$info .= "<p><a href='?page=xtec-add-users'>Torna a Afegeix un nou usuari/ària</a></p>";
				echo $info ;
			echo "</div>";
		}
		else {
			?>
		
			<div id='about' class='updated fade' style='background-color: rgb(255, 251, 204); '>
				<p>Podeu afegir dos tipus d'usuaris al vostre bloc:</p>
				<ul>
					<li>1. Usuaris XTEC
						<br><i>Podeu afegir usuaris de la XTEC al vostre bloc. Per fer-ho hauran d'haver-se validat almenys una vegada al Portal d'XTECBlocs.</i>
					</li>
					<li>2. Altres usuaris
						<br><i>Podeu crear i afegir nous usuaris, o bé afegir usuaris existents al vostre bloc.</i>
					</li>
				</ul>				
			</div>
	
			<form action="?page=xtec-add-users" method="post">
				
				<br/>
				
				<div id='step_1'>
					<p><span class=num>1.</span> <strong>Quin tipus d'usuari/ària voleu afegir?</strong></p>
					<p>
						<input	type='radio' 
								onchange="display_step_2('xtec')"
								name='User_Type'
								value='xtec'
								<?php if ($user_type=='xtec') { echo 'checked'; } ?>
						>Usuari/ària XTEC</input>
						<input	type='radio'
								onchange="display_step_2('other')"
								name='User_Type'
								value='other'
								<?php if ($user_type=='other') { echo 'checked'; } ?>
						>Altre Usuari/ària</input>
					</p>
				</div>
				
				<br/>
				
				<div id='step_2' <?php echo $display_step_2; ?>>
					<p><span class=num>2.</span> <strong>Inseriu el nom de l'usuari/ària que voleu afegir.</strong></p>
					
					<?php if (!empty($errors_step_2)) { echo "<div id='errors' class='error below-h2'>$errors_step_2</div>"; }?>
					<?php if (!empty($message)) { echo "<div id='message' class='updated fade below-h2'>$message</div>"; }?>
					
					<table class='form-table'>
						<tr id='row_username'>
							<th><label for='username'>Nom d'usuari/ària:</label></th>
							<td>
								<input	type='text'
										id='username'
										size='30'
										maxlength='30'
										name='user[username]'
										value="<?php echo $username; ?>"
										<?php echo $username_readonly; ?> />
									<span id='check_user' <?php if ($display_check_user) { ?>style="display:''"<?php } else { ?>style="display:none"<?php } ?>><input type="image" src='<?php echo plugin_dir_url(__FILE__);?>check.png' name="Check user"/><small>Comprova'n la disponibilitat</small></span>
									<br>								
									<span id='username_info'><?php if ($display_username_info) { echo $username_info; } ?></span>
							</td>
						</tr>					
					</table>
					
					<?php if ( $confirm_user_addition ) { ?>
					<div id='confirm_user' class='updated fade below-h2' style='background-color: rgb(255, 251, 204); '>
						<p>Aquest nom d'usuari/ària ja existeix. Esteu segur que voleu afegir l'usuari/ària <strong><?php echo $username ?></strong> al vostre bloc?</p>
						<p>
							<input class="button" type="submit" name='Confirm user addition' value="Sí, vull afegir aquest usuari/ària" />
							<input class="button" type="submit" name='Discard' value="No, no conec aquest usuari/ària" />
						</p>
					</div>
					<?php } ?>					
				</div>
				
				<?php if ($display_step_3_add) { ?>
					<br/>
					<div id='step_3_add' >
						<p><span class=num>3.</span> <strong>Escolliu el rol de l'usuari/ària per a aquest bloc.</strong></p>
						
						<table class='form-table'>					
							<tr id='select_role'>
								<th><label for='role'><?php _e('Role'); ?>:</label></th>
								<td>
									<select id='role' name="user[role]">
										<?php if ($user_type == 'xtec') { 
											$selected='';
											if ($role=='administrator') { $selected = "selected='selected'";}
											echo "<option value='administrator' $selected>$administrator_tag</option>"; 
										} ?> 
										<option <?php if ($role=='editor') { echo "selected='selected'";} ?>value='editor'><?php echo $editor_tag ?></option>
										<option <?php if ($role=='author') { echo "selected='selected'";} ?>value='author'><?php echo $author_tag ?></option>
										<option <?php if ($role=='contributor' || $role == '') { echo "selected='selected'";} ?> value='contributor'><?php echo $contributor_tag ?></option>
										<option <?php if ($role=='subscriber') { echo "selected='selected'";} ?>value='subscriber'><?php echo $subscriber_tag ?></option>
									</select>
								</td>
							</tr>						
						</table>
						
						<p id='add_user' class="submit">
							<?php wp_nonce_field('add-user') ?>
							<input class="button" type="submit" name="Add user" value="Afegeix l'usuari/ària" />
							<input class='button' type='submit' name='Discard' value='Cancel·la' />
						</p>
					</div>
				<?php } ?>
				
				<?php if ($display_step_3_create_add) { ?>	
					<br/>
					<div id='step_3_create_add' >
						<p><span class=num>3.</span> <strong>Assigneu una contrasenya i un correu electrònic per al nou usuari/ària i escolliu el rol de l'usuari/ària per a aquest bloc.</strong></p>
						
						<?php if (!empty($errors_step_3)) { echo "<div id='errors' class='error below-h2'>$errors_step_3</div>"; }?>
						
						<table class='form-table'>
						
							<tr id='row_password' >
								<th><label for='password'><?php _e('Password')?>:</label></th>
								<td>
									<input id='password' type='text' size='30' maxlength='20' name='user[password]' value="<?php echo $password; ?>" />
								</td>
							</tr>
							
							<tr id='row_email' >
								<th><label for='email'><?php _e('Email') ?> (opcional):</label></th>
								<td>
									<input id='email' type='text' size='50' maxlength='100' name='user[email]' value="<?php echo $email; ?>" />
									<br>
									<small>Si l'usuari/ària no disposa de correu electrònic deixeu aquest camp en blanc.</small>
								</td>
							</tr>
							
							<tr id='select_role'>
								<th><label for='role'><?php _e('Role'); ?>:</label></th>
								<td>
									<select id='role' name="user[role]">
										<option <?php if ($role=='editor') { echo "selected='selected'";} ?>value='editor'><?php echo $editor_tag ?></option>
										<option <?php if ($role=='author') { echo "selected='selected'";} ?>value='author'><?php echo $author_tag ?></option>
										<option <?php if ($role=='contributor' || $role == '') { echo "selected='selected'";} ?> value='contributor'><?php echo $contributor_tag ?></option>
										<option <?php if ($role=='subscriber') { echo "selected='selected'";} ?>value='subscriber'><?php echo $subscriber_tag ?></option>
									</select>
								</td>
							</tr>
							
						</table>
						
						<p id='create_add_user' class="submit">
							<?php wp_nonce_field('add-user') ?>
							<input class="button" type="submit" name="Create add user" value="Crea i afegeix l'usuari/ària" />
							<input class='button' type='submit' name='Discard' value='Cancel·la' />
						</p>
					</div>
				<?php } ?>
				
			</form>
			<?php
		}
		?>
	</div>
	<?php
}


function xtec_manage_users()
{
	global $wpdb;
	
	?>	

	<div class="wrap">
		
		<?php screen_icon(); ?>
		
		<h2>Gestió d'usuaris</h2>

		<?php

		if ( isset($_POST['Change_user_password']) ) {
			$password = $_POST['password'];
			$user_id = $_POST['user_id'];
			$username = get_userdata($user_id)->user_login;
			if ( !xtec_current_user_can('change_user_password',$user_id) ) {
				wp_die(__('Cheatin&#8217; uh?'));
			}
			if ( empty($password) ) {
				$error = "<p>S'ha produit un error a l'intentar fer el canvi de contrasenya. No heu introduit cap contrasenya.</p>";
			}
			else {
				wp_set_password($password,$user_id);
				$message = "<p>S'ha canviat la contrasenya de l'usuari/ària <strong>$username</strong>. La nova contrasenya és <strong>$password</strong>.</p>";
			}
		}
		else if ( isset($_POST['Delete_user']) ) {
			$user_id = $_POST['user_id'];
			$username = get_userdata($user_id)->user_login;
			if ( !xtec_current_user_can('delete_user',$user_id) ) {
				wp_die(__('Cheatin&#8217; uh?'));
			}
			wpmu_delete_user($user_id);
			$message = "<p>S'ha eliminat l'usuari/ària <strong>$username</strong> d'XTECBlocs.</p>";
		}
		?>

		<?php if ( isset($_REQUEST['action']) ) {
			$doaction = $_REQUEST['action'];
		}
		
		if ( $doaction == 'change_user_password' ) {
			$user_id = $_REQUEST['user_id'];
			if ( !$user_id ) {
				wp_die(__('Cheatin&#8217; uh?'));
			}
			else if ( !xtec_current_user_can('change_user_password',$user_id) ) {
				wp_die(__('Cheatin&#8217; uh?'));
			}
			else {
				echo "<p>Heu seleccionat canviar la contrasenya de l'usuari/ària <strong>". get_userdata($user_id)->user_login . "</strong>. Escriviu a continuació la nova contrasenya que li assignareu.</p>";
				?>
				<form action="?page=xtec-manage-users" method="post">
					<input type="hidden" name='user_id' value='<?php echo $user_id?>'/>
					<table class='form-table'>
						<tr id='row_password' >
							<th><label for='password'>Nova contrasenya:</label></th>
							<td>
								<input id='password' type='text' size='30' maxlength='20' name='password' value="<?php echo $password; ?>" />
							</td>
						</tr>
						
					</table>
					<p class="submit">
						<input class="button" type="submit" name="Change_user_password" value="Canvia la contrasenya" />
						<input class='button' type='submit' name='Discard' value='Cancel·la' />
					</p>
				</form>
				<?php
			}
		}
		else if ( $doaction == 'delete_user' ) {
			$user_id = $_REQUEST['user_id'];
			if ( !$user_id ) {
				wp_die(__('Cheatin&#8217; uh?'));
			}
			else if ( !xtec_current_user_can('delete_user',$user_id) ) {
				wp_die(__('Cheatin&#8217; uh?'));
			}
			else {
				echo "<p>Heu seleccionat eliminar l'usuari/ària <strong>". get_userdata($user_id)->user_login . "</strong>. Esteu segur que voleu eliminar-lo d'XTECBlocs?</p>";
				?>
				<form action="?page=xtec-manage-users" method="post">
					<input type="hidden" name='user_id' value='<?php echo $user_id?>'/>
					<p class="submit">
						<input class="button" type="submit" name="Delete_user" value="Si, vull eliminar-lo" />
						<input class='button' type='submit' name='Discard' value="No, deixa-ho córrer" />
					</p>
				</form>
				<?php
			}
		}
		else { ?>
		
			<div id='about' class='updated fade' style='background-color: rgb(255, 251, 204); '>	
				<p>En aquesta pàgina es llisten tots els usuaris que heu creat i els blocs als que pertanyen.</p>
				<p>Des d'aquí podeu canviar la contrasenya d'aquests usuaris i en el cas que no pertanyin a cap bloc també podeu eliminar-los definitivament.</p>
			</div>
			
			<?php

			if ( !empty($message) ) {
				echo "<div id='about' class='updated fade' style='background-color: rgb(255, 251, 204); '>";
				echo $message;
				echo "</div>";
			}
			if ( !empty($error) ) {
				echo "<div class='error below-h2'>";
				echo $error;
				echo "</div>";
			}
			
			// Select users created by current user
			global $current_user;
			$sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'xtec_user_creator' AND meta_value = '$current_user->user_login'";
			
			$users = $wpdb->get_results($sql);
			if ( !empty($users) ) {
				?>
				<br>
				
				<table class="widefat fixed" cellspacing="0">
				
					<thead>				
						<tr class="thead">
							<th scope="col" id="username" class="manage-column column-username" style=""><?php echo __('Username') ?></th>
							<th scope="col" id="blogs" class="manage-column column-name" style="">És membre de ...</th>
						</tr>
					</thead>
				
					<tbody>
						<?php
						foreach ( $users as $user ) {
							$blogs = get_blogs_of_user($user->user_id,true);
							?>
							<tr class="alternate">
								<td>
									<strong><?php echo get_userdata($user->user_id)->user_login ?></strong>
									<div class="row-actions">
										<a href="?page=xtec-manage-users&amp;action=change_user_password&amp;user_id=<?php echo $user->user_id; ?>">Canvia la contrasenya</a>
										<?php if ( xtec_current_user_can('delete_user',$user->user_id) ) { ?>
										 | <a href="?page=xtec-manage-users&amp;action=delete_user&amp;user_id=<?php echo $user->user_id; ?>">Elimina l'usuari/ària</a>
										<?php } ?>
									</div>
								</td>
								<td>
									<?php
									foreach ( $blogs as $blog ) {
										if ( $blog->blogname != 'XTECBlocs' ) {
											echo "<a href='$blog->siteurl'>$blog->blogname</a><br>";
										}							
									}
									?>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			<?php 
			}
			else {
				echo "<div class='updated fade' style='background-color: rgb(255, 251, 204); '>";
					echo "<p>Actualment no existeix cap usuari creat per vos.</p>";
				echo "</div>";
			}
		}
		?>
	</div>
	<?php	
}

/**
 * Checks if the current user can do an specific action.
 * 
 * @param string $action The action to check.
 * @param string $param Extra arguments.
 * @return bool True if the user can do the action false otherwise. 
 */
function xtec_current_user_can($action,$param = '')
{	
	// Site admins can do it all except delete users who are still members of some blog
	if ( is_super_admin() ) { 
		if ( $action == 'delete_user' ) {
			if ( get_blogs_of_user($user->user_id,true) ) { return false; }
		}
		else if ( $action == 'create_blog_today' ) {
			return array();
		}
		else {
			return true;
		}
	}
	
	switch ( $action ) {
		
		case 'add_users':
			// Can add users: XTEC users from LDAP who can edit users
			
			global $current_user;
			wp_get_current_user();
			if ( current_user_can('add_users') && xtec_is_xtec_user($current_user->ID) ) { return true; }
			else { return false; }			
			
			break;
		
		case 'manage_users':
			// Can manage users: XTEC users from LDAP
			
			global $current_user;
			wp_get_current_user();
			return xtec_is_xtec_user($current_user->ID);
			
			break;
			
		case 'create_blogs':
			// Can create blogs: XTEC users from LDAP.
			// $param -> limit per day blogs creation 
			
			$limit = $param;
			if ( $limit == 0 ) { return false; }
			
			// ESTARIA BÉ TENIR A LES OPCIONS DEL SITE UNA OPCIÓ PER DIR QUINS USUARIS PODEN CREAR BLOCS //
			
			global $current_user;
			wp_get_current_user();
			return xtec_is_xtec_user($current_user->ID);
			
			break;
			
		case 'create_blog_today':
			// Can create blog today: users who have not exceeded the maximum blog daily creation.
			// Returns: empty array, if user can create blog today.
			//			array with blogs created today for user, if user exceeds the maximum blog daily creation.
			// $param -> limit per day blogs creation 
			
			$limit = $param;
			$today_created_blogs = array();
			
			global $current_user;			
			wp_get_current_user();			
			$blogs = get_blogs_of_user($current_user->ID,true);
			
			foreach ( $blogs as $blog ) {
				$ahir = date("Y-m-d H:i:s",time()-86400);
				$registered = get_blog_details($blog->userblog_id)->registered;
				if ( $ahir < $registered ){										
					array_push($today_created_blogs,$blog->userblog_id);
				}
			}
						
			if ( count($today_created_blogs) < $limit ) {
				return array();
			}
			else { return $today_created_blogs; }

			break;
		
		case 'delete_user':
			// Can delete user: user who have created whenever the user to delete is not member of any bloc or is not member of XTECBlocs
			// $param -> user_id
			$user_id = $param;
			global $current_user;
			if ( $current_user->user_login == get_user_meta($user_id,'xtec_user_creator',true) ) {
				$blogs = get_blogs_of_user($user_id,true);
				if ( !$blogs ) { return true; }
				else {
					foreach ( $blogs as $blog ) {
						if ( $blog->blogname == 'XTECBlocs' ) { return true; }							
					}
					return false;
				}
			}
			else { return false; }
			
			break;
		
		case 'change_user_password':
			// Can change user password: user who have created
			// $param -> user_id
			$user_id = $param;
			global $current_user;
			
			if ( $current_user->user_login == get_user_meta($user_id,'xtec_user_creator',true) ) { return true; }
			else { return false; }
			
			break;
	}
}

/**
 * Checks if a user can do an specific action.
 * 
 * @param string $user_id The ID of the user.
 * @param string $action The action to check.
 * @return bool True if the user can do the action false otherwise. 
 */
function xtec_user_can($user_id,$action)
{
	// Site admins can do it all
	$user_login = get_userdata($user_id)->user_login;
	if ( is_super_admin($user_login) ) { return true; }
	
	switch ( $action ) {
		case 'be_administrator':
			// Can be administrators of a blog XTEC users from LDAP.
			return xtec_is_xtec_user($user_id);
			
			break;
	}	
}

/**
 * Checks if is a XTEC user.
 * 
 * @param string $user_id The user ID.
 * @return bool True if is a XTEC user. 
 */
function xtec_is_xtec_user($user_id)
{
	return ( 'LDAP_XTEC' == get_user_meta($user_id,'xtec_user_creator',true) );
}


//XTEC ************ AFEGIT - Control de l'us d'@ xtec en funció del tipus d'usuari
//2011.02.13 @jmiro227

/**
 * Check if the email address is valid according user typology.
 * 
 * @param string $email The user email.
 * @param string $user_type The user type.
 * @return bool True if it is a valid email. 
 */
function is_valid_email($email,$user_type)
{
	if (( $user_type == 'other' ) && preg_match("/^.+@xtec\.cat$/",$email)) return ( false);
	else if (( $user_type == 'xtec' ) && !(preg_match("/^.+@xtec\.cat$/",$email))) return ( false );
        else return ( true );
}

//************ FI

/**
 * Checks if is a valid username.
 * 
 * @param string $username The username.
 * @return bool True if is a valid username false otherwise. 
 */
function xtec_is_valid_username($username)
{
	return !preg_match('/[^a-z0-9\_\.\-]/',$username);
}
