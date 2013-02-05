<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

	<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post">
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','steam');?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<small><?php the_time('d F Y') ?> <!-- by <?php the_author() ?> --></small>
				
				<div class="entry">
					<?php the_content(__('Read the rest of this entry &raquo;','steam')); ?>
				</div>
		
				<p class="postmetadata"><?php _e('Posted in','steam');?> <?php the_category(', ') ?> <strong>|</strong> <?php edit_post_link(__('Edit','steam'),'','<strong> |</strong>'); ?>  <?php comments_popup_link(__('No Comments','steam').' &#187;', __('1 Comment','steam').' &#187;', __('% Comments','steam').' &#187;'); ?></p> 
				
				<!--
				<?php trackback_rdf(); ?>
				-->
			</div>
	
		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries','steam')) ?></div>
			<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;','steam')) ?></div>
		</div>
		
	<?php else : ?>

		<h2 class="center"><?php _e('Not Found','steam');?></h2>
		<p class="center"><?php _e("Sorry, but you are looking for something that isn't here.",'steam'); ?></p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
