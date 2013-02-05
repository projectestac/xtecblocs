<?php get_header(); ?>

<?php get_sidebar(); ?>



  <div id="content">
  
    <div>
	
	</div>
        
    <div class="tabs"></div>
            <!-- begin content --><div id="first-time">
			
<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); ?>

<div class="post" id="post-<?php the_ID(); ?>">

<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','news-portal');?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>



<div class="entry">

<?php the_content( __('Read the rest of this entry','news-portal').' &raquo;'); ?>

</div>

</div>



<?php endwhile; ?>

<p align="center"><?php next_posts_link( __('&laquo; Previous Entries','news-portal') )?> <?php previous_posts_link(__('Next Entries &raquo;','news-portal') )?></p>

<?php else : ?>

<h2 align="center"><?php _e('Not Found','news-portal');?></h2>

<p align="center"><?php _e('Sorry, but you are looking for something that isn\'t here.','news-portal');?></p>

<?php endif; ?>
			
			
	  </div><!-- end content --> 

	  </div>

	  <?php get_footer(); ?>
</div>
</body>
</html>
