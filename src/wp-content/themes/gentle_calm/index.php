<?php 
get_header();
?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="entry">
			<h2 class="entrydate"><?php the_date() ?></h2>
			<h3 class="entrytitle" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
			<div class="entrybody">
				<?php the_content(__('(more...)','gentle-calm')); ?>
				<p class="comments_link">
					<img src="<?php bloginfo('stylesheet_directory'); ?>/images/file.gif" title="file" alt="*" /> 
					<?php _e("Filed by",'gentle-calm'); ?> <?php the_author();?> <?php _e("at",'gentle-calm');?> <?php the_time() ?> <?php _e("under",'gentle-calm');?> <?php the_category(',');?><br/>
					<?php 
						$comments_img_link = '<img src="' . get_stylesheet_directory_uri() . '/images/comments.gif"  title="comments" alt="*" />';
						comments_popup_link(__('No Comments','gentle-calm'), $comments_img_link .' '. __('1 Comment','gentle-calm'), $comments_img_link .' '. __('% Comments','gentle-calm')); 
					?>
				</p>
			</div>
	<!--
	<?php trackback_rdf(); ?>
	-->
</div>

<?php comments_template(); // Get wp-comments.php template ?>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.','gentle-calm'); ?></p>
<?php endif; ?>

<?php posts_nav_link(' &#8212; ', __('&laquo; Previous Page','gentle-calm'), __('Next Page &raquo;','gentle-calm')); ?>
</div>
</div><!-- The main content column ends  -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
