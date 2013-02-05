<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats please -->

	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />
  <?php if (file_exists(TEMPLATEPATH . '/my.css')) : ?>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_url').'/my.css'; ?>" media="screen" />
  <?php endif ?>      
  <?php include TEMPLATEPATH . '/rss.php' ?>
  <link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/smoothscroll.js"></script>
  <?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>	
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="container">

<ul class="skip">
<li><a href="#wrapper"><?php _e('Skip to content','stardust') ?></a></li>
<li><a href="#menu"><?php _e('Skip to menu','stardust') ?></a></li>
</ul>

<hr />

<div id="header">
  <h1><a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a></h1>
  <p class="payoff"><?php bloginfo('description'); ?>&nbsp;</p>
   
  <?php include TEMPLATEPATH . '/searchform.php' ?>
  <?php include TEMPLATEPATH . '/rssbutton.php' ?>

  <?php wp_page_menu(('show_home=1&menu_class=menu1&depth=1')); ?>
</div><!-- end header -->