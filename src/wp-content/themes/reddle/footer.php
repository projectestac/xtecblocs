<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Reddle
 * @since Reddle 1.0
 */
?>

	</div><!-- #main -->

	<footer id="colophon" role="contentinfo">
		<?php
			/* A sidebar in the footer? Yep. You can can customize
			 * your footer with three columns of widgets.
			 */
			get_sidebar( 'footer' );
		?>

		<div id="site-info">
			<?php do_action( 'reddle_credits' ); ?>
			<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'reddle' ) ); ?>" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'reddle' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'reddle' ), 'WordPress' ); ?></a>
			<span class="sep"> | </span>
			<?php printf( __( 'Theme: %1$s by %2$s.', 'reddle' ), 'Reddle', '<a href="http://automattic.com/" rel="designer">Automattic</a>' ); ?>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>