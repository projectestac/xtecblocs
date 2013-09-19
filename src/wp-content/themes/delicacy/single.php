<?php get_header(); ?>
	<!-- CONTENT -->
	<div id="content">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
    <article id="post-<?php the_ID(); ?>" <?php post_class('one-post'); ?>>
	<h1 class="post-title"><?php the_title(); ?></h1>
	<div class="entry-meta">
		<span class="cat"><?php the_category(', ') ?></span><span class="date"><?php the_time( get_option('date_format') ); ?></span><span class="comments"><?php comments_popup_link(__('No comments','delicacy'), __('1 comment','delicacy'), __('Comments: %','delicacy')); ?></span>
	</div>
	<div class="entry-content">
    	<?php the_content(); ?>
		<div class="clear"></div>
		<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'delicacy' ) . '</span>', 'after' => '</div>' ) ); ?>
		<p><?php the_tags( __( 'Tagged: ', 'delicacy' ), ', ', ''); ?></p>
    	<?php edit_post_link('Edit this entry','<p>', '</p>'); ?>
	</div>
	</article>

	<div class="deco-line"></div>
			
	<?php comments_template(); ?>


	<?php endwhile; endif; ?>
	
	</div><!-- end #content -->
	<?php get_sidebar(); ?>
</div><!-- end #content-wrapper -->

<?php get_footer(); ?>