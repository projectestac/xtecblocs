<!-- begin footer -->
</div>
<div id="footer">
		<p>
		<?php $current_site = get_current_site(); ?>
  		<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a> | 
		<a href="http://www.ifelse.co.uk/gentle-calm">Gentle Calm</a> <?php _e('was designed by','gentle-calm');?> <a href="http://www.ifelse.co.uk">Phu Ly</a>. 
			<?php _e('Powered by <a href=\'http://wordpress.org\' title=\'%s\'><strong>WordPress</strong></a>','gentle-calm');?>.
			<a href="http://validator.w3.org/check/referer" title="Validate this document as XHTML 1.0 @ W3C.org" >XHTML</a>.
		</p>
	</div>

<?php do_action('wp_footer'); ?>
</body>
</html>
