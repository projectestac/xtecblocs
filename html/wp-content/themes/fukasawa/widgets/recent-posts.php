<?php 

class fukasawa_recent_posts extends WP_Widget {

	function __construct() {
        $widget_ops = array( 
			'classname' 	=> 'widget_fukasawa_recent_posts', 
			'description' 	=> __( 'Displays recent blog entries.', 'fukasawa' )
		);
        parent::__construct( 'widget_fukasawa_recent_posts', __( 'Recent Posts', 'fukasawa' ), $widget_ops );
    }
	
	function widget ($args, $instance ) {
	
		// Outputs the content of the widget
		extract( $args ); // Make before_widget, etc available.
		
		$widget_title = apply_filters( 'widget_title', isset( $instance['widget_title'] ) ? $instance['widget_title'] : '' );
		$number_of_posts = isset( $instance['number_of_posts'] ) ? $instance['number_of_posts'] : 5;
		
		echo $before_widget;
		
		if ( ! empty( $widget_title ) ) {
		
			echo $before_title . $widget_title . $after_title;
			
		} ?>
		
			<ul>
				
				<?php

				global $post;
				$sticky = get_option( 'sticky_posts' );	

				$not_in = array( $sticky[0], $post->ID );
				
				if ( $number_of_posts == 0 ) $number_of_posts = 5;
				
				$recent_posts = get_posts( array(
					'post_status' 		=> 'publish',
					'post_type' 		=> 'post',
					'post__not_in' 		=> $not_in, 
					'posts_per_page' 	=> $number_of_posts,
				) );
				
				if ( $recent_posts ) :

					foreach( $recent_posts as $post ) :

						setup_postdata( $post );

						?>
			
						<li>
						
							<a href="<?php the_permalink(); ?>" class="clear">
									
								<div class="post-icon">
								
									<?php 

									$post_format = get_post_format() ? get_post_format() : 'standard'; 
									
									if ( has_post_thumbnail() ) {
									
										the_post_thumbnail( 'thumbnail' );
										
									} elseif ( $post_format == 'gallery' ) {

										$attachment_parent = get_the_ID();

										$images = get_posts( array(
											'orderby'        => 'menu_order',
											'order'          => 'ASC',
											'numberposts'    => 1,
											'post_mime_type' => 'image',
											'post_parent'    => $attachment_parent,
											'post_status'    => null,
											'post_type'      => 'attachment',
										) );
					
										if ( $images ) { 
											
											foreach( $images as $image ) { 

												$attimg = wp_get_attachment_image( $image->ID, 'thumbnail' );
												echo $attimg;
											
											}
										
										}
										
									} else { ?>
									
										<div class="genericon genericon-<?php echo $post_format; ?>"></div>
									
									<?php } ?>
									
								</div>
								
								<div class="inner">
									<p class="title"><?php the_title(); ?></p>
									<p class="meta"><?php the_time( get_option( 'date_format' ) ); ?></p>
								</div>
													
							</a>
							
						</li>
		
					<?php 
					endforeach; 

					wp_reset_postdata(); 
					
				endif; ?>
		
			</ul>
					
		<?php echo $after_widget; 
	}
	
	
	function update( $new_instance, $old_instance ) {
	
		// Update and save the widget
		return $new_instance;
		
	}
	
	function form( $instance ) {
	
		// Get the options into variables, escaping html characters on the way
		$widget_title = isset( $instance['widget_title'] ) ? $instance['widget_title'] : '';
		$number_of_posts = isset( $instance['number_of_posts'] ) ? $instance['number_of_posts'] : '';
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>"><?php  _e( 'Title:', 'fukasawa' ); ?>
			<input id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" type="text" class="widefat" value="<?php echo $widget_title; ?>" /></label>
		</p>
						
		<p>
			<label for="<?php echo $this->get_field_id( 'number_of_posts' ); ?>"><?php _e( 'Number of posts to display:', 'fukasawa' ); ?>
			<input id="<?php echo $this->get_field_id( 'number_of_posts' ); ?>" name="<?php echo $this->get_field_name( 'number_of_posts' ); ?>" type="text" class="widefat" value="<?php echo $number_of_posts; ?>" /></label>
			<small>(<?php _e( 'Defaults to 5 if empty', 'fukasawa' ); ?>)</small>
		</p>
		
		<?php
	}
}
register_widget( 'fukasawa_recent_posts' ); ?>