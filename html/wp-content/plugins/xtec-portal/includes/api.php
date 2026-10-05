<?php

defined('ABSPATH') || exit;

/**
 * Gets the lastest public blogs updated or registered sorted from newest to oldest.
 *
 * @param int $how_many Number of blogs to get.
 * @param int $days Number of days to consider in the datetime comparation from the current time.
 * @param string $what Datetime to compare: 'last_updated' or 'registered'.
 * @param int $init Number of the first blogs to ignore.
 * @param int $not_new Set as '1' to ignore the last updates of the new blogs.
 * @return array The post title, the post date, the author name, the post content, the post guid value, the blog title, the blog
 *     url, the blog id and the blog registered date of the blogs.
 */
function xtec_api_lastest_blogs(int $how_many = 10, int $days = 5, string $what = 'last_updated', int $init = 0, int $not_new = 0): array
{
    global $wpdb;
    $counter = 0;

    // $what is a column name, so it can't be passed to prepare()
    if (!in_array($what, array('last_updated', 'registered'), true)) {
        $what = 'last_updated';
    }

    $condition = '';
    if ($not_new === 1) {
        $condition = ' and `registered` < `last_updated` - 30 ';
    }

    // Get a list of blogs in order of most recent update.
    $blogs = $wpdb->get_results($wpdb->prepare("SELECT blog_id,registered FROM $wpdb->blogs WHERE $what >= DATE_SUB(CURRENT_DATE(), INTERVAL %d DAY) and `public`='1' and `archived` = '0' and `spam` = '0' and `deleted` = '0' $condition ORDER BY $what DESC limit %d, %d", $days, $init, $how_many));
    //get a list with all the ids of the blogs that exist NOW
    $blogsId = $wpdb->get_results(" SELECT blog_id FROM xtec_blocs_global.wp_blogs ");

    foreach ($blogsId as $object) {
        $blogsExistents[] = $object->blog_id;
    }

    foreach ($blogs as $blog) {
        if (in_array($blog->blog_id, $blogsExistents, true)) {
            if (($what = 'registered') && ((int)$blog->blog_id !== 1)) {
                // we need _posts and _options tables for this to work
                $blogOptionsTable = 'wp_' . (int)$blog->blog_id . '_options';
                $blogPostsTable = 'wp_' . (int)$blog->blog_id . '_posts';
                $options = $wpdb->get_results("SELECT option_value FROM $blogOptionsTable WHERE option_name IN ('siteurl','blogname') ORDER BY option_id, option_name DESC");
                // we fetch the title and link for the latest post
                $thispost = $wpdb->get_results('SELECT post_title, guid, post_content, post_date, post_author ' .
                    "FROM $blogPostsTable " .
                    "WHERE post_status = 'publish' " .
                    "AND post_type = 'post' " .
                    "AND post_password = '' " .
                    "AND post_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 5 DAY) " .
                    "ORDER BY $blogPostsTable.id DESC limit 0,3");
                $thispost_current = array_shift($thispost);
                if (isset($thispost_current)) {
                    $author = get_userdata($thispost_current->post_author);
                    $posts[] = array('post_title' => $thispost_current->post_title,
                        'post_date' => $thispost_current->post_date,
                        'author_name' => $author ? $author->display_name : '',
                        'post_content' => $thispost_current->post_content,
                        'guid' => $thispost_current->guid,
                        'blog_title' => $options[1]->option_value,
                        'blog_url' => $options[0]->option_value,
                        'blog_id' => $blog->blog_id,
                        'registered' => $blog->registered);
                }
                // if it is found put it to the output
                if ($thispost) {
                    $counter++;
                }
                // don't go over the limit
                if ($counter >= $how_many) {
                    break;
                }
            }

        }
    }

    if (isset($posts) && is_array($posts)) {
        return $posts;
    }

    return [];

}
