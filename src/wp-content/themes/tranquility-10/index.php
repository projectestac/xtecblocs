<?php 
get_header();
?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<!--<?php //the_date('','<h2>','</h2>'); ?>-->
	
<div class="post" id="post-<?php the_ID(); ?>">
	<h3 class="storytitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
	<div class="meta"><?php the_author() ?> | <?php the_category(',') ?> | <?php the_time('d F Y') ?> <?php edit_post_link(__('edit','tranquility')); ?></div>
	
	<div class="storycontent">
		<?php the_content(__('(more...)','tranquility')); ?>
	</div>
	
	<div class="feedback">
            <?php wp_link_pages(); ?>
            <?php comments_popup_link(__('Comments (0)','tranquility'), __('Comments (1)','tranquility'), __('Comments (%)','tranquility')); ?>
	</div>

</div>

<?php comments_template(); // Get wp-comments.php template ?>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.','tranquility'); ?></p>
<?php endif; ?>

<?php posts_nav_link(' | ', __('&laquo; Previous Page','tranquility'), __('Next Page &raquo;','tranquility')); ?>

<?php get_footer(); ?>
