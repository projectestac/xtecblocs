<?php

load_theme_textdomain('xtec', get_template_directory() . '/languages');

if ( function_exists('register_sidebar') ) {
    register_sidebar(array(
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
        'before_title' => '<h2 class="title">',
        'after_title' => '</h2>',
    ));
	unregister_sidebar_widget ('Links'); // ??
	register_sidebar_widget(__('Links for Freshy','xtec'), 'yy_widget_links');
}

function yy_widget_links($args) {
	global $wpdb;
	$title = empty($options['title']) ? __('Links','xtec') : $options['title'];
	
	 $link_cats = $wpdb->get_results("SELECT term_id, name FROM $wpdb->terms");
	 ?>
	 <h2><?php echo $before_widget.$before_title.$title.$after_title; ; ?></h2>
	<ul>
	<?php
	 foreach ($link_cats as $link_cat) {
	 ?>
	  <li id="linkcat-<?php echo $link_cat->term_id; ?>"><?php echo $link_cat->name; ?>
	   <ul>
	    <?php wp_get_links($link_cat->term_id); ?>
	   </ul>
	  </li>
	 <?php } ?>
	 </ul>
	 <?php
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
			<h2><?php _e('Navigation','xtec'); ?></h2>
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
			<h2><?php _e('Navigation','xtec'); ?></h2>
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
				<h2><?php _e('Pages','xtec'); ?></h2>
				<ul>
				<?php wp_list_pages('sort_column=menu_order&title_li='); ?>
				</ul>
				<h2><?php _e('Blog','xtec'); ?></h2>
				<ul>
				<?php wp_list_cats('sort_column=name&optioncount=1&title_li=&hierarchical=1&feed=RSS&feed_image='.get_bloginfo('stylesheet_directory').'/images/icons/feed-icon-10x10.gif'); ?>
				</ul>
			<?php
		}
	}
	else {
		?>
			<h2><?php _e('Pages','xtec'); ?></h2>
			<ul>
			<?php wp_list_pages('sort_column=menu_order&title_li='); ?>
			</ul>
			<h2><?php _e('Blog','xtec'); ?></h2>
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
		$r['title_li'] = __('Pages','xtec');
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
	$freshy_theme_default['header_bg']='header.jpg';
	$freshy_theme_default['header_bg_custom']='';
	$freshy_theme_default['sidebar_titles_type']='stripes';
	
	$freshy_theme_default['first_menu_label']='Inici';
	$freshy_theme_default['blog_menu_label']='Blog';
	$freshy_theme_default['last_menu_label']='Contact';
	$freshy_theme_default['last_menu_type']='';
	$freshy_theme_default['contact_email']='';
	$freshy_theme_default['contact_link']='';
	
	$freshy_theme_default['menu_type']='auto';
	$freshy_theme_default['args_pages']='sort_column=menu_order&title_li=';
	$freshy_theme_default['args_cats']='hide_empty=0&sort_column=name&optioncount=1&title_li=&hierarchical=1&feed=RSS&feed_image='.get_bloginfo('stylesheet_directory').'/images/icons/feed-icon-10x10.gif';
	
	
	/*$freshy_theme_lime['highlight_color']='#FF3C00';
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
	$freshy_theme_blue['header_bg']='header.jpg';
	$freshy_theme_blue['header_bg_custom']='';
	$freshy_theme_blue['sidebar_titles_type']='stripes';

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

	if ($freshy_options['css_style_custom']=='') {

	?>

	<style type="text/css">
	
	#title {
		<?php if ($freshy_options['header_bg_custom']=='') { ?>
		background-image:url("<?php bloginfo('stylesheet_directory'); ?>/images/headers/<?php echo $freshy_options['header_bg']; ?>") ;
			
		<?php } else { ?>
		background-image:url("<?php echo $freshy_options['header_bg_custom']; ?>");
		background-position: <?php echo $freshy_options['position_vertical_bg_custom']; ?> <?php echo $freshy_options['position_horitzontal_bg_custom']; ?>;
		
		<?php } ?>
	}
	
	#title h1 a {	color: <?php echo $freshy_options['color_title_custom']; ?>; }
	#title h1 a:visited {	color: <?php echo $freshy_options['color_title_custom']; ?>; }
	#title h1 a:link {	color: <?php echo $freshy_options['color_title_custom']; ?>; }
	#title .description {color: <?php echo $freshy_options['color_subtitle_custom']; ?>; }
	
	</style>

	<?php
	}
	
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
	add_theme_page(__('XTEC Theme Options','xtec'), __('XTEC Theme Options','xtec'), 'edit_theme_options', basename(__FILE__), 'freshy_theme_page');
}
/*
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
	}
	//else if ($theme=='red') $freshy_options=$freshy_theme_red;
	//else if ($theme=='blue') $freshy_options=$freshy_theme_blue;
	$freshy_options['theme']=$theme;
	$freshy_options['advanced_options']=$options;
	update_option('freshy_options', $freshy_options);
}*/

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
				echo '<div class="updated"><p>' . __('XTEC options updated.','xtec') . '</p></div>';
			}
		}
		
		echo '
		<div class="wrap">
		<h2>'.__('XTEC Options','xtec').'</h2>
		
		<form name="freshy_options_form" method="post">
		<input type="hidden" name="freshy_options_update" value="update" />
		<input type="hidden" name="changedtheme" id="changedtheme" value="0" />
					
					
		<fieldset class="options">
		<legend>'.__('Theme switcher','xtec').'</legend>
		<table id="freshy_menu_options" width="100%" cellspacing="2" cellpadding="5" class="editform">
			<col style="width:50%;"/><col/>
			<tr>
				<td>
					<label>'.__('Enter the label of the Homepage menu link','xtec').' </label>
					<br/>
					<small>'.__('info : modifying these labels should break internationalisation','xtec').'</small>
				</td>
				<td>
					<input name="first_menu_label" type="text" value="'.$freshy_options['first_menu_label'].'"/>
				</td>
			</tr>';
			
			if(function_exists('yy_menu')) {
				echo '
				<tr>
					<td>
						<label>'.__('Enter the label of the Blog menu link','xtec').' </label>
						<br/>
						<small>'.__('info : this is specially for YammYamm','xtec').'</small>
					</td>
					<td>
						<input name="blog_menu_label" type="text" value="'.$freshy_options['blog_menu_label'].'"/>
					</td>
				</tr>';
			}

			echo '
			<tr>
				<td>	
					<label>'.__('Enter the label of the last menu link','xtec').' </label>
				</td>
				<td>
					<input name="last_menu_label" type="text" value="'.$freshy_options['last_menu_label'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Title color, ex: #006699','xtec').' </label>
				</td>
				<td>
					<input style="width:100px;" id="color_title_custom" name="color_title_custom" type="text" value="'.$freshy_options['color_title_custom'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Subtitle color, ex: #3af567','xtec').' </label>
				</td>
				<td>
					<input style="width:100px;" id="color_subtitle_custom" name="color_subtitle_custom" type="text" value="'.$freshy_options['color_subtitle_custom'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Absolute url of your own css style','xtec').' </label><br />
					<small><a href="'. get_bloginfo('stylesheet_directory').'/style-default.css" title="'.__('Click','xtec').'">'.__('Download css default','xtec').'</a></small>
				</td>
				<td>
					<input style="width:370px;" id="css_style_custom" name="css_style_custom" type="text" value="'.$freshy_options['css_style_custom'].'"/>
				</td>
			</tr>
			<tr>
				<td>
					<label>'.__('Absolute url of your own image','xtec').' </label>
				</td>
				<td>
					<div style="border:1px solid silver;float:left;margin:2px;width:400px;height:50px;display:block;background:url(';
						echo $freshy_options['header_bg_custom'].') transparent 0 0;">
					<input style="margin:10px;width:370px;" id="header_bg_custom" name="header_bg_custom" type="text" value="'.$freshy_options['header_bg_custom'].'"/>
					</div>
				</td>
			</tr>
			';

			if ($freshy_options['header_bg_custom']	!= "" ) {
			
			echo '
			<tr>
				<td>
					<label>'.__('Background vertical position','xtec').' </label>
				</td>
				<td>
					<select id="position_vertical_bg_custom" name="position_vertical_bg_custom" size="3">\n';
			$RB_var="";
			if($freshy_options['position_vertical_bg_custom']=="top") { $RB_var='selected="yes"'; }
			echo '<option value="top" '.$RB_var.' >'.__('Top','xtec').'</option>\n';
			$RB_var="";
			if($freshy_options['position_vertical_bg_custom']=="middle") { $RB_var='selected="yes"'; }
			echo '<option value="middle" '.$RB_var.' >'.__('Middle','xtec').'</option>\n';
			$RB_var="";
			if($freshy_options['position_vertical_bg_custom']=="bottom") { $RB_var='selected="yes"'; }
			echo '<option value="bottom" '.$RB_var.' >'.__('Bottom','xtec').'</option>\n';
					
			echo '
					</select>
				</td>
			</tr>
						<tr>
				<td>
					<label>'.__('Background horitzontal position','xtec').' </label>
				</td>
				<td>
					<select id="position_horitzontal_bg_custom" name="position_horitzontal_bg_custom" size="3">\n';
			$RB_var="";
			if($freshy_options['position_horitzontal_bg_custom']=="left") { $RB_var='selected="yes"'; }
			echo '<option value="left" '.$RB_var.' >'.__('Left','xtec').'</option>\n';
			$RB_var="";
			if($freshy_options['position_horitzontal_bg_custom']=="center") { $RB_var='selected="yes"'; }
			echo '<option value="center" '.$RB_var.' >'.__('Center','xtec').'</option>\n';
			$RB_var="";
			if($freshy_options['position_horitzontal_bg_custom']=="right") { $RB_var='selected="yes"'; }
			echo '<option value="right" '.$RB_var.' >'.__('Right','xtec').'</option>\n';
					
			echo '
					</select>
				</td>
			</tr>
			';
			
			} // if $freshy_options['header_bg_custom'] != ""
			
		echo '
		</table>
		
		</fieldset>
					
		<p class="submit"><input type="submit" name="Submit" value="'.__('Update Options &raquo;','xtec').'"/></p>
		</form>
		</div>
		';
		
		echo '
		<div id="preview" class="wrap">
		<h2 id="preview-post">'.__('Preview (updated when options are saved)', 'xtec').'</h2>

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
