<?php
	
define ('TEMPLATE_DOMAIN','freshy');
load_theme_textdomain(TEMPLATE_DOMAIN);

if ( function_exists('register_sidebar') ) {
	
	$freshy_options = get_option('freshy_options');
	if ($freshy_options['sidebar_left'] && $freshy_options['sidebar_right']) $sidebars = 2;
	else $sidebars = 1;
	
    register_sidebars($sidebars, array(
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
        'before_title' => '<h2 class="title">',
        'after_title' => '</h2>',
    ));

}

add_action('widgets_init', 'freshy_widgets_init');

// XTEC ********** ELIMINAT - Loading of CSS that hides buttons when editing images
// 2015.05.19 @jcaballero
/*
add_filter("mce_css", "freshy_editor_mce_css", 0);
*/
//*********FI

add_filter("mce_buttons", "freshy_editor_mce_buttons", 0);

if (!class_exists('Nice_theme')) add_action('wp_head','freshy_head');

// XTEC ********** ELIMINAT - Loading of CSS that hides buttons when editing images
// 2015.05.19 @jcaballero
/*
function freshy_editor_mce_css($stylesheets) {
	$stylesheets = get_bloginfo('stylesheet_directory').'/content.css';
	return $stylesheets;
}
*/
//**********FI

function freshy_editor_mce_buttons($buttons) {
	$buttons[] = "styleselect";
	return $buttons;
}

function freshy_widgets_init()
{
	register_sidebar_widget('Categories OR Pages', 'freshy_menu');
	register_widget_control('Categories OR Pages', 'freshy_menu_control', 300, 90);
}

function freshy_head()
{
	if (get_option('nt_file'))
	{
		print '<link rel="stylesheet" href="'.get_bloginfo('stylesheet_directory').'/'.get_option('nt_file').'" type="text/css" media="screen"/>';
	}
}

function freshy_menu_control() {
	$options = $newoptions = get_option('widget_categories');
	if ( $_POST['categories-submit'] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['categories-title']));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_categories', $options);
	}
	$title = wp_specialchars($options['title']);
?>
			<p><label for="categories-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="categories-title" name="categories-title" type="text" value="<?php print $title; ?>" /></label></p>
			<input type="hidden" id="categories-submit" name="categories-submit" value="1" />
<?php
}

function freshy_widget_links($args) {
	global $wpdb;
	$title = empty($options['title']) ? __('Links') : $options['title'];
	?>
	<h2><?php print $before_widget.$before_title.$title.$after_title; ; ?></h2>
	<ul>
	<?php freshy_links_menu(); ?>
	</ul>
	<?php
}

function freshy_links_menu() {
	global $wpdb;
	$ver = substr(get_bloginfo('version'), 0, 3);
	if (floatval($ver) <= 2)
	{
		$link_cats = $wpdb->get_results("SELECT cat_id, cat_name FROM $wpdb->linkcategories");
		foreach ($link_cats as $link_cat)
		{ ?>
			<li id="linkcat-<?php print $link_cat->cat_id; ?>"><?php print $link_cat->cat_name; ?>
			<ul>
			<?php wp_get_links($link_cat->cat_id); ?>
			</ul>
			</li>
		<?php
		}
	}
	else {
		$link_cats = $wpdb->get_results("SELECT DISTINCT cat_name, category_id FROM $wpdb->categories INNER JOIN $wpdb->link2cat ON $wpdb->categories.cat_id=$wpdb->link2cat.category_id");
		foreach ($link_cats as $link_cat)
		{ ?>
			<li id="linkcat-<?php print $link_cat->cat_id; ?>"><?php print $link_cat->cat_name; ?>
			<ul>
			<?php wp_get_links('category='.$link_cat->category_id.'&before=<li>&after=</li>&show_description=0&limit=100'); ?>
			</ul>
			</li>
			<?php
		}
	}	
}

