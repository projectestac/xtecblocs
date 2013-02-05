<?php get_header(); ?>
			<div id="content">
				<div id="main">
				<?php if (have_posts()) : ?>
					<?php while (have_posts()) : the_post(); ?>
						<div class="post" id="post-<?php the_ID(); ?>">
						<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
						<span class="post-date" title="Date"><?php the_time('d F Y') ?></span>
						<span class="post-cath" title="Category"><?php the_category(', ') ?></span>
						<span class="post-comm"><?php comments_popup_link(__('No Comments','colors-idea'), __('1 Comment','colors-idea'), __('% Comments','colors-idea')); ?></span>
						<span class="post-edit"><?php edit_post_link(__('Edit','colors-idea'),'',''); ?></span>
						<span class="entry"><?php the_content( __('Read the rest of this entry &raquo;','colors-idea')); ?></span>
						</div>	
						<?php comments_template(); ?>
					<?php endwhile; ?>
					<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries','colors-idea')) ?></div>
					<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;','colors-idea')) ?></div>
				<?php else : ?>
					<h2 align="center"><?php _e('Not Found','colors-idea');?>'</h2>
					<p align="center"><?php _e('Sorry, but you are looking for something that isn\'t here.','colors-idea');?></p>
				<?php endif; ?>	
				</div>	
				<?php get_sidebar(); ?>
			</div>
		</div>
	</div>
</center>
	
<?php get_footer(); ?>

<?php //XTEC ************ ELIMINAT - Standardizing theme ?>
<?php //2011.04.07 @fbassas ?>

<?php /*
</body>
</html>
*/ ?>

<?php //************ FI ?>
