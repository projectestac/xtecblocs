<div id="sidebar">
<div id="searchdiv">
    <form id="searchform" method="get" action="index.php">
      <input type="text" name="s" id="s" size="20"/>
      <input name="sbutt" type="submit" value="<?php _e('Find','light');?>" alt="Submit"  />
    </form>
  </div>
<?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : ?>
 <h2><?php _e('Monthly Archives','light');?></h2>
  <ul>
    <?php wp_get_archives('type=monthly'); ?>
  </ul>
  <h2><?php _e('Categories','light');?></h2>
  <ul>
    <?php wp_list_cats(); ?>
  </ul>
<h2><?php _e('Stay Updated','light');?></h2>
  <ul id="feed">
    <li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS','light'); ?>"><?php _e('RSS Articles','light');?></a></li>
  </ul>
<?php endif; ?>
</div>
