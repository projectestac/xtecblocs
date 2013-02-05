<?php get_header(); ?>
  <div id="content">
  
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  
    <div class="post" id="post-<?php the_ID(); ?>">
        <h2><a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link','glossy-blue');?>: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
		<span class="post-cat"><?php the_category(', ') ?></span> <span class="post-calendar"><?php the_time('F jS, Y') ?></span>
		<div class="post-content">
		<?php the_content('<p class="serif">'. __('Read the rest of this entry &raquo;','glossy-blue').'</p>'); ?>
		
		<?php link_pages('<p><strong>'. __('Pages','glossy-blue') .':</strong> ', '</p>', 'number'); ?>
		
		<?php edit_post_link( __('Edit','glossy-blue'), '', ''); ?>
		
		</div>
		
		<?php comments_template(); ?>
		
			<?php endwhile; else: ?>

		<p><?php _e('Sorry, no posts matched your criteria.','glossy-blue');?></p>

<?php endif; ?>

	</div><!--/post -->

  </div><!--/content -->

<?php get_sidebar(); ?>
  
<?php get_footer(); ?>

