<?php

require_once('./wp-load.php');

set_time_limit (0);

$blog_list = get_blog_list(0,'all');

foreach ($blog_list AS $blog) {
echo 'Blog '.$blog['blog_id'].': '.$blog['domain'].$blog['path'].' ';

switch_to_blog($blog['blog_id']);

$op=get_blog_option( $blog['blog_id'], $setting);

$wp_rewrite->init();
$wp_rewrite->flush_rules();

echo 'Rewrite rules flushed'.'<BR>';
}

echo 'Done'.'<BR>';
?>
