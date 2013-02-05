
<hr />
<div id="footer">
	<p>
		<?php $current_site = get_current_site(); ?>
  		<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a> | 
		<?php _e('Powered by <a href=\'http://wordpress.org\' title=\'%s\'><strong>WordPress</strong></a>','steam');?>
		<br /><a href="<?php bloginfo('rss2_url'); ?>">Entries (RSS)</a>
		<?php _e('and','steam')?> <a href="<?php bloginfo('comments_rss2_url'); ?>">Comments (RSS)</a>.

	</p>
</div>
</div>

<!--
	Gorgeous design by Samir M. Nassar - http://steamedpenguin.com/design/Steam/
	Based on Kubrick by Michael Heilemann - http://binarybonsai.com/kubrick/
-->
<?php /* "Just what do you think you're doing Dave?" */ ?>

		<?php do_action('wp_footer'); ?>

</body>
</html>
