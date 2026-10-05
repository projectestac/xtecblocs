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

const XTEC_LASTEST_POSTS_DB_VERSION = '1.0';

add_action('auto-draft_to_publish', 'xtec_lastest_posts_to_publish');
add_action('draft_to_publish', 'xtec_lastest_posts_to_publish');
add_action('publish_to_publish', 'xtec_lastest_posts_to_publish');

/**
 * Deletes older posts and registers the post publication.
 */
function xtec_lastest_posts_to_publish(): void
{
    global $wpdb;

    $days = 60; // Days of all entries
    $timeOld = time() - $days * 24 * 60 * 60;

    // Delete old posts
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->globalposts} WHERE `time` < %s", (string)$timeOld));

    // Create a new entry in global posts
    $wpdb->query(
        $wpdb->prepare(
            "INSERT INTO {$wpdb->globalposts} (blogId,time,postType) VALUES (%d, %s, '1')",
            $wpdb->blogid,
            (string)time()
        )
    );
}

/**
 *    Gets the lastest public posts.
 *
 * @param int $how_many Number of blogs to get.
 * @param int $days Number of days to consider in the datetime comparation from the current time.
 * @param int $init Number of the first posts to ignore.
 * @return array The date, the title, the author name, the content, the guid value, the blog title, the blog url and the blog ID of
 *     the posts.
 */
function xtec_lastest_posts_lastest_posts($how_many = 10, $days = 5, $init = 0): array
{
    global $wpdb;
    $counter = 0;

    // Fix the date in timestamp
    $date = time() - 24 * 60 * 60 * $days;

    // Takes the doble in case any of them is descarted
    $how_many_2 = $how_many * 2;

    // get a list of blogs in order of most recent update
    $blogs = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT DISTINCT blogId FROM {$wpdb->globalposts},$wpdb->blogs WHERE time > %d AND `public` = '1' " .
            "AND `archived` = '0' AND `spam` = '0' AND `deleted` = '0' AND `blogId` = `blog_id` AND blogId<> 1 " .
            "ORDER BY id DESC LIMIT %d, %d",
            $date,
            $init,
            $how_many_2
        )
    );

    if ($blogs) {
        $posts = [];
        foreach ($blogs as $blog) {
            $blogPostsTable = $wpdb->get_blog_prefix($blog->blogId) . 'posts';
            // we fetch the title and link for the latest post
            $thispost = $wpdb->get_results("SELECT post_title, guid, post_content, post_date, post_author " .
                "FROM $blogPostsTable " .
                "WHERE post_status = 'publish' " .
                "AND post_type = 'post' " .
                "AND post_password = '' " .
                "AND post_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 5 DAY) " .
                "ORDER BY $blogPostsTable.id DESC LIMIT 0,1");

            if (isset($thispost[0])) {
                $author = get_userdata($thispost[0]->post_author);

                $blog_detail = get_blog_details($blog->blogId);

                $posts[] = [
                    'post_date' => $thispost[0]->post_date,
                    'post_title' => $thispost[0]->post_title,
                    'author_name' => $author ? $author->display_name : '',
                    'post_content' => $thispost[0]->post_content,
                    'guid' => $thispost[0]->guid,
                    'blog_title' => $blog_detail->blogname,
                    'blog_url' => $blog_detail->siteurl,
                    'blog_id' => $blog->blogId,
                ];
            }
        }
        arsort($posts);

        // Discard invalid values
        $posts_array = [];
        foreach ($posts as $post) {
            if ($post['post_date'] !== 0) {
                $posts_array[] = [
                    'post_date' => $post['post_date'],
                    'post_title' => $post['post_title'],
                    'author_name' => $post['author_name'],
                    'post_content' => $post['post_content'],
                    'guid' => $post['guid'],
                    'blog_title' => $post['blog_title'],
                    'blog_url' => $post['blog_url'],
                    'blog_id' => $post['blog_id'],
                ];
                $counter++;
            }
            // don't go over the limit
            if ($counter >= $how_many) {
                break;
            }
        }
        return $posts_array;
    }
    return [];
}

/**
 * Gets the number of public active blogs.
 *
 * @return int Number of public active blogs.
 */
function xtec_lastest_posts_num_active_blogs(): int
{
    global $wpdb;
    $blogs = $wpdb->get_col(
        "SELECT DISTINCT blogId FROM {$wpdb->globalposts}, $wpdb->blogs WHERE blogId=blog_id AND `public`='1' " .
        "AND `archived` = '0' AND `spam` = '0' AND `deleted` = '0'"
    );
    return count($blogs);
}

/**
 * Gets the number of posts of the most active public blog.
 *
 * @return int Number of posts of the most active blog.
 */
function xtec_lastest_posts_num_posts_of_most_active_blog(): int
{
    global $wpdb;

    $sql = "SELECT count(*) AS postNumber FROM {$wpdb->globalposts},{$wpdb->blogs} WHERE blogid=blog_id " .
        "AND `public`='1' AND `archived` = '0' AND `spam` = '0' AND `deleted` = '0' GROUP BY(blogid) " .
        "ORDER BY postNumber DESC LIMIT 0,1";
    $blogs = $wpdb->get_results($sql);

    return isset($blogs[0]) ? $blogs[0]->postNumber : 0;
}

/**
 * Gets most active public blogs.
 *
 * @param int $how_many Number of blogs to get.
 * @param int $init Number of the first blogs to ignore.
 * @return array The blog ID, the blog name, the blog url, the last updated date and the number of posts of the blogs.
 */
function xtec_lastest_posts_most_active_blogs($how_many = 5, $init = 0): array
{
    global $wpdb;

    //Gets the blocs with more entries
    $sql = "SELECT blogid,count(*) AS postNumber,last_updated FROM {$wpdb->globalposts},{$wpdb->blogs} " .
        "WHERE blogid=blog_id AND `public`='1' AND `archived` = '0' AND `spam` = '0' AND `deleted` = '0' " .
        "GROUP BY(blogid) ORDER BY postNumber desc,last_updated LIMIT %d, %d";
    $blogs = $wpdb->get_results($wpdb->prepare($sql, $init, $how_many));
    $posts = [];
    if (is_array($blogs) && count($blogs) > 0) {
        foreach ($blogs as $blog) {
            $blog_detail = get_blog_details($blog->blogid, true);
            // Hide blog 'aroga (Espai de monitorització)' and main site (blog id is 1)
            if ((!preg_match('/\/aroga\/$/', $blog_detail->path)) && ((int)$blog->blogid !== 1)) {
                $posts[] = [
                    'blogId' => $blog->blogid,
                    'blog_title' => $blog_detail->blogname,
                    'blog_url' => $blog_detail->siteurl,
                    'last_updated' => $blog->last_updated,
                    'postNumber' => $blog->postNumber,
                ];
            }
        }
    }

    return $posts;
}

/**
 * Creates XTEC Lastest Posts database table.
 */
function xtec_lastest_posts_activation_hook(): void
{
    global $wpdb;

    $table_name = $wpdb->globalposts;

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        $sql = "CREATE TABLE $table_name (
              id int(10) NOT NULL AUTO_INCREMENT,
              blogId int(10) NOT NULL DEFAULT '0',
              time varchar(20) NOT NULL DEFAULT '',
              postType tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (id));";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($sql);
    }

    add_option('xtec_lastest_posts_db_version', XTEC_LASTEST_POSTS_DB_VERSION);
}
