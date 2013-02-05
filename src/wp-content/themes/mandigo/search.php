<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

	<?php if (have_posts()) : ?>

		<h2 class="pagetitle"><?php _e('Search Results','mandigo');?></h2>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link( __('&laquo; Previous Entries','mandigo') )?></div>
			<div class="alignright"><?php previous_posts_link( __('Next Entries &raquo;','mandigo') )?></div>
		</div>


		<?php while (have_posts()) : the_post(); ?>

			<div class="post">
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','mandigo');?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>
				<small><?php the_time('l, F jS, Y') ?></small>

				<p class="postmetadata"><?php _e('Posted in','mandigo');?> <?php the_category(', ') ?> | <?php edit_post_link( __('Edit','mandigo'), '', ' | '); ?>  <?php comments_popup_link( __('No Comments','mandigo').' &#187;', __('1 Comment','mandigo').' &#187;', __('% Comments','mandigo').' &#187;'); ?></p>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link( __('&laquo; Previous Entries','mandigo') )?></div>
			<div class="alignright"><?php previous_posts_link( __('Next Entries &raquo;','mandigo') )?></div>
		</div>

	<?php else : ?>

		<h2 class="center"><?php _e('No posts found. Try a different search?','mandigo');?></h2>
		<p class="center"><?php _e('Sorry, no posts matched your search criteria. Please try and search again.','mandigo');?></p>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
