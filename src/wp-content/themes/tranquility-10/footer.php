<!-- begin footer -->
</div>

</div>

<div class="credit">
	<p><?php $current_site = get_current_site(); ?><a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a>
		<?php _e('Powered by <a href=\'http://wordpress.org\' title=\'%s\'><strong>WordPress</strong></a>','tranquility');?>  | <?php _e('Theme by','tranquility');?> <a href="http://www.roytanck.com">Roy Tanck</a></p>
</div>



<?php wp_footer(); ?>
</body>
</html>