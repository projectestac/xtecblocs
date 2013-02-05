<!-- Start Obar -->

	<div class="obar">
<ul>

<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(1) ) : else : ?>

	<?php wp_list_pages('depth=3&title_li=<h2>'. __('Pages') .'</h2>' ); ?>

	<li><h2><?php _e('Categories', 'digg-3'); ?></h2>
		<ul>
			<?php wp_list_cats('sort_column=name&optioncount=1&hierarchical=0'); ?>
		</ul>
	</li>

	<li><h2><?php _e('Archives', 'digg-3'); ?></h2>
		<ul>
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</li>

<?php endif; ?>

</ul>
	</div>

<!-- End Obar -->
