<?php get_header(); ?>

	<?php if (have_posts()) : ?>
	
		<?php $post = $posts[0]; // Thanks Kubrick for this code ?>
		
		<?php if (is_category()) { ?>				
		<h2><?php _e('Archive for','almost-spring'); echo single_cat_title(); ?></h2>
		
 	  	<?php } elseif (is_day()) { ?>
		<h2><?php _e('Archive for','almost-spring'); the_time('F j, Y'); ?></h2>
		
	 	<?php } elseif (is_month()) { ?>
		<h2><?php _e('Archive for','almost-spring'); the_time('F, Y'); ?></h2>

		<?php } elseif (is_year()) { ?>
		<h2><?php _e('Archive for','almost-spring'); the_time('Y'); ?></h2>

		<?php } elseif (is_author()) { ?>
		<h2><?php _e('Author Archive','almost-spring'); ?></h2>

		<?php } ?>
		
		<?php while (have_posts()) : the_post(); ?>
		
			<div class="post">
	
				<h2 class="posttitle" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent link to','almost-spring'); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
			
				<p class="postmeta"> 
				<?php the_time('F j, Y') ?> @ <?php the_time() ?> 
				&#183; <?php _e('Filed under','almost-spring'); ?> <?php the_category(', ') ?>
				<?php edit_post_link(__('Edit','almost-spring'), ' &#183; ', ''); ?>
				</p>
			
				<div class="postentry">
				<?php the_content("<p>".__('Read the rest of this entry &raquo;','almost-spring')."</p>"); ?>
				</div>
			
				<p class="postfeedback">
				<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent link to','almost-spring'); ?> <?php the_title(); ?>" class="permalink"><?php _e('Permalink','almost-spring'); ?></a>
				<?php comments_popup_link(__('Comments','almost-spring'), __('Comments (1)','almost-spring'), __('Comments (%)','almost-spring'), 'commentslink', __('Comments off','almost-spring')); ?>
				</p>
				
				<!--
				<?php trackback_rdf(); ?>
				-->
			
			</div>
				
		<?php endwhile; ?>

		<?php posts_nav_link(' &#183; ', __('Next entries &raquo;','almost-spring'), __('&laquo; Previous entries','almost-spring')); ?>
		
	<?php else : ?>

		<h2><?php _e('Not Found','almost-spring'); ?></h2>

		<p><?php _e('Sorry, but the page you requested cannot be found.','almost-spring'); ?></p>
		
		<h3><?php _e('Search','almost-spring'); ?></h3>
		
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
