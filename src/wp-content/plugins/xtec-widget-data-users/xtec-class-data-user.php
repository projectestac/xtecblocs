<?php
/*
Plugin Name: XTEC Widget Data Users
Plugin URI:
Description: Widget to add information about user blog
Version: 1.0
Author: Àrea TAC - Departament d'Ensenyament de Catalunya
*/

// Init widget
function add_users_data_widget() {
	register_widget('users_data_widget');
}
add_action('widgets_init', 'add_users_data_widget');

// Load language file
function xtec_widget_load_language_file(){
	$domain = 'xtec-widget-users-data';
	$abs_rel_path = false;
	$plugin_rel_path = plugin_basename( dirname( __FILE__ )) . '/i18n/';
	load_plugin_textdomain( $domain, $abs_rel_path, $plugin_rel_path );
}
add_action('plugins_loaded', 'xtec_widget_load_language_file');

// Widget class
class users_data_widget extends WP_Widget {
	// Widget Constructor
// XTEC ************ MODIFICAT - Use of deprecated PHP4 style class constructor is not supported since PHP 7.
// 2017.11.23 @nacho
//************ ORIGINAL
/*
	function users_data_widget() {
 */
//************ FI
	function __construct() {
		$options = array(
			'classname' => 'xtec_user_data_widget',
		    'description' => __('Widget to add data users','xtec-widget-users-data')
		);
		parent::__construct('users_data_widget', __('Users information','xtec-widget-users-data'), $options);

		add_action('admin_enqueue_scripts', array($this, 'upload_scripts'));
	}

	function upload_scripts() {
		// LOAD ADD MEDIA
	    wp_enqueue_media();

	    // LOAD CUSTOM JS AND CSS
	    wp_register_script( 'widget-admin-users-data-js', plugins_url() . '/xtec-widget-data-users/assets/js/widget-admin-js.js', array('jquery'),'1.1', true );
		wp_enqueue_script( 'widget-admin-users-data-js' );
		wp_enqueue_style( 'widget-users-data-css', plugins_url() . '/xtec-widget-data-users/assets/css/widget-style.css' );
	}

	function form( $instance ) {

		// Default image
		$imageBase64 = plugins_url() . '/xtec-widget-data-users/assets/images/user_default.png';
		$defaults = array( 'name' => '', 'description' => '', 'email' => '', 'web' => '', 'twitter'=> '', 'image_uri' => $imageBase64 );

		$instance = wp_parse_args( ( array ) $instance, $defaults);
		$image_uri = $instance['image_uri'];
        $name = $instance['name'];
        $description = $instance['description'];
        $email = $instance['email'];
        $web = $instance['web'];
        $twitter = $instance['twitter'];

        // Get position form into array
        $fieldId = $this->get_field_name('image_uri');
        $fieldId = str_replace('widget-users_data_widget[','',$fieldId);
        $fieldId = str_replace('][image_uri]','',$fieldId);

        // Print admin form
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('image_uri'); ?>"><?php _e('Image','xtec-widget-users-data'); ?>:</label><br />
    		<div class="image-content-widget">
    			<img class="custom_media_image" name="<?php echo $this->get_field_name('image_uri'); ?>" src="<?php echo $instance['image_uri']; ?>" <?php if( $instance['image_uri'] == ''){ ?> style="display:none" <?php } ?> />
    			<span id="<?php echo $this->get_field_id('image_uri'); ?>" class="dashicons dashicons-no widget-image-cross" onclick="widgetRemoveImage(<?php echo $fieldId ?>)" <?php if( $instance['image_uri'] == ''){ ?> style="display:none" <?php } ?> /></span>
        	</div>
        	<div style="clear:both"></div>
        	<input type="hidden" class="widefat custom_media_url widget-image-url" name="<?php echo $this->get_field_name('image_uri'); ?>" value="<?php echo $instance['image_uri']; ?>" >
	        <input type="button" class="button button-primary custom_media_button" id="custom_media_button_<?php echo rand(); ?>" name="button_<?php echo $this->get_field_name('image_uri'); ?>" value="<?php _e('Upload Image','xtec-widget-users-data'); ?>" style="margin-top:5px;" />
	    </p>
        <p>
            <?php _e('Name and surnames','xtec-widget-users-data'); ?>:
            <input class="widefat" type="text" name="<?php echo $this->get_field_name('name');?>" maxlength="140" value="<?php echo esc_attr($name);?>"/>
        </p>
        <p>
            <?php _e('About user','xtec-widget-users-data'); ?>:
            <textarea class="widefat" name="<?php echo $this->get_field_name('description');?>" maxlength="140" rows="6" cols="25"><?php echo esc_attr($description);?></textarea>
        </p>
        <p>
            <?php _e('E-mail','xtec-widget-users-data'); ?>:
            <input class="widefat" type="email" name="<?php echo $this->get_field_name('email');?>" maxlength="140" value="<?php echo esc_attr($email);?>" onkeyup="xtecCheckMail('<?php echo $this->get_field_name('email');?>')" autocomplete="off"/>
        </p>
        <p>
            <?php _e('Web','xtec-widget-users-data'); ?>:
            <input class="widefat" type="url" name="<?php echo $this->get_field_name('web');?>" maxlength="140" value="<?php echo esc_attr($web);?>"/>
        </p>
        <p>
            <?php _e('Twitter','xtec-widget-users-data'); ?>:
            <input class="widefat" type="text" name="<?php echo $this->get_field_name('twitter');?>" maxlength="140" value="<?php echo esc_attr($twitter);?>"/>
        </p>
        <?php
	}

	function update($new_instance, $old_instance) {
		// Guarda las opciones del Widget
	    $instance = $old_instance;

	    //Parse twitter before save
	    $twitter = str_replace('https://','',$new_instance['twitter']);
		$twitter = str_replace('https//','',$twitter);
		$twitter = str_replace('http://','',$twitter);
		$twitter = str_replace('http//','',$twitter);
		$twitter = str_replace('twitter.com/','',$twitter);
		$twitter = str_replace('twitter.com','',$twitter);
		$twitter = str_replace('@','',$twitter);

        // Con sanitize_text_field eliminamos HTML de los campos
        $instance['image_uri'] = sanitize_text_field($new_instance['image_uri']);
        $instance['name'] = sanitize_text_field($new_instance['name']);
        $instance['firstname'] = sanitize_text_field($new_instance['firstname']);
        $instance['description'] = sanitize_text_field($new_instance['description']);
        $instance['email'] = sanitize_text_field($new_instance['email']);
        $instance['web'] = sanitize_text_field($new_instance['web']);
        $instance['twitter'] = sanitize_text_field($twitter);
        return $instance;

	}

	// Print public widget
	function widget($args, $instance) {

		extract($args);
		$image_uri = $instance['image_uri'];
		$name = $instance['name'];
        $description = $instance['description'];
        $email = $instance['email'];
        $web = $instance['web'];
        $twitter = $instance['twitter'];

        $imageDefault = plugins_url() . '/xtec-widget-data-users/assets/images/user_default.png';
        $imageTwitter = plugins_url() . '/xtec-widget-data-users/assets/images/twitter.png';
        $imageUrl = plugins_url() . '/xtec-widget-data-users/assets/images/url.png';
        $imageMail = plugins_url() . '/xtec-widget-data-users/assets/images/mail.png';

        // Crop mail
        $emailComplete = $email;
        if( strlen($email) > 28 ){
        	$email = substr($email,0,25).'...';
        }

        // Check http/https
        if ( ! preg_match('#^(http|https)://.*#s', trim($web)) ){
        	$webComplete = 'http://'.$web;
		} else {
			$webComplete = $web;
			$web = str_replace('https://','',$web);
			$web = str_replace('https//','',$web);
			$web = str_replace('http://','',$web);
			$web = str_replace('http//','',$web);
		}

        // Crop web
        if( strlen($web) > 28 ){
        	$web = substr($web,0,25).'...';
        }


        $twitter = str_replace('@','',$twitter);
        $twitterComplete = 'https://twitter.com/'.$twitter;

		// Crop twitter
        if( strlen($twitter) > 28 ){
        	$twitter = substr($twitter,0,25).'...';
        }

        $imageUriCheck = str_replace(array("https://","http://"),"",$image_uri);
        $imageDefaultCheck = str_replace(array("https://","http://"),"",$imageDefault);

        if ( $imageUriCheck != $imageDefaultCheck || $name != '' || $description != '' || $email != '' || $web != '' || $twitter != '' ){

    ?>

			<div class="widget-global-content">
			<?php
				// Check if image exist
				if ( $instance['image_uri'] != '' ){
			?>
				<p>
					<div class="widget-content-image">
						<img  class="widget-image" src="<?php echo $image_uri; ?>">
					</div>
				</p>
			<?php
				}
				// Check if name exist
				if ( $instance['name'] != '' ){
			?>
					<p class="widget-name">
						<strong><?php echo $name; ?></strong>
					</p>
			<?php
				}
				// Check if description exist
				if ( $instance['description'] != '' ){
			?>
					<p><?php echo $description ?></p>
			<?php
				}
				// Check if email exist
				if ( $instance['email'] != '' ){
			?>
					<p id="widget_mail_info" class="widget-text-list widget-email-info">
						<span class="genericon genericon-mail iconMail"></span>
						<span class="widget-mail-content email-info" data-small="<?php echo $email; ?>" data-large="<?php echo $emailComplete ?>"><?php echo $email; ?></span>
					</p>
			<?php
				}
				// Check if web exist
				if ( $instance['web'] != '' ){
			?>
					<p class="widget-text-list">
						<span class="genericon genericon-website iconUrl"></span>
						<a class="widget-link" target="_blank" href="<?php echo $webComplete; ?>">
							<?php echo $web; ?>
						</a>
					</p>
			<?php 
				}
				// Check if twitter exist
				if ( $instance['twitter'] != '' ){
			?>
					<p class="widget-text-list">
						<span class="genericon genericon-twitter iconTwitter"></span>
						<a class="widget-link" target="_blank" href="<?php echo $twitterComplete; ?>">
							@<?php echo $twitter; ?>
						</a>
					</p>
			<?php
				}
			?>

			</div>

	<?php

		}

		// LOAD CUSTOM JS AND CSS
	    wp_register_script( 'widget-users-data-js', plugins_url() . '/xtec-widget-data-users/assets/js/widget-js.js', array('jquery'),'1.1', true );
		wp_enqueue_script( 'widget-users-data-js' );
		wp_enqueue_style( 'widget-users-data-css', plugins_url() . '/xtec-widget-data-users/assets/css/widget-style.css' );
		wp_enqueue_style( 'fukasawa_genericons', plugins_url() . '/xtec-widget-data-users/assets/genericons/genericons.css' );

	}
}
