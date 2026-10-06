<?php
/**
 * @package Reddle
 * @since Reddle 1.0
 */
?>

<article id="post-0" class="post error404 not-found">
	<header class="entry-header">
		<h1 class="entry-title"><?php _e( 'Hi there. You seem to be lost.', 'reddle' ); ?></h1>
	</header>

	<div class="entry-content">
		<p><?php _e( 'It looks like nothing was found at this location. Perhaps it <em>was</em> there but now it&rsquo;s gone. Maybe try one of the links below or a search?', 'reddle' ); ?></p>

		<?php get_search_form(); ?>

	</div><!-- .entry-content -->
</article><!-- #post-0 -->
