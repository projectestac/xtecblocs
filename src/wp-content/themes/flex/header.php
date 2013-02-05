<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats please -->
	<style type="text/css" media="screen">
		@import url( <?php bloginfo('stylesheet_url'); ?> );
		
body{
			background: #6C7C8B url("<?php bloginfo('stylesheet_directory'); ?>/images/background.png") repeat-x;
		}
	</style>
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php wp_get_archives('type=monthly&format=link'); ?>
	<?php //comments_popup_script(); // off by default ?>
	<?php wp_head(); ?>
</head>
<body id="home" class="log">
	<div id="container">
		<!-- The header begins  -->
		<div id="header">
			<h1><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>" >:<?php bloginfo('name'); ?></a></h1>
			<div id="subtitle">
				<!-- Here's the tagline  -->
				<?php bloginfo('description'); ?>
			</div>
		</div>
		<div id="headbar"></div>
		<!-- The header ends  -->
		<div id="maincol"><!-- The main content column begins  -->
			<div class="col">