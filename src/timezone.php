<?php

//XTEC ************ FITXER AFEGIT - Per corregir el time zone de tots els blocs
//2014.07.23 @jmiro227

require_once('./wp-load.php');

set_time_limit (0);

$blog_timezone_str_n = 'Europe/Brussels';

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

$n = ( isset($_GET['n']) ) ? intval($_GET['n']) : 0;

$blogs = $wpdb->get_results( "SELECT * FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' AND spam = '0' AND deleted = '0' AND archived = '0' ORDER BY registered DESC LIMIT {$n}, 5", ARRAY_A );

if ( empty( $blogs ) ) {
  echo '<p>' . __( 'All done!' ) . '</p>';
  exit;
}

echo "<ul>";

foreach ( (array) $blogs as $details ) {

  switch_to_blog( $details['blog_id'] );

  $siteurl = site_url();
  $blog_timezone_str_i = get_option('timezone_string');
  $blog_gmt_offset_i = get_option('gmt_offset');

  echo "<li>$siteurl : [$blog_timezone_str_i] [$blog_gmt_offset_i]";

  update_option('timezone_string', $blog_timezone_str_n);

  $blog_timezone_str_o = get_option('timezone_string');
  $blog_gmt_offset_o = get_option('gmt_offset');

  echo " --> [$blog_timezone_str_o] [$blog_gmt_offset_o]</li>";

  restore_current_blog();

  if ( ( $blog_timezone_str_n != $blog_timezone_str_i ) and ( $blog_timezone_str_n != $blog_timezone_str_o) ) {
    echo '<p>' . sprintf( __( 'Warning! Problem updating %1$s'), $siteurl ) . " : [$blog_timezone_str_i] --> [$blog_timezone_str_n]" . '</p>';
    exit;
  }
}

echo "</ul>";
?><p><?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:' ); ?> <a class="button" href="timezone.php?n=<?php echo ($n + 5) ?>"><?php _e("Next Sites"); ?></a></p>
<script type='text/javascript'>
<!--
function nextpage() {
  location.href = "timezone.php?n=<?php echo ($n + 5) ?>";
}
setTimeout( "nextpage()", 250 );
//-->
</script><?php

?>
