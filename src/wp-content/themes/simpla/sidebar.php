<div id="sidebar">

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>

<h2><?php _e('Pages','simpla');?></h2>
<ul>
<li><a href="<?php echo get_settings('home'); ?>/"><?php _e('Home','simpla');?></a></li>
<?php wp_list_pages('title_li='); ?> 
</ul>
<h2><?php _e('Categories','simpla');?></h2>
<ul>
<?php wp_list_cats('sort_column=name&optioncount=1&hierarchical=0'); ?>
</ul>
<ul>
<?php get_links_list(); ?>
</ul>

<?php endif; ?>
</div>
