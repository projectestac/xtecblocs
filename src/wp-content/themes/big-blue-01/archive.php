<?php get_header(); ?>

	<div id="content">

		<?php if (have_posts()) : ?>

		 <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
<?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for the','big-blue');?> &#8216;<?php single_cat_title(); ?>&#8217; <?php _e('Category','big-blue');?></h2>

 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for','big-blue'); ?> <?php the_time('d F Y'); ?></h2>

	 <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for','big-blue');?> <?php the_time('F Y'); ?></h2>

		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for','big-blue');?> <?php the_time('Y'); ?></h2>

	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle"><?php _e('Author Archive','big-blue');?></h2>

		<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="pagetitle"><?php _e('Blog Archives','big-blue');?></h2>

		<?php } ?>


						
		<?php while (have_posts()) : the_post(); ?>
		<div class="entry">
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
				<small><?php the_time('d F Y') ?> <?php if(function_exists('the_views')) { the_views(); } ?></small>


					<br /><br /><?php the_content_rss('', TRUE, '', 50); ?><br /><br />


				<p class="postmetadata"><?php _e('Posted in','big-blue');?> <?php the_category(', ') ?> | <?php edit_post_link(__('Edit','big-blue'), '', ' | '); ?>  <?php comments_popup_link(__('No Comments &#187;','big-blue'), __('1 Comment &#187;','big-blue'), __('% Comments &#187;','big-blue')); ?></p>

			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries','big-blue')) ?></div>
			<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;','big-blue')) ?></div>
		</div>

	<?php else : ?>
				<div class="entry">
		<h2 class="center"><?php _e('Not Found','big-blue');?></h2>
</div>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
