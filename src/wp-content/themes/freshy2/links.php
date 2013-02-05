<?php
/*
Template Name: Links
*/
?>

<?php get_header(); ?>

	<div id="content">
		<div class="post" id="post-none">
			<ul id="linkslist">
			<?php get_links_list(); ?>
			</ul>
		</div>	
	</div>
	
	<?php // sidebars ?>
	<?php if ($freshy_options['sidebar_right'] == true) get_sidebar(); ?>
	<?php if ($freshy_options['sidebar_left'] == true) include (TEMPLATEPATH . '/sidebar_left.php'); ?>
	
</div>

<?php get_footer(); ?>
