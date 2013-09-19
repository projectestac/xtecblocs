<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Chalkboard
 * @since Chalkboard 1.0
 */
?>
		<div id="secondary" class="widget-area" role="complementary">
			<?php do_action( 'before_sidebar' ); ?>
			<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>
			<?php endif; // end sidebar widget area ?>
		</div>

		<?php if ( is_active_sidebar( 'footer-sidebar-1' ) || is_active_sidebar( 'footer-sidebar-2' ) || is_active_sidebar( 'footer-sidebar-3' ) ) : ?>
			<div id="tertiary" class="widget-area" role="complementary">
				<?php do_action( 'before_sidebar' ); ?>
				<div class="widget-left">
					<?php if ( ! dynamic_sidebar( 'footer-sidebar-1' ) ) : ?>
					<?php endif; // end sidebar widget area ?>
				</div>

				<div class="widget-middle">
					<?php if ( ! dynamic_sidebar( 'footer-sidebar-2' ) ) : ?>
					<?php endif; // end sidebar widget area ?>
				</div>

				<div class="widget-right">
					<?php if ( ! dynamic_sidebar( 'footer-sidebar-3' ) ) : ?>
					<?php endif; // end sidebar widget area ?>
				</div>

			</div><!-- #tertiary .widget-area -->
		<?php endif; // end if is_active_sidebar ?>
