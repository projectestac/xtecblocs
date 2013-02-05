<?php get_header(); ?>

	<div id="content">

	<?php if (have_posts()) : ?>

		<h2 class="pagetitle"><?php _e('Search Results','tranquility');?></h2>

		<?php while (have_posts()) : the_post(); ?>

			<div class="entry">
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to');?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>
				<small><?php the_time('d F Y') ?></small>
<br /><br /><?php the_content_rss('', TRUE, '', 50); ?><br /><br />
				<p class="postmetadata"><?php _e('Posted in','tranquility');?> <?php the_category(', ') ?> | <?php edit_post_link(__('Edit','tranquility'), '', ' | '); ?>  <?php comments_popup_link(__('No Comments &#187;','tranquility'), __('1 Comment &#187;','tranquility'), __('% Comments &#187;','tranquility')); ?></p>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries','tranquility')) ?></div>
			<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;','tranquility')) ?></div>
		</div>

	<?php else : ?>
	<div class="entry">
		<h2 class="center"><?php _e('No posts found. Try a different search?','tranquility');?></h2>

</div>
	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>