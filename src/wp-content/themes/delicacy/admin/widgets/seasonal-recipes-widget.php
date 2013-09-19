<?php
/**
 * Plugin Name: Seasonal Recipes Slider
 */

add_action( 'widgets_init', 'delicacy_seasonal_recipes_load_widgets' );

function delicacy_seasonal_recipes_load_widgets() {
	register_widget( 'delicacy_seasonal_recipes_widget' );
}

class delicacy_seasonal_recipes_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function delicacy_seasonal_recipes_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'delicacy_seasonal_recipes_widget', 'description' => __('Widget for Header Widget area. Rotates links for posts with a selected tag in the header (i.e. recipes for dishes currently in season).', 'delicacy') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'delicacy_seasonal_recipes_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'delicacy_seasonal_recipes_widget', __('Delicacy: Featured posts', 'delicacy'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$number = $instance['number'];
		$tag = $instance['tag'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		?>
		    
			<?php
			$recent_posts = new WP_Query(array(
				'showposts' => $number,
				'tag_id' => $tag,
			));
			
			$pointer = 1;
			?>
			
				<?php while($recent_posts->have_posts()): $recent_posts->the_post(); ?>

                	<?php
						$posttags = get_the_tags();
						if ($posttags) {
						  foreach($posttags as $tags) {
							if($tags->term_id == $tag){
							$tag_name = $tags->name;
							}
						  }
						}
					?>
					<?php if($pointer == 1) { ?>
		                <span class="headline-title"><?php echo $title ?>&nbsp;<?php echo $tag_name ?>&nbsp;&nbsp;&nbsp;&nbsp;|</span>
						<div id="headline-slider">
					<?php } ?>

					<a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
				    <?php $pointer++; ?>
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
		$instance['tag'] = strip_tags( $new_instance['tag'] );

		return $instance;
	}


	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Featured:', 'delicacy'), 'tag' => '', 'number' => 5);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title','delicacy') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>
		
		<!-- Number of posts -->
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e('Number of posts to rotate','delicacy') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" size="3" />
		</p>

		<!-- Tag -->
		<p>
			<label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Select tag','delicacy') ?>:</label>
			<select id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" style="width:100%;">
				<?php $tags = get_tags('hide_empty=1'); ?>
				<?php foreach($tags as $tag) { ?>
				<option value='<?php echo $tag->term_id; ?>' <?php if ($tag->term_id == $instance['tag']) echo 'selected="selected"'; ?>><?php echo $tag->name; ?></option>
				<?php } ?>
			</select>
		</p>

	<?php
	}
}

?>