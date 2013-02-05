<?php get_header(); ?>

	<div class="narrowcolumnwrapper"><div class="narrowcolumn">

		<div class="content">

			<?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">

				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>

				<div class="postinfo">
<?php _e('Posted on', 'digg-3'); ?> <span class="postdate"><?php the_time('F jS, Y') ?></span> <?php _e('by', 'digg-3'); ?> <?php the_author() ?> <?php edit_post_link(__('Edit', 'digg-3'), ' &#124; ', ''); ?>
				</div>

				<div class="entry">

					<?php the_content(__('Read more &raquo;', 'digg-3') ); ?>

					<p class="postinfo">
<?php _e('Filed under&#58;', 'digg-3'); ?> <?php the_category(', ') ?> &#124; <?php comments_popup_link(__('No Comments', 'digg-3').' &#187;', __('1 Comment', 'digg-3').' &#187;', __('% Comments', 'digg-3').' &#187;'); ?>
					</p>

					<!-- 
					<?php trackback_rdf(); ?>
					 -->
				</div>
			</div>

<?php endwhile; ?>

<?php include (TEMPLATEPATH . '/browse.php'); ?>

<?php else : ?>

			<div class="post">

				<h2><?php _e('Not Found', 'digg-3'); ?></h2>

				<div class="entry">
<p><?php _e('Sorry, but you are looking for something that isn&#39;t here.', 'digg-3'); ?></p>
				</div>

			</div>

<?php endif; ?>

		</div><!-- End content -->

	</div></div><!-- End narrowcolumnwrapper and narrowcolumn classes -->

<?php get_footer(); ?>
