</div>
<div id="footer">


<?php $current_site = get_current_site(); ?>
<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a>
		<br /><?php _e('Powered by <a href=\'http://wordpress.org\' title=\'%s\'><strong>WordPress</strong></a>','simpla');?> &amp; <?php _e('was designed by','simpla');?> <a href="http://ifelse.co.uk">Phu Ly</a>

<?php wp_footer(); ?>
</div>

</body>
</html>
