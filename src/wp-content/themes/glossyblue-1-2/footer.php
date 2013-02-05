  <hr class="clear" />
</div><!--/page -->
<div id="credits">

<div class="alignleft">
		<?php $current_site = get_current_site(); ?>
  		<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a>
<a href="http://www.ndesign-studio.com/resources/wp-themes">WordPress Theme</a> &amp; <a href="http://www.ndesign-studio.com/stock-icons/">Icons</a> <?php _e('by','glossy-blue');?> <a href="http://www.ndesign-studio.com">N.Design Studio</a>.  

</div> 
<div class="alignright"><a href="<?php bloginfo('rss2_url'); ?>" class="rss"><?php _e('Entries RSS','glossy-blue');?></a> <a href="<?php bloginfo('comments_rss2_url'); ?>" class="rss"><?php _e('Comments RSS','glossy-blue');?></a> <span class="loginout"><?php wp_loginout(); ?></span></div>
</div>
<?php wp_footer(); ?>
</body>
</html>
