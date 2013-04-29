<?php get_header(); ?>

		<div id="content">
		<?php if (have_posts()) : ?>

 			<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
				<h3 class="block-title">
				<?php if ( is_day() ) : ?>
					<?php printf( __( 'Daily Archives: %s', 'delicacy' ), '<span>' . get_the_date() . '</span>' ); ?>
				<?php elseif ( is_month() ) : ?>
					<?php printf( __( 'Monthly Archives: %s', 'delicacy' ), '<span>' . get_the_date( 'F Y' ) . '</span>' ); ?>
				<?php elseif ( is_year() ) : ?>
					<?php printf( __( 'Yearly Archives: %s', 'delicacy' ), '<span>' . get_the_date( 'Y' ) . '</span>' ); ?>
				<?php elseif ( is_category() )  : ?>
					<?php _e( 'Blog Archives for category', 'delicacy' ) ?> <?php single_cat_title();  ?>
				<?php elseif ( is_tag() ) : ?>
					<?php _e( 'Blog Archives for tag', 'delicacy' ) ?> <?php single_tag_title(); ?>
				<?php else : ?>
					<?php _e( 'Blog Archives', 'delicacy' ); ?>
				<?php endif; ?>
				</h3>
        <div class="post-list">

			<?php while (have_posts()) : the_post(); ?>
			
			<div id="post-<?php the_ID(); ?>" <?php post_class('list-post-item'); ?>>

				<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
				<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail(); ?></a>
				<?php } ?>
				<h2><a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<div class="entry-meta">
                    <span class="date"><?php the_time( get_option('date_format') ); ?></span>
                    <span class="comments"><?php comments_popup_link(__('No comments','delicacy'), __('1 comment','delicacy'), __('Comments: %','delicacy')); ?></span>
                    <?php if(get_the_title() == '') : ?>
                        <a href="<?php the_permalink(); ?>" class="thepermalink" title="<?php _e( 'Permalink', 'delicacy' ); ?>"><?php _e( 'Permalink', 'delicacy' ); ?></a>
                    <?php endif; ?>
                </div>

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
        
        <?php else : ?>

		<h3 class="block-title"><?php _e('Not found', 'delicacy'); ?></h3>

	<?php endif; ?>

			</div><!-- end #post-list -->
	</div><!-- end #content -->

<?php get_sidebar(); ?>

	</div><!-- end #content-wrapper -->

<?php get_footer(); ?>
