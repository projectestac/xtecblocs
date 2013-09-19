<?php get_header(); ?>

	<div id="content">
	
	<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><?php the_title(); ?></h2>
				
				<div class="entry">
					<?php the_content('<span class="readmore">'.__('Read the rest of this entry &raquo;',TEMPLATE_DOMAIN).'</span>'); ?>
				</div>
			</div>
			
			<div class="meta">
				<dl>
				<?php if(function_exists('the_tags')) : ?>
					<?php the_tags('<dt>Tags</dt><dd>', ', ', '</dd>'); ?> 
				<?php endif; ?>
				<?php if(function_exists('the_bunny_tags')) : ?>
					<?php the_bunny_tags('<dt>Tags</dt><dd>', '</dd>', ', '); ?>
				<?php endif; ?>
				<?php if(function_exists('the_bookmark_links')) : ?>
					<dt><?php _e('Spread the word',TEMPLATE_DOMAIN); ?></dt><dd><?php the_bookmark_links(); ?></dd>
				<?php endif; ?>
				<?php if ('open' == $post-> comment_status) : ?>
					<dt><img alt="<?php _e('Comments rss',TEMPLATE_DOMAIN); ?>" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/feed-icon-16x16.gif" /></dt><dd><?php comments_rss_link(__('Comments rss',TEMPLATE_DOMAIN)); ?></dd>
				<?php endif; ?>
				<?php if ('open' == $post->ping_status) : ?>
					<dt><img alt="<?php _e('Trackback',TEMPLATE_DOMAIN); ?>" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/trackback-icon-16x16.gif" /></dt><dd><a href="<?php trackback_url(true); ?> " rel="trackback" title="<?php _e('Trackback',TEMPLATE_DOMAIN); ?>"><?php _e('Trackback',TEMPLATE_DOMAIN); ?></a></dd>
				<?php endif; ?>
				<?php if ($user_ID) : ?>
					<dt><img alt="<?php _e('Edit',TEMPLATE_DOMAIN); ?>" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/edit-icon-16x16.gif" /></dt><dd><?php edit_post_link(__('Edit',TEMPLATE_DOMAIN),'',''); ?></dd>
				<?php endif; ?>
				</dl>
			</div>
			
		<?php comments_template(); ?>
			
		<?php endwhile; ?>
	
	<?php else : ?>

		<h2><?php _e('Not Found',TEMPLATE_DOMAIN); ?></h2>
		<p><?php _e('Sorry, but you are looking for something that isn\'t here.',TEMPLATE_DOMAIN); ?></p>

	<?php endif; ?>
		
	</div>
		
	<?php // sidebars ?>
	<?php if ($freshy_options['sidebar_right'] == true) get_sidebar(); ?>
	<?php if ($freshy_options['sidebar_left'] == true) include (TEMPLATEPATH . '/sidebar_left.php'); ?>

</div>

<?php get_footer(); ?>