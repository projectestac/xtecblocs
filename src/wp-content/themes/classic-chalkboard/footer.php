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

<?php global $classicchalkboard_options; $classicchalkboard_settings = get_option( 'classicchalkboard_options', $classicchalkboard_options ); ?>

		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="site-info">
				<?php do_action( 'chalkboard_credits' ); ?>
				<a href="http://wordpress.org/" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'classicchalkboard' ); ?>"><?php printf( __( 'Proudly powered by %s', 'classicchalkboard' ), 'WordPress' ); ?></a>
			</div><!-- .site-info -->

			<div class="author-credit">

<?php if( $classicchalkboard_settings['footer_link']) : ?>
<a href="http://www.edwardrjenkins.com/" rel="nofollow">
<?php esc_attr_e( 'Classic Chalkboard Theme by Edward R. Jenkins' , 'classicchalkboard' ); ?>
</a>
<?php else: ?>

<?php esc_attr_e( 'Classic Chalkboard Theme by Edward R. Jenkins' , 'classicchalkboard' ); ?>
<?php endif; ?>
			
</div>
		</footer><!-- #colophon .site-footer -->
	</div><!-- #page .hfeed .site -->
</div><!-- .wrapper -->

<?php wp_footer(); ?>

</body>
</html>