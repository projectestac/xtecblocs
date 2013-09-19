<?php get_header(); ?>

    <div id="content">

	<?php if (have_posts()) : ?>

		<h3 class="block-title"><?php _e('Search results for', 'delicacy'); ?> "<?php echo get_search_query(); ?>"</h3>

		<div class="post-list">

		<?php while (have_posts()) : the_post(); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class('list-post-item'); ?>>
            <?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail(); ?></a>
			<?php } ?>
			<h2><a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<div class="entry-meta"><span class="cat"><?php the_category(', ') ?></span><span class="date"><?php the_time( get_option('date_format') ); ?></span><span class="comments"><?php comments_popup_link(__('No comments','delicacy'), __('1 comment','delicacy'), __('Comments: %','delicacy')); ?></span></div>
			<?php the_excerpt(); ?>
		</div>
		<div class="deco-line"></div>

		<?php endwhile; ?>

            <?php
                if(function_exists('wp_pagenavi')) :
                    wp_pagenavi(); 
                else :
            ?>
                <div class="wp-pagenavi">
                    <div class="alignleft"><?php next_posts_link('&laquo; '.__('Previous Entries','delicacy')) ?></div> 
                    <div class="alignright"><?php previous_posts_link(__('Next entries','delicacy').' &raquo;') ?></div>
                </div>
            <?php endif; ?>

		</div><!-- end #post-list -->

	<?php else : ?>
    
		<h3 class="block-title"><?php _e('Not found', 'delicacy') ?></h3>
		<p><?php _e('Sorry, but no posts were found <br />Try another search...', 'delicacy') ?></p>
		<?php get_search_form(); ?>

	<?php endif; ?>

	</div><!-- end #content -->
	
<?php get_sidebar(); ?>

</div><!-- end #content-wrapper -->

<?php get_footer(); ?>
