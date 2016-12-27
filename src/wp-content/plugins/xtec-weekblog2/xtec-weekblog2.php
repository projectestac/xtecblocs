<?php
/*
Plugin Name: XTEC WeekBlog 2
Description: Allows network admins to manage WeekBlogs, a new custom post type.
Version: 1.0
Author: Francesc Bassas i Bullich
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Text Domain: xtecweekblog
*/

/*  Copyright 2011  Francesc Bassas i Bullich

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$plugin_header_translate = array(
    __("Allows network admins to manage WeekBlogs, a new custom post type.", 'xtecweekblog')
);

add_action( 'init', 'xtecweekblog_create_post_type' );

/**
 * Creates weekblog custom post type.
 */
function xtecweekblog_create_post_type() {

	// loads plugin textdomain
	load_plugin_textdomain('xtecweekblog', null, dirname( plugin_basename( __FILE__ )) . '/languages' );
	
	// adds css to fix the week column width on the xtecweekblogs list table
	if ((isset($_GET['post_type']) && $_GET['post_type'] == 'xtecweekblog') || (isset($post_type) && $post_type == 'xtecweekblog')) {
       	wp_enqueue_style( 'xtecweekblogs_list_table', plugins_url( '/css/xtecweekblogs_list_table.css', __FILE__ ));
    }
	
    // register xtecweekblog post type
    register_post_type( 'xtecweekblog',
                        array( 'labels' => array( 'name' => __('WeekBlogs', 'xtecweekblog'),
                                                  'singular_name' => __('WeekBlog', 'xtecweekblog'),
                                                  'add_new_item' => __('Add new WeekBlog', 'xtecweekblog'),
                                                  'edit_item' => __('Edit WeekBlog', 'xtecweekblog'),
                                                  'new_item' => __('New WeekBlog', 'xtecweekblog'),
                                                  'view_item' => __('View WeekBlog', 'xtecweekblog'),
                                                  'search_items' => __('Search WeekBlogs', 'xtecweekblog'),
                                                  'not_found_in_trash' => __('No WeekBlogs found in Trash', 'xtecweekblog'),
                                                ),
                               'description' => __('The outstanding blog of the week.', 'xtecweekblog'),
                               'public' => true,
                               'has_archive' => true,
                               'capabilities' => array( 'edit_post' => 'manage_network',
                                                        'edit_posts' => 'manage_network',
                                                        'edit_others_posts' => 'manage_network',
                                                        'publish_posts' => 'manage_network',
                                                        'read_post' => 'manage_network',
                                                        'read_private_posts' => 'manage_network',
                                                        'delete_post' => 'manage_network'
                                                      ),
		                       'supports' => array('thumbnail'),
                               'register_meta_box_cb' => 'xtecweekblog_meta_box_cb'
                               // menu icons aren't fully supported, tricked by xtecweekblog_icons
                               // 'menu_icon' => plugins_url('/images/weekblog16x16.png', __FILE__)
                        )
	);
}

/**
 * Adds xtecweekblog meta boxes.
 */
function xtecweekblog_meta_box_cb() {
	add_meta_box('xtecweekblog-meta', __('Params', 'xtecweekblog'), 'xtecweekblog_meta', 'xtecweekblog', 'normal');
    remove_meta_box('postimagediv', 'xtecweekblog', 'side');
    add_meta_box('postimagediv', __('Custom Image', 'xtecweekblog'), 'post_thumbnail_meta_box', 'xtecweekblog', 'normal', 'low');
}

/**
 * Displays weekblog meta box.
 */