function freshy_menu($args_pages='', $args_cats='') {
	
	global $post, $wpdb, $cat, $ID, $notfound, $freshy_options;
	
	$options = get_option('widget_categories');

	$c = 0;
	$h = 0;
	if ($freshy_options['menu_rss'] == 1) $rss = '&feed_image='.get_bloginfo('stylesheet_directory').'/images/icons/feed-icon-10x10.gif';
	$title = empty($options['title']) ? __('Navigation') : $options['title'];
	
	if ($freshy_options['menu_type']=='auto') {

		// page menu
		if (($post->post_status=='static' || is_page()) && $notfound!='1' && $args_pages!='none') {
			?>
			<h2><?php print $title; ?></h2>
			<ul>
			<?php
	        wp_list_pages($args_pages.'&title_li=');
	        ?>
	        </ul>
	        <?php
		}
		// cats & posts menu
		else if (!is_page() && $notfound!='1' && $args_cats!='none') {
			?>
			<h2><?php print $title; ?></h2>
			<ul>
			<?php
	        wp_list_cats($args_cats.'&hierarchical=$h'.$rss);
	        ?>
	        </ul>
	        <?php
		}
		// bad things happened but dispay something anyway
		else {
			?>
				<h2><?php _e('Categories',TEMPLATE_DOMAIN); ?></h2>
				<ul>
				<?php wp_list_cats($args_cats.'&hierarchical=$h'.$rss); ?>
				</ul>
			<?php
		}
	}
	else {
		?>
			<h2><?php _e('Pages',TEMPLATE_DOMAIN); ?></h2>
			<ul>
			<?php wp_list_pages('sort_column=menu_order&title_li='); ?>
			</ul>
			<h2><?php _e('Categories',TEMPLATE_DOMAIN); ?></h2>
			<ul>
			<?php wp_list_cats($args_cats.'&optioncount=$c&hierarchical=$h'.$rss); ?>
			</ul>
		<?php
	}
}

function freshy_get_page_root($id,$level=0)
{
	$parents = array();
	$curpost = get_post($id);
	array_push($parents,$id);
	while ($curpost->post_parent != 0) {
		array_push($parents,$curpost->post_parent);
		$curpost = get_post($curpost->post_parent);
	}
	$parents = array_reverse($parents);
	if (class_exists('YammYamm')) $level = $level+1;
	return $parents[$level];
}

function freshy_layout_class() {
	global $freshy_options, $post;
	$freshy_options = get_option('freshy_options');
	$return ='';

	$ids_right = explode(',',$freshy_options['hide_sidebar_posts']);
	$ids_left = explode(',',$freshy_options['hide_sidebar_left_posts']);
	
	if ($freshy_options['sidebar_left'] == 1 && in_array($post->ID, $ids_left) === FALSE) $return .= 'sidebar_left';
	if ($freshy_options['sidebar_right'] == 1 && in_array($post->ID, $ids_right) === FALSE) $return .= ' sidebar_right';
	
	return 'class="'.$return.'"';
}

// SET OPTIONS

$freshy_options=array();
$freshy_default_options=array();

freshy_set_options();

