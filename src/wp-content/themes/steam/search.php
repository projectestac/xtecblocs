<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

	<?php if (have_posts()) : ?>

		<h2 class="pagetitle">Search Results</h2>
		
		<div class="navigation">
			<div class="alignleft"><?php posts_nav_link('','',__('&laquo; Previous Entries','steam')) ?></div>
			<div class="alignright"><?php posts_nav_link('',__('Next Entries &raquo;','steam'),'') ?></div>
		</div>


		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post">
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to','steam');?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>
				<small><?php the_time('d F Y') ?></small>
				
				<div class="entry">
					<?php the_excerpt() ?>
				</div>
		
				<p class="postmetadata"><?php _e('Posted in','steam');?> <?php the_category(', ') ?> <strong>|</strong> <?php edit_post_link(__('Edit','steam'),'','<strong>|</strong>'); ?>  <?php comments_popup_link(__('No Comments','steam').' &#187;', __('1 Comment','steam').' &#187;', __('% Comments','steam').' &#187;'); ?></p> 
				
				<!--
				<?php trackback_rdf(); ?>
				-->
			</div>
	
		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php posts_nav_link('','',__('&laquo; Previous Entries','steam')) ?></div>
			<div class="alignright"><?php posts_nav_link('',__('Next Entries &raquo;','steam'),'') ?></div>
		</div>
	
	<?php else : ?>

		<h2 class="center"><?php _e('Not Found','steam');?></h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>
		
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
