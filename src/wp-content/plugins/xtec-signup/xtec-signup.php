<?php

/*
Plugin Name: XTEC Signup
Plugin URI:
Description: Adds a custom signup header and allows to limit maximum signup of blogs per day.
Dependencies: XTEC Users
Version: 1.1
Author: Francesc Bassas i Bullich
Author URI:
*/

add_action('network_admin_menu', 'xtec_signup_network_admin_menu');
add_action('preprocess_signup_form','xtec_signup');
add_action('before_signup_form', 'xtec_signup_header');

/**
 * Adds plugin network admin menu.
 */
function xtec_signup_network_admin_menu()
{
    add_submenu_page('settings.php', 'Signup', 'Signup', 'manage_network_options', 'ms-signup', 'xtec_signup_network_options');
}

/**
 * Displays plugin network options page.
 */
function xtec_signup_network_options()
{
	switch ( $_GET['action'] ) {
		case 'siteoptions':
			if ( $_POST['xtec_signup_maxblogsday'] ) {
				$xtec_signup_maxblogsday = $_POST['xtec_signup_maxblogsday'];
				update_site_option("xtec_signup_maxblogsday",$xtec_signup_maxblogsday);
			}
			?>
			<div id="message" class="updated"><p><?php _e( 'Options saved.' ) ?></p></div>
			<?php
		break;
	}
	?>
	<div class="wrap">
		<form method="post" action="?page=ms-signup&action=siteoptions">
			<h2><?php _e('XTEC Signup') ?></h2>
			<table class="form-table">
				<tbody>
					<tr valign="top"> 
							<th scope="row"><?php _e('Maximum blocs per day')?></th> 
							<td>
								<input type="text" name="xtec_signup_maxblogsday" value="<?php echo get_site_option('xtec_signup_maxblogsday') ?>" />
							</td>
					</tr>
				</tbody>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Desa els canvis"></p>
		</form>
	</div>
<?php
}

/*
 * Overrides 'wp-signup.php' after 'preprocess_signup_form' action.
 */
function xtec_signup()
{
	$limit = get_site_option('xtec_signup_maxblogsday');
	
	/* xtec_current_user_can() is defined in XTEC Users plugin */	
	if (is_user_logged_in() && xtec_current_user_can('create_blogs',$limit)) {		
		
		/* xtec_current_user_can() is defined in XTEC Users plugin */
		$blogs = xtec_current_user_can('create_blog_today',$limit);

		if ($blogs) {
			?>
			<h2>Limit de creació excedit</h2>
			<p>El limit de creació de blocs diari és <strong><?php echo $limit; ?></strong>. Si voleu crear-ne més avui, abans n'haureu d'eliminar algun.</p>
			
			<p>Blocs creats en les darreres 24h:</p>
			<ul>
				<?php
				foreach ( $blogs as $idblog ) {
					$siteurl = get_blog_option($idblog,'siteurl');
					$blogname = get_blog_option($idblog,'blogname');		
					echo "<li><a href='$siteurl'\>$blogname</a></li>";
				}
				?>
			</ul>
			<?php	
		}
		else {
			signup_another_blog();
		}
	}
	else {
		echo "<p class=\"notAllowed\">No teniu autorització per crear blocs nous.</p>\n";
	}
	?>
	
	</div>
	</div>

	<?php
	/** @todo Use 'show_admin_bar' function. */
	//get_footer();
	die();
}

/*
 * Adds a custom header.
 */
function xtec_signup_header(){
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		<meta name="generator" content="Bluefish 1.0.7"/> <!-- leave this for stats -->
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		<link rel="stylesheet" href="<?php echo plugins_url('/css/xtec_signup.css', __FILE__); ?>" type="text/css" media="screen" />
		<?php wp_head(); ?>
	</head>
	<body>
	<?php
}