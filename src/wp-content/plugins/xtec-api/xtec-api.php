<?php

/*
Plugin Name: XTEC API
Description: Adds some useful functions.
Version: 1.0
Author: Francesc Bassas i Bullich & Germán Antolin Prioto
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * Gets the lastest public blogs updated or registered sorted from newest to oldest.
 *
 * @param int $how_many Number of blogs to get.
 * @param int $days Number of days to consider in the datetime comparation from the current time.
 * @param string $what Datetime to compare: 'last_updated' or 'registered'.
 * @param int $init Number of the first blogs to ignore.
 * @param int $not_new Set as '1' to ignore the last updates of the new blogs.
 * @return array The post title, the post date, the user login, the post content, the post guid value, the blog title, the blog url, the blog id and the blog registered date of the blogs.
 */
function xtec_api_lastest_blogs($how_many = 10, $days=5, $what='last_updated', $init=0, $not_new=0) {
    global $wpdb;
    $counter = 0;
    if ( $not_new == 1 ) { $not_new = ' and `registered` < `last_updated` - 30 '; } else { $not_new = ''; }
    // get a list of blogs in order of most recent update
    $blogs = $wpdb->get_results("SELECT blog_id,registered FROM $wpdb->blogs WHERE $what >= DATE_SUB(CURRENT_DATE(), INTERVAL $days DAY) and `public`='1' and `deleted` = '0' $not_new ORDER BY $what DESC limit $init,$how_many");
    //get a list with all the ids of the blogs that exist NOW
    // XTEC ************ AFEGIT - get all the blogs' ids.
    // 2015.02.16 @vsaavedr
    $blogsId = $wpdb->get_results(" SELECT blog_id FROM xtec_blocs_global.wp_blogs ");
    foreach ($blogsId as $key => $object) {
        $blogsExistents[] = $object->blog_id;
    }
	// ************ FI
    foreach ( $blogs as $blog ) {
        // XTEC ************ AFEGIT - Checking whether the blog->blog_id is a valid id of a blog or not.
        // 2015.02.16 @vsaavedr
        if( in_array($blog->blog_id, $blogsExistents) ) {
        // ************ FI
        	// XTEC ************ AFEGIT - Checking whether the blog->blog_id is a valid id of a blog or not.
        	// 2015.04.21 @vsaavedr
        	if( ($what = 'registered' ) && ($blog->blog_id != 1) ) {
        	// ************ FI
	            // we need _posts and _options tables for this to work
	            $blogOptionsTable = "wp_".$blog->blog_id."_options";
	            $blogPostsTable = "wp_".$blog->blog_id."_posts";
	            $options = $wpdb->get_results("SELECT option_value FROM $blogOptionsTable WHERE option_name IN ('siteurl','blogname') ORDER BY option_id, option_name DESC");
	            // we fetch the title and link for the latest post
	            $thispost = $wpdb->get_results("SELECT post_title, guid, post_content, post_date, post_author " .
	                                           "FROM $blogPostsTable " .
	                                           "WHERE post_status = 'publish' " .
	                                           "AND post_type = 'post' " .
	                                           "AND post_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 5 DAY) ".
	                                           "ORDER BY $blogPostsTable.id DESC limit 0,3");

	            $thisusername = get_userdata($thispost[0]->post_author)->user_login;
	            $posts[] = array('post_title'=>$thispost[0]->post_title,
	                             'post_date'=>$thispost[0]->post_date,
	                             'user_login'=>$thisusername,
	                             'post_content'=>$thispost[0]->post_content,
	                             'guid'=>$thispost[0]->guid,
	                             'blog_title'=>$options[1]->option_value,
	                             'blog_url'=>$options[0]->option_value,
	                             'blog_id'=>$blog->blog_id,
	                             'registered'=>$blog->registered);
	            // if it is found put it to the output
	            if ( $thispost ) { $counter++; }
	            // don't go over the limit
	            if ( $counter >= $how_many ) {
	            	break;
	            }
       		}
            // XTEC ************ MODIFICAT - Solved error that made this function returns no data.
            // 2015.04.21 @vsaavedr

        }
    }

    if (is_array($posts)) {
    	return $posts;
    } else {
    	return array();
    }
    // ************ ORIGINAL
            /*else if($counter == 0){
            	$posts = array();
            }
        }
    }
    return $posts;*/
    // ************ FI
}