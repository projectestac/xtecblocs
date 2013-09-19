<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

<?php
$stylesheet = get_option('xtec_encurs_colour');
if ( empty($stylesheet) ) {
?>
	<link rel='stylesheet' href='<?php bloginfo('stylesheet_directory'); ?>/style.css' type='text/css'>
<?php
}
else {
?>
	<link rel='stylesheet' href='<?php bloginfo('stylesheet_directory'); ?>/<?php echo $stylesheet?>.css' type='text/css'>
<?php
}
?>
<!-- <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />  -->
<!-- <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/impressora.css" type="text/css" media="print" />  -->
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<?php 
wp_get_archives('type=monthly&format=link'); 
wp_head(); 
?>
</head>
<body>
<div id="outer-page">
<div id="page">

<?php
$mainurl = get_option('xtec_encurs_mainurl');
$imageurl = get_option('xtec_encurs_imageurl');
?>

<div id="header">
	<div id="headerimg">
		<?php
		if ( empty($mainurl) ) { echo "<a href='" . get_settings('home') . "'>"; }
		else { echo "<a href='$mainurl'>"; }
		?>
		<img src=
		<?php
		if ( empty($imageurl) ) { echo bloginfo('stylesheet_directory'). "/images/header.jpg"; }
		else { echo $imageurl; }
		?>
		/></a>
		<h1 class="title"><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
		<span class="description"><?php bloginfo('description'); ?></span>
	</div>
</div>
<div id="menu">
	<ul>
	<?php
		if ( !empty($mainurl) ) { ?>
			<li class="page_item">
				<a href="<?php echo $mainurl ?>">Inici</a>
			</li>
			<li class="<?php if (is_home()) { ?>current_page_item<?php } else { ?>page_item<?php } ?>">
				<a href="<?php echo get_settings('home'); ?>"><?php echo get_option('xtec_encurs_homename') ?></a>
			</li>
		<?php }
		else { ?>
			<li class="<?php if (is_home()) { ?>current_page_item<?php } else { ?>page_item<?php } ?>">
				<a href="<?php echo get_settings('home'); ?>">Inici</a>
			</li>
		<?php }
	?>
	<?php XGF_wp_list_pages('sort_column=menu_order&depth=1&title_li='); ?>
	</ul>
</div>
<hr />