function freshy_set_options() {
	
	global $freshy_options;
	
	$freshy_default_options['first_menu_label']='Home';
	$freshy_default_options['blog_menu_label']='Blog';
	$freshy_default_options['last_menu_label']='Contact';
	$freshy_default_options['last_menu_type']='';
	$freshy_default_options['contact_email']='';
	$freshy_default_options['contact_link']='';
	$freshy_default_options['sidebar_left']=0;
	$freshy_default_options['sidebar_right']=1;
	
	$freshy_default_options['menu_type']='auto';
	$freshy_default_options['args_pages']='sort_column=menu_order&title_li=';
	$freshy_default_options['args_cats']='hide_empty=0&sort_column=name&optioncount=0&title_li=&hierarchical=1';
	$freshy_default_options['menu_rss']=0;
	$freshy_default_options['date']=1;
	$freshy_default_options['time']=0;
	$freshy_default_options['author']=1;
	$freshy_default_options['header_search']=0;
	$freshy_default_options['header_rss']=1;
	$freshy_default_options['hide_sidebar_posts']='';
	$freshy_default_options['hide_sidebar_left_posts']='';
	
	$existing_options = get_option('freshy_options'); 
	if (is_array($existing_options)) {
		foreach ($existing_options as $key=>$val) { 
			$freshy_options[$key]=$val;
		}
		foreach ($freshy_default_options as $key=>$val) { 
			if (!$freshy_options[$key]) $freshy_options[$key]=$val;
		}
	}
	else {
		$freshy_options=$freshy_default_options;
		update_option('freshy_options', $freshy_options);
	}
}

// ADMIN

add_action('admin_menu', 'freshy_add_theme_page');

function freshy_add_theme_page() {
	add_theme_page('Freshy Options', 'Freshy Options', 'edit_themes', basename(__FILE__), 'freshy_theme_page');
}

