<!-- begin sidebar -->
<div id="navcol">
<?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : ?>
		<h2><?php _e('Links');?></h2>
		<ul>
			<li class="sidebullet">
			<a href="<?php bloginfo('blog_url'); ?>"><?php _e('Home', 'flex');?></a></li>
			<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS', 'flex'); ?>"><?php _e('<abbr title="Really Simple Syndication">RSS</abbr>', 'flex'); ?></a></li>
		</ul>
		<h2><?php _e('Archives', 'flex'); ?></h2>
		<ul>
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
		
		<h2><?php _e('Categories', 'flex'); ?></h2>
		<ul>
			<?php list_cats(0, '', 'name', 'asc', '', 1, 0, 1, 1, 1, 1, 0,'','','','','') ?>
		</ul>
		<ul id="linkslist">
		<?php wp_list_pages('title_li=<h2>' . __('Pages', 'flex') . '</h2>' ); ?>
		<?php get_links_list(); ?>
		</ul>		
		<?php 
			if (function_exists('wp_theme_switcher')){ 
				echo "<h2>Themes</h2>";
				wp_theme_switcher(''); 
			}		
		?>
		<div id="searchdiv">
		<form id="searchform" method="get" action="">
			<input type="text" name="s" size="15"/>
			<input type="submit" value="<?php _e('Search', 'flex'); ?>" />
		</form>
		</div>
	
	<?php endif; ?>
	</div>
<!-- end sidebar -->
