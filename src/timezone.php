<?php

//XTEC ************ FITXER AFEGIT - Per corregir el time zone de tots els blocs
//2014.07.23 @jmiro227

require_once('./wp-load.php');

set_time_limit (0);

function get_blog_list_all( $start = 0, $num = 10, $deprecated = '' ) {
	_deprecated_function( __FUNCTION__, '3.0' );

	global $wpdb;
	$blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC", $wpdb->siteid), ARRAY_A );

	foreach ( (array) $blogs as $details ) {
		$blog_list[ $details['blog_id'] ] = $details;
		$blog_list[ $details['blog_id'] ]['postcount'] = $wpdb->get_var( "SELECT COUNT(ID) FROM " . $wpdb->get_blog_prefix( $details['blog_id'] ). "posts WHERE post_status='publish' AND post_type='post'" );
	}
	unset( $blogs );
	$blogs = $blog_list;

	if ( false == is_array( $blogs ) )
		return array();

	if ( $num == 'all' )
		return array_slice( $blogs, $start, count( $blogs ) );
	else
		return array_slice( $blogs, $start, $num );
}

$block_size = $_GET['block_size'];
$block_num = $_GET['block_num'];
$log_file = $_GET['log_file'];

$f = fopen($log_file, "w");

echo 'Block number: '.$block_num.'<BR>'.'Block_size: '.$block_size.'<BR><BR>';
$m = 'Block number: '.$block_num."\n".'Block_size: '.$block_size."\n\n";
fwrite($f, $m); 

$blog_list = get_blog_list_all( $block_size * $block_num , $block_size );

foreach ($blog_list AS $blog) {

switch_to_blog($blog['blog_id']);

$blog_timezone_str = get_option('timezone_string');
$blog_gmt_offset = get_option('gmt_offset');

echo 'Blog '.$blog['blog_id'].': '.$blog['domain'].$blog['path'].'-['.$blog_timezone_str.']-['.$blog_gmt_offset.']';
$m='Blog '.$blog['blog_id'].': '.$blog['domain'].$blog['path'].'-['.$blog_timezone_str.']-['.$blog_gmt_offset.']';

update_option('timezone_string', 'Europe/Brussels');

$blog_timezone_str = get_option('timezone_string');
$blog_gmt_offset = get_option('gmt_offset');

echo '--->['.$blog_timezone_str.']-['.$blog_gmt_offset.']'.'<BR>';
$m = $m.'--->['.$blog_timezone_str.']-['.$blog_gmt_offset.']'."\n";

fwrite($f, $m); 

}

echo "<BR>Done<BR>";
$m="\nDone\n";
fwrite($f, $m);
fclose($f); 

?>
