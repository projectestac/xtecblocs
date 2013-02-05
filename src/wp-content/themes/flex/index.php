<?php 
get_header();
?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="entry">
			<!--
				<h2 class="entrydate"><?php the_date() ?></h2>
			-->
			<h3 class="entrytitle" id="post-<?php the_ID(); ?>">
				<a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a>
			</h3>
			<div class="entrybody">
				<div class="entrymeta">
					<?php _e('Posted by', 'flex');?> <?php the_author() ?> <?php _e('on', 'flex');?> <?php the_time('d F Y') ?> <?php _e('to', 'flex');?> <?php the_category(',') ?>
				</div>
				<?php the_content( __('(more...)', 'flex') ); ?>
				<p class="comments_link">
					<?php 
						$comments_img_link = '<img src="' . get_stylesheet_directory_uri() . '/images/comments.gif"  title="comments" alt="*" />';
				comments_popup_link(__('No Comments', 'flex'), $comments_img_link .' '.__('1 Comment', 'flex'), $comments_img_link .' '.__('% Comments', 'flex') );?>
				</p>
			</div>
	<!--
	<?php trackback_rdf(); ?>
	-->
</div>

<?php comments_template(); // Get wp-comments.php template ?>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>

		<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries', 'flex')) ?></div>
		<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;', 'flex')) ?></div>

</div>
</div><!-- The main content column ends  -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
