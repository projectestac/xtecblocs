<?php get_header(); ?>

	<div id="content">

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
<div class="entry">
			<div id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','big-blue');?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<small><?php the_time('d F Y') ?> <!-- by <?php the_author() ?> --></small>

				
					<?php the_content( __('Read the rest of this entry &raquo;','big-blue')); ?>


				<p class="postmetadata"><?php _e('Posted in','big-blue');?> <?php the_category(', ') ?> | <?php edit_post_link(__('Edit','big-blue'), '', ' | '); ?>  <?php comments_popup_link(__('No Comments &#187;','big-blue'), __('1 Comment &#187;','big-blue'), __('% Comments &#187;','big-blue')); ?></p>
				</div></div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries','big-blue')) ?></div>
			<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;','big-blue')) ?></div>
		</div>

	<?php else : ?>
<div class="entry">
		<h2><?php _e('Not Found','big-blue');?></h2>
		<?php _e('Sorry, but you are looking for something that isn\'t here.','big-blue');?>
</div>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
