<?php

load_theme_textdomain('freshy', get_template_directory() . '/languages');

if ( function_exists('register_sidebar') ) {
    register_sidebar(array(
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
        'before_title' => '<h2 class="title">',
        'after_title' => '</h2>',
    ));
	
}



function freshy_menu($args_pages='', $args_cats='') {
	
	global $post, $wpdb, $cat, $ID, $notfound, $freshy_options;

	if ($freshy_options['menu_type']=='auto') {

		// page menu
		if (($post->post_status=='static' || is_page()) && $notfound!='1' && $args_pages!='none') {
			$current_page = $post->ID;
			$i=0; // loop to get the top parent page
			while($current_page) {
				$i++;
				if (i>100) break; // avoid infinite loop
				$page_query = $wpdb->get_row("SELECT ID, post_title, post_parent FROM $wpdb->posts WHERE ID = '$current_page'");
				$current_page = $page_query->post_parent;
			}
			$parent_id = $page_query->ID;
			$parent_title = $page_query->post_title;

	        $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = '$parent_id'");
	        ?>
			<h2><?php _e('Navigation', 'freshy'); ?></h2>
			<ul>
			<?php
	        wp_list_pages($args_pages.'&child_of='.$parent_id.'&title_li=');
	        ?>
	        </ul>
	        <?php
		}
		// cats & posts menu
		else if (!is_page() && $notfound!='1' && $args_cats!='none') {
			?>
			<h2><?php _e('Navigation', 'freshy'); ?></h2>
			<ul>
			<?php
	        wp_list_cats($args_cats);
	        ?>
	        </ul>
	        <?php
		}
		// bad things happened but dispay something anyway
		else {
			?>
				<h2><?php _e('Pages', 'freshy'); ?></h2>
				<ul>
				<?php wp_list_pages('sort_column=menu_order&title_li='); ?>
				</ul>
				<h2><?php _e('Blog', 'freshy'); ?></h2>
				<ul>
				<?php wp_list_cats('sort_column=name&optioncount=1&title_li=&hierarchical=1&feed=RSS&feed_image='.get_bloginfo('stylesheet_directory').'/images/icons/feed-icon-10x10.gif'); ?>
				</ul>
			<?php
		}
	}
	else {
		?>
			<h2><?php _e('Pages', 'freshy'); ?></h2>
			<ul>
			<?php wp_list_pages('sort_column=menu_order&title_li='); ?>
			</ul>
			<h2><?php _e('Blog', 'freshy'); ?></h2>
			<ul>
			<?php wp_list_cats('sort_column=name&optioncount=1&title_li=&hierarchical=1&feed=RSS&feed_image='.get_bloginfo('stylesheet_directory').'/images/icons/feed-icon-10x10.gif'); ?>
			</ul>
		<?php
	}
}

