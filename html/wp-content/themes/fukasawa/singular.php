<?php get_header(); ?>

<div class="content thin">
											        
	<?php 
	if ( have_posts() ) : 
		while ( have_posts() ) : the_post(); 
			?>
			
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry post single' ); ?>>
			
				<?php
				
				$post_format = get_post_format(); 

				if ( ! post_password_required() ) :
				
					if ( $post_format == 'video' ) :
					
						if ( $pos = strpos( $post->post_content, '<!--more-->' ) ) : ?>
				
							<figure class="featured-media clear">
							
								<?php
										
								// Fetch post content
								$content = get_post_field( 'post_content', get_the_ID() );
								
								// Get content parts
								$content_parts = get_extended( $content );
								
								// oEmbed part before <!--more--> tag
								$embed_code = wp_oembed_get( $content_parts['main'] );
								
								echo $embed_code;
								
								?>
							
							</figure><!-- .featured-media -->
						
							<?php
						endif;
						
					elseif ( $post_format == 'gallery' ) : ?>
					
						<figure class="featured-media clear">
							<?php fukasawa_flexslider( 'post-image' ); ?>
						</figure><!-- .featured-media -->
									
					<?php elseif ( has_post_thumbnail() ) : ?>
							
						<figure class="featured-media clear">
							<?php the_post_thumbnail( 'post-image' ); ?>
						</figure><!-- .featured-media -->
							
					<?php endif; ?>

				<?php endif; ?>
				
				<div class="post-inner">
					
					<header class="post-header">

						<?php 
						$post_title_elem = is_front_page() ? 'h2' : 'h1';
						the_title( '<' . $post_title_elem . ' class="post-title">', '</' . $post_title_elem . '>' ); 
						?>

					</header><!-- .post-header -->
						
					<div class="post-content entry-content">
					
						<?php 
						if ( $post_format == 'video' && isset( $content_parts ) ) { 
							$content = $content_parts['extended'] ? $content_parts['extended'] : '';
							$content = apply_filters( 'the_content', $content );
							echo $content;
						} else {
							the_content();
						}

						if ( ! is_single() ) {
							edit_post_link( __( 'Edit post', 'fukasawa' ), '<p>', '</p>' );
						}

						?>
					
					</div><!-- .post-content -->

					<?php

					// Archive template output
					if ( is_page_template( 'template-archive.php' ) ) {
						get_template_part( 'template-parts/archive-list' );
					}
				
					$link_pages = wp_link_pages( $args = array(
						'before'           => '<p class="page-links"><span class="title">' . __( 'Pages:','fukasawa' ) . '</span>',
						'after'            => '</p>',
						'link_before'      => '<span>',
						'link_after'       => '</span>',
						'separator'        => '',
						'pagelink'         => '%',
						'echo'             => false
					) ); 
					
					if ( is_single() || $link_pages ) : ?>
					
						<div class="post-meta-bottom clear">

							<?php 
							
							echo $link_pages;
							
							if ( is_single() ) : ?>
						
								<ul>
									<li class="post-date"><a href="<?php the_permalink(); ?>"><?php the_date( get_option( 'date_format' ) ); ?></a></li>

									<?php if ( has_category() ) : ?>
										<li class="post-categories"><?php _e( 'In', 'fukasawa' ); ?> <?php the_category( ', ' ); ?></li>
									<?php endif; ?>

									<?php if ( has_tag() ) : ?>
										<li class="post-tags"><?php the_tags('', ' '); ?></li>
									<?php endif; ?>

									<?php edit_post_link( __( 'Edit post', 'fukasawa' ), '<li>', '</li>' ); ?>
								</ul>

							<?php endif; ?>
							
						</div><!-- .post-meta-bottom -->

					<?php endif; ?>
				
				</div><!-- .post-inner -->

				<?php if ( is_single() ) : ?>
				
					<div class="post-navigation clear">

						<?php

						$prev_post = get_previous_post();
						$next_post = get_next_post();

						if ( $prev_post ) : ?>
							<a class="post-nav-prev" href="<?php echo get_permalink( $prev_post->ID ); ?>">
								<p>&larr; <?php _e( 'Previous post', 'fukasawa' ); ?></p>
							</a>
						<?php endif; ?>
						
						<?php if ( $next_post ) : ?>
							<a class="post-nav-next" href="<?php echo get_permalink( $next_post->ID ); ?>">					
								<p><?php _e( 'Next post', 'fukasawa' ); ?> &rarr;</p>
							</a>
						<?php endif; ?>
					
					</div><!-- .post-navigation -->

				<?php endif;
				
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