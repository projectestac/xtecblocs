<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<title><?php wp_title( ''); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if(of_get_option('favicon_radio') == 1) : ?>
	<link rel="shortcut icon" href="<?php echo of_get_option('favicon_url'); ?>" type="image/x-icon" />
	<?php endif; ?>
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div id="wrapper">
	    <div id="inner-wrapper">
			<div id="header">
			    <div id="header-top">
					<div id="logo">
					    <?php if (of_get_option('logo_image')) { ?>
						<a href="<?php echo home_url(); ?>"><img src="<?php echo of_get_option('logo_image'); ?>" alt="<?php bloginfo( 'name' ); ?>" /></a>
						<?php }else {?>
					    <h1 id="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> - <?php bloginfo('description'); ?>" rel="home"><?php bloginfo('name'); ?></a></h1><p><?php bloginfo('description'); ?></p>
						<?php } ?>
					</div>
                    <?php if (of_get_option('delicacy_search')) : ?>
					<div id="search-form">
					    <?php get_search_form(); ?>
					</div>
                    <?php endif; ?>
				</div>
			<div id="navigation">
					<?php
					if(has_nav_menu('primary-menu')){
						 wp_nav_menu(array(
						 	'theme_location' => 'primary-menu',
						 	'container' => 'div',
						 	'container_class' => 'main-menu',
						 	'menu_class' => 'sf-menu',
						 	'depth' => '0'
						 ));
					}else {
					?>
						<div class="main-menu">
						<ul class="sf-menu">
							<?php wp_list_pages('title_li='); ?>
						</ul>
						</div>
					<?php
					}
					?>
			</div><!-- end #navigation -->
            <?php $header_widget = of_get_option('delicacy_header_widget'); ?>
			<div id="intro" <?php if(!$header_widget){echo 'class="nowidget"';} ?>>
				<div class="menu-shadow"></div>
                <?php if (of_get_option('delicacy_header_widget')) : ?>
				<div class="headline">
	    			<?php
					if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Header Widget')):
					endif;
					?>
				</div>
                <?php endif; ?>
			</div><!-- end #intro -->
			</div><!-- end #header -->
        	<div id="content-wrapper">
