<?php
/**
 * Plugin Name: Recent Posts
 */

add_action( 'widgets_init', 'delicacy_recent_posts_load_widgets' );

function delicacy_recent_posts_load_widgets() {
	register_widget( 'delicacy_recent_posts_widget' );
}

class delicacy_recent_posts_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function delicacy_recent_posts_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'delicacy_recent_posts_widget', 'description' => __('Displays a list of recent posts with post thumbnails, date and number of comments', 'delicacy') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'delicacy_recent_posts_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'delicacy_recent_posts_widget', __('Delicacy: Recent posts', 'delicacy'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$number = $instance['number'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		?>
		
			<?php
			$recent_posts = new WP_Query(array(
				'showposts' => $number,
			));
			?>
			
			<div class="post-block">
			
			<?php while($recent_posts->have_posts()): $recent_posts->the_post(); ?>
				<div class="sidebar-post-item">

					<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { /* if post has a thumbnail */ ?>
					<?php the_post_thumbnail('side-thumb'); ?>
					<?php } ?>

					<h4><a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
					<div class="sidebar-post-meta"><span class="date"><?php the_time( get_option('date_format') ); ?></span><span class="comments"><?php comments_popup_link(__('No comments','delicacy'), __('1 comment','delicacy'), __('Comments: %','delicacy')); ?></span></div>
				</div>
				
			<?php endwhile; ?>
			
			</div>
			
		<?php

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags( $new_instance['number'] );

		return $instance;
	}


	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Recent posts', 'delicacy'), 'number' => 5);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title','delicacy') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>
		
		<!-- Number of posts -->
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e('Number of posts to show','delicacy') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" size="3" />
		</p>


	<?php
	}
}

?>