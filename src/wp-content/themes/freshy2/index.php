<?php get_header(); ?>

	<div id="content">
		
	<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post" id="post-<?php the_ID(); ?>">
				
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Read','freshy-2'); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				
				<?php if ($freshy_options['author']) : ?><small class="author"><?php the_author(); ?></small><?php endif; ?>
				<?php if ($freshy_options['date']) : ?>
					<small class="date"><?php if ($freshy_options['author']) : ?>|<?php endif; ?> <?php the_time(get_option('date_format')) ?></small>
				<?php endif; ?>
				<?php if ($freshy_options['time']) : ?>
					<small class="date"><?php if ($freshy_options['date']) : ?>|<?php endif; ?> <?php the_time() ?></small>
				<?php endif; ?>
					
				<div class="entry">
					<?php the_content('<span class="readmore">'.__('Read the rest of this entry &raquo;','freshy-2').'</span>'); ?>
				</div>
				
				<div class="meta">
					<dl>
						<dt><?php _e('Comments','freshy-2'); ?></dt><dd><?php comments_popup_link(__('No Comments &#187;','freshy-2'), __('1 Comment &#187;','freshy-2'), __('% Comments &#187;','freshy-2')); ?></dd>
						<dt><?php _e('Categories','freshy-2'); ?></dt><dd><?php the_category(', ') ?></dd>
					<?php if(function_exists('the_tags')) : ?>
						<?php the_tags('<dt>Tags</dt><dd>', ', ', '</dd>'); ?> 
					<?php endif; ?>
					<?php if(function_exists('the_bunny_tags')) : ?>
						<?php the_bunny_tags('<dt>Tags</dt><dd>', '</dd>', ', '); ?>
					<?php endif; ?>
					<?php if(function_exists('the_bookmark_links')) : ?>
						<dt><?php _e('Spread the word','freshy-2'); ?></dt><dd><?php the_bookmark_links(); ?></dd>
					<?php endif; ?>
					<?php if ('open' == $post-> comment_status) : ?>
						<dt><img alt="<?php _e('Comments rss','freshy-2'); ?>" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/feed-icon-16x16.gif" /> <?php comments_rss_link(__('Comments rss','freshy-2')); ?></dt>
					<?php endif; ?>
					<?php if ('open' == $post->ping_status) : ?>
						<dt><img alt="<?php _e('Trackback','freshy-2'); ?>" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/trackback-icon-16x16.gif" /> <a href="<?php trackback_url(true); ?> " rel="trackback" title="<?php _e('Trackback','freshy-2'); ?>"><?php _e('Trackback','freshy-2'); ?></a></dt>
					<?php endif; ?>
					<?php if ($user_ID) : ?>
						<dt><img alt="<?php _e('Edit','freshy-2'); ?>" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/edit-icon-16x16.gif" /> <?php edit_post_link(__('Edit','freshy-2'),'',''); ?></dt>
					<?php endif; ?>
					</dl>
				</div>
				
			</div>
			
		<?php endwhile; ?>

		<p class="navigation">
			<span class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries','freshy-2')) ?></span>
			<span class="alignright"><?php previous_posts_link(__('Next Entries &raquo;','freshy-2')) ?></span>
		</p>
	
	<?php else : // nothing found ?>
		<div class="post" id="post-none">
			<h2><?php _e('Not found','freshy-2'); ?></h2>
			<p><?php _e("Sorry, but you are looking for something that is not here",'freshy-2'); ?></p>
		</div>
	<?php endif; ?>
	
	</div>
	
	<?php // sidebars ?>
	<?php if ($freshy_options['sidebar_right'] == true) get_sidebar(); ?>
	<?php if ($freshy_options['sidebar_left'] == true) include (TEMPLATEPATH . '/sidebar_left.php'); ?>
	
</div>

<?php get_footer(); ?>