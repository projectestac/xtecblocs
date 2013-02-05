<?php get_header(); ?>
  <div id="content">
  
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
    <div class="post" id="post-<?php the_ID(); ?>">
        <h2><?php the_title(); ?></h2>
        <div class="post-content">
			<?php the_content('<p class="serif">'. __('Read the rest of this page &raquo;','glossy-blue').'</p>'); ?>

			<?php link_pages('<p><strong>'. __('Pages','glossy-blue') .':</strong> ', '</p>', 'number'); ?>
        </div>
    </div>
	
	<?php comments_template(); ?>
	
  <?php endwhile; endif; ?>

  </div><!--/content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