// modded version to highlight parent menu !
function freshy_wp_list_pages($args = '') {
	parse_str($args, $r);
	if ( !isset($r['depth']) )
		$r['depth'] = 0;
	if ( !isset($r['show_date']) )
		$r['show_date'] = '';
	if ( !isset($r['child_of']) )
		$r['child_of'] = 0;
	if ( !isset($r['title_li']) )
		$r['title_li'] = __('Pages');
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
		$output .= freshy_page_level_out($r['child_of'],$page_tree, $r, 0, false);
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
function freshy_page_level_out($parent, $page_tree, $args, $depth = 0, $echo = true) {
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


// SET OPTIONS

$freshy_options=array();
//update_option('freshy_options', $freshy_options);
$freshy_theme_default=array();
$freshy_theme_red=array();
$freshy_theme_blue=array();
$freshy_theme_lime=array();

freshy_set_options();

function freshy_set_options() {
	
	global $freshy_options, $freshy_theme_red, $freshy_theme_lime, $freshy_theme_blue;
	
	$freshy_theme_default['highlight_color']='#FF3C00';
	$freshy_theme_default['description_color']='#ADCF20';
	$freshy_theme_default['author_color']='#a3cb00';
	$freshy_theme_default['sidebar_bg']='#FFFFFF';
	$freshy_theme_default['sidebar_titles_color']='#f78b0c';
	$freshy_theme_default['sidebar_titles_bg']='#FFFFFF';
	$freshy_theme_default['menu_bg']='menu_start_triple.gif';
	$freshy_theme_default['menu_color']='#000000';
	$freshy_theme_default['header_bg']='header_image6.jpg';
	$freshy_theme_default['header_bg_custom']='';
	$freshy_theme_default['sidebar_titles_type']='stripes';
	
	$freshy_theme_default['first_menu_label']='Home';
	$freshy_theme_default['blog_menu_label']='Blog';
	$freshy_theme_default['last_menu_label']='Contact';
	$freshy_theme_default['last_menu_type']='';
	$freshy_theme_default['contact_email']='';
	$freshy_theme_default['contact_link']='';
	
	$freshy_theme_default['menu_type']='auto';
	$freshy_theme_default['args_pages']='sort_column=menu_order&title_li=';
	$freshy_theme_default['args_cats']='hide_empty=0&sort_column=name&optioncount=1&title_li=&hierarchical=1&feed=RSS&feed_image='.get_bloginfo('stylesheet_directory').'/images/icons/feed-icon-10x10.gif';
	
	$freshy_theme_lime['highlight_color']='#FF3C00';
	$freshy_theme_lime['description_color']='#ADCF20';
	$freshy_theme_lime['author_color']='#a3cb00';
	$freshy_theme_lime['sidebar_bg']='#FFFFFF';
	$freshy_theme_lime['sidebar_titles_color']='#f78b0c';
	$freshy_theme_lime['sidebar_titles_bg']='#FFFFFF';
	$freshy_theme_lime['menu_bg']='menu_start_triple.gif';
	$freshy_theme_lime['menu_color']='#000000';
	$freshy_theme_lime['header_bg']='header_image6.jpg';
	$freshy_theme_lime['header_bg_custom']='';
	$freshy_theme_lime['sidebar_titles_type']='stripes';

	
	$freshy_theme_red['highlight_color']='#d80f2a';
	$freshy_theme_red['description_color']='#eca50d';
	$freshy_theme_red['author_color']='#eca50d';
	$freshy_theme_red['sidebar_bg']='#F3F3F3';
	$freshy_theme_red['sidebar_titles_color']='#000000';
	$freshy_theme_red['sidebar_titles_bg']='#c2c2c2';
	$freshy_theme_red['menu_bg']='menu_start_triple_red.gif';
	$freshy_theme_red['menu_color']='#ffffff';
	$freshy_theme_red['header_bg']='header_image8.jpg';
	$freshy_theme_red['header_bg_custom']='';
	$freshy_theme_red['sidebar_titles_type']='stripes';
	

	$freshy_theme_blue['highlight_color']='#f5690c';
	$freshy_theme_blue['description_color']='#ff6c00';
	$freshy_theme_blue['author_color']='#f5bb0c';
	$freshy_theme_blue['sidebar_bg']='#dbefff';
	$freshy_theme_blue['sidebar_titles_color']='#0f80d8';
	$freshy_theme_blue['sidebar_titles_bg']='#FFFFFF';
	$freshy_theme_blue['menu_bg']='menu_start_triple_lightblue.gif';
	$freshy_theme_blue['menu_color']='#ffffff';
	$freshy_theme_blue['header_bg']='header_image3.jpg';
	$freshy_theme_blue['header_bg_custom']='';
	$freshy_theme_blue['sidebar_titles_type']='stripes';

	/*
	
	if (get_option('freshy_options')) {
		$freshy_options = get_option('freshy_options');
	}
	else {
		$freshy_options=$freshy_theme_default;
		update_option('freshy_options', $freshy_options);
	}
	*/
	if (get_option('freshy_options')) {
		$existing_options = get_option('freshy_options');
		foreach ($freshy_theme_default as $key=>$val) { 
			$freshy_options[$key]=$val;
		}
		foreach ($existing_options as $key=>$val) { 
			$freshy_options[$key]=$val;
		}
	}
	else {
		$freshy_options=$freshy_theme_default;
		update_option('freshy_options', $freshy_options);
	}

}


// ADD HEAD TO THE TEMPLATE

add_action('wp_head', 'freshy_head');

function freshy_head() {
	
	global $freshy_options, $freshy_theme_lime;
	
	$menu_triple = str_replace("menu_start_triple", "menu_triple", $freshy_options['menu_bg']);
	$menu_end_triple = str_replace("menu_start_triple", "menu_end_triple", $freshy_options['menu_bg']);

	
	?>

	<style type="text/css">
	.menu li a {
		background-image:url("<?php bloginfo('stylesheet_directory'); ?>/images/menu/<?php echo $menu_triple; ?>");
	}
	.menu li a.first_menu {
		background-image:url("<?php bloginfo('stylesheet_directory'); ?>/images/menu/<?php echo $freshy_options['menu_bg']; ?>");
	}
	.menu li a.last_menu {
		background-image:url("<?php bloginfo('stylesheet_directory'); ?>/images/menu/<?php echo $menu_end_triple; ?>");
	}
	.menu li.current_page_item a {
		color:<?php echo $freshy_options['menu_color']; ?> !important;
	}
	
	.description {
		color:<?php echo $freshy_options['description_color']; ?>;
	}
	#content .commentlist dd.author_comment {
		background-color:<?php echo $freshy_options['author_color']; ?> !important;
	}
	html > body #content .commentlist dd.author_comment {
		background-color:<?php echo $freshy_options['author_color']; ?> !important;
	}
	#content .commentlist dt.author_comment .date {
		color:<?php echo $freshy_options['author_color']; ?> !important;
		border-color:<?php echo $freshy_options['author_color']; ?> !important;
	}
	#content .commentlist .author_comment .author,
	#content .commentlist .author_comment .author a {
		color:<?php echo $freshy_options['author_color']; ?> !important;
		border-color:<?php echo $freshy_options['author_color']; ?> !important;
	}
	#sidebar h2 {
		color:<?php echo $freshy_options['sidebar_titles_color']; ?>;
		background-color:<?php echo $freshy_options['sidebar_titles_bg']; ?>;
		border-bottom-color:<?php echo $freshy_options['sidebar_titles_color']; ?>;
	}
	#sidebar {
		background-color:<?php echo $freshy_options['sidebar_bg']; ?>;
	}
	*::-moz-selection {
		background-color:<?php echo $freshy_options['highlight_color']; ?>;
	}

	#content a:hover {
		border-bottom:1px dotted <?php echo $freshy_options['highlight_color']; ?>;
	}

	#sidebar a:hover,
	#sidebar .current_page_item li a:hover,
	#sidebar .current-cat li a:hover,
	#sidebar .current_page_item a,
	#sidebar .current-cat a ,
	.readmore,
	#content .postmetadata a
	{
		color : <?php echo $freshy_options['highlight_color']; ?>;
	}
	
	#title_image {
		margin:0;
		text-align:left;
		display:block;
		height:95px;
		<?php
		if ($freshy_options['header_bg_custom']=='') { ?>
		background-image:url("<?php bloginfo('stylesheet_directory'); ?>/images/headers/<?php echo $freshy_options['header_bg']; ?>");
	
		<?php } else if ($freshy_options['header_bg_random']=='true') { ?>
		background-image:url("<?php bloginfo('stylesheet_directory'); ?>/images/headers/<?php echo $freshy_options['header_bg']; ?>");
		
		<?php } else { ?>
		background-image:url("<?php echo $freshy_options['header_bg_custom']; ?>");
		<?php } ?>
	}
	
	</style>

	<?php
}

