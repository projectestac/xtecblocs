
<hr style="display:none"/>

<div id="footer">
	<small class="footer_content">
		<a href="<?php bloginfo('rss2_url'); ?>"><img alt="<?php _e('rss', 'freshy'); ?>" src="<?php bloginfo('stylesheet_directory'); ?>/images/rss_blog.gif"/></a>
		<a href="<?php bloginfo('comments_rss2_url'); ?>"><img alt="<?php _e('Comments rss', 'freshy'); ?>" src="<?php bloginfo('stylesheet_directory'); ?>/images/rss_<?php _e('comments', 'freshy'); ?>.gif"/></a>
		<a href="http://validator.w3.org/check?uri=referer"><img alt="valid xhtml 1.1" src="<?php bloginfo('stylesheet_directory'); ?>/images/valid_xhtml11_80x15_2.png"/></a>
		<a href="http://www.jide.fr"><img alt="design by jide" src="<?php bloginfo('stylesheet_directory'); ?>/images/micro_jide.png"/></a>
		<a href="http://wordpress.org/"><img alt="powered by Wordpress" src="<?php bloginfo('stylesheet_directory'); ?>/images/get_wordpress_80x15_2.png"/></a>
		<a href="http://www.mozilla.com/firefox"><img alt="<?php _e('get firefox', 'freshy'); ?>" src="<?php bloginfo('stylesheet_directory'); ?>/images/get_firefox_80x15.png"/></a>
		<br/>
		<?php $current_site = get_current_site(); ?>
		<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?>
		</a> - <?php _e('Powered by <a href=\'http://wordpress.org\' title=\'%s\'><strong>WordPress</strong></a>', 'freshy');?> <?php _e('&','freshy')?> <?php _e('was designed by', 'freshy');?> <a href='http://www.jide.fr/'>Julien De Luca</a>.


	</small>
	
</div>

</div>

</body>
</html>
<?php wp_footer(); ?>