	<div id="sidebar">
		<?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : ?>
<?php if (function_exists('wp_theme_switcher')) { ?>
	<?php } ?>

<div class="categ">
<?php wp_meta(); ?>
<h2 class="one"><?php _e('Categories','colors-idea');?></h2>
<ul>
<?php wp_list_cats('sort_column=name&hierarchical=0'); ?>
</ul>
</div>

<div class="cal">
<h2 class="one"><?php _e('Calendar','colors-idea');?></h2><span class="calendar">
<div align="center"><?php get_calendar(1); ?></div></span>
</div>

<div class="posts">
<h2 class="one"><?php _e('Recent Posts','colors-idea');?></h2>
<ul>
<?php wp_get_archives('type=postbypost&limit=20'); ?>
</ul>
</div>

<div class="blogroll">
<h2 class="one"><?php _e('Blogroll','colors-idea'); ?></h2>
<ul>
<?php get_links(-1, '<li>', '</li>', '', FALSE, 'name', FALSE, FALSE, -1, FALSE); ?>
</ul>
</div>
<?php endif; ?>



	</div>