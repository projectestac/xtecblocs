<?php
get_header();
?>

<div id="content">
  <?php if (have_posts()) : ?>
  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
  <?php 
	if (is_category()) { ?>
  <h2 class='archives'><?php _e('Archive for the','light');?><?php echo single_cat_title(); ?>' <?php _e('Category','light');?></h2>
  <?php }
	 
	elseif (is_day()) { ?>
  <h2 class='archives'><?php _e('Archive for','light');?>
    <?php the_time('F jS, Y'); ?>
  </h2>
  <?php }
	
	elseif (is_month()) { ?>
  <h2 class='archives'><?php _e('Archive for','light');?>
    <?php the_time('F, Y'); ?>
  </h2>
  <?php } 
	
	elseif (is_year()) { ?>
  <h2 class='archives'><?php _e('Archive for','light');?>
    <?php the_time('Y'); ?>
  </h2>
  <?php } 
	
	elseif (is_author()) { ?>
  <h2 class='archives'><?php _e('Author Archive','light');?></h2>
  <?php }
	 
	elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
    <h2><?php _e('Blog Archives','light');?></h2>
    <?php } ?>
  <?php while (have_posts()) : the_post(); ?>
  <div class="entry">
    <h3 class="entrytitle" id="post-<?php the_ID(); ?>"> <a href="<?php the_permalink() ?>" rel="bookmark">
      <?php the_title(); ?>
      </a> </h3>
    <div class="entrymeta">
      <?php _e('Posted');?> <?php 
			the_time('F j, Y ');
			$comments_img_link= '<img src="' . get_stylesheet_directory_uri() . '/images/comments.gif"  title="comments" alt="*" /><strong>';
			comments_popup_link($comments_img_link .' '.__('Comments (0)','light') , $comments_img_link .' '. __('Comments (1)','light'), $comments_img_link . ' '. __('Comments (%)','light') ); edit_post_link( __('Edit','light'));?>
      </strong> </div>
    <div class="entrybody">
      <?php the_content(__('Read more &raquo;','light'));?>
<div class="sociable"><?php if(function_exists('wp_email')) { email_link(); } ?>
</div>
    </div>
    <!--
	<?php trackback_rdf(); ?>
	-->
  </div>
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
