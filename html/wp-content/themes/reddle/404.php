<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Reddle
 * @since Reddle 1.0
 */

get_header(); ?>

	<div id="primary">
		<div id="content" role="main">

			<?php get_template_part( 'content', '404' ); ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>