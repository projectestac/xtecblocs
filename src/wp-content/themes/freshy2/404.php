<?php get_header(); ?>

	<div id="content">
				
		<div class="post" id="post-none">
			<h2><?php _e('Not found',TEMPLATE_DOMAIN); ?></h2>
			<p><?php _e("Sorry, but you are looking for something that is not here",TEMPLATE_DOMAIN); ?></p>
		</div>
	
	</div>
	
	<?php // sidebars ?>
	<?php if ($freshy_options['sidebar_right'] == true) get_sidebar(); ?>
	<?php if ($freshy_options['sidebar_left'] == true) include (TEMPLATEPATH . '/sidebar_left.php'); ?>
	
</div>

<?php get_footer(); ?>