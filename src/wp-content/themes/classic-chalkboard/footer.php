<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Chalkboard
 * @since Chalkboard 1.0
 */
?>

		</div><!-- #main .site-main -->

		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="site-info">
				<?php do_action( 'chalkboard_credits' ); ?>
				<a href="http://wordpress.org/" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'classicchalkboard' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'classicchalkboard' ), 'WordPress' ); ?></a>
				<span class="sep"> &bull; </span>
				<?php printf( __( 'Theme: %1$s by %2$s.', 'classicchalkboard' ), 'classicchalkboard', '<a href="http://www.edwardrjenkins.com" rel="designer">Edward R. Jenkins</a>' ); ?>
			</div><!-- .site-info -->
		</footer><!-- #colophon .site-footer -->
	</div><!-- #page .hfeed .site -->
</div><!-- .wrapper -->

<?php wp_footer(); ?>

</body>
</html>