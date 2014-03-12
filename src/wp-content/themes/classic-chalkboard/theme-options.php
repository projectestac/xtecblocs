<?php
// Default options values
$classicchalkboard_options = array(
	'green_background' => false,
	'footer_link' => true
);
if ( is_admin() ) : // Load only if we are viewing an admin page
function classicchalkboard_register_settings() {
	// Register settings and call sanitation functions
	register_setting( 'classicchalkboard_theme_options', 'classicchalkboard_options', 'classicchalkboard_validate_options' );
}add_action( 'admin_init', 'classicchalkboard_register_settings' );
function classicchalkboard_theme_options() {
	// Add theme options page to the admin menu
	add_theme_page( 'Theme Options', 'Theme Options', 'edit_theme_options', 'theme_options', 'classicchalkboard_theme_options_page' );
}add_action( 'admin_menu', 'classicchalkboard_theme_options' );
// Function to generate options page
function classicchalkboard_theme_options_page() {
	global $classicchalkboard_options, $classicchalkboard_categories, $classicchalkboard_layouts;
	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. ?>
	<div class="wrap">
	<?php screen_icon(); echo "<h2>" . __( 'classicchalkboard Theme Options', 'classicchalkboard' ) . "</h2>";
	// This shows the page's name and an icon if one has been provided ?>
	<?php if ( false !== $_REQUEST['updated'] ) : ?>
	<div class="updated fade"><p><strong><?php esc_attr_e( 'Options saved' , 'classicchalkboard' ); ?></strong></p></div>
	<?php endif; // If the form has just been submitted, this shows the notification ?>
	<form method="post" action="options.php">
	<?php $options = get_option( 'classicchalkboard_options', $classicchalkboard_options ); ?>
	
	<?php settings_fields( 'classicchalkboard_theme_options' );
	/* This function outputs some hidden fields required by the form,
	including a nonce, a unique number used to ensure the form has been submitted from the admin page
	and not somewhere else, very important for security */ ?>
<table class="form-table">
	<h3><?php esc_attr_e('Chalkboard Background' , 'classicchalkboard' ); ?></h3>
	<tr valign="top"><th scope="row">Use the green background</th>
	<td>
	<input type="checkbox" id="green_background" name="classicchalkboard_options[green_background]" value="1" <?php checked( true, $options['green_background'] ); ?> />
	<label for="green_background">Check the box to use the green chalkboard background instead of the default blackboard.</label>
	</td>
	</tr>

<table class="form-table">
	<h3><?php esc_attr_e('Footer Link' , 'classicchalkboard' ); ?></h3>
	<p><?php esc_attr_e('Disable the footer links.' , 'classicchalkboard'); ?></p>
	<tr valign="top"><th scope="row">Footer Credit Link</th>
	<td>
	<input type="checkbox" id="footer_link" name="classicchalkboard_options[footer_link]" value="1" <?php checked( true, $options['footer_link'] ); ?> />
	<label for="footer_link">De-select to remove the footer credit link.</label>
	</td>
	</tr>
</table>

	<p class="submit"><input type="submit" class="button-primary" value="Save Options" /></p>
	</form>
<p>
<?php esc_attr_e('Thank you for using classicchalkboard. A lot of time went into development. Donations small or large always appreciated.' , 'classicchalkboard'); ?></p>
<form action="https://www.paypal.com/cgi-bin/webscr" target="_blank" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="QD8ECU2CY3N8J">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<a href="http://www.edwardrjenkins.com/free-wordpress-theme-chalkboard/" target="_blank"><?php esc_attr_e('Classic Chalkboard Documentation' , 'classicchalkboard' ); ?></a>
	</div>
	<?php
}
function classicchalkboard_validate_options( $input ) {
	global $classicchalkboard_options;
	$options = get_option( 'classicchalkboard_options', $classicchalkboard_options );
	if ( ! isset( $input['green_background'] ) )
	$input['green_background'] = null;
	if ( ! isset( $input['footer_link'] ) )
	$input['footer_link'] = null;
	return $input;
}
endif;  // EndIf is_admin()