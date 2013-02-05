</div>
</div>
<div id="eof"></div>
<div id="footer">
<div class="footer"><?php $current_site = get_current_site(); ?><a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a>
 <?php _e('is proudly powered by','big-blue');?> <a href="http://wordpress.org/">WordPress</a> <a href="<?php bloginfo('rss2_url'); ?>"><?php _e('Entries (RSS)','big-blue');?></a> <?php _e('and','big-blue');?> <a href="<?php bloginfo('comments_rss2_url'); ?>"><?php _e('Comments (RSS)','big-blue');?></a>. <?php _e('Theme by','big-blue');?> <a href="http://www.blogohblog.com">Bob</a>	
  <?php wp_footer(); ?></div>
</div></body>
</html>