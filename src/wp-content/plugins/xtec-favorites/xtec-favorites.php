<?php
  /*
Plugin Name: XTEC Favorites
Plugin URI:
Description: Adds a favorite blogs system for the users.
Version: 1.0
Author: Germán Antolin Priotto
Author URI:
*/

/*  Copyright 2011  German Antolin Priotto

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

global $xtec_favorites_db_version;
$xtec_favorites_db_version = '1.0';

/** @todo Delete ubid field of database 'wp_user_blogs' table. */

add_action('delete_blog', 'xtec_favorites_delete_blog', 10, 2);

function xtec_favorites_delete_blog($blogId, $drop)
{
    global $wpdb;
    $sql = "DELETE FROM {$wpdb->base_prefix}user_blogs WHERE blogId = $blogId";
    $wpdb->query($sql);    
}

/**
 * Adds a preferred blog to the current user.
 * 
 * @param int $blogId The ID of the blog.
 */
function xtec_favorites_add_preferred($blogId)
{
    global $wpdb;
    global $userdata;

    // verify that not exists
    $exists = $wpdb->get_var( "SELECT count(ubid) FROM {$wpdb->base_prefix}user_blogs WHERE userId = {$userdata->ID} and blogId = $blogId" );

    //Create a new entry in user prefered blogs
    if ( !$exists ) {
        $sql = "INSERT INTO {$wpdb->base_prefix}user_blogs (userId,blogId) VALUES ($userdata->ID, $blogId)";
        $wpdb->query($sql);
    }
    return true;
}

/**
 * Deletes a preferred blog of the current user.
 * 
 * @param int $blogId The ID of the blog.
 */
function xtec_favorites_delete_preferred($blogId)
{
    global $wpdb;
    global $userdata;
    $sql = "DELETE FROM {$wpdb->base_prefix}user_blogs WHERE blogId=$blogId AND userId=$userdata->ID";
    $wpdb->query($sql);
}

/**
 * Gets the preferred blogs of the current user.
 * 
 * @return array The IDs of the blogs.
 */
function xtec_favorites_get_user_preferred_blogs()
{
    global $wpdb;
    global $userdata;
    $blogs = $wpdb->get_results("SELECT userId, blogId FROM {$wpdb->base_prefix}user_blogs WHERE userId = $userdata->ID");
    foreach ( $blogs as $blog ) {
        $blogsArray[] = $blog->blogId;
    }
    return $blogsArray;
}

/**
 * Creates XTEC Favorites database table.
 */
function xtec_favorites_activation_hook() {
    global $wpdb;
    global $xtec_favorites_db_version;

    $table_name = $wpdb->base_prefix . 'user_blogs';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
                ubid int(11) NOT NULL AUTO_INCREMENT,
                userId int(11) NOT NULL DEFAULT '0',
                blogId int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (ubid),
                KEY userId (userId));";        

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');              
        dbDelta($sql);
        
    }
    add_option('$xtec_favorites_db_version', $xtec_favorites_db_version);
}

/** @todo Make this plugin can only be activated from the main site. */
register_activation_hook(__FILE__,'xtec_favorites_activation_hook');