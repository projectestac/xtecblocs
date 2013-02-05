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
</head>
<body>

<div id="wrap">
<div id="top">
<div id="header">
<h1 class="blogtitle"><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
<p class="desc"><?php bloginfo('description'); ?></p>
</div>
<div id="nav">
<ul class="nav">
<li><a href="<?php bloginfo('url'); ?>"><?php _e('Home','big-blue');?></a></li>
<?php wp_list_pages('title_li='); ?>
</ul>
</div></div>
<div id="main">