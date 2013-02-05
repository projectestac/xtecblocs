<?php

/*
Plugin Name: XTEC Lastest Posts
Description: Allows to view the lastest posts
Version: 1.0
Author: Germán Antolin Priotto
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/*  Copyright 2010  Germán Antolin Priotto

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

global $xtec_lastest_posts_db_version;
$xtec_lastest_posts_db_version = '1.0';

add_action('auto-draft_to_publish', 'xtec_lastest_posts_to_publish');
add_action('draft_to_publish', 'xtec_lastest_posts_to_publish');
add_action('publish_to_publish', 'xtec_lastest_posts_to_publish');

/**
 * Deletes older posts and registers the post publication. 
 * 
 * @param int $post Post data
 */
function xtec_lastest_posts_to_publish($post)
{
    global $wpdb;
    $days = 60; //Days of all entries
    $timeOld = time() - $days * 24 * 60 * 60;
    //Delete old posts
    $sql = "DELETE FROM wp_globalposts WHERE `time`<'$timeOld'";
    $wpdb->query($sql);        
    //Create a new entry in global posts
    $sql = "INSERT INTO wp_globalposts (blogId,time,postType) VALUES ($wpdb->blogid,'".time()."','1')";
    $wpdb->query($sql);
}

/**
 * 	Gets the lastest public posts.
 * 
 * @param int $how_many Number of blogs to get.
 * @param int $days Number of days to consider in the datetime comparation from the current time.
 * @param int $init Number of the first posts to ignore.
 * @return array The date, the title, the user login, the content, the guid value, the blog title, the blog url and the blog ID of the posts.
 */
function xtec_lastest_posts_lastest_posts($how_many = 10, $days=5, $init=0)
{
    global $wpdb;
    $counter = 0;
    
    // Fix the date in timestamp
    $date = time()-24*60*60*$days;

    // Takes the doble in case any of them is descarted
    $how_many_2 = $how_many * 2;

    // get a list of blogs in order of most recent update
    $blogs = $wpdb->get_results("SELECT DISTINCT blogId FROM wp_globalposts,$wpdb->blogs WHERE time>$date AND `public` = '1' AND `deleted` = '0' AND `blogId` = `blog_id` AND blogId<> 1 ORDER BY id DESC LIMIT $init,$how_many_2");

    if ( $blogs ) {
        foreach ( $blogs as $blog ) {
            // we need _posts and _options tables for this to work
            $blogPostsTable = "wp_".$blog->blogId."_posts";            
            // we fetch the title and link for the latest post
            $thispost = $wpdb->get_results("SELECT post_title, guid, post_content, post_date, post_author " .
                                           "FROM $blogPostsTable " .
                                           "WHERE post_status = 'publish' ".
                                           "AND post_type = 'post' " .
                                           "AND post_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 5 DAY) " .
                                           "ORDER BY $blogPostsTable.id DESC LIMIT 0,1");
                                           
            $thisusername = get_userdata($thispost[0]->post_author)->user_login;

            $blog_detail = get_blog_details($blog->blogId);
            
            $posts[] = array('post_date'=>$thispost[0]->post_date,
                             'post_title'=>$thispost[0]->post_title,
                             'user_login'=>$thisusername,
                             'post_content'=>$thispost[0]->post_content,
                             'guid'=>$thispost[0]->guid,
                             'blog_title'=>$blog_detail->blogname,
                             'blog_url'=>$blog_detail->siteurl,
                             'blog_id'=>$blog->blogId);
        }        
        arsort($posts);
        // Discard not valid values
        foreach ( $posts as $post ) {
            if ( $post['post_date'] != 0 ) {
                $posts_array[] = array('post_date'=>$post['post_date'],
                                       'post_title'=>$post['post_title'],
                                       'user_login'=>$post['user_login'],
                                       'post_content'=>$post['post_content'],
                                       'guid'=>$post['guid'],
                                       'blog_title'=>$post['blog_title'],
                                       'blog_url'=>$post['blog_url'],
                                       'blog_id'=>$post['blog_id']);
                $counter++;
            }
            // don't go over the limit
            if ( $counter >= $how_many ) { 
                break; 
            }
        }        
        return $posts_array;
    }
}

/**
 * Gets the number of public active blogs.
 * 
 * @return int Number of public active blogs.
 */
function xtec_lastest_posts_num_active_blogs()
{
    global $wpdb;
    $blogs = $wpdb->get_col("SELECT DISTINCT blogId FROM wp_globalposts, $wpdb->blogs WHERE blogId=blog_id AND `public`='1'");
    return count($blogs);
}

/**
 * Gets the number of posts of the most active public blog.
 * 
 * @return int Number of posts of the most active blog.
 */
function xtec_lastest_posts_num_posts_of_most_active_blog()
{
    global $wpdb;
    $sql = "SELECT count(*) AS postNumber FROM wp_globalposts,wp_blogs WHERE blogid=blog_id AND `public`='1' AND `deleted` = '0' GROUP BY(blogid) ORDER BY postNumber DESC LIMIT 0,1";
    $blogs = $wpdb->get_results($sql);
    return $blogs[0]->postNumber;
}

/**
 * Gets most active public blogs.
 * 
 * @param int $how_many Number of blogs to get.
 * @param int $init Number of the first blogs to ignore.
 * @return array The blog ID, the blog name, the blog url, the last updated date and the number of posts of the blogs.
 */
function xtec_lastest_posts_most_active_blogs($how_many = 5, $init = 0)
{
    global $wpdb;
    //Gets the blocs with more entries
    $sql = "SELECT blogid,count(*) AS postNumber,last_updated FROM wp_globalposts,wp_blogs WHERE blogid=blog_id AND `public`='1' AND `deleted` = '0' GROUP BY(blogid) ORDER BY postNumber desc,last_updated LIMIT $init,$how_many";
    $blogs = $wpdb->get_results($sql);
    if ( count($blogs) > 0 ) {
        foreach ( $blogs as $blog ) {
            $blog_detail = get_blog_details($blog->blogid);
            $posts[] = array('blogId'=>$blog->blogid,'blog_title'=>$blog_detail->blogname,'blog_url'=>$blog_detail->siteurl,'last_updated'=>$blog->last_updated,'postNumber'=>$blog->postNumber);
        }
    }
    return $posts;
}

/**
 * Creates XTEC Lastest Posts database table.
 */
function xtec_lastest_posts_activation_hook()
{
    global $wpdb;
    global $xtec_lastest_posts_db_version;

    $table_name = $wpdb->base_prefix . 'globalposts';

    if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
        $sql = "CREATE TABLE $table_name (
              id int(10) NOT NULL AUTO_INCREMENT,
              blogId int(10) NOT NULL DEFAULT '0',
              time varchar(20) NOT NULL DEFAULT '',
              postType tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (id));";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
              
        dbDelta($sql);
    }

    add_option('$xtec_descriptors_db_version', $xtec_lastest_posts_db_version);
}

/** @todo Make this plugin can only be activated from the main site. */
register_activation_hook(__FILE__,'xtec_lastest_posts_activation_hook');