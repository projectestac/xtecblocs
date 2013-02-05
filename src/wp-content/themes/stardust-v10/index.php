<?php get_header(); ?>

<hr />

<div id="wrapper">
<div id="content">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<?php 
     $time = get_the_time(_c('M d Y|Dates','stardust'));
     list($mo, $da, $ye) = explode(" ", $time);
	?>
  <div class="date" title="<?php the_time(_c('d-m-Y|Dates','stardust')); ?>">
    <p>
         <span class="mese"><?php echo($mo); ?></span>
         <span class="giorno"><?php echo($da); ?></span>
         <span class="anno"><?php echo($ye); ?></span>
    </p>
    </div>
	 <h2 class="storytitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<div class="meta"><span class="tags"><?php _e("Category:"); ?> <?php the_category(',') ?></span> &#8212; <span class="user"><?php the_author() ?> @ <?php the_time() ?></span> <?php edit_post_link(__('Edit This')); ?></div>

	<div class="storycontent">
		<?php the_content(__('Continue reading ','stardust') . the_title('"', '"', false)); ?>

		<?php if (function_exists('the_tags')): ?>
    <p class="meta"><?php the_tags(__('Tags: ').' ',', '); ?></p>
		<?php endif;?>
	</div>
	<div class="feedback">
		<?php wp_link_pages(); ?>
		<p><?php comments_popup_link(__('Comments (0)'), __('Comments (1)'), __('Comments (%)')); ?></p>
	</div>

</div>
<hr />
<?php comments_template(); // Get wp-comments.php template ?>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>

<?php posts_nav_link(' &#8212; ', __('&laquo; Previous Page'), __('Next Page &raquo;')); ?>

<?php get_footer(); ?>
