<?php get_header(); ?>

	<div id="content">
	
	<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post" id="post-<?php the_ID(); ?>">
				
				<h2><?php the_title(); ?></h2>

				<?php if ($freshy_options['author']) : ?><small class="author"><?php the_author(); ?></small><?php endif; ?>
				<?php if ($freshy_options['date']) : ?>
					<small class="date"><?php if ($freshy_options['author']) : ?>|<?php endif; ?> <?php the_date() ?></small>
				<?php endif; ?>
				<?php if ($freshy_options['time']) : ?>
					<small class="date"><?php if ($freshy_options['date']) : ?>|<?php endif; ?> <?php the_time() ?></small>
				<?php endif; ?>
					
				<div class="entry">
					<?php the_content('<span class="readmore">'.__('Read the rest of this entry &raquo;','freshy-2').'</span>'); ?>
				</div>
	
				<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
				
			</div>
				
			<div class="meta">
				<dl>
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
					<dt><img alt="<?php _e('Comments rss','freshy-2'); ?>" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/feed-icon-16x16.gif" /></dt><dd><?php comments_rss_link(__('Comments rss','freshy-2')); ?></dd>
				<?php endif; ?>
				<?php if ('open' == $post->ping_status) : ?>
					<dt><img alt="<?php _e('Trackback','freshy-2'); ?>" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/trackback-icon-16x16.gif" /></dt><dd><a href="<?php trackback_url(true); ?> " rel="trackback" title="<?php _e('Trackback','freshy-2'); ?>"><?php _e('Trackback','freshy-2'); ?></a></dd>
				<?php endif; ?>
				<?php if ($user_ID) : ?>
					<dt><img alt="<?php _e('Edit','freshy-2'); ?>" src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/icons/edit-icon-16x16.gif" /></dt><dd><?php edit_post_link(__('Edit','freshy-2'),'',''); ?></dd>
				<?php endif; ?>
				</dl>
			</div>
			
			<p class="navigation">
				<span class="alignleft"><?php previous_post_link('&laquo; %link') ?></span>
				<span class="alignright"><?php next_post_link('%link &raquo;') ?></span>
				<br style="clear:both"/>
			</p>
			
			<?php comments_template(); ?>
						
		<?php endwhile; ?>
	
	<?php else : ?>

		<h2><?php _e('Not Found','freshy-2'); ?></h2>
		<p><?php _e('Sorry, but you are looking for something that isn\'t here.','freshy-2'); ?></p>

	<?php endif; ?>
		
	</div>
		
	<?php // sidebars 
	global $post;
	$ids_right = explode(',',$freshy_options['hide_sidebar_posts']);
	$ids_left = explode(',',$freshy_options['hide_sidebar_left_posts']);

	?>
	<?php if ($freshy_options['sidebar_right'] == true && in_array($post->ID, $ids_right) === FALSE) get_sidebar(); ?>
	<?php if ($freshy_options['sidebar_left'] == true && in_array($post->ID, $ids_left) === FALSE) include (TEMPLATEPATH . '/sidebar_left.php'); ?>

</div>

<?php get_footer(); ?>