function freshy_theme_page() {
	
	global $freshy_options;
	$freshy_options = get_option('freshy_options');
	
	if ( $_GET['page'] == basename(__FILE__) ) {
		
		if (isset($_POST['freshy_options_update'])) {
			
		  $freshy_updated_options = array();
			$freshy_updated_options = $_POST;
			
			update_option('nt_file',$freshy_updated_options['theme']);

			// menus
			$freshy_updated_options['custom_menus'] = $freshy_options['custom_menus'];
			
			if (isset($freshy_updated_options['custom_menus_delete'])) {
				$i = array_shift(array_keys($freshy_updated_options['custom_menus_delete']));
				unset($freshy_updated_options['custom_menus'][$i]);
			}

			if ($freshy_updated_options['new_custom_menu_label']!='') {
				if (!is_array($freshy_updated_options['custom_menus'])) $freshy_updated_options['custom_menus'] = array();
				$tmp = array();
				$tmp['label'] = $freshy_updated_options['new_custom_menu_label'];
				$tmp['url'] = $freshy_updated_options['new_custom_menu_url'];
				array_push($freshy_updated_options['custom_menus'],$tmp);
			}
			
			// quick links
			$freshy_updated_options['custom_quicklinks'] = $freshy_options['custom_quicklinks'];
			
			if (isset($freshy_updated_options['custom_quicklinks_delete'])) {
				$i = array_shift(array_keys($freshy_updated_options['custom_quicklinks_delete']));
				unset($freshy_updated_options['custom_quicklinks'][$i]);
			}
			
			if ($freshy_updated_options['new_custom_quicklink_label']!='') {
				if (!is_array($freshy_updated_options['custom_quicklinks'])) $freshy_updated_options['custom_quicklinks'] = array();
				$tmp = array();
				$tmp['label'] = $freshy_updated_options['new_custom_quicklink_label'];
				$tmp['url'] = $freshy_updated_options['new_custom_quicklink_url'];
				array_push($freshy_updated_options['custom_quicklinks'],$tmp);
			}

			update_option('freshy_options', $freshy_updated_options);
			
			$freshy_options = get_option('freshy_options');
			print '<div class="updated"><p>' . __('Freshy options updated.',TEMPLATE_DOMAIN) . '</p></div>';
		}

		print '<div class="wrap">
		<h2>'.__('Freshy Options',TEMPLATE_DOMAIN).'</h2>
		
		<form name="freshy_options_form" method="post">
		<input type="hidden" name="freshy_options_update" value="update" />
		
		<table class="form-table">';
		?>
			<tr>
				<th scope="row">
					<label for="theme"><?php _e('Choose a custom style :',TEMPLATE_DOMAIN) ?></label>
				</th>
				<td>
					<select id="theme" name="theme">
						<?php
						
						$theme_info = current_theme_info();
						$path = WP_CONTENT_DIR.$theme_info->template_dir.'/';
						$themes = freshy_list_files(TEMPLATEPATH,'custom_',array('custom_template.css'));
						
						print '<option value="custom_template.css"';
						if (get_option('nt_file') == "custom_template.css") print ' selected="selected"';
						print '>'.__('Default style','customize').'</option>';
						foreach ($themes as $theme)
						{
							print '<option value="'.$theme.'"';
							if (get_option('nt_file') == $theme) print ' selected="selected"';
							print '>'.$theme.'</option>';
						}
						?>
					</select>
				</td>
			</tr>
		<?php
			
		print '<tr valign="top"><td colspan="2"><h3>'.__('Layout :',TEMPLATE_DOMAIN).'</h3></td></tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Display left sidebar',TEMPLATE_DOMAIN).'</label>
				</th>
				<td>
					<input type="checkbox" class="form-checkbox" ';
					if ($freshy_options['sidebar_left'] == 1) print 'checked="checked" ';
					print 'value="1" id="edit-sidebar-left" name="sidebar_left"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Display right sidebar',TEMPLATE_DOMAIN).'</label>
				</th>
				<td>
					<input type="checkbox" class="form-checkbox" ';
					if ($freshy_options['sidebar_right'] == 1) print 'checked="checked" ';
					print 'value="1" id="edit-sidebar-right" name="sidebar_right"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Hide left sidebar on these posts',TEMPLATE_DOMAIN).'</label>
				</th>
				<td>
					<input type="text" name="hide_sidebar_left_posts" value="'.$freshy_options['hide_sidebar_left_posts'].'"/>
					<span class="setting-description">'.__('Enter posts ids separated by commas',TEMPLATE_DOMAIN).'</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Hide right sidebar on these posts',TEMPLATE_DOMAIN).'</label>
				</th>
				<td>
					<input type="text" name="hide_sidebar_posts" value="'.$freshy_options['hide_sidebar_posts'].'"/>
					<span class="setting-description">'.__('Enter posts ids separated by commas',TEMPLATE_DOMAIN).'</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Navigation menu behaviour',TEMPLATE_DOMAIN).'</label>
				</th>
				<td>					
					<label for="menu_type_auto"><input name="menu_type" id="menu_type_auto" type="radio" value="auto" ';
					if ($freshy_options['menu_type'] == 'auto') {print 'checked="checked" ';}
					print '/> '.__('Auto : Display subpages on pages and blog categories in blog',TEMPLATE_DOMAIN).'</label>
					<br/>
					<label for="menu_type_normal"><input name="menu_type" id="menu_type_normal" type="radio" value="normal" ';
					if ($freshy_options['menu_type'] == 'normal') {print 'checked="checked" ';}
					print '/> '.__('Normal : Always display categories and pages',TEMPLATE_DOMAIN).'</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Navigation menu rss',TEMPLATE_DOMAIN).'</label>
				</th>
				<td>					
					<label for="menu_rss_true"><input name="menu_rss" id="menu_rss_true" type="radio" value="1" ';
					if ($freshy_options['menu_rss'] == 1) {print 'checked="checked" ';}
					print '/> '.__('Display rss icon with categories',TEMPLATE_DOMAIN).'</label>
					<br/>
					<label for="menu_rss_false"><input name="menu_rss" id="menu_rss_false" type="radio" value="0" ';
					if ($freshy_options['menu_rss'] == 0) {print 'checked="checked" ';}
					print '/> '.__('Do not display rss icon with categories',TEMPLATE_DOMAIN).'</label>
				</td>
			</tr>
			<tr valign="top"><td colspan="2"><h3>'.__('Date options :',TEMPLATE_DOMAIN).'</h3>
				<small>'.__('You can change date formatting in',TEMPLATE_DOMAIN).' <a href="options-general.php">Settings &gt; General</a></small>		
			</td></tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Show date on posts',TEMPLATE_DOMAIN).' </label>
				</th>
				<td>
					<input type="checkbox" class="form-checkbox" ';
					if ($freshy_options['date'] == 1) print 'checked="checked" ';
					print 'value="1" id="edit-date" name="date"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Show time on posts',TEMPLATE_DOMAIN).' </label>
				</th>
				<td>
					<input type="checkbox" class="form-checkbox" ';
					if ($freshy_options['time'] == 1) print 'checked="checked" ';
					print 'value="1" id="edit-time" name="time"/>
				</td>
			</tr>
			<tr valign="top"><td colspan="2"><h3>'.__('Other options :',TEMPLATE_DOMAIN).'</h3></td></tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Show author on posts',TEMPLATE_DOMAIN).' </label>
				</th>
				<td>
					<input type="checkbox" class="form-checkbox" ';
					if ($freshy_options['author'] == 1) print 'checked="checked" ';
					print 'value="1" id="edit-author" name="author"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Show search in header',TEMPLATE_DOMAIN).' </label>
				</th>
				<td>
					<input type="checkbox" class="form-checkbox" ';
					if ($freshy_options['header_search'] == 1) print 'checked="checked" ';
					print 'value="1" id="edit-header-search" name="header_search"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Show RSS icon in header',TEMPLATE_DOMAIN).' </label>
				</th>
				<td>
					<input type="checkbox" class="form-checkbox" ';
					if ($freshy_options['header_rss'] == 1) print 'checked="checked" ';
					print 'value="1" id="edit-header-rss" name="header_rss"/>
				</td>
			</tr>
			<tr valign="top"><td colspan="2"><h3>'.__('Menu :',TEMPLATE_DOMAIN).'</h3></td></tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Enter the label of the Homepage menu link',TEMPLATE_DOMAIN).' </label>
				</th>
				<td>
					<input name="first_menu_label" type="text" value="'.$freshy_options['first_menu_label'].'"/>
					<span class="setting-description">'.__('Modifying these labels should break internationalisation',TEMPLATE_DOMAIN).'</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('Enter the label of the last menu link',TEMPLATE_DOMAIN).' </label>
				</th>
				<td>
					<input name="last_menu_label" type="text" value="'.$freshy_options['last_menu_label'].'"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label>'.__('The last menu link should be...',TEMPLATE_DOMAIN).'</label>
				</th>
				<td>					
					<label for="last_menu_type_email"><input name="last_menu_type" id="last_menu_type_email" type="radio" value="email" ';
					if ($freshy_options['last_menu_type'] == 'email') {print 'checked="checked" ';}
					print '/> '.__('A contact button to this email',TEMPLATE_DOMAIN).' :</label>
					<input name="contact_email" type="text" value="'.$freshy_options['contact_email'].'"/>

					<br/>
					
					<label for="last_menu_type_link"><input name="last_menu_type" id="last_menu_type_link" type="radio" value="link" ';
					if ($freshy_options['last_menu_type'] == 'link') {print 'checked="checked" ';}
					print '/> '.__('A custom link with this url',TEMPLATE_DOMAIN).' :</label>
					<input name="contact_link" type="text" value="'.$freshy_options['contact_link'].'"/>
						
					<br/>
												
					<label for="last_menu_type_none"><input name="last_menu_type" id="last_menu_type_none" type="radio" value="" ';
					if ($freshy_options['last_menu_type'] == '') {print 'checked="checked" ';}
					print '/> '.__('Nothing',TEMPLATE_DOMAIN).'</label>
				</td>
			</tr>
			<tr valign="top"><td colspan="2"><h3>'.__('Custom menus :',TEMPLATE_DOMAIN).'</h3></td></tr>';
			if (is_array($freshy_options['custom_menus'])) {
			foreach ($freshy_options['custom_menus'] as $i => $custom_menu) {
				print '<tr valign="top">
					<th scope="row" style="text-align:right">
						<label><a href="'.$custom_menu['url'].'">'.$custom_menu['label'].'</a>
					</th>
					<td>
						<input type="submit" name="custom_menus_delete['.$i.']" value="'.__('Delete').'"/>
					</td>
				</tr>';
			} }
			print '<tr valign="top">
				<th scope="row">
					<label>'.__('New custom menu entry',TEMPLATE_DOMAIN).' </label>
				</th>
				<td>
					<label>'.__('label',TEMPLATE_DOMAIN).' :</label> <input name="new_custom_menu_label" type="text" value=""/><br/>
					<label>'.__('url',TEMPLATE_DOMAIN).' :</label> <input name="new_custom_menu_url" type="text" value=""/>
				</td>
			</tr>
			<tr valign="top"><td colspan="2"><h3>'.__('Custom quicklinks :',TEMPLATE_DOMAIN).'</h3></td></tr>';
			if (is_array($freshy_options['custom_quicklinks'])) {
			foreach ($freshy_options['custom_quicklinks'] as $i => $custom_quicklink) {
				print '<tr valign="top">
					<th scope="row" style="text-align:right">
						<label><a href="'.$custom_quicklink['url'].'">'.$custom_quicklink['label'].'</a> 
					</th>
					<td>
						<input type="submit" name="custom_quicklinks_delete['.$i.']" value="'.__('Delete').'"/>
					</td>
				</tr>';
			} }
			print '<tr valign="top">
				<th scope="row">
					<label>'.__('New custom quicklink entry',TEMPLATE_DOMAIN).' </label>
				</th>
				<td>
					<label>'.__('label',TEMPLATE_DOMAIN).' :</label> <input name="new_custom_quicklink_label" type="text" value=""/><br/>
					<label>'.__('url',TEMPLATE_DOMAIN).' :</label> <input name="new_custom_quicklink_url" type="text" value=""/>
				</td>
			</tr>
		</table>
						
		<p class="submit"><input type="submit" name="Submit" value="'.__('Update Options &raquo;',TEMPLATE_DOMAIN).'"/></p>
		</form>
		</div>
		';
		/*
		print '
		<div id="preview" class="wrap">
		<h2 id="preview-post">'.__('Preview').' <small>('.__('updated when options are saved - attention : changes you make take effect immediately on the site !').')</small></h2>
		<iframe src="../?preview=true" width="100%" height="600" ></iframe>
		</div>
		';*/
	}
}

