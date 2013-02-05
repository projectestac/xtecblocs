<div id="rightside">

<ul>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Main Sidebar') ) : else : ?>

<li><?php include (TEMPLATEPATH . '/searchform.php'); ?></li>

<li id="categories"><h2><?php _e('Categories','andreas09'); ?></h2>
<ul>
<?php wp_list_cats('sort_column=name&optioncount=0&hierarchical=1'); ?>
</ul>
</li>

<li id="archives"><h2><?php _e('Archives','andreas09'); ?></h2>
<ul>
<?php wp_get_archives('type=monthly'); ?>
</ul>
</li>

<li><h2><?php _e('Subscribe','andreas09'); ?></h2>
<ul>
<li class="feed"><a href="<?php bloginfo('rss2_url'); ?>"><?php _e('Entries (RSS)','andreas09'); ?></a></li>
<li class="feed"><a href="<?php bloginfo('comments_rss2_url'); ?>"><?php _e('Comments (RSS)','andreas09'); ?></a></li>
</ul>
</li>

<li><h2><?php _e('Meta','andreas09'); ?></h2>
<ul>
<?php wp_register(); ?>
<li><?php wp_loginout(); ?></li>
<?php wp_meta(); ?>
</ul>
</li>

<?php endif; ?>
</ul>
	
</div>
