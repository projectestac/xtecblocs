<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Reddle
 * @since Reddle 1.0
 */
?>

			<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
			<div id="secondary" class="widget-area" role="complementary">
				<?php dynamic_sidebar( 'sidebar-1' ); ?>
			</div><!-- #secondary .widget-area -->
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
			<div id="tertiary" class="widget-area" role="complementary">
				<?php dynamic_sidebar( 'sidebar-2' ); ?>
			</div><!-- #tertiary .widget-area -->
			<?php endif; ?>