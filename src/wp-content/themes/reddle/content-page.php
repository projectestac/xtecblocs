<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Reddle
 * @since Reddle 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if ( '' != get_the_post_thumbnail() ) :
			$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
	?>
	<div class="entry-image">
			<img class="featured-image" src="<?php echo $thumbnail[0]; ?>" alt="">
	</div>

	<?php endif; ?>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'reddle' ), 'after' => '</div>' ) ); ?>
		<?php edit_post_link( __( 'Edit', 'reddle' ), '<span class="edit-link">', '</span>' ); ?>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
