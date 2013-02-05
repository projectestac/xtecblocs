<?php
get_header();
?>
<div id="content">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <div class="entry">
    <h3 class="entrytitle" id="post-<?php the_ID(); ?>"> <a href="<?php the_permalink() ?>" rel="bookmark">
      <?php the_title(); ?>
      </a> </h3>
    <div class="entrymeta">
   <?php _e('Posted','light');?> <?php 
			the_time('F j, Y ');
			$comments_img_link= '<img src="' . get_stylesheet_directory_uri() . '/images/comments.gif"  title="comments" alt="*" /><strong>';
			comments_popup_link($comments_img_link .' '. __('Comments (0)','light') .'', $comments_img_link .' '. __('Comments (1)','light') .'', $comments_img_link . ' '. __('Comments (%)','light') ); 
			edit_post_link(__('Edit','light'));?>
      </strong> </div>
	  
    <div class="entrybody">
      <?php the_content(__('Read more &raquo;','light'));?>

<div class="sociable"><?php if(function_exists('wp_email')) { email_link(); } ?>
</div>
    </div>
	<?php trackback_rdf(); ?>

  </div>
  <?php comments_template(); // Get wp-comments.php template ?>
  <?php endwhile; else: ?>
  <p>
    <?php _e('Sorry, no posts matched your criteria.','light'); ?>
  </p>
  <?php endif; ?>
  <p>
    <?php posts_nav_link(' &#8212; ', __('&laquo; Previous Page','light'), __('Next Page &raquo;','light')); ?>
  </p>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
