<?php // If you want to add your own content to the sidebar, modifiy my_sidebar_left.php instead. ?>
	<div id="sidebar_left" class="sidebar">
		<div>
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
			<h2><?php _e('Archives',TEMPLATE_DOMAIN); ?></h2>
			<ul>
				<?php wp_get_archives('type=monthly'); ?>
			</ul>
		<?php endif; ?>
		<?php include (TEMPLATEPATH . '/my_sidebar_left.php'); ?>
		</div>
	</div>