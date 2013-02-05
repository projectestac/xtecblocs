<?php include (TEMPLATEPATH . '/right_sidebar.php'); ?>

		</div></div><!-- wide column wrap -->

	</div><!-- end page -->

	<div id="footer">

<p>
		<?php $current_site = get_current_site(); ?>
  		<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a>
<?php bloginfo('name'); ?> <?php _e('Powered by <a href=\'http://wordpress.org\' title=\'%s\'><strong>WordPress</strong></a>','quadruple-blue');?> <?php _e('and','quadruple-blue');?> <a href="http://www.wpdesigner.com" title="WordPress Themes">WPDesigner</a>. </p>
	</div>

</div>

<?php wp_footer(); ?>
</body>
</html>
