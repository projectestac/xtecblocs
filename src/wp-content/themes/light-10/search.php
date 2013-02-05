<?php
get_header();
?>

<div id="content">	
<h2 class="archives"><?php _e('Search Results','light');?></h2>

  <?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
  	<div class="entry">
    <p><strong> <a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></strong> </p>
	  <div class="entrymeta">
		<?php the_time('F dS, Y ');?>
	</div>
  </div>
  <?php endwhile; else: ?>
  <p><?php _e('Sorry, no posts matched your criteria.','light'); ?></p>

  <?php endif; ?>
  <p><?php posts_nav_link(' &#8212; ', __('&laquo; Previous Page','light'), __('Next Page &raquo;','light')); ?></p>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
