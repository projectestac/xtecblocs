<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); ?>
</head>
<body>
<center>
	<div id="page">
		<div id="header">
		
			<div id="search">
				<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
				<input type="text" value="<?php the_search_query(); ?>" name="s" id="s"/></form>
			</div>
		
			<div id="h1">
				<h1>
					<a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a>
				</h1>
			</div>

			<div id="tabs1">
				<ul>
					<li>
						<a href="<?php bloginfo('url'); ?>" title="Home"><?php _e('Home','colors-idea');?></a>
					</li>
					<?php wp_list_pages('depth=1&title_li='); ?>
				</ul>
		    </div>		
		
			<!-- Here's RSS feed, if you don't need it, just delete next 2 lines  -->
			<div id="rss2">
				<a id="rss" href="<?php bloginfo('rss2_url'); ?>" title="RSS FEED">
				<img src="<?php bloginfo('stylesheet_directory'); ?>/images/rss.png" alt="RSS" border="0"></a>
			</div>
		
		</div>
		
		<div id="blog">