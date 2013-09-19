<?php get_header(); ?>

	<div id="content">
		
	<?php if (have_posts()) : ?>
		
		<h2><?php _e('Search results',TEMPLATE_DOMAIN); ?></h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
		
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post" id="post-<?php the_ID(); ?>">
				
				<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Read',TEMPLATE_DOMAIN); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>
				<small class="author"><?php the_author(); ?> |</small>
				<small class="date"><?php the_date(__('j m Y',TEMPLATE_DOMAIN)) ?></small>
				
			</div>
			
		<?php endwhile; ?>

		<p class="navigation">
			<span class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries',TEMPLATE_DOMAIN)) ?></span>
			<span class="alignright"><?php previous_posts_link(__('Next Entries &raquo;',TEMPLATE_DOMAIN)) ?></span>
		</p>
	
	<?php else : // nothing found ?>
		<div class="post" id="post-none">
			<h2><?php _e('Not found',TEMPLATE_DOMAIN); ?></h2>
			<p><?php _e("Sorry, but you are looking for something that is not here",TEMPLATE_DOMAIN); ?></p>
			<?php include (TEMPLATEPATH . '/searchform.php'); ?>
		</div>
	<?php endif; ?>
	
	</div>
	
	<?php // sidebars ?>
	<?php if ($freshy_options['sidebar_right'] == true) get_sidebar(); ?>
	<?php if ($freshy_options['sidebar_left'] == true) include (TEMPLATEPATH . '/sidebar_left.php'); ?>
	
</div>

<?php get_footer(); ?>