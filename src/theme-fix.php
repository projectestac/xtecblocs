<?php

//XTEC ************ FITXER AFEGIT - Fix missig theme
//2013.05.30 @jmiro227
//http://agora-virtual.xtec.cat/blocs/theme-fix.php?block_size=1000&block_num=0&log_file=./wp-content/blogs.dir/theme-fix.log&cookies_file=cookies.txt

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

echo '#!/bin/sh<BR>' .   '#Block number: '.$block_num.'<BR>'.'#Block_size: '.$block_size.'<BR><BR>';
$m = '#!/bin/sh'. "\n" . '#Block number: '.$block_num."\n".'#Block_size: '.$block_size."\n\n";
fwrite($f, $m); 

$blog_list = get_blog_list_all( $block_size * $block_num , $block_size );

foreach ($blog_list AS $blog) {

#wget --output-document /dev/null  --no-verbose --load-cookies '/home/jaume.miro/E03JMM/ImpuTAC/Notes/ENS-1068-00109(XTECBLOCS-Upgrade)/scripts xtecblocs/cookies-agora-virtual.txt' 'http://agora-virtual.xtec.cat/blocs/kkk/wp-admin/themes.php'

echo 'wget --output-document /dev/null --no-verbose --load-cookies ' . '"$1"' . ' "http://' .  $blog['domain'].$blog['path'] . 'wp-admin/themes.php"' . '<BR>'; 

$m='wget --output-document /dev/null --no-verbose --load-cookies ' . '"$1"' . ' "http://' .  $blog['domain'].$blog['path'] . 'wp-admin/themes.php"'."\n";

#echo 'Blog '.$blog['blog_id'].': '.$blog['domain'].$blog['path'].'<BR>';
#$m='Blog '.$blog['blog_id'].': '.$blog['domain'].$blog['path']."\n";
fwrite($f, $m); 

}

echo "<BR>#Done<BR>";
$m="\n#Done\n";
fwrite($f, $m);
fclose($f); 

?>