// ADMIN

add_action('admin_menu', 'freshy_add_theme_page');
add_action('admin_head', 'freshy_admin_head');
//add_action('admin_footer', 'freshy_admin_footer');

/*
function header_graphic() {
  $array_header_strings=freshy_list_files('../wp-content/themes/'.freshy_stupid_dir().'/images/headers');
  echo $array_header_strings.count();
}
*/

function freshy_admin_head() {
	echo '<script type="text/javascript" src="';
	bloginfo('stylesheet_directory');
	echo '/freshy-admin-js.js"></script>';
}


function freshy_add_theme_page() {
	add_theme_page(__('Freshy Theme Options', 'freshy'), __('Freshy Theme Options', 'freshy'), 'edit_theme_options', basename(__FILE__), 'freshy_theme_page');
}

function freshy_set_theme($theme,$options) { 
	
	global $freshy_options, $freshy_theme_red, $freshy_theme_lime, $freshy_theme_blue;
	
	if ($theme=='lime') {
		foreach ($freshy_theme_lime as $key=>$val) { 
			$freshy_options[$key]=$val;
		}
	}
	else if ($theme=='red') {
		foreach ($freshy_theme_red as $key=>$val) { 
			$freshy_options[$key]=$val;
		}
	}
	else if ($theme=='blue') {
		foreach ($freshy_theme_blue as $key=>$val) { 
			$freshy_options[$key]=$val;
		}
	}/*
	else if ($theme=='red') $freshy_options=$freshy_theme_red;
	else if ($theme=='blue') $freshy_options=$freshy_theme_blue;*/
	$freshy_options['theme']=$theme;
	$freshy_options['advanced_options']=$options;
	update_option('freshy_options', $freshy_options);
}

