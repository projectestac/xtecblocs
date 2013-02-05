<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>

	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />	
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats please -->

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php wp_get_archives('type=monthly&format=link'); ?>
	<?php //comments_popup_script(); // off by default ?>
	<?php wp_head(); ?>
</head>
<body><div id="container">

<div id="header">

	<div id="menu">
		<ul>
			<li><a href="<?php bloginfo('url'); ?>/" title="<?php bloginfo('name', 'digg-3'); ?>"><?php _e('Home', 'digg-3'); ?></a></li>
			<li><a href="<?php echo get_permalink(2); ?>" title="<?php _e('About', 'digg-3'); ?>"><?php _e('About', 'digg-3'); ?></a></li>
		</ul>
	</div>

	<div id="pagetitle">
		<h1><a href="<?php bloginfo('url'); ?>/" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>
	</div>

	<div id="syndication">
		<a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS', 'digg-3'); ?>" class="feed"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>', 'digg-3'); ?></a> &#124; <a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('Syndicate comments using RSS', 'digg-3'); ?>"><?php _e('Comments RSS', 'digg-3');?></a>
	</div>
	<div id="searchbox">
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
	</div>

</div>

<div class="pagewrapper"><div class="page">

<?php include (TEMPLATEPATH . '/obar.php'); ?>
