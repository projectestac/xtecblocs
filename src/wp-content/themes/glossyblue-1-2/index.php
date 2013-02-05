<?php get_header(); ?>
  <div id="content">
  
  <?php if (have_posts()) : ?>
  
  	<?php while (have_posts()) : the_post(); ?>
  
    <div class="post" id="post-<?php the_ID(); ?>">
	  <div class="post-date"><span class="post-month"><?php the_time('M') ?></span> <span class="post-day"><?php the_time('d') ?></span></div>
	  <div class="entry">
        <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','glossy-blue');?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
		<span class="post-cat"><?php the_category(', ') ?></span> <span class="post-comments"><?php comments_popup_link( __('No Comments','glossy-blue').' &#187;', __('1 Comment','glossy-blue').' &#187;', __('% Comments','glossy-blue').' &#187;'); ?></span>
		<div class="post-content">
			<?php the_content( __('Read the rest of this entry &raquo;','glossy-blue') ); ?>
		</div>
	  </div>
	</div>
	
	<?php endwhile; ?>
	
	<div class="navigation">
	  <span class="previous-entries"><?php next_posts_link( __('Previous Entries','glossy-blue') ) ?></span> <span class="next-entries"><?php previous_posts_link( __('Next Entries','glossy-blue') )?></span>
	</div>
	
	<?php else : ?>
	
		<h2 class="center"><?php _e('Not Found','glossy-blue');?></h2>
		<p class="center">S<?php _e('Sorry, but you are looking for something that isn\'t here.','glossy-blue');?></p>
		
  <?php endif; ?>
	
  </div><!--/content -->
  
<?php get_sidebar(); ?>

<?php get_footer(); ?>