function freshy_list_files($dirpath,$filter='') {
	$returned_array=array();
    $dh = opendir($dirpath);
	while (false !== ($file = readdir($dh))) {
		if (!is_dir("$dirpath/$file")) {
			if ($filter!='' && strstr($file, $filter) != false) $returned_array[$file] = $file;
			else if ($filter=='') $returned_array[$file] = $file;
		}
	}
    closedir($dh);
    return $returned_array;
}

function freshy_theme_page() {
	
	global $freshy_options;
	
	if ( $_GET['page'] == basename(__FILE__) ) {
		
		$array_themes_strings = array('lime','blue','red');
		
		if (isset($_POST['freshy_options_update'])) {
			
		   	$freshy_updated_options = array();
			$freshy_updated_options = $_POST;
			if (isset($freshy_updated_options['theme']) && $freshy_updated_options['changedtheme']==1) freshy_set_theme($freshy_updated_options['theme'],$freshy_updated_options['advanced_options']);
			else {
				$freshy_updated_options['header_bg_custom']=wp_kses_bad_protocol($freshy_updated_options['header_bg_custom'],array('http','https'));
				update_option('freshy_options', $freshy_updated_options);
				$freshy_options = get_option('freshy_options');
				echo '<div class="updated"><p>' . __('Freshy options updated.','freshy') . '</p></div>';
			}
		}
		
		echo '
		<div class="wrap">
		<h2>'.__('Freshy Options','freshy').'</h2>
		
		<form name="freshy_options_form" method="post">
		<input type="hidden" name="freshy_options_update" value="update" />
		<input type="hidden" name="changedtheme" id="changedtheme" value="0" />
		
		<fieldset class="options">
		<legend>'.__('Theme switcher','freshy').'</legend>
		<table width="100%" cellspacing="2" cellpadding="5" class="editform">
			<col style="width:50%;"/><col/>
			<tr>
				<td>
					<label>'.__('Choose a color theme','freshy').' </label>
					<br/>
					<small>'.__('note : just choose the theme and update, it won\'t save other changes', 'freshy').'</small>
				</td>
				<td>
				<select name="theme" onclick="document.getElementById(\'changedtheme\').value=\'1\';">
					<option value="none">'.__('none','freshy').'</option>
				';
				foreach ($array_themes_strings as $theme) { 
					echo '
						<option value="'.$theme.'"';
							if ($freshy_options['theme'] == $theme) echo 'selected="selected" ';
							echo '>
							'.$theme.'
						</option>
					';
				}
				echo '</select>				
				</td>
			</tr>
		</table>
		</fieldset>
					
		<fieldset class="options">
		<legend>'.__('Top Menu options','freshy').'</legend>
		<table id="freshy_menu_options" width="100%" cellspacing="2" cellpadding="5" class="editform">
			<col style="width:50%;"/><col/>
			<tr>
				<td>
					<label>'.__('Sidebar menu behaviour','freshy').'</label>
				</td>
				<td>					
					<label for="menu_type_auto"><input name="menu_type" id="menu_type_auto" type="radio" value="auto" ';
					if ($freshy_options['menu_type'] == 'auto') {echo 'checked="checked" ';}
					echo '/> '.__('Auto : Display subpages on pages and blog categories in blog','freshy').'</label>

					<br/>
					
					<label for="menu_type_normal"><input name="menu_type" id="menu_type_normal" type="radio" value="normal" ';
					if ($freshy_options['menu_type'] == 'normal') {echo 'checked="checked" ';}
					echo '/> '.__('Normal : Always display categories and pages','freshy').'</label>
				</td>
			</tr>	
			<tr>
				<td>
					<label>'.__('Enter the label of the Homepage menu link','freshy').' </label>
					<br/>
					<small>'.__('info : modifying these labels should break internationalisation','freshy').'</small>
				</td>
				<td>
					<input name="first_menu_label" type="text" value="'.$freshy_options['first_menu_label'].'"/>
				</td>
			</tr>';
			/*
			<tr>
				<td>
					<label>'.__('The first menu link should be...','freshy').'</label>
				</td>
				<td>
					<label for="first_menu_type_home"><input name="first_menu_type" id="first_menu_type_home" type="radio" value="email" ';
					if ($freshy_options['first_menu_type'] == 'home') {echo 'checked="checked" ';}
					echo '/> '.__('A link to the homepage','freshy').'</label>

					<br/>
					
					<label for="first_menu_type_link"><input name="first_menu_type" id="first_menu_type_link" type="radio" value="link" ';
					if ($freshy_options['first_menu_type'] == 'link') {echo 'checked="checked" ';}
					echo '/> '.__('A custom link with this url','freshy').' :</label>
					<input name="first_menu_url" type="text" value="'.$freshy_options['first_menu_url'].'"/>
					
				</td>
			</tr>
			*/
			if(function_exists('yy_menu')) {
				echo '
				<tr>
					<td>
						<label>'.__('Enter the label of the Blog menu link','freshy').' </label>
						<br/>
						<small>info : this is specially for YammYamm</small>
					</td>
					<td>
						<input name="blog_menu_label" type="text" value="'.$freshy_options['blog_menu_label'].'"/>
					</td>
				</tr>';
			}
			/*
			echo '
				<!-- custom 
			<tr>
				<td>
					<label>'.__('Custom menu link','freshy').' </label>
				</td>
				<td>
					<input name="custom_menu_label_0" type="text" value="'.$freshy_options['custom_menu_label_0'].'"/>
					<label>url :</label>
					<input name="custom_menu_url_0" type="text" value="'.$freshy_options['custom_menu_url_0'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Custom menu link','freshy').' </label>
				</td>
				<td>
					<input name="custom_menu_label_1" type="text" value="'.$freshy_options['custom_menu_label_1'].'"/>
					<label>url :</label>
					<input name="custom_menu_url_1" type="text" value="'.$freshy_options['custom_menu_url_1'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Custom menu link','freshy').' </label>
				</td>
				<td>
					<input name="custom_menu_label_2" type="text" value="'.$freshy_options['custom_menu_label_2'].'"/>
					<label>url :</label>
					<input name="custom_menu_url_2" type="text" value="'.$freshy_options['custom_menu_url_2'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Custom menu link','freshy').' </label>
				</td>
				<td>
					<input name="custom_menu_label_3" type="text" value="'.$freshy_options['custom_menu_label_3'].'"/>
					<label>url :</label>
					<input name="custom_menu_url_3" type="text" value="'.$freshy_options['custom_menu_url_3'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Custom menu link','freshy').' </label>
				</td>
				<td>
					<input name="custom_menu_label_4" type="text" value="'.$freshy_options['custom_menu_label_4'].'"/>
					<label>url :</label>
					<input name="custom_menu_url_4" type="text" value="'.$freshy_options['custom_menu_url_4'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Custom menu link','freshy').' </label>
				</td>
				<td>
					<input name="custom_menu_label_5" type="text" value="'.$freshy_options['custom_menu_label_5'].'"/>
					<label>url :</label>
					<input name="custom_menu_url_5" type="text" value="'.$freshy_options['custom_menu_url_5'].'"/>
				</td>
			</tr>-->
				*/
			echo '
			<tr>
				<td>
					<label>'.__('Enter the label of the last menu link','freshy').' </label>
				</td>
				<td>
					<input name="last_menu_label" type="text" value="'.$freshy_options['last_menu_label'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('The last menu link should be...','freshy').'</label>
				</td>
				<td>					
					<label for="last_menu_type_email"><input name="last_menu_type" id="last_menu_type_email" type="radio" value="email" ';
					if ($freshy_options['last_menu_type'] == 'email') {echo 'checked="checked" ';}
					echo '/> '.__('A contact button with this email address','freshy').' :</label>
					<input name="contact_email" type="text" value="'.$freshy_options['contact_email'].'"/>

					<br/>
					
					<label for="last_menu_type_link"><input name="last_menu_type" id="last_menu_type_link" type="radio" value="link" ';
					if ($freshy_options['last_menu_type'] == 'link') {echo 'checked="checked" ';}
					echo '/> '.__('A custom link with this url','freshy').' :</label>
					<input name="contact_link" type="text" value="'.$freshy_options['contact_link'].'"/>
						
					<br/>
												
					<label for="last_menu_type_none"><input name="last_menu_type" id="last_menu_type_none" type="radio" value="" ';
					if ($freshy_options['last_menu_type'] == '') {echo 'checked="checked" ';}
					echo '/> '.__('Nothing','freshy').'</label>
				</td>
			</tr>
		</table>
		</fieldset>
		
		<fieldset class="options">
		<legend><a style="cursor:pointer;" onclick="switchDiv(\'freshy_advanced_options\');">'.__('Advanced options','freshy').'</a></legend>
		<table';
			if ($freshy_options['advanced_options'] != 1) echo ' style="display:none;"';
			echo ' id="freshy_advanced_options" width="100%" cellspacing="2" cellpadding="5" class="editform">
			<col style="width:50%;"/><col/>
			<tr>
				<td colspan="2">
					<label><input name="advanced_options" type="checkbox" value="1"';
					if ($freshy_options['advanced_options'] == 1) {echo 'checked="checked" ';}
					echo '/> '.__('Always show advanced options','freshy').'</label>
				</td>
			</tr>

			<tr>
				<td>
					<label>'.__('Use these arguments for the pages menu','freshy').' </label>
				</td>
				<td>
					<input id="args_pages" name="args_pages" type="text" value="'.$freshy_options['args_pages'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Use these arguments for the blog categories menu','freshy').' </label>
				</td>
				<td>
					<input id="args_cats" name="args_cats" type="text" value="'.$freshy_options['args_cats'].'"/>
				</td>
			</tr>

			<tr>
				<td>
					<label>'.__('Site description color (under the title of the site)','freshy').' </label>
					<br/>
					<small>'.__('tip : you can use color names like red, darkblue or ThreeDFace', 'freshy').'</small>
				</td>
				<td>
					<input name="description_color" type="text" value="'.$freshy_options['description_color'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Highlight color (for links and selection)','freshy').' </label>
				</td>
				<td>
					<input name="highlight_color" type="text" value="'.$freshy_options['highlight_color'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Author color (for comments by the author)','freshy').' </label>
				</td>
				<td>
					<input name="author_color" type="text" value="'.$freshy_options['author_color'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Sidebar background color','freshy').' </label>
				</td>
				<td>
					<input name="sidebar_bg" type="text" value="'.$freshy_options['sidebar_bg'].'"/>
				</td>
			</tr>
		<!--	<tr>
				<td>
					<label>'.__('Use stripes or plain color for the sidebar titles background ?','freshy').' </label>
				</td>
				<td>
					<input name="sidebar_titles_type" type="text" value="'.$freshy_options['sidebar_titles_type'].'"/>
				</td>
			</tr>-->
			<tr>
				<td>
					<label>'.__('Sidebar titles color (the text color)','freshy').' </label>
				</td>
				<td>
					<input name="sidebar_titles_color" type="text" value="'.$freshy_options['sidebar_titles_color'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Sidebar titles background color (the strpies color)','freshy').' </label>
				</td>
				<td>
					<input name="sidebar_titles_bg" type="text" value="'.$freshy_options['sidebar_titles_bg'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Active menu text color (the site navigation menu)','freshy').' </label>
				</td>
				<td>
					<input name="menu_color" type="text" value="'.$freshy_options['menu_color'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Active menu background color (the site navigation menu)','freshy').' </label>
				</td>
				<td>
					<ul style="style-type:none;padding:0;">';
					
					$array_color_strings=freshy_list_files('../wp-content/themes/'.freshy_stupid_dir().'/images/menu','menu_start');
					foreach ($array_color_strings as $menu) { 
						echo '
							<li style="border:1px solid silver;float:left;margin:2px;width:50px;height:50px;display:block;background:url('
							.get_bloginfo('stylesheet_directory').'/images/menu/'.$menu.') transparent bottom left;">
								<input name="menu_bg" type="radio" value="'.$menu.'" ';
								if ($freshy_options['menu_bg'] == $menu) {echo 'checked="checked" ';}
								echo '/>
							</li>
						';
					}
					echo '
					</ul>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Choose a header image...','freshy').' </label>
				</td>
				<td>
					<ul style="clear:both;style-type:none;padding:0;">';
					$array_header_strings=freshy_list_files('../wp-content/themes/'.freshy_stupid_dir().'/images/headers');
					foreach ($array_header_strings as $header_bg) { 
						echo '
							<li style="border:1px solid silver;float:left;margin:2px;width:180px;height:50px;display:block;background:url('
							.get_bloginfo('stylesheet_directory').'/images/headers/'.$header_bg.') transparent 0 0;">
								<input onchange="document.getElementById(\'header_bg_custom\').value=\'\';" name="header_bg" type="radio" value="'.$header_bg.'" ';
								if ($freshy_options['header_bg'] == $header_bg && $freshy_options['header_bg_custom']=='') {echo 'checked="checked" ';}
								echo '/>
							</li>
						';
					}
					echo '
					</ul>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('...Or type the absolute url of your own image','freshy').' </label>
				</td>
				<td>
					<div style="border:1px solid silver;float:left;margin:2px;width:400px;height:50px;display:block;background:url(';
						echo $freshy_options['header_bg_custom'].') transparent 0 0;">
					<input style="margin:10px;width:370px;" id="header_bg_custom" name="header_bg_custom" type="text" value="'.$freshy_options['header_bg_custom'].'"/>
					</div>
				</td>
			</tr>
		</table>
		</fieldset>
						
		<p class="submit"><input type="submit" name="Submit" value="'.__('Update Options &raquo;','freshy').'"/></p>
		</form>
		</div>
		';
		
		$msg_preview =__('Preview (updated when options are saved)','freshy');
		echo '
		<div id="preview" class="wrap">
		<h2 id="preview-post">'. $msg_preview
		.
		'</h2>
			<iframe src="../?preview=true" width="100%" height="600" ></iframe>
		</div>
		';
	}	

}

function freshy_stupid_dir() {
	return substr(strrchr(get_bloginfo('stylesheet_directory'), "/"),1);	
}

function freshy_theme_page_head() {
?>
<style type='text/css'>
</style>
<?php
}
?>
