<?php get_header(); ?>

<div id="content"> 

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="entry">
		<div class="post" id="post-<?php the_ID(); ?>">
			<h2><a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link','big-blue');?>: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
<small><?php the_time('d F Y') ?> | <?php ('by');?> <?php the_author() ?> | <?php if(function_exists('the_views')) { the_views(); } ?></small>
		
				<?php the_content('<p class="serif">'.__('Read the rest of this entry &raquo;','big-blue').'</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>'. __('Pages','big-blue').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

				

			</div>
		</div>

<div class="entry">
	<?php comments_template(); ?>
	</div>

	<?php endwhile; else: ?>
<div class="entry">
		<p><?php _e('Sorry, no posts matched your criteria.','big-blue');?></p>
</div>
<?php endif; ?>

	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
