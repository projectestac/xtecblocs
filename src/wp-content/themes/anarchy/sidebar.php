<div id="menu">

<ul>
	<?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : ?>
	<?php get_links_list(); ?>
	<li id="categories" style="font-weight:bold; padding-top:20px;"><?php _e('Categories','anarchy'); ?>
		<ul>
		<?php wp_list_cats(); ?>
		</ul>
	</li>
	<li id="search" style="font-weight:bold; padding-top:20px;">
		<label for="s"><?php _e('Search','anarchy'); ?></label>	
		<form id="searchform" method="get" action="<?php echo $REQUEST_URI; ?>index.php">
			<div>
				<input type="text" name="s" id="s" size="15" /><br />
				<input type="submit" name="submit" value="<?php _e('Search','anarchy'); ?>" />
			</div>
		</form>
	</li>
	<li id="archives" style="font-weight:bold; padding-top:20px;"><?php _e('Archives','anarchy'); ?>
		<ul>
		<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</li>
	<li id="calendar" style="font-weight:bold; padding-top:20px;">
	<?php get_calendar(); ?>
	</li>
<?php if (function_exists('wp_theme_switcher')) { ?>
	<li id="themes" style="font-weight:bold; padding-top:20px;"><?php _e('Themes','anarchy'); ?>
	<?php wp_theme_switcher(); ?>
	</li>
<?php } ?>
 <li id="wp_meta" style="font-weight:bold; padding-top:20px;"><?php _e('Meta'); ?>
 	<ul>
		<li><?php wp_register(); ?></li>
		<li><?php wp_loginout(); ?></li>
		<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS','anarchy'); ?>"><?php _e('<abbr title="Really Simple Syndication">RSS</abbr>','anarchy'); ?></a></li>
		<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS','anarchy'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>','anarchy'); ?></a></li>
		<li><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional','anarchy'); ?>"><?php _e('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>','anarchy'); ?></a></li>
		<li><a href="http://wordpress.org/" title="<?php _e('Powered by WordPress, state-of-the-art semantic personal publishing platform.','anarchy'); ?>"><abbr title="WordPress">WP</abbr></a></li>

	</ul>
 </li>
<?php endif; ?>

</ul>

</div>