function xtecweekblog_meta() {
	
    echo '<input type="hidden" name="xtecweekblog_meta_box_nonce" value="'. wp_create_nonce('xtecweekblog_meta_box'). '" />';
    
    global $post;    
    ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label for="xtecweekblog-name"><?php _e('WeekBlog URL', 'xtecweekblog')?></label></th>
            <td>
                <?php echo network_site_url();?><br>
                <input type='text' name='_xtecweekblog-name' id='xtecweekblog-name' style='width:98%' value='<?php echo esc_html(stripslashes(get_post_meta($post->ID, '_xtecweekblog-name', true)))?>' /><br>
                <?php // alert if blog of weekblog not exists
                if ($post->post_status != 'auto-draft') {
                	if (get_post_meta($post->ID, '_xtecweekblog-name', true)) {
                		if(!xtecweekblog_validate_name($post->ID)) echo "<div style='color:#FF0000'><p>" . __('WeekBlog URL is wrong.', 'xtecweekblog') . "</p></div>";
                	}
                	else {
                	    if(!xtecweekblog_validate_name($post->ID)) echo "<div style='color:#FF0000'><p>" . __('WeekBlog URL is wrong.', 'xtecweekblog') . "</p></div>";
                	}
				}
				?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="xtecweekblog-description"><?php _e('Description', 'xtecweekblog')?></label></th>
            <td>
                <input type="text" name="_xtecweekblog-description" id="xtecweekblog-description" maxlength="175" value="<?php echo esc_html(stripslashes(get_post_meta($post->ID, '_xtecweekblog-description', true)))?>" style="width:98%" />
                <p class="howto"><?php _e('Description max length: 175 chars', 'xtecweekblog')?></p>
                <?php // alert if description of weekblog is not defined
                if ($post->post_status != 'auto-draft') {
                    if (get_post_meta($post->ID, '_xtecweekblog-name', true)) {
                		if(!xtecweekblog_validate_description($post->ID)) echo "<div style='color:#FF0000'><p>" . __('WeekBlog description is not defined.', 'xtecweekblog') . "</p></div>";
                	}
                	else {
                	    if(!xtecweekblog_validate_description($post->ID)) echo "<div style='color:#FF0000'><p>" . __('WeekBlog description is not defined.', 'xtecweekblog') . "</p></div>";
                	}
                }
                ?>
            </td>
        </tr>
    </table>
    <?php
}

add_action('save_post', 'xtecweekblog_save');

/**
 * Saves weekblog data.
 * 
 * @param int $post_id Post ID.
 */
function xtecweekblog_save($post_id) {
	
	// check nonce
	if (!isset($_POST['xtecweekblog_meta_box_nonce']) || !wp_verify_nonce($_POST['xtecweekblog_meta_box_nonce'], 'xtecweekblog_meta_box')) {
		return;
	}

    // exit on autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	
	// check capabilities
	if ('post' == $_POST['post_type']) {
		if (!current_user_can('edit_post', $post_id)) return;
	} elseif (!current_user_can('edit_page', $post_id)) return;

	// save weekblog name
	if(isset($_POST['_xtecweekblog-name'])) {
		update_post_meta($post_id, '_xtecweekblog-name', $_POST['_xtecweekblog-name']);
	} else {
		delete_post_meta($post_id, '_xtecweekblog-name');
	}
	
	// save weekblog description
	if(isset($_POST['_xtecweekblog-description'])) {
		update_post_meta($post_id, '_xtecweekblog-description', $_POST['_xtecweekblog-description']);
	} else {
		delete_post_meta($post_id, '_xtecweekblog-description');
	}
}

add_filter('post_updated_messages', 'xtecweekblog_updated_messages');

/**
 * Customizes weekblog updated messages.
 * 
 * @param array $messages Default updated messages.
 * @return array Udated messages.
 */
function xtecweekblog_updated_messages($messages) {
    global $post, $post_ID;
    $messages['xtecweekblog'] = array( 0 => '', // Unused. Messages start at index 1.
                                       1 => sprintf( __('Weekblog updated. <a href="%s">View weekblog</a>', 'xtecweekblog'), esc_url( get_permalink($post_ID) ) ),
                                       2 => __('Custom field updated.', 'xtecweekblog'),
                                       3 => __('Custom field deleted.', 'xtecweekblog'),
                                       4 => __('Weekblog updated.', 'xtecweekblog'),
                                       5 => isset($_GET['revision']) ? sprintf( __('Weekblog restored to revision from %s', 'xtecweekblog'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                                       6 => sprintf( __('Weekblog published. <a href="%s">View weekblog</a>', 'xtecweekblog'), esc_url( get_permalink($post_ID) ) ),
                                       7 => __('Weekblog saved.', 'xtecweekblog'),
                                       8 => sprintf( __('Weekblog submitted. <a target="_blank" href="%s">Preview weekblog</a>', 'xtecweekblog'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
                                       9 => sprintf( __('Weekblog scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview weekblog</a>', 'xtecweekblog'),
                                       date_i18n( __( 'M j, Y @ G:i', 'xtecweekblog' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
                                       10 => sprintf( __('Weekblog draft updated. <a target="_blank" href="%s">Preview weekblog</a>', 'xtecweekblog'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
                                     );
    return $messages;
}

add_filter( 'admin_post_thumbnail_html', 'xtecweekblog_thumbnail_html');

/**
 * Output HTML for the xtecweekblog thumbnail meta-box.
 * 
 * @param string $content Default HTML for the post thumbnail meta-box.
 * @return string html
 */
function xtecweekblog_thumbnail_html ($content) {
	
	global $post;
	
	// shows info message
	if ($post->post_type == 'xtecweekblog') {
		$content .=  '<p class="howto">' . __('Image size must be at least of 363 x 98 px. If the image is bigger than the minimum size then, when it be displayed, it will be automatically cropped.', 'xtecweekblog') . '</p>';
	}
	
	// checks image
	if (($post != NULL) && ($post->post_status != 'auto-draft')) {
		if (!xtecweekblog_validate_image($post->ID)) {	
	        $content .= '<p style="color:#FF0000">' . __('Custom Image is not defined.', 'xtecweekblog') . '</p>';
		}
		else {
			// checks thumbnail
			$thumbnail_id = get_post_meta($post->ID, '_thumbnail_id', true);
			$image_attributes = wp_get_attachment_image_src($thumbnail_id, 'full');
			
			// shows cropped image
			if ($image_attributes[1] > 300 || $image_attributes[2] > 98 ) {
				$content .= '<p>' . __('Cropped Image:','xtecweekblog') . '</p><p>' . get_the_post_thumbnail($post->ID,'xtecweekblog') . '</p>';
			}
			
			// checks image size
		    if (!xtecweekblog_validate_image_size($post->ID)){
		        $content .= '<p style="color:#FF0000">' . __('Image size is too small', 'xtecweekblog') . '</p>';	
		    }
		}
	}	
	return $content;
}

add_filter("manage_edit-xtecweekblog_columns", "xtecweekblog_edit_columns");

/**
 * Customizes xtecweekblogs list table columns.
 * 
 * @param array $columns Default xtecweekblogs list table columns.
 * @return array Xtecweekblogs list table columns.
 */
function xtecweekblog_edit_columns($columns) {
  return array( 'cb' => "<input type=\"checkbox\" />",
                '_xtecweekblog-name' => __('WeekBlog URL', 'xtecweekblog'),
                'thumbnail' => __('Image', 'xtecweekblog'),
                '_xtecweekblog-description' => __('Description', 'xtecweekblog'),
                'week' => __('Week', 'xtecweekblog'),
                'date' => __('Date', 'xtecweekblog')  
              );
}

add_action("manage_xtecweekblog_posts_custom_column", "xtecweekblog_custom_columns", 10, 2);

/**
 * Output HTML for the column of a specific xtecweekblog.
 * 
 * @param string $column Column name.
 * @param string $post_id Post ID.
 */
function xtecweekblog_custom_columns($column, $post_id) {
	
    global $post;
    
    switch ($column) {
    	
        case "_xtecweekblog-name":
            $custom = get_post_custom($post_id);
            echo '<strong>';
            echo '<span class="row-title" style="color:#21759B">' . $custom["_xtecweekblog-name"][0] . '</span>';
            _post_states( $post );
            echo '</strong>';
            if (xtecweekblog_validate_name($post_id)) {
            	// valid weekblog, print name and URL
                echo "<p><a href='" . network_site_url() . $custom["_xtecweekblog-name"][0] . "'>" . network_site_url() . $custom["_xtecweekblog-name"][0] . "</a></p>";
            }
            else {
            	// invalid weekblog, print invalid name and notify
      	        echo '<p style="color:#FF0000">' . __('Invalid name', 'xtecweekblog') . '</p>';
            }
      		// print row actions | extracted from WordPress 3.1.2 core
            $post_type_object = get_post_type_object( $post->post_type );
            $can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post_id );      
            $actions = array();
            if ( $can_edit_post && 'trash' != $post->post_status ) {
                $actions['edit'] = '<a href="' . get_edit_post_link( $post_id, true ) . '" title="' . esc_attr( __( 'Edit this item' ) ) . '">' . __( 'Edit' ) . '</a>';
                $actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( __( 'Edit this item inline' ) ) . '">' . __( 'Quick&nbsp;Edit' ) . '</a>';
            }
            if ( current_user_can( $post_type_object->cap->delete_post, $post_id ) ) {
                if ( 'trash' == $post->post_status )
                    $actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash', 'xtecweekblog' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post_id ) ), 'untrash-' . $post->post_type . '_' . $post_id ) . "'>" . __( 'Restore', 'xtecweekblog' ) . "</a>";
                elseif ( EMPTY_TRASH_DAYS )
                    $actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash', 'xtecweekblog' ) ) . "' href='" . get_delete_post_link( $post_id ) . "'>" . __( 'Trash', 'xtecweekblog' ) . "</a>";
                if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
                    $actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently', 'xtecweekblog' ) ) . "' href='" . get_delete_post_link( $post_id, '', true ) . "'>" . __( 'Delete Permanently', 'xtecweekblog' ) . "</a>";
            }
            if ( in_array( $post->post_status, array( 'pending', 'draft' ) ) ) {
                if ( $can_edit_post )
                    $actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_id ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'Preview', 'xtecweekblog' ) . '</a>';
                } elseif ( 'trash' != $post->post_status ) {
                    $actions['view'] = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'xtecweekblog' ), $custom["_xtecweekblog-name"][0] ) ) . '" rel="permalink">' . __( 'View', 'xtecweekblog' ) . '</a>';
            }
            $actions = apply_filters( is_post_type_hierarchical( $post->post_type ) ? 'page_row_actions' : 'post_row_actions', $actions, $post );
            echo xtecweekblog_row_actions( $actions );
            get_inline_data( $post );
            break;
            
        case "_xtecweekblog-description":
            $custom = get_post_custom($post_id);
            if (xtecweekblog_validate_description($post_id)) echo $custom["_xtecweekblog-description"][0];
            else echo '<p style="color:#FF0000">' . __('Description is not defined', 'xtecweekblog') . '</p>';
            break;
            
        case 'thumbnail':
        	if (xtecweekblog_validate_image($post_id)) {
        		if (!xtecweekblog_validate_image_size($post_id)){
        			echo '<p style="color:#FF0000">' . __('Image size is too small', 'xtecweekblog') . '</p>';	
        		}
        		echo get_the_post_thumbnail($post_id,'xtecweekblog');
        	}
            else echo '<p style="color:#FF0000">' . __('Custom Image is not defined', 'xtecweekblog') . '</p>';
            break;
            
        case 'week':
            echo mysql2date('W', $post->post_date);
            break;
    }
}

/**
 * Generate row actions div
 *
 * @param array $actions The list of actions
 * @param bool $always_visible Wether the actions should be always visible
 * @return string
 */
function xtecweekblog_row_actions( $actions, $always_visible = false ) {
	$action_count = count( $actions );
	$i = 0;

	if ( !$action_count )
		return '';

	$out = '<div class="' . ( $always_visible ? 'row-actions-visible' : 'row-actions' ) . '">';
	foreach ( $actions as $action => $link ) {
		++$i;
		( $i == $action_count ) ? $sep = '' : $sep = ' | ';
		$out .= "<span class='$action'>$link$sep</span>";
	}
	$out .= '</div>';

	return $out;
}

add_filter( 'manage_edit-xtecweekblog_sortable_columns', 'xtecweekblog_sortable_columns' );

/**
 * Defines new sortable xtecweekblogs list table columns .
 * 
 * @param array $columns Default sortable columns.
 * @return array Sortable columns.
 */
function xtecweekblog_sortable_columns($columns) {
	$columns['_xtecweekblog-name'] = '_xtecweekblog-name';
	return $columns;
}

/**
 * Gets the current weekblog.
 * 
 * @return array Current weekblog post.
 */
function xtecweekblog_current_weekblog () {
	$week = date('W');
	$year = date('Y');
	//Check if there is a blog published this week
	$weekblog = query_posts('post_type=xtecweekblog&post_status=publish&posts_per_page=1&orderby=date&order=DESC&year=' . $year . '&w=' . $week);
	if (!$weekblog) {
		//Check if there is a blog scheduled for this week
		$weekblog = query_posts('post_type=xtecweekblog&post_status=future&posts_per_page=1&orderby=date&order=ASC&year=' . $year . '&w=' . $week);
	}
	return array_shift($weekblog);
}

/**
 * Validates xtecweekblog.
 * 
 * @param int $post_id Post ID.
 * 
 * @return bool True if xtecweekblog validates, false otherwise.
 */
function xtecweekblog_validate($post_id) {
	return xtecweekblog_validate_name($post_id) && xtecweekblog_validate_description($post_id) && xtecweekblog_validate_image($post_id) && xtecweekblog_validate_image_size($post_id);
}

/**
 * Validates xtecweekblog name.
 * 
 * @param int $post_id Post ID.
 * 
 * @return bool True if xtecweekblog name validates, false otherwise.
 */
function xtecweekblog_validate_name($post_id) {
	if (!get_id_from_blogname(get_post_meta($post_id, '_xtecweekblog-name', true))) return false;
	else return true;
}

/**
 * Validates xtecweekblog description.
 * 
 * @param int $post_id Post ID.
 * 
 * @return bool True if xtecweekblog description validates, false otherwise.
 */
function xtecweekblog_validate_description($post_id) {
	if (!get_post_meta($post_id, '_xtecweekblog-description', true)) return false;
	else return true;
}

/**
 * Validates xtecweekblog image.
 * 
 * @param int $post_id Post ID.
 * 
 * @return bool True if xtecweekblog image validates, false otherwise.
 */
function xtecweekblog_validate_image($post_id) {
	if (!get_post_meta($post_id, '_thumbnail_id', true)) return false;
	else return true;
}

/**
 * Validates xtecweekblog image size.
 * 
 * @param int $post_id Post ID.
 * 
 * @return bool True if xtecweekblog image size validates, false otherwise.
 */
function xtecweekblog_validate_image_size($post_id) {
	$thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
	$image_attributes = wp_get_attachment_image_src($thumbnail_id, 'full');
	if ( $image_attributes[1] < 363 || $image_attributes[2] < 98) return false;
	else return true;
}

add_action('admin_menu', 'xtecweekblog_admin_menu');

/**
 * Adds plugin options menu.
 */
function xtecweekblog_admin_menu() {
	$page = add_submenu_page('options-general.php', __('WeekBlog', 'xtecweekblog'), __('WeekBlog', 'xtecweekblog'), 'manage_options', 'ms-weekblog', 'xtecweekblog_options' );
  $screen = get_current_screen();
  if( !method_exists( $screen, 'add_help_tab' ) )
      return;
  $screen->add_help_tab(array( 'title' => '', 'id' => 'id1', 'content' => __('This screen helps you manage the blog of the week.', 'xtecweekblog')));
	//add_contextual_help( $page, '<p>' . __('This screen helps you manage the blog of the week.', 'xtecweekblog') . '</p>');
}

/**
 * Output HTML for plugin options page.
 */
function xtecweekblog_options() {
  $action = isset($_GET['action'])?$_GET['action']:'';
	switch ($action) {
		case 'blogoptions':
			if ($_POST['xtecweekblog_default_msg']) {
				$xtecweekblog_default_msg = stripslashes($_POST['xtecweekblog_default_msg']);
				update_option("xtecweekblog_default_msg", $xtecweekblog_default_msg);
			}
			?>
			<div id="message" class="updated"><p><?php _e('Options saved.', 'xtecweekblog');?></p></div>
			<?php
		break;
	}	
    ?>
    <div class='wrap'>	
	    <form method="post" action="?page=ms-weekblog&action=blogoptions">
		    <h2><?php _e('Weekblog Settings','xtecweekblog')?></h2>
			<table class="form-table">
                <tbody>
                        <tr valign="top"> 
                        <th scope="row"><label for="xtecweekblog_default_msg"><?php _e('Default Message', 'xtecweekblog')?></label></th> 
                        <td>
                            <textarea name="xtecweekblog_default_msg" id="xtecweekblog_default_msg" cols="45" rows="4"><?php echo get_option('xtecweekblog_default_msg') ?></textarea>
                        </td>
                    </tr>
			    </tbody>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save Changes', 'xtecweekblog');?>"></p>
		</form>
    </div>
    <?php
}

add_action( 'admin_head', 'xtecweekblog_menu_icon' );

/**
 * Adds CSS to customize xtecweekblog menu icon.
 */
function xtecweekblog_menu_icon() {
    ?>
    <style type="text/css" media="screen">
        #menu-posts-xtecweekblog .wp-menu-image {
            background: url(<?php echo plugins_url('/images/weekblog16x16.png', __FILE__) ?>) no-repeat 6px -17px !important;
        }
		#menu-posts-xtecweekblog:hover .wp-menu-image, #menu-posts-xtecweekblog.wp-has-current-submenu .wp-menu-image {
            background-position:6px 7px!important;
        }
    #icon-edit.icon32-posts-xtecweekblog {background: url(<?php echo plugins_url('images/weekblog32x32.png', __FILE__) ?>) no-repeat;}
    </style>
    <?php
}