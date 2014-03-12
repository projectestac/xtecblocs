<?php
/**
 * The template for displaying image attachments.
 *
 * @package Reddle
 * @since Reddle 1.0
 */

get_header(); ?>

		<div id="primary" class="image-attachment">
			<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

						<div class="entry-meta">
							<?php
								$metadata = wp_get_attachment_metadata();
								printf( __( 'Published <span class="entry-date"><abbr class="published" title="%1$s">%2$s</abbr></span> at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> in <a href="%6$s" title="Return to %7$s" rel="gallery">%8$s</a>', 'reddle' ),
									esc_attr( get_the_time() ),
									esc_html( get_the_date() ),
									esc_url( wp_get_attachment_url() ),
									$metadata['width'],
									$metadata['height'],
									esc_url( get_permalink( $post->post_parent ) ),
									esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ),
									get_the_title( $post->post_parent )
								);

								edit_post_link( __( 'Edit', 'reddle' ), '<span class="edit-link">', '</span>' );
							?>
						</div><!-- .entry-meta -->

						<nav id="image-navigation">
							<span class="previous-image"><?php previous_image_link( false, __( '&larr; Previous' , 'reddle' ) ); ?></span>
							<span class="next-image"><?php next_image_link( false, __( 'Next &rarr;' , 'reddle' ) ); ?></span>
						</nav><!-- #image-navigation -->
					</header><!-- .entry-header -->

					<div class="entry-content">

						<div class="entry-attachment">
							<div class="attachment">
								<?php reddle_the_attached_image(); ?>
							</div><!-- .attachment -->

							<?php if ( has_excerpt() ) : ?>
							<div class="entry-caption wp-caption-text">
								<?php the_excerpt(); ?>
							</div>
							<?php endif; ?>
						</div><!-- .entry-attachment -->

						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'reddle' ), 'after' => '</div>' ) ); ?>

					</div><!-- .entry-content -->
				</article><!-- #post-## -->

				<?php comments_template(); ?>

			<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>