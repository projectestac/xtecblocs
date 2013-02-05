<?php
/*
Plugin Name: SlideShare
Plugin URI: http://yoast.com/wordpress/slideshare/
Description: A plugin for WordPress to easily display slideshare.net presentations
Version: 1.7.2
Author: Joost de Valk
Author URI: http://yoast.com/
*/

if ( ! class_exists( 'SlideShare_Admin' ) ) {

	require_once('yst_plugin_tools.php');

	class SlideShare_Admin extends Yoast_Plugin_Admin {

		var $hook 		= 'slideshare';
		var $longname	= 'SlideShare Configuration';
		var $shortname	= 'SlideShare';
		var $filename	= 'slideshare/slideshare.php';
		var $ozhicon	= 'images/page_white_powerpoint.png';


		function SlideShare_Admin() {
			add_action( 'admin_menu', array(&$this, 'register_settings_page') );
			add_filter( 'plugin_action_links', array(&$this, 'add_action_link'), 10, 2 );
			add_filter( 'ozh_adminmenu_icon', array(&$this, 'add_ozh_adminmenu_icon' ) );				

			add_action('admin_print_scripts', array(&$this,'config_page_scripts'));
			add_action('admin_print_styles', array(&$this,'config_page_styles'));	

			//XTEC ************ ELIMINAT - Es treu el widget del tauler d'administració.
			//2011.05.11 @fbassas
			
			/*
			add_action('wp_dashboard_setup', array(&$this,'widget_setup'));
			*/
			
			//************ FI

			add_action('admin_init', array(&$this, 'options_init') );
		}

		function options_init(){
		    register_setting( 'yoast_slideshare_options', 'slideshare' );
		}
		
		function config_page() {
			$options  = get_option('slideshare');
			
			if (!is_array($options) || empty($options['postwidth']) || $options['postwidth'] === 0) {
				global $content_width;
				$options['postwidth'] = $content_width;
			}
			?>
			<div class="wrap">
				<a href="http://yoast.com/"><div id="yoast-icon" class="icon32"><br /></div></a>
				<h2><?php _e("SlideShare Configuration", 'slideshare'); ?></h2>
				<div class="postbox-container" style="width:70%;">
					<div class="metabox-holder">	
						<div class="meta-box-sortables">
						<form action="options.php" method="post" id="slideshare-conf">
						<?php settings_fields('yoast_slideshare_options'); ?>
						<?php 
							$rows = array();
							$rows[] = array(
								"id" => "slidesharepostwidth",
								"label" => __("Default width", 'slideshare'),
								"content" => '<input size="5" type="text" id="slidesharepostwidth" name="slideshare[postwidth]" value="'.$options['postwidth'].'"/> pixels',
							);
							$content = $this->form_table($rows).'<div class="submit"><input type="submit" class="button-primary" name="submit" value="'.__("Update SlideShare Settings", 'slideshare').' &raquo;" /></div>';
							$this->postbox('slidesharesettings',__('Settings', 'slideshare'),$content);
							$this->postbox('usageexplanation',__('Explanation of usage', 'slideshare'),'<p>'.__('Just copy and paste the "Embed (wordpress.com)" code from', 'slideshare').' <a href="http://www.slideshare.net/">SlideShare</a>, '.__("and you're done", 'slideshare').'</p>');
							$this->postbox('defaultwidthexpl',__("Explanation of default width", 'slideshare'),'<p>'.__("If you enter nothing here, you can change the width by hand by changing the w= value, that is bolded and red here:", 'slideshare').'</p>'.'<pre>[slideshare id=1234&amp;doc=how-to-change-the-width-123456789-1&amp;<strong style="color:red;">w=425</strong>]</pre>'.'<p>'.__("If you <em>do</em> enter a value, it will always replace the width with that value.", 'slideshare')); ?>
						</form>
						</div>
					</div>
				</div>
				<div class="postbox-container" style="width:20%;">
					<div class="metabox-holder">	
						<div class="meta-box-sortables">
							<?php
								$this->plugin_like();
								$this->plugin_support();
								$this->news(); 
							?>
						</div>
						<br/><br/><br/>
					</div>
				</div>
			</div>
			<?php
		}
	}
	
	$ssa = new SlideShare_Admin();
}

function slideshare_init() {
	$plugin_dir = basename( dirname( __FILE__ ) );
	load_plugin_textdomain( 'slideshare', null, $plugin_dir . '/lang' );
}
add_action( 'init', 'slideshare_init' );

function slideshare_insert($atts, $content=null) {	
	$options = get_option('SlideShare');
	
	if(isset($atts)) {
		$args			= str_replace('&#038;','&',$atts['id']);
		$args			= str_replace('&amp;','&',$args);
		$r 				= wp_parse_args($args);
	
		if ($options['postwidth'] == '') {
			$width		= $r['w'];			
		} else {
			$width		= $options['postwidth'];
		}
		if ($width == 0) {
			global $content_width;
			$width = $content_width;
		}
		if ($width == 0) {
			$width = 400;
		}
		$height			= round($width / 1.22);
		
		if ($r['type'] == "d") {
			$player		= "ssplayerd.swf";
		} else {
			$player		= "ssplayer2.swf";
		}
		
		$content		= '<object width="'.$width.'" height="'.$height.'">'
							.'<param name="movie" value="http://static.slideshare.net/swf/'.$player.'?doc='.$r['doc'].'"/>'
							.'<param name="allowFullScreen" value="true"/>'
							.'<param name="allowScriptAccess" value="always"/>'
							.'<embed src="http://static.slideshare.net/swf/'.$player.'?doc='.$r['doc'].'"  type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$width.'" '.'height="'.$height.'">'
							.'</embed>'
							.'</object>';
	}
	
	return $content;
}

add_shortcode('slideshare', 'slideshare_insert');

