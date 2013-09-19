<?php
/**
 * @package Chalkboard
 * @since Chalkboard 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title"><a href="' . get_permalink() . '" title="' . esc_attr( sprintf( __( 'Permalink to %s', 'classicchalkboard' ), the_title_attribute( 'echo=0' ) ) ) . '" rel="bookmark">', '</a></h1>' ); ?>
	</header><!-- .entry-header -->

	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'classicchalkboard' ) ); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'classicchalkboard' ), 'after' => '</div>' ) ); ?>
	</div><!-- .entry-content -->
	<?php endif; ?>

	<footer class="entry-meta">
		<?php if ( false != get_post_format() ) : ?>
			<a class="entry-format" href="<?php echo esc_url( get_post_format_link( get_post_format() ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'All %s posts', 'classicchalkboard' ), get_post_format_string( get_post_format() ) ) ); ?>"><?php echo get_post_format_string( get_post_format() ); ?></a>
			<span class="sep"> &bull; </span>
		<?php endif; ?>
		<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
			<?php chalkboard_posted_on(); ?>
			<?php
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'classicchalkboard' ) );
				if ( $categories_list && chalkboard_categorized_blog() ) :
			?>
			<span class="cat-links">
				<?php printf( __( 'in %1$s', 'classicchalkboard' ), $categories_list ); ?>
			</span>
			<?php endif; // End if categories ?>

			<?php
				/* translators: used between list items, there is a space after the comma */
				$tags_list = get_the_tag_list( '', __( ', ', 'classicchalkboard' ) );
				if ( $tags_list ) :
			?>
			<span class="sep"> &bull; </span>
			<span class="tags-links">
				<?php printf( __( 'Tagged %1$s', 'classicchalkboard' ), $tags_list ); ?>
			</span>
			<?php endif; // End if $tags_list ?>
		<?php endif; // End if 'post' == get_post_type() ?>

		<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
		<span class="sep"> &bull; </span>
		<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'classicchalkboard' ), __( '1 Comment', 'classicchalkboard' ), __( '% Comments', 'classicchalkboard' ) ); ?></span>
		<?php endif; ?>

		<?php edit_post_link( __( 'Edit', 'classicchalkboard' ), '<span class="sep"> &bull; </span><span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
