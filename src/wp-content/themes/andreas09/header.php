<?php

$wp_andreas09_ImageColour = get_settings('wp_andreas09_ImageColour');

if (!$wp_andreas09_ImageColour) {

$wp_andreas09_ImageColour = 'blue';

update_option('wp_andreas09_ImageColour', $wp_andreas09_ImageColour);

}

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">



<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">

<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

<meta name="description" content="" />

<meta name="keywords" content="" />

<meta name="author" content="Ainslie Johnson / Original design by Andreas Viklund - http://andreasviklund.com" />

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />

<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/<?php echo "" . get_settings( 'wp_andreas09_ImageColour' )

 . ".css"; ?>" type="text/css" media="screen" />

<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; <?php _e('Blog Archive','andreas09'); ?> <?php } ?> <?php wp_title(); ?></title>

</head>



<body>



<div id="container">



<div id="sitename">



<h1><a href="<?php echo get_settings('home'); ?>"><?php bloginfo('name'); ?></a></h1>

<h2><?php bloginfo('description'); ?></h2>



</div>






<?php if (is_page()) { $highlight = ""; } else {$highlight = "current"; } ?>

<div id="mainmenu">
<ul class="level1">
<li class="<?php echo $highlight; ?>"><a href="<?php echo get_settings('home'); ?>"><?php _e('Home'); ?></a></li>
<?php
    if(function_exists("wp_andreas09_nav")) {
      wp_andreas09_nav("sort_column=menu_order&list_tag=0&show_all_parents=1&show_root=1");
    }
?>
</div>



<div id="wrap">
