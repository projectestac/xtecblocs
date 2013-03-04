<?php

//XTEC ************ FITXER AFEGIT - Per actualitzar els permanent links i solucionar el problema dels tags
//2013.02.27 @jmiro227

require_once('./wp-load.php');

set_time_limit (0);

$block_size = $_GET['block_size'];
$block_num = $_GET['block_num'];
$log_file = $_GET['log_file'];

$f = fopen($log_file, "w");

echo 'Block number: '.$block_num.'<BR>'.'Block_size: '.$block_size.'<BR><BR>';
$m = 'Block number: '.$block_num."\n".'Block_size: '.$block_size."\n\n";
fwrite($f, $m); 

$blog_list = get_blog_list( $block_size * $block_num , $block_size );

foreach ($blog_list AS $blog) {

switch_to_blog($blog['blog_id']);
$wp_rewrite->flush_rules();

echo 'Blog '.$blog['blog_id'].': '.$blog['domain'].$blog['path'].'<BR>';
$m='Blog '.$blog['blog_id'].': '.$blog['domain'].$blog['path']."\n";
fwrite($f, $m); 

}

echo "<BR>Done<BR>";
$m="\nDone\n";
fwrite($f, $m);
fclose($f); 

?>
