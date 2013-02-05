<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
		<h2><?php the_title(); ?></h2>
			<div class="entry">
				<?php the_content('<p class="serif">'. __('Read the rest of this page','encurs').' &raquo;</p>'); ?>
				 <?php /*  the_meta();  */ ?>

				<?php wp_link_pages(array('before' => '<p><strong>'. __('Pages:','encurs').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

			</div>
			<?php edit_post_link(__('Edit this entry.','encurs'), '', ''); ?>
		</div>
		<?php endwhile; endif; ?>
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
