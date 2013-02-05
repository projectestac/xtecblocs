<?php

load_theme_textdomain('encurs', get_template_directory() . '/languages');

if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>',
    ));

// modded version to highlight parent menu !
function XGF_wp_list_pages($args = '') 
{
	parse_str($args, $r);
	if ( !isset($r['depth']) )
		$r['depth'] = 0;
	if ( !isset($r['show_date']) )
		$r['show_date'] = '';
	if ( !isset($r['child_of']) )
		$r['child_of'] = 0;
	if ( !isset($r['title_li']) )
		$r['title_li'] = __('Pages','encurs');
	if ( !isset($r['echo']) )
		$r['echo'] = 1;

	$output = '';

	$pages = & get_pages($args);
	if ( $pages ) {

		if ( $r['title_li'] )
			$output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';

		$page_tree = Array();
		foreach ( $pages as $page ) {
			$page_tree[$page->ID]['title'] = $page->post_title;
			$page_tree[$page->ID]['name'] = $page->post_name;
			if ( !empty($r['show_date']) ) {
				if ( 'modified' == $r['show_date'] )
					$page_tree[$page->ID]['ts'] = $page->post_modified;
				else
					$page_tree[$page->ID]['ts'] = $page->post_date;
			}
			if ( $page->post_parent != $page->ID)
				$page_tree[$page->post_parent]['children'][] = $page->ID;
		}
		$output .= XGF_page_level_out($r['child_of'],$page_tree, $r, 0, false);
		if ( $r['title_li'] )
			$output .= '</ul></li>';
	}

	$output = apply_filters('wp_list_pages', $output);

	if ( $r['echo'] )
		echo $output;
	else
		return $output;
}

// modded version to highlight parent menu !
function XGF_page_level_out($parent, $page_tree, $args, $depth = 0, $echo = true) 
{
	global $wp_query, $post, $wpdb;
	$queried_obj = $wp_query->get_queried_object();
	$output = '';
	
	$current_page = $post->ID;
	$i=0; // loop to get the top parent page
	while($current_page) {
		$i++;
		if (i>100) break; // avoid infinite loop
		$page_query = $wpdb->get_row("SELECT ID, post_title, post_parent FROM $wpdb->posts WHERE ID = '$current_page'");
		$current_page = $page_query->post_parent;
	}
	$parent_id = $page_query->ID;

	if ( $depth )
		$indent = str_repeat("\t", $depth);

	if ( !is_array($page_tree[$parent]['children']) )
		return false;

	foreach ( $page_tree[$parent]['children'] as $page_id ) {
		$cur_page = $page_tree[$page_id];
		$title = $cur_page['title'];

		$css_class = 'page_item';
		if ( $page_id == $queried_obj->ID || $page_id == $parent_id)
			$css_class .= ' current_page_item';

		$output .= $indent . '<li class="' . $css_class . '"><a href="' . get_page_link($page_id) . '" title="' . wp_specialchars($title) . '">' . $title . '</a>';

		if ( isset($cur_page['ts']) ) {
			$format = get_settings('date_format');
			if ( isset($args['date_format']) )
				$format = $args['date_format'];
			$output .= " " . mysql2date($format, $cur_page['ts']);
		}

		if ( isset($cur_page['children']) && is_array($cur_page['children']) ) {
			$new_depth = $depth + 1;

			if ( !$args['depth'] || $depth < ($args['depth']-1) ) {
				$output .= "$indent<ul>\n";
				$output .= freshy_page_level_out($page_id, $page_tree, $args, $new_depth, false);
				$output .= "$indent</ul>\n";
			}
		}
		$output .= "$indent</li>\n";
	}
	if ( $echo )
		echo $output;
	else
		return $output;
}

function themeoptions_admin_menu()
{
	// here's where we add our theme options page link to the dashboard sidebar
	add_theme_page("Opcions de l'aparença", "Opcions de l'aparença", 'edit_theme_options', basename(__FILE__), 'themeoptions_page');
}

function themeoptions_page()
{
	// here is the main function that will generate our options page
	
	if ( $_POST['update_themeoptions'] == 'true' ) { themeoptions_update(); }
	
	?>
	<div class="wrap">
		<div id="icon-themes" class="icon32"><br /></div>
		<h2>Opcions de l'aparença</h2>

		<form method="POST" action="">
			<input type="hidden" name="update_themeoptions" value="true" />
			
			<h4>Estil personalitzat</h4>
			<select name ="colour">
				<?php $colour = get_option('xtec_encurs_colour'); ?>
				<option value="dark_cyan" <?php if ($colour=='dark_cyan') { echo 'selected'; } ?> >Dark Cyan Stylesheet</option>
				<option value="yellow_2" <?php if ($colour=='yellow_2') { echo 'selected'; } ?> >Yellow 2 Stylesheet</option>
				<option value="yellow_green" <?php if ($colour=='yellow_green') { echo 'selected'; } ?>>Yellow Green Stylesheet</option>
				<option value="dark_slate_gray" <?php if ($colour=='dark_slate_gray') { echo 'selected'; } ?>>Dark Slate Gray Stylesheet</option>
				<option value="clear_cyan" <?php if ($colour=='clear_cyan') { echo 'selected'; } ?>>Clear Cyan Stylesheet</option>
			</select>

			<h4>Imatge de la capçalera</h4>
			<p><input type="text" name="imageurl" id="imageurl" size="96" value="<?php echo get_option('xtec_encurs_imageurl'); ?>"/> URL de la imatge</p>

			<h4>Bloc principal</h4>
			<p><input type="text" name="mainurl" id="mainurl" size="96" value="<?php echo get_option('xtec_encurs_mainurl'); ?>"/> URL del bloc</p>

			<h4>Nom de la pàgina d'inici</h4>
			<p><input type="text" name="homename" id="homename" size="32" value="<?php echo get_option('xtec_encurs_homename'); ?>"/></p>

			<p><input type="submit" name="search" value="Desa els canvis" class="button" /></p>
		</form>

	</div>
	<?php
}

function themeoptions_update()
{
	// this is where validation would go
	update_option('xtec_encurs_colour',$_POST['colour']);
	update_option('xtec_encurs_imageurl',$_POST['imageurl']);
	update_option('xtec_encurs_mainurl',$_POST['mainurl']);
	update_option('xtec_encurs_homename',$_POST['homename']);
}

add_action('admin_menu', 'themeoptions_admin_menu');

?>