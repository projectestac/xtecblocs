			<div class="right_sidebar_container_bottom"><div class="right_sidebar_container_top">

				<div class="right_sidebar_col">
<ul>

<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(2) ) : else : ?>

	<li><h2><?php _e('Search','quadruple-blue'); ?></h2>
	<ul>
		<li id="search"><?php include (TEMPLATEPATH . '/searchform.php'); ?></li>
	</ul>
	</li>

	<li><h2><?php _e('Categories','quadruple-blue'); ?></h2>
		<ul>
			<?php wp_list_cats('sort_column=name&optioncount=0&hierarchical=0'); ?>
		</ul>
	</li>

<?php endif; ?>

</ul>
				</div>
				<div class="right_sidebar_col">

<ul>

<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(3) ) : else : ?>

	<li><h2><?php _e('Calendar','quadruple-blue'); ?></h2>
		<ul>
			<li><?php get_calendar(); ?></li>
		</ul>
	</li>

	<li><h2><?php _e('Archives','quadruple-blue'); ?></h2>
		<ul>
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</li>

<?php endif; ?>

</ul>
				</div>

			</div></div><!-- end right sidebar container wrap -->