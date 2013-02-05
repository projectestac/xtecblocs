<?php

load_theme_textdomain('mandigo', get_template_directory() . '/languages');

if (function_exists('register_sidebar')) register_sidebar();


// Set default values
if (get_option('mandigo_scheme')) { $current = get_option('mandigo_scheme'); }
else { $current = 'blue'; update_option('mandigo_scheme', $current); }

if (get_option('mandigo_headoverlay')) { $headoverlay = get_option('mandigo_headoverlay'); }
else { $headoverlay = 0; update_option('mandigo_headoverlay', $headoverlay); }

if (get_option('mandigo_dates')) { $dates = get_option('mandigo_dates'); }
else { $dates = 0; update_option('mandigo_dates', $dates); }

add_action('wp_head', 'mandigo_head');
function mandigo_head() {
$current = get_option('mandigo_scheme');
echo '<link rel="stylesheet" href="'. get_bloginfo('stylesheet_directory') .'/'. $current .'.css" type="text/css" media="screen" />
';
}



// SEARCH WIDGET
function widget_mandigo_search() {
?>
			<li><h2><?php _e('Search','mandigo');?></h2>
				<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</li>
<?php
}
if (function_exists('register_sidebar_widget')) register_sidebar_widget(__('Search','mandigo'),'widget_mandigo_search');




// CALENDAR WIDGET
function widget_mandigo_calendar() {
?>
			<li><h2><?php the_time('F Y'); ?></h2>
				<?php get_calendar(); ?>
			</li>
<?php
}
if (function_exists('register_sidebar_widget')) register_sidebar_widget(__('Calendar','mandigo'),'widget_mandigo_calendar');




// META WIDGET
function widget_mandigo_meta() {
$options = get_option('widget_meta');
$title = empty($options['title']) ? __('Meta','mandigo') : $options['title'];
?>
				<li><h2><?php echo $title; ?></h2>
                                <span id="rss"><a href="feed:<?php bloginfo('rss2_url'); ?>" title="RSS feed for <?php bloginfo('name'); ?>"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/<?php echo get_option('mandigo_scheme'); ?>/rss_l.gif" alt="Entries (RSS)" id="rssicon" onmouseover="hover(1,'rssicon','rss_l')" onmouseout="hover(0,'rssicon','rss_l')" /></a></span>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
					<li><a href="http://www.onehertz.com/portfolio/wordpress/" title="Other Wordpress themes by the same author" target="_blank">Mandigo theme</a></li>
					<?php wp_meta(); ?>
				</ul>
				</li>
<?php
}
if (function_exists('register_sidebar_widget')) register_sidebar_widget(__('Meta','mandigo'),'widget_mandigo_meta');




// ADMIN
add_action('admin_menu', 'add_mandigo_options_page');
function add_mandigo_options_page() { add_theme_page(__('Theme Options','mandigo'), __('Theme Options','mandigo'), 'edit_theme_options', basename(__FILE__), 'mandigo_options_page'); }
function mandigo_set_scheme($scheme)                         { update_option('mandigo_scheme', $scheme); }
function mandigo_set_overlay($headoverlay)                   { update_option('mandigo_headoverlay', $headoverlay); }
function mandigo_set_dates($dates)                           { update_option('mandigo_dates', $dates); }
function mandigo_set_overlay_oversize($headoverlay_oversize) { update_option('mandigo_headoverlay_oversize', $headoverlay_oversize); }
function mandigo_set_exclude_pages($exclude_pages)           { update_option('mandigo_exclude_pages', $exclude_pages); }
function mandigo_set_bold_links($bold_links)                  { update_option('mandigo_bold_links', $bold_links); }

