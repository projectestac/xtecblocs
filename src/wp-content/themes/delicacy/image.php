<?php get_header(); ?>

	<!-- CONTENT -->
	<div id="content">
			
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class('single'); ?>>
                    <header>
                        <h2><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?> </a></h2>
                        <div class="entry-meta">
                            <span class="cat"><?php the_category(', ') ?></span><span class="date"><?php the_time( get_option('date_format') ); ?></span><span class="comments"><?php comments_popup_link(__('No comments','delicacy'), __('1 comment','delicacy'), __('Comments: %','delicacy')); ?></span>
                        </div>
                    </header>
                    <div class="entry-content">
                        <div style="text-align: center;"><a href="<?php echo wp_get_attachment_url($post->ID); ?>"><?php echo wp_get_attachment_image( $post->ID, 'medium' ); ?></a></div>      
                        <?php if ( !empty($post->post_excerpt) ) the_excerpt(); ?><br />
                        <div style="float:right;"><?php next_image_link('',__('Next image &raquo;','delicacy')) ?></div><?php previous_image_link('',__('&laquo; Previous image','delicacy')) ?><br />
                    </div>
                </article>
			<?php endwhile; // end of the loop. ?>
			
			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() )
					comments_template( '', true );
			?>

	</div>
	<!-- END CONTENT -->
		
	<?php get_sidebar(); ?>

<?php get_footer(); ?>