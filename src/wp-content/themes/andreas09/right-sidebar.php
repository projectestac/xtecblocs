<!-- Right Sidebar Template -->

<div id="rightside">
<ul>

<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Right Sidebar') ) : else : ?>

<li><h2><?php _e('Recent Posts','andreas09'); ?></h2>
<ul>
<?php wp_get_archives('type=postbypost&limit=10'); ?>
</ul>
</li>

<?php get_links_list(); ?>

<?php endif; ?>
</ul>
</div>
