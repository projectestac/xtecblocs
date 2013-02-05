<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); ?>
<script type="text/javascript">hover = function(state,target,img) {   document.getElementById(target).src = '<?php echo bloginfo('stylesheet_directory'); ?>/images/<?php echo get_option('mandigo_scheme'); ?>/' + img +(state ? '_hover' : '') + '.gif'; }</script>
<?php
  if (get_option('mandigo_headoverlay_oversize')) {
    $lastminutecss  = " #head_overlay { height: ". (30 + get_option('mandigo_headoverlay_oversize')*18) ."px; }\n";
  };
  if (get_option('mandigo_bold_links')) {
    $lastminutecss .= " a { font-weight: bold; }\n";
  };
  if ($lastminutecss) {
    echo "<style>\n$lastminutecss</style>\n";
  }
?>
</head>
<body>
<div id="page">


<div id="header">
	<div id="headerimg">
		<h1><a href="<?php echo get_settings('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
		<div class="description"><?php bloginfo('description'); ?></div>
<?php if (get_option('mandigo_headoverlay')) { ?>
		<div id="head_overlay" style="background: url(<?php echo bloginfo('stylesheet_directory'); ?>/images/head_overlay.<?php echo (preg_match("/MSIE [4-6]/",$_SERVER['HTTP_USER_AGENT']) ? 'gif' : 'png') ; ?>);">&nbsp;</div>
<?php } ?>
		<ul class="pages<?php echo (strpos($_SERVER['HTTP_USER_AGENT'],"MSIE ") ? ' pages_ie7' : '') ; ?>">
			<li class="page_item"><a href="<?php echo get_settings('home'); ?>/"><?php _e('Home','mandigo');?></a></li>
<?php wp_list_pages('depth=1&title_li=&exclude='.get_option('mandigo_exclude_pages')); ?>
</ul>
	</div>
</div>
