<?php get_header(); ?>

<div class="content thin">
											        
	<?php 
	if ( have_posts() ) : 
		while ( have_posts() ) : the_post(); 
			?>
	
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'single post' ); ?>>
					
				<figure class="featured-media">
					<?php echo wp_get_attachment_image( $post->ID, 'post-image' ); ?>
				</figure>
				
				<div class="post-inner">
				
					<div class="post-header">
						<h1 class="post-title"><?php echo basename( get_attached_file( $post->ID ) ); ?></h1>
					</div><!-- .post-header -->
					
					<?php 

					$image_caption = wp_get_attachment_caption();
					
					if ( $image_caption ) : ?>
														
						<div class="post-content entry-content section-inner thin">
							<?php echo wpautop( $image_caption ); ?>
						</div>
						
					<?php endif; ?>
					
					<div class="post-meta-bottom clear">
					
						<ul>
							<li><?php _e( 'Uploaded by', 'fukasawa' ); ?> <?php the_author_posts_link(); ?></p>
							<li class="post-date"><a href="<?php the_permalink(); ?>"><?php the_date( get_option( 'date_format' ) ); ?></a></li>
							
							<?php 
							$image_array = wp_get_attachment_image_src( $post->ID, 'full', false ); 
							$url = $image_array['0']; ?>
							<li><?php _e( 'Resolution:', 'fukasawa' ); ?> <?php echo $image_array['1'] . 'x' . $image_array['2'] . ' px'; ?></li>
						</ul>
					
					</div><!-- .post-meta-bottom -->
					
				</div><!-- .post-inner -->
				
				<?php

				// Output comments wrapper if comments are open, or if there's a comment number – and check for password
				if ( ( comments_open() || get_comments_number() ) && ! post_password_required() ) {
					comments_template( '', true );
				}

				?>

			</article><!-- .post -->

			<?php
		endwhile; 
	endif; 
	?>

</div><!-- .content -->
		
<?php get_footer(); ?>