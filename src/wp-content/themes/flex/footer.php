<!-- begin footer -->
<div id="footer">
		<p>
		<?php $current_site = get_current_site(); ?>
  		<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a> | 
			<a href="http://www.ifelse.co.uk/flex">Flex theme</a> <?php _e('was designed by', 'flex');?> <a href="http://www.ifelse.co.uk">Phu Ly</a>.
			<?php _e('Powered by <a href=\'http://wordpress.org\' title=\'%s\'><strong>WordPress</strong></a>', 'flex');?>.
		</p> 
	</div>
</div>
<?php do_action('wp_footer'); ?>
</body>
</html>
