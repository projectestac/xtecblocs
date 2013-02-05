
<!-- begin sidebar -->
<ul id="menu">

	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
	
	<li>
		<h2><?php _e('Pages','tranquility');?></h2>
		<ul>
			<?php wp_list_pages('title_li='); ?>
		</ul>
	</li>
	
	<li>
		<h2><?php _e('Categories:','tranquility'); ?></h2>
		<ul>
			<?php wp_list_cats(); ?>
		</ul>
	</li>
	
	<li>
		<h2><?php _e('Links','tranquility');?></h2>
		<ul>
			<?php get_links_list(); ?>
		</ul>
	</li>
	

    <li>
				<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</li>
	
	<li>
		<h2><?php _e('Archives:','tranquility'); ?></h2>
		<ul>
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</li>
		
	<li>
		<h2><?php _e('Meta:','tranquility'); ?></h2>
		<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS','tranquility'); ?>"><?php _e('<abbr title="Really Simple Syndication">RSS</abbr>','tranquility'); ?></a></li>
			<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS','tranquility'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>','tranquility'); ?></a></li>
			<li><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional','tranquility'); ?>"><?php _e('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>','tranquility'); ?></a></li>
			<?php wp_meta(); ?>
		</ul>
	</li>
	
	<?php endif; ?>
	
</ul>
<!-- end sidebar -->
