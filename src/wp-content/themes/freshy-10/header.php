<!-- language redirection -->
<?php if(function_exists('yy_redirect')) yy_redirect(); ?>

<?php //$freshy_options = get_option('freshy_options'); 
	global $yy_options;
	global $freshy_options;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" title="Freshy"/>
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_get_archives('type=monthly&format=link'); ?>
<?php wp_head(); ?>
</head>
<body>

<div id="page">
	
	<!-- header -->
	<div id="header">
		<div id="title">
			<h1>
				<a href="<?php if(function_exists('yy_home_url')) { echo yy_home_url(); } else { echo get_settings('home'); } ?>">
					<span><?php bloginfo('name'); ?></span>
				</a>
			</h1>
			<div class="description">
				<small><?php bloginfo('description'); ?></small>
			</div>
		</div>
		<div id="title_image"></div>
	</div>
	
	<!-- main div -->
	<div id="frame">

	<!-- main menu -->
	<?php if(function_exists('yy_menu')) : /* yammyamm is installed and function yy_menu exists */ ?>
		<ul class="menu" id="main_menu">
			
			<!-- the home is the language root page -->
			<?php if ($yy_options['home_type']=='page') { ?>
					
			<li class="<?php if (yy_is_home()) { ?>current_page_item<?php } else { ?>page_item<?php } ?>">
				<a class="first_menu" href="<?php echo yy_home_url(); ?>">
					<?php _e($freshy_options['first_menu_label'], 'freshy'); ?>
				</a>
			</li>
			<li class="<?php if (((is_home()) && !(is_paged())) or (is_archive()) or (is_single()) or (is_paged()) or (is_search())) { ?>current_page_item<?php } else { ?>page_item<?php } ?>">
				<a href="<?php echo yy_home_url('blog'); ?>">
					<?php _e($freshy_options['blog_menu_label'], 'freshy'); ?>
				</a>
			</li>
			
			<?php }
			else { ?>
			<!-- the home is the language blog category -->
			
			<li class="<?php if (((is_home()) && !(is_paged())) or (is_archive()) or (is_single()) or (is_paged()) or (is_search())) { ?>current_page_item<?php } else { ?>page_item<?php } ?>">
				<a class="first_menu" href="<?php echo yy_home_url('blog'); ?>">
					<?php _e($freshy_options['blog_menu_label'], 'freshy'); ?>
				</a>
			</li>
			<!--
			<li class="<?php if (yy_is_home('page')) { ?>current_page_item<?php } else { ?>page_item<?php } ?>">
				<a href="<?php echo yy_home_url('page'); ?>">
					<?php $freshy_options['first_menu_label'] ?>
				</a>
			</li>
			-->
			<?php }?>
			
			
			<!-- pages -->
			<?php yy_menu('sort_column=menu_order&depth=1&title_li=','none'); ?>

			
			<li class="last_menu">
				
				<!-- if an email is set in the options -->
				<?php if ($freshy_options['last_menu_type']=='email') { // the home is language root page ?>
					
				<a class="last_menu" href="mailto:<?php echo $freshy_options['contact_email']; ?>">
					<?php _e($freshy_options['last_menu_label'], 'freshy'); ?>
				</a>
					
				<?php }
				else if ($freshy_options['last_menu_type']=='link') { // the home is language root page ?>
					
				<a class="last_menu" href="<?php echo $freshy_options['contact_link']; ?>">
					<?php _e($freshy_options['last_menu_label'], 'freshy'); ?>
				</a>
					
				<?php }
				else { ?>
				<!-- put an empty link to have the end of the menu anyway -->
					
				<a class="last_menu_off">
				</a>
					
				<?php }?>
					
			</li>
			
			<!-- languages flags -->
			<?php yy_lang_menu('lang_menu'); ?>
				
		</ul>
	
	<?php else : /* yammyamm is not installed or function yy_menu does not exist, use default menu */ ?>
		<ul class="menu">
			
			<li class="<?php if (is_home()) { ?>current_page_item<?php } else { ?>page_item<?php } ?>">
				<a class="first_menu" href="<?php echo get_settings('home'); ?>">
					<?php _e($freshy_options['first_menu_label'], 'freshy'); ?>
				</a>
			</li>
					
			<?php freshy_wp_list_pages('sort_column=menu_order&depth=1&title_li='); ?>
				
			<li class="last_menu">
				
				<!-- if an email is set in the options -->
				<?php if ($freshy_options['last_menu_type']=='email') { // the home is language root page ?>
					
				<a class="last_menu" href="mailto:<?php echo $freshy_options['contact_email']; ?>">
					<?php _e($freshy_options['last_menu_label'], 'freshy'); ?>
				</a>
					
				<?php }
				else if ($freshy_options['last_menu_type']=='link') { // the home is language root page ?>
					
				<a class="last_menu" href="<?php echo $freshy_options['contact_link']; ?>">
					<?php _e($freshy_options['last_menu_label'], 'freshy'); ?>
				</a>
					
				<?php }
				else { ?>
				<!-- put an empty link to have the end of the menu anyway -->
					
				<a class="last_menu_off">
				</a>
					
				<?php }?>
					
			</li>

		</ul>
	<?php endif; ?>
	
	<hr style="display:none"/>
