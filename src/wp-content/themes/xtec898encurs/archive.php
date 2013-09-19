<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

		<?php if (have_posts()) : ?>
		<div class="pageheader">
		 <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
<?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for the','encurs');?> &#8216;<?php single_cat_title(); ?>&#8217; <?php _e('Category','encurs');?></h2>

 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for','encurs');?> <?php the_time('j/M/y'); ?></h2>

	 <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for','encurs');?> <?php the_time('M/Y') ?></h2>

		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle"><?php _e('Archive for','encurs');?> <?php the_time('Y'); ?></h2>

	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle"><?php _e('Author Archive','encurs');?></h2>

		<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="pagetitle"><?php _e('Blog Archives','encurs');?></h2>

		<?php } ?>


		<div class="navigation">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries','encurs')) ?></div>
			<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;','encurs')) ?></div>
		</div>
		
		</div>
		
		<?php while (have_posts()) : the_post(); ?>
		<div class="post">
				<div class="post_date" title="<?php the_time('l d, F Y')?>">
					<span class="date_day"><?php the_time('d')?></span>
					<span class="date_month"><?php the_time('m')?></span>
					<span class="date_year"><?php the_time('Y')?></span>
				</div>
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','encurs');?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>
				
				<div class="entry">
					<?php the_content() ?>
				</div>

				<p class="postmetadata"><span class="post_edit"><?php edit_post_link(__('Edit'), '', ''); ?> </span>
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
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
