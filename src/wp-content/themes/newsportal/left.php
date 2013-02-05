  <div id="left_side">
  
  <?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar(1) ) : ?>
		
		
		      <div class="block block-user">
<?php if (function_exists('wp_theme_switcher')) { ?>

<h3><?php _e('Themes','news-portal');?></h3>

<?php wp_theme_switcher('dropdown'); ?>

<?php } ?>
   
 </div>
 
 
        <div class="block block-user">
   <h3><?php _e('Categories','news-portal');?></h3>

<ul>

<?php wp_list_cats('sort_column=name&hierarchical=0'); ?>

</ul>
   
 </div>
 
 
      <div class="block block-user">
	  
	  <h3><?php _e('Archives','news-portal');?></h3>

<ul>
<?php wp_get_archives('type=monthly'); ?>

</ul>
  
   
 </div>
 
 
      <div class="block block-user">
 
 <h3><?php _e('Meta:','news-portal'); ?></h3>

<ul>

<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS','news-portal'); ?>"><?php _e('<abbr title="Really Simple Syndication">RSS</abbr>','news-portal'); ?></a></li>

<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS','news-portal'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>','news-portal'); ?></a></li>

<li><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional','news-portal'); ?>"><?php _e('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>','news-portal'); ?></a></li>

<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>

<?php wp_meta(); ?>

</ul>

 </div>
  
  <?php endif; ?>
  
  </div>
  
