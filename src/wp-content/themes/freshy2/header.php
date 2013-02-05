<?php
define ('TEMPLATE_DOMAIN','freshy2');
load_theme_textdomain(TEMPLATE_DOMAIN);
global $freshy_options;
$freshy_options = get_option('freshy_options');
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" title="Freshy"/>
<!--[if lte IE 6]>
<link rel="stylesheet" href="<?php print get_bloginfo('stylesheet_directory').'/fix-ie.php'; ?>" type="text/css" media="screen"/>
<![endif]-->
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_get_archives('type=monthly&format=link'); ?>
<?php wp_head(); ?>
</head>
<body>
<div id="body">

<div id="header">
	<div class="container">
		<div id="title">
			<h1>
				<a href="<?php echo get_settings('home'); ?>">
					<span><?php bloginfo('name'); ?></span>
				</a>
			</h1>
			<div class="description">
				<small><?php bloginfo('description'); ?></small>
			</div>
			<div id="quicklinks">
				<ul>
					<?php if ($freshy_options['custom_quicklinks'])
					{
						foreach ($freshy_options['custom_quicklinks'] as $custom_quicklink)
						{
							?>
							<li>
								<a href="<?php echo $custom_quicklink['url']; ?>">
									<?php _e($custom_quicklink['label'],TEMPLATE_DOMAIN); ?>
								</a>
							</li>
							<?php
						}
					}
					?>
					<?php if($freshy_options['header_rss']) : ?>
						<li><a title="rss" href="<?php bloginfo('rss2_url'); ?>" class="rss">rss</a></li>
					<?php endif; ?>
				</ul>
				<?php if($freshy_options['header_search']) : include (TEMPLATEPATH . '/searchform.php');  endif; ?>
			</div>
		</div>
		<div id="header_image">

			<div id="menu">
			<div class="menu_container">

			<ul>
			
			<?php if ('page' != get_option('show_on_front')) : // no page has been chosen as frontpage ?>
					
				<li class="<?php if (is_home()) echo 'current_page_item'; ?>">
					<a href="<?php echo get_settings('home'); ?>">
						<?php _e($freshy_options['first_menu_label'],'freshy-2'); ?>
					</a>
				</li>
					
			<?php endif; ?>

			<?php wp_list_pages('sort_column=menu_order&title_li='); ?>
				
			<?php if ($freshy_options['custom_menus'])
			{
				foreach ($freshy_options['custom_menus'] as $custom_menu)
				{
					?>
					<li>
						<a href="<?php echo $custom_menu['url']; ?>">
							<?php _e($custom_menu['label'],'freshy-2'); ?>
						</a>
					</li>
					<?php
				}
			}
			?>
				
			<?php if ($freshy_options['last_menu_type']=='email' || $freshy_options['last_menu_type']=='link') : ?>
					
				<li class="last_menu">
					
				<?php if ($freshy_options['last_menu_type']=='email') : ?>
						
					<a href="mailto:<?php echo $freshy_options['contact_email']; ?>">
						<?php _e($freshy_options['last_menu_label'],'freshy-2'); ?>
					</a>
						
				<?php elseif ($freshy_options['last_menu_type']=='link') : ?>
						
					<a href="<?php echo $freshy_options['contact_link']; ?>">
						<?php _e($freshy_options['last_menu_label'],'freshy-2'); ?>
					</a>
						
				<?php endif; ?>	
					
				</li>
						
			<?php endif; ?>
				
			</ul>
				
			</div><span class="menu_end"></span>
			</div>
			
		</div>
	</div>
</div>
	
<div id="page" <?php echo freshy_layout_class() ?>>
	<div class="container">
		<div id="frame">