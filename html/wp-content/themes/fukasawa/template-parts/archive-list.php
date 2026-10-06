<?php

/* 
	Included as part of template-archive.php.
 */

?>

<div class="archive-container">
					
	<h3><?php _e( 'Posts', 'fukasawa' ); ?></h3>
									
	<ul>
		<?php 
		
		$posts_archive = get_posts( array(
			'post_status'		=> 'publish',
			'posts_per_page'	=> -1,
		) );

		foreach ( $posts_archive as $archive_post ) : ?>
			<li>
				<a href="<?php echo get_the_permalink( $archive_post->ID ); ?>">
					<?php echo get_the_title( $archive_post->ID );?> 
					<time><?php echo get_the_time( get_option( 'date_format' ), $archive_post->ID ); ?></time>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	
	<h3><?php _e( 'Categories','fukasawa') ?></h3>
	
	<ul>	            
		<?php wp_list_categories( 'title_li='); ?>
	</ul>
	
	<h3><?php _e( 'Tags','fukasawa') ?></h3>
	
	<ul>
		<?php 
							
		$tags = get_tags();
		
		if ( $tags ) {
			foreach ( $tags as $tag ) {
				echo '<li><a href="' . get_tag_link( $tag->term_id ) . '">' . $tag->name . '</a></li> ';
			}
		}
		?>
	</ul>
	
	<h3><?php _e( 'Contributors', 'fukasawa' ); ?></h3>
	
	<ul>
		<?php wp_list_authors(); ?> 
	</ul>
	
	<h3><?php _e( 'Archives by Year', 'fukasawa' ); ?></h3>
	
	<ul>
		<?php wp_get_archives( 'type=yearly' ); ?>
	</ul>
	
	<h3><?php _e( 'Archives by Month', 'fukasawa' ); ?></h3>
	
	<ul>
		<?php wp_get_archives( 'type=monthly' ); ?>
	</ul>

	<h3><?php _e( 'Archives by Day', 'fukasawa' ); ?></h3>
	
	<ul>
		<?php wp_get_archives( 'type=daily' ); ?>
	</ul>

</div><!-- .archive-container -->