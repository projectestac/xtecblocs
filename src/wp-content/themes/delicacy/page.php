<?php get_header(); ?>
	<div id="content">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
		<div <?php post_class('one-post'); ?> id="post-<?php the_ID(); ?>">

			<h1 class="post-title"><?php the_title(); ?></h1>

			<div class="entry-meta"><span class="date"><?php the_time( get_option('date_format') ); ?></span><span class="comments"><?php comments_popup_link(__('No comments','delicacy'), __('1 comment','delicacy'), __('Comments: %','delicacy')); ?></span></div>
			<div class="entry-content">
				<?php the_content(); ?>

				<?php wp_link_pages(array('before' => __( 'Pages: ', 'delicacy' ), 'next_or_number' => 'number')); ?>


			<?php edit_post_link(__( 'Edit this entry', 'delicacy' ), '<p>', '</p>'); ?>
			</div>
		</div>

		<div class="deco-line"></div>

		<?php comments_template(); ?>

		<?php endwhile; endif; ?>

	</div><!-- end #content -->


	<?php get_sidebar(); ?>

	</div><!-- end #content-wrapper -->

<?php get_footer(); ?>
