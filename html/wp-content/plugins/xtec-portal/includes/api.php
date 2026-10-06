<?php

defined('ABSPATH') || exit;

/**
 * Copyright 2026 Departament d'Educació i Formació Professional
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Gets the latest public blogs updated or registered sorted from newest to oldest.
 *
 * @param int $how_many Number of blogs to get.
 * @param int $days Number of days to consider in the datetime comparation from the current time.
 * @param string $what Datetime to compare: 'last_updated' or 'registered'.
 * @param int $init Number of the first blogs to ignore.
 * @param int $not_new Set as '1' to ignore the last updates of the new blogs.
 * @return array The post title, the post date, the author name, the post content, the post guid value, the blog title, the blog
 *     url, the blog id and the blog registered date of the blogs.
 */
function xtec_api_latest_blogs(int $how_many = 10, int $days = 5, string $what = 'last_updated', int $init = 0, int $not_new = 0): array
{
    global $wpdb;
    $counter = 0;
    $posts = [];

    // $what is a column name, so it can't be passed to prepare()
    if (!in_array($what, ['last_updated', 'registered'], true)) {
        $what = 'last_updated';
    }

    $condition = '';
    if ($not_new === 1) {
        $condition = ' and `registered` < `last_updated` - 30 ';
    }

    // Get a list of blogs in order of most recent update.
    $blogs = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT blog_id,registered FROM $wpdb->blogs WHERE $what >= DATE_SUB(CURRENT_DATE(), INTERVAL %d DAY) " .
            "and `public`='1' and `archived` = '0' and `spam` = '0' and `deleted` = '0' $condition " .
            "ORDER BY $what DESC limit %d, %d",
            $days,
            $init,
            $how_many
        )
    );

    foreach ($blogs as $blog) {
        if ((int)$blog->blog_id !== 1) {
            $blogPostsTable = $wpdb->get_blog_prefix($blog->blog_id) . 'posts';
            $blogDetails = get_blog_details($blog->blog_id);
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
                $posts[] = [
                    'post_title' => $thispost_current->post_title,
                    'post_date' => $thispost_current->post_date,
                    'author_name' => $author ? $author->display_name : '',
                    'post_content' => $thispost_current->post_content,
                    'guid' => $thispost_current->guid,
                    'blog_title' => $blogDetails->blogname,
                    'blog_url' => $blogDetails->siteurl,
                    'blog_id' => $blog->blog_id,
                    'registered' => $blog->registered,
                ];
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

    return $posts;
}

/**
 * Counts the blogs of the network that are not deleted.
 *
 * @return array The number of blogs ('blogs') and of private blogs ('blogsPrivate').
 */
function xtec_api_blogs_number(): array
{
    global $wpdb;

    return [
        'blogs' => (int)$wpdb->get_var("SELECT count(*) FROM $wpdb->blogs WHERE `deleted` = '0'"),
        'blogsPrivate' => (int)$wpdb->get_var(
            "SELECT count(*) FROM $wpdb->blogs WHERE `public` = '0' AND `deleted` = '0'"
        ),
    ];
}
