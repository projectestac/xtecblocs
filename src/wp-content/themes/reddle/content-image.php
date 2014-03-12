<?php
/**
 * The template for displaying posts in the Image Post Format on index and archive pages
 *
 * Learn more: http://codex.wordpress.org/Post_Formats
 *
 * @package Reddle
 * @since Reddle 1.0
 */

// Grab the first image from the post (for backward compatibility for versions prior to 3.6)
preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_the_content(), $matches );
$first_image = isset( $matches[1][0] ) ? $matches[1][0] : '';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		if ( '' != get_the_post_thumbnail() || ! empty( $first_image )  ) :
			if ( '' != get_the_post_thumbnail() ) :
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
	?>
	<div class="entry-image">
		<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'reddle' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">
			<img class="featured-image" src="<?php echo $thumbnail[0]; ?>" alt="">
		</a>
	</div>

	<?php elseif ( ! empty( $first_image ) && ! is_search() ) : ?>

	<figure class="entry-image">
		<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'reddle' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">
			<img class="featured-image" src="<?php echo esc_url( $first_image ); ?>" alt="" />
		</a>
	</figure>
	<?php
			endif; // ! empty( $first_image ) && ! is_search()
		endif; // '' != get_the_post_thumbnail() || ! empty( $first_image )
	?>

	<header class="entry-header">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta">
			<?php reddle_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>

		<?php if ( comments_open() || ( '0' != get_comments_number() && ! comments_open() ) ) : ?>
		<p class="comments-link"><?php comments_popup_link( '<span class="no-reply">' . __( '0', 'reddle' ) . '</span>', __( '1', 'reddle' ), __( '%', 'reddle' ) ); ?></p>
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->

	<footer class="entry-meta">
		<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
			<?php
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'reddle' ) );
				if ( $categories_list && reddle_categorized_blog() ) :
			?>
			<p class="cat-links taxonomy-links">
				<?php printf( __( 'Posted in %1$s', 'reddle' ), $categories_list ); ?>
			</p>
			<?php endif; // End if categories ?>

			<?php
				/* translators: used between list items, there is a space after the comma */
				$tags_list = get_the_tag_list( '', __( ', ', 'reddle' ) );
				if ( $tags_list ) :
			?>
			<p class="tag-links taxonomy-links">
				<?php printf( __( 'Tagged %1$s', 'reddle' ), $tags_list ); ?>
			</p>
			<?php endif; // End if $tags_list ?>
		<?php endif; // End if 'post' == get_post_type() ?>

		<p class="date-link"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'reddle' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark" class="permalink"><span class="month"><?php the_time( 'M' ); ?></span><span class="sep">&middot;</span><span class="day"><?php the_time( 'd' ); ?></span></a></p>

		<?php edit_post_link( __( 'Edit', 'reddle' ), '<p class="edit-link">', '</p>' ); ?>
	</footer><!-- #entry-meta -->
</article><!-- #post-## -->