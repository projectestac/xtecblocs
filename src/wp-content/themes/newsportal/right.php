  
  <div id="right_side">
  <?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar(2) ) : ?>
		
        <div class="block block-node">
		
		<h3><?php _e('Blogroll','news-portal'); ?></h3>

<ul>

<?php get_links(-1, '<li>', '</li>', '', FALSE, 'name', FALSE, FALSE, -1, FALSE); ?>

</ul> 
     </div>
	 
	 	 
	  <div class="block block-node">
		
	 
	 
	 <?php

$today = current_time('mysql', 1);

if ( $recentposts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_date_gmt < '$today' ORDER BY post_date DESC LIMIT 10")):

?>

<h3><?php _e("Recent Posts",'news-portal'); ?></h3>

<ul>

<?php

foreach ($recentposts as $post) {

if ($post->post_title == '')

$post->post_title = sprintf(__('Post').' #%s', $post->ID);

echo "<li><a href='".get_permalink($post->ID)."'>";

the_title();

echo '</a></li>';

}

?>

</ul>

<?php endif; ?>

</div>

	 
	  <div class="block block-node">
		


<h3><?php _e('Search','news-portal');?></h3>

<ul>

<li>

<form method="get" id="searchform" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<input type="text" size="10" value="<?php echo wp_specialchars($s, 1); ?>" name="s" id="s" />
	<input type="submit" id="sidebarsubmit" value="<?php _e('Search','news-portal');?>" />
</form>

</li> 

</ul> 


     </div>
 
 <?php endif; ?>
 
  </div>
