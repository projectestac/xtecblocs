<?php
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>

	<div id="content">
		<div class="post" id="post-none">
			<h2><?php _e('Archives by month',TEMPLATE_DOMAIN); ?></h2>
			<ul>
				<?php wp_get_archives('type=monthly'); ?>
			</ul>
			
			<h2><?php _e('Archives by category',TEMPLATE_DOMAIN); ?></h2>
			<ul>
				<?php wp_list_cats(); ?>
			</ul>
		</div>	
	</div>
	
	<?php // sidebars ?>
	<?php if ($freshy_options['sidebar_right'] == true) get_sidebar(); ?>
	<?php if ($freshy_options['sidebar_left'] == true) include (TEMPLATEPATH . '/sidebar_left.php'); ?>
	
</div>

<?php get_footer(); ?>