function freshy_list_files($dirpath,$filter='',$excludes='')
{
	$return_array=array();

	if (is_dir($dirpath))
	{
	   	if ($dh = opendir($dirpath))
	   	{
			while (false !== ($file = readdir($dh)))
			{
				if (!is_dir($dirpath.$file))
				{
					$exclude_file=false;
					if ($excludes!='')
					{
						foreach ($excludes as $exclude)
						{
							if ($exclude == $file)
							{
								$exclude_file=true;
								break;
							}
						}
					}
					if ($exclude_file!=true && $filter!='' && strstr($file, $filter) != false) $return_array[$file] = $file;
					else if ($exclude_file!=true && $filter=='') $return_array[$file] = $file;
				}
			}
		    closedir($dh);
		}
	}
	else $this->message = '<div class="error"><p>'.__('It seems that the directory','customize').' "'.$dirpath.'" '.__('does not exist','customize').'</p></div>';
    return $return_array;
}

function freshy_get_comment_excerpt() {
	global $comment;
	$comment_text = strip_tags($comment->comment_content);
	$blah = explode(' ', $comment_text);
	if (count($blah) > 10) {
		$k = 10;
	} else {
		$k = count($blah);
	}
	$excerpt = '';
	for ($i=0; $i<$k; $i++) {
		$excerpt .= $blah[$i] . ' ';
	}
	return apply_filters('get_comment_excerpt', $excerpt);
}

?>