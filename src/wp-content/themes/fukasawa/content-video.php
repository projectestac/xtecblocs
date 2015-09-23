<div class="post-container">

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
		<?php if ($pos=strpos($post->post_content, '<!--more-->')): ?>

			<div class="featured-media">
			
				<?php
						
					// Fetch post content
					$content = get_post_field( 'post_content', get_the_ID() );
					
					// Get content parts
					$content_parts = get_extended( $content );
					
					// oEmbed part before <!--more--> tag
					$embed_code = wp_oembed_get($content_parts['main']); 
					
					echo $embed_code;
				
				?>
			
			</div> <!-- /featured-media -->
		
		<?php endif; ?>
		
		<div class="post-header">
		
			<?php 
				$post_title = get_the_title();
				if ( !empty( $post_title ) ) : 
			?>
			
			    <h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
		    
		    <?php else : ?>
			    
			    <div class="posts-meta">
			    
			    	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_time(get_option('date_format')); ?></a>
			    	
		    	</div>
		    
		    <?php endif; ?>
		    	    
		</div> <!-- /post-header -->
		
		<div class="post-excerpt">
			
			<?php 
				if ($pos=strpos($post->post_content, '<!--more-->')) {
					echo  '<p>' . mb_strimwidth($content_parts['extended'], 0, 160, '...') . '</p>';
				} else {
					the_excerpt('100');
				}
			?>
		
		</div> <!-- /post-excerpt -->
	
	</div> <!-- /post -->

</div> <!-- /post -->