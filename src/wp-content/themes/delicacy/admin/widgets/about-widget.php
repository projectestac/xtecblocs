<?php
/**
 * Plugin Name: About Widget
 */

add_action( 'widgets_init', 'delicacy_about_load_widgets' );

function delicacy_about_load_widgets() {
	register_widget( 'delicacy_about_widget' );
}

class delicacy_about_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function delicacy_about_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'delicacy_about_widget', 'description' => __('About section with autor image', 'delicacy') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'delicacy_about_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'delicacy_about_widget', __('Delicacy: About', 'delicacy'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$text = $instance['text'];
		$image = $instance['image'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		?>
			    <div class="sidebar-about">
				    <?php if($image) { ?><img src="<?php echo $image; ?>" alt="" class="about-image" /><?php } ?>
				    <?php echo $text; ?>
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
		$instance['image'] = strip_tags( $new_instance['image'] );
		$instance['text'] = $new_instance['text'];

		return $instance;
	}


	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('About','delicacy'), 'text' => '', 'image' => '');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title','delicacy') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>

		<!-- About text -->
		<p>
			<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e('About the author text','delicacy') ?>:</label>
			<textarea id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" style="width:96%;" rows="6"><?php echo $instance['text']; ?></textarea>
		</p>
		
		<!-- Image -->
		<p>
			<label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php _e('Author image URL','delicacy') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" value="<?php echo $instance['image']; ?>" style="width:90%;" />
			<small><?php _e('Suggested image dimensions: 90x120px', 'delicacy') ?></small>
		</p>

	<?php
	}
}

?>