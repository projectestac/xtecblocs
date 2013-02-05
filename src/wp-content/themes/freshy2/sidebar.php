<?php // If you want to add your own content to the sidebar, modifiy my_sidebar.php instead. ?>
<?php global $freshy_options; ?>
	<div id="sidebar" class="sidebar">
		<div>
		<?php if ($freshy_options['sidebar_left'] && $freshy_options['sidebar_right']) $sidebar_id = 2;
		else $sidebar_id = 1 ?>
						
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($sidebar_id) ) : ?>
	
			<?php freshy_menu($freshy_options['args_pages'],$freshy_options['args_cats']); ?>
			
			<h2><?php _e('Search','freshy-2'); ?></h2>
			<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			
		<?php endif; ?>
		<?php include (TEMPLATEPATH . '/my_sidebar.php'); ?>
		</div>
	</div>