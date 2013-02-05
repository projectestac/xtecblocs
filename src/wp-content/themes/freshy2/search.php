<?php get_header(); ?>

	<div id="content">
		
	<?php if (have_posts()) : ?>
		
		<h2><?php _e('Search results','freshy-2'); ?></h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
		
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post" id="post-<?php the_ID(); ?>">
				
				<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Read','freshy-2'); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>
				<small class="author"><?php the_author(); ?> |</small>
				<small class="date"><?php the_date(__('j m Y','freshy-2')) ?></small>
				
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
			<?php include (TEMPLATEPATH . '/searchform.php'); ?>
		</div>
	<?php endif; ?>
	
	</div>
	
	<?php // sidebars ?>
	<?php if ($freshy_options['sidebar_right'] == true) get_sidebar(); ?>
	<?php if ($freshy_options['sidebar_left'] == true) include (TEMPLATEPATH . '/sidebar_left.php'); ?>
	
</div>

<?php get_footer(); ?>