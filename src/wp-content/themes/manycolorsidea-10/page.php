<?php get_header(); ?>
<div id="content">
	<div id="main">
	<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>

<div class="entry">
<?php the_content(__('Read the rest of this entry &raquo;','colors-idea')); ?>
</div>
</div>
<?php comments_template(); ?>
<?php endwhile; ?>
<p align="center"><?php next_posts_link(__('&laquo; Previous Entries','colors-idea')) ?> <?php previous_posts_link(__('Next Entries &raquo;','colors-idea')) ?></p>
<?php else : ?>
<h2 align="center"><?php _e('Not Found','colors-idea');?>'</h2>
<p align="center"><?php _e('Sorry, but you are looking for something that isn\'t here.','colors-idea');?></p>
	<?php endif; ?>
	</div>
	
<?php get_sidebar(); ?>

</div>
<?php get_footer(); ?>

</body>
</html>