function mandigo_options_page() {
	if ( $_GET['page'] == basename(__FILE__) ) {
		$ct = current_theme_info();
		
		if (isset($_POST['changedscheme'])) {
			mandigo_set_scheme($_POST['scheme']);
			mandigo_set_overlay($_POST['headoverlay']);
			mandigo_set_dates($_POST['dates']);
			mandigo_set_overlay_oversize($_POST['headoverlay_oversize']);
			$exclude[] = '';
			foreach ( $_POST as $field => $value ) {
				if ( preg_match("/exclude_(\d+)/",$field,$id) ) { $exclude[] = $id[1]; }
			}
			mandigo_set_exclude_pages(implode(",",$exclude));
			mandigo_set_bold_links($_POST['boldlinks']);
		}
		$current              = get_option('mandigo_scheme');
		$headoverlay          = get_option('mandigo_headoverlay');
		$dates                = get_option('mandigo_dates');
		$headoverlay_oversize = get_option('mandigo_headoverlay_oversize');
		$exclude              = split(",",get_option('mandigo_exclude_pages'));
		$boldlinks            = get_option('mandigo_bold_links');

		$pages = & get_pages('sort_column=menu_order');
		foreach ( $pages as $page ) {
			if (!$page->post_parent) { $pages_select .= '<input type="checkbox" name="exclude_'. $page->ID .'"'. (array_search($page->ID, $exclude) ? ' checked' : '') .' /> '. $page->post_title . '<br />'; }
		}

		echo '
		
		<div class="wrap">
		<h2>'.__('Mandigo Options','mandigo','mandigo').'</h2>
		
		<form name="mandigo_options_form" method="post" action="?page=functions.php">
		<input type="hidden" name="changedscheme" id="changedscheme" value="1" />
		
		<fieldset class="options">
		<legend>'.__("Color Schemes", "mandigo").'</legend>
		<input type="radio" name="scheme" value="blue"  '.  ($current == 'blue'   ? 'checked="checked"' : '') .' /><img src="'. get_bloginfo('template_directory') .'/scheme-blue.jpg"  alt="blue"    /> &nbsp;
		<input type="radio" name="scheme" value="red"   '.  ($current == 'red'    ? 'checked="checked"' : '') .' /><img src="'. get_bloginfo('template_directory') .'/scheme-red.jpg"   alt="red"     /> &nbsp;
		<input type="radio" name="scheme" value="green" '.  ($current == 'green'  ? 'checked="checked"' : '') .' /><img src="'. get_bloginfo('template_directory') .'/scheme-green.jpg" alt="green"   /> &nbsp;
		<input type="radio" name="scheme" value="pink" '.   ($current == 'pink'   ? 'checked="checked"' : '') .' /><img src="'. get_bloginfo('template_directory') .'/scheme-pink.jpg" alt="pink"     /> &nbsp;
		<input type="radio" name="scheme" value="purple" ' .($current == 'purple' ? 'checked="checked"' : '') .' /><img src="'. get_bloginfo('template_directory') .'/scheme-purple.jpg" alt="purple" /> &nbsp;
		<input type="radio" name="scheme" value="orange" ' .($current == 'orange' ? 'checked="checked"' : '') .' /><img src="'. get_bloginfo('template_directory') .'/scheme-orange.jpg" alt="orange" />  &nbsp;
		<input type="radio" name="scheme" value="teal"   ' .($current == 'teal'   ? 'checked="checked"' : '') .' /><img src="'. get_bloginfo('template_directory') .'/scheme-teal.jpg"   alt="teal"   /><br /><br />
		'.__("If you prefer to use your own header image with one of the color schemes, there is a blank header file named head.png in the mandigo/extras/ subfolder. Consult the README page for more information.","mandigo").'
		</fieldset>
<br />
		<fieldset class="options">
		<legend>'.__("Miscellaneous Options","mandigo").'</legend>
		<label><b>'.__("Page Navigation Overlay","mandigo").'</b></label><br />
                '.__("When enabled, this options overlays a translucent black stripe on the header for better readability.","mandigo").'<br/><br/>
		<input type="radio" name="headoverlay" value="0"  '. ($headoverlay ? '' : 'checked="checked"') .' /><img src="'. get_bloginfo('template_directory') .'/option-headoverlay-off.jpg" alt="off" /> &nbsp; 
		<input type="radio" name="headoverlay" value="1"  '. ($headoverlay ? 'checked="checked"' : '') .' /><img src="'. get_bloginfo('template_directory') .'/option-headoverlay-on.jpg"  alt="on"  /> &nbsp; 

		'.__("Make the stripe span:","mandigo").' <select name="headoverlay_oversize">
		<option value="0"'. ($headoverlay_oversize == 0 ? ' selected' : '') .'>'.__('1 line','mandigo').'</option>
		<option value="1"'. ($headoverlay_oversize == 1 ? ' selected' : '') .'>'.__('2 lines','mandigo').'</option>
		<option value="2"'. ($headoverlay_oversize == 2 ? ' selected' : '') .'>'.__('3 lines','mandigo').'</option>
		</select><br /><br />

		<label><b>'.__('Pages to Exclude from Header Navigation','mandigo').'</b></label><br />
		'. $pages_select .'<br />

		<label><b>'.__('Readability','mandigo').'</b></label><br/>
		<input type="checkbox" name="boldlinks" '. ($boldlinks ? 'checked="checked"' : '') .' />' .__('Display all links in bold for better readability','mandigo').'<br /><br /> 
					
		<label><b>'.__('Date Format','mandigo').'</b></label><br/>
		<input type="radio" name="dates" value="0"  '. ($dates ? '' : 'checked="checked"') .' />dd/mm/yyyy &nbsp; 
		<input type="radio" name="dates" value="1"  '. ($dates ? 'checked="checked"' : '') .' />month/dd/yyyy
		</fieldset>

		<p class="submit"><input type="submit" name="Submit" value="'.__('Update Options &raquo;','mandigo').'"/></p>
		</form>

		

		</div>
		';
		
		echo '
		<div id="preview" class="wrap">
		<h2 id="preview-post">'.__('Preview (updated when options are saved)','mandigo').'</h2>
		<iframe src="../?preview=true" width="100%" height="600" ></iframe>
		</div>
		';
	}	
}

function mandigo_readme_page() {
	echo '<div class="wrap">';
	echo '<pre>';
	include("README.txt");
	echo '</pre>';
	echo '</div>';
}
?>
