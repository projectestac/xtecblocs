<?php /*

  Hi,

  Please DO NOT remove the link to my website from the footer. I have been working hard to make this theme and you have downloaded it for FREE. This is all I ask from you in return for Mandigo which didn't cost you a cent.

  Thank you.

  tom

*/ ?>
<div id="footer">
	<p>
	<?php $current_site = get_current_site(); ?>
        <p> <a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a> - 
<?php _e('Powered by <a href=\'http://wordpress.org\' title=\'%s\'><strong>WordPress</strong></a>','mandigo');?> &amp; <?php _e('was designed by','mandigo');?> <a href='http://www.onehertz.com/portfolio/'>Tom</a>.

		<br /><a href="feed:<?php bloginfo('rss2_url'); ?>"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/<?php echo get_option('mandigo_scheme'); ?>/rss_s.gif" alt="" /> <?php _e('Entries (RSS)','mandigo');?></a>
		<?php _e('and');?> <a href="feed:<?php bloginfo('comments_rss2_url'); ?>"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/<?php echo get_option('mandigo_scheme'); ?>/rss_s.gif" alt="" /> <?php _e('Comments (RSS)','mandigo');?></a>.

	</p>
</div>
</div>

		<?php wp_footer(); ?>
</body>
</html>
