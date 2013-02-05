<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats please -->
	<style type="text/css" media="screen">
		@import url( <?php bloginfo('stylesheet_url'); ?> );
		/******************************************************************* 
			Show link pointer images for external sites
		*******************************************************************/
		.entrybody a[href^="http:"] {
			background: transparent url("<?php bloginfo('stylesheet_directory'); ?>/images/external_link.gif") no-repeat 100% 50%;
			padding-right: 10px;
			white-space: nowrap;
				}
		.entrybody a:hover[href^="http:"] {
			background: #F3F4EC url("<?php bloginfo('stylesheet_directory'); ?>/images/external_link.gif") no-repeat 100% 50%;
		}
		/* This avoids the icon being shown on internal links.*/
		.entrybody a[href^="http://<?php echo $_SERVER['HTTP_HOST']; ?>"],
		.entrybody a[href^="http://www.<?php echo $_SERVER['HTTP_HOST']; ?>"] {
			background: inherit;
			padding-right: 0px;
		}
	</style>
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php wp_get_archives('type=monthly&format=link'); ?>
	<?php wp_head(); ?>
</head>
<body id="home" class="log">
<!-- The header begins  -->
		<div id="header">
			<h1><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>" ><?php bloginfo('name'); ?></a></h1>
			<div id="subtitle">
				<!-- Here's the tagline  -->
				<?php bloginfo('description'); ?>
			</div>
		</div>
		<div id="headbar"></div>
		<!-- The header ends  -->
	<div id="container">
		<div id="maincol"><!-- The main content column begins  -->
			<div class="col">