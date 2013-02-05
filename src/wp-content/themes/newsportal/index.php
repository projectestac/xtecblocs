<?php get_header(); ?>

<?php get_sidebar(); ?>



  <div id="content">
  
 
        

  
  <!-- begin content --><div id="first-time">
			
<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); ?>

<div class="post" id="post-<?php the_ID(); ?>">

<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','news-portal');?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>

<?php the_time('d F Y') ?> <?php _e('by','news-portal');?> <?php the_author() ?>

<div class="entry">

<?php the_content( __('Read the rest of this entry &raquo;','news-portal') ); ?>

</div>

<p class="info"><?php _e('Posted in','news-portal');?> <?php the_category(', ') ?> <strong>|</strong> <?php edit_post_link( __('Edit','news-portal'),'','<strong>|</strong>'); ?> <?php comments_popup_link( __('No Comments','news-portal').' &raquo;', __('1 Comment','news-portal').' &raquo;', __('% Comments','news-portal').' &raquo;'); ?></p>

</div>

<?php comments_template(); ?>

<?php endwhile; ?>

	<div class="nav">
		<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries','news-portal')) ?></div>
		<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;','news-portal')) ?></div>
	</div>

<?php else : ?>

<h2 align="center"><?php _e('Not Found','news-portal');?></h2>

<p align="center"><?php _e('Sorry, but you are looking for something that isn\'t here.','news-portal');?></p>

<?php endif; ?>
			
			
	  </div><!-- end content --> 

	  </div>

	  <?php get_footer(); ?>
</div>

<?php //XTEC ************ ELIMINAT - Standardizing theme ?>
<?php //2011.04.07 @fbassas ?>

<?php /*
</body>
</html>
*/ ?>

<?php //************ FI ?>
