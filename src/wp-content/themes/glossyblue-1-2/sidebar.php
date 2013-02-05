<div id="sidebar">
<ul>
  <?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : ?>
  <li>
  <?php include (TEMPLATEPATH . '/searchform.php'); ?>
  </li>
  
  <?php /* Menu for subpages of current page (copied from K2 theme) */
		global $notfound;
		if (is_page() and ($notfound != '1')) {
			$current_page = $post->ID;
			while($current_page) {
				$page_query = $wpdb->get_row("SELECT ID, post_title, post_status, post_parent FROM $wpdb->posts WHERE ID = '$current_page'");
				$current_page = $page_query->post_parent;
			}
			$parent_id = $page_query->ID;
			$parent_title = $page_query->post_title;

			if ($wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = '$parent_id' AND post_status != 'attachment'")) {
	?>
	
  <li>
    <h2 class="sidebartitle"><?php echo $parent_title; ?> <?php _e('Subpages','glossy-blue'); ?></h2>
	<ul class="list-page">
	  <?php wp_list_pages('sort_column=menu_order&title_li=&child_of='. $parent_id); ?>
	</ul>
	<?php } } ?>
  </li>
  <li>
    <h2 class="sidebartitle"><?php _e('Categories','glossy-blue'); ?></h2>
	<ul class="list-cat">
	  <?php wp_list_cats('sort_column=name&optioncount=1&hierarchical=0'); ?>
	</ul>
  </li>
  <li>
    <h2 class="sidebartitle"><?php _e('Archives','glossy-blue'); ?></h2>
    <ul class="list-archives">
      <?php wp_get_archives('type=monthly'); ?>
    </ul>
  </li>
  <li>
    <h2 class="sidebartitle"><?php _e('Links','glossy-blue'); ?></h2>
    <ul class="list-blogroll">
      <?php get_links('-1', '<li>', '</li>', '<br />', FALSE, 'id', FALSE, FALSE, -1, FALSE); ?>
    </ul>
  </li>
  <?php endif; ?>
 </ul>
</div><!--/sidebar -->
