<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<div class="post_date" title="<?php the_time('l d, F Y')?>">
					<span class="date_day"><?php the_time('d')?></span>
					<span class="date_month"><?php the_time('m')?></span>
					<span class="date_year"><?php the_time('Y')?></span>
				</div>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','encurs');?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>

				<div class="entry">
					<?php the_content(__('Read the rest of this entry &raquo;','encurs')); ?>
					<?php /* the_meta(); */ ?>
				</div>

				<p class="postmetadata"><span class="post_edit"><?php edit_post_link(__('Edit','encurs'), '', ''); ?> </span>
				<? _e('Categories','encurs'); ?>: <?php the_category(', ') ?><br />
				<? _e('Comments','encurs'); ?>: <?php comments_popup_link(__('No Comments','encurs').' &#187;', __('1 Comment','encurs').' &#187;', __('% Comments','encurs').' &#187;'); ?><br />
				<? _e('Author','encurs'); ?>: <strong><?php the_author() ?></strong>
				</p>

			</div>

		<?php endwhile; ?>
		<div class="pagefooter">
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries','encurs')) ?></div>
			<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;','encurs')) ?></div>
		</div>
	</div>
	<?php else : ?>

		<h2 class="center"><?php _e('Not Found','encurs');?></h2>
		<p class="center"><?php _e('Sorry, but you are looking for something that isn\'t here.','encurs');?></p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
