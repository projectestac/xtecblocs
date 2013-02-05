<?php

/*
Plugin Name: XTEC Viquiatles
Plugin URI:
Description: Easily embed maps from Viquiatles site.
Version: 1.1
Author: Francesc Bassas i Bullich
Author URI:
*/

add_shortcode('viquiatles','xtec_viquiatles_shortcode');
add_action('wp_head','viquiatles_head');
add_action('init','xtec_viquiatles_addbuttons');
add_action('network_admin_menu', 'xtec_viquiatles_network_admin_menu');
add_action('update_wpmu_options','xtec_viquiatles_save_options');

/**
 * The Viquiatles shortcode handler.
 * 
 * @param array $atts Attributes.
 * @return string Output. 
 */
function xtec_viquiatles_shortcode($atts)
{
	// Initialize variables before extract()
	$id = '';
	$width = 0;
	$height = 0;
	extract ( shortcode_atts ( array ('id' => 'empty', 'width' => get_site_option('xtec_viquiatles_embed_width'), 'height' => get_site_option('xtec_viquiatles_embed_height') ), $atts ) );
	
	return "<div id='vatles_api'><div id='map$id' style='width:".$height."px;height:".$width."px'></div><script type='text/javascript' src='http://atles.eduwiki.cat/sitemedia/js/embed.js'></script><script type='text/javascript' src='http://atles.eduwiki.cat/json/showmap/$id/map$id'></script></div>";
}

/**
 * Prints Viquiatles stylesheet link and adds JavaScript code for the Google Maps API. 
 */
function viquiatles_head()
{
	echo '<link type="text/css" rel="stylesheet" href="' . plugins_url('/css/xtec_viquiatles.css', __FILE__) . '" media="screen" />' . "\n";
	$key = get_site_option('xtec_google_maps_api_key');
	echo "<script type='text/javascript' src='http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=$key'></script>" . "\n";
}

/**
 * If the TinyMCE button is setted it is added to the TinyMCE editor visual mode.
 */
function xtec_viquiatles_addbuttons()
{
	if ( !get_site_option('xtec_viquiatles_tinymce_button') )
		return;

	if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') )
		return;

	// Add only in Rich Editor mode
	if ( get_user_option('rich_editing') == 'true' ) {
		add_filter('mce_external_plugins','xtec_add_viquiatles_tinymce_plugin');
		add_filter('mce_buttons', 'xtec_register_viquiatles_button');
	}
}

/**
 * Adds Viquiatles plugin for TinyMCE.
 * 
 * @param array $plugin_array External plugins for TinyMCE.
 * @return array External plugins for TinyMCE with Viquiatles plugin.
 */
function xtec_add_viquiatles_tinymce_plugin($plugin_array)
{
   $plugin_array['xtec_viquiatles'] = WP_PLUGIN_URL . '/../plugins/xtec-viquiatles/js/editor_plugin.js';
   return $plugin_array;
}

/**
 * Adds Viquiatles button to TinyMCE
 * 
 * @param array $buttons TinyMCE buttons.
 * @return array TinyMCE buttons with Viquiatles button.
 */
function xtec_register_viquiatles_button($buttons)
{
   array_push($buttons,'separator','xtec_viquiatles');
   return $buttons;
}

/**
 * Adds plugin network admin menu.
 */
function xtec_viquiatles_network_admin_menu()
{
    add_submenu_page('settings.php', 'Viquiatles', 'Viquiatles', 'manage_network_options', 'ms-viquiatles', 'xtec_viquiatles_network_options');
}

/**
 * Displays plugin network options page.
 */
function xtec_viquiatles_network_options()
{	
	switch ( $_GET['action'] ) {
		case 'siteoptions':
			if ( $_POST['xtec_google_maps_api_key'] ) {
				$xtec_google_maps_api_key = $_POST['xtec_google_maps_api_key'];
				update_site_option("xtec_google_maps_api_key",$xtec_google_maps_api_key);
			}
			if ( $_POST['xtec_viquiatles_embed_width'] ) {
				$xtec_viquiatles_embed_width = $_POST['xtec_viquiatles_embed_width'];
				update_site_option("xtec_viquiatles_embed_width",$xtec_viquiatles_embed_width);
			}
			if ( $_POST['xtec_viquiatles_embed_height'] ) {
				$xtec_viquiatles_embed_height = $_POST['xtec_viquiatles_embed_height'];
				update_site_option("xtec_viquiatles_embed_height",$xtec_viquiatles_embed_height);
			}
			if ( $_POST['xtec_viquiatles_tinymce_button'] ) {
				$xtec_viquiatles_tinymce_button = $_POST['xtec_viquiatles_tinymce_button'];
				update_site_option("xtec_viquiatles_tinymce_button",$xtec_viquiatles_tinymce_button);
			}
			?>
			<div id="message" class="updated"><p><?php _e( 'Options saved.' ) ?></p></div>
			<?php
		break;
	}
	?>
	<div class="wrap">
		<h2><?php _e('XTEC Viquiatles') ?></h3>
		<form method="post" action="?page=ms-viquiatles&action=siteoptions">
			<table class="form-table">
				<tbody>
					<tr valign="top"> 
						<th scope="row"><?php _e('Google Maps API key')?></th> 
						<td>
							<input type="text" name="xtec_google_maps_api_key" style="width: 95%" value="<?php echo get_site_option('xtec_google_maps_api_key') ?>" />
						</td>
					</tr>
					<tr valign="top"> 
						<th scope="row"><?php _e('Viquiatles embed width')?></th> 
						<td>
							<input type="text" name="xtec_viquiatles_embed_width" value="<?php echo get_site_option('xtec_viquiatles_embed_width') ?>" />
						</td>
					</tr>
					<tr valign="top"> 
						<th scope="row"><?php _e('Viquiatles embed height')?></th> 
						<td>
							<input type="text" name="xtec_viquiatles_embed_height" value="<?php echo get_site_option('xtec_viquiatles_embed_height') ?>" />
						</td>
					</tr>
					<tr valign="top"> 
						<th scope="row"><?php _e('Viquiatles TinyMCE button')?></th> 
						<td>
							<?php $viquiatles_button = get_site_option('xtec_viquiatles_tinymce_button') ?>
							<input type="checkbox" name="xtec_viquiatles_tinymce_button" value="1" <?php if( $viquiatles_button ) { echo 'checked=checked '; } ?>/>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Desa els canvis"></p>
		</form>
	</div>
<?php
}