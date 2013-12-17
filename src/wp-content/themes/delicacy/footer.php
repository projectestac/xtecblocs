	</div><!-- end #inner-wrapper -->
</div><!-- end #wrapper -->

		<div id="footer">
<?php
			// XTEC ***** ELIMINAT - We remove the copyright referencer that make no sense in the blogs context
			// 2013.12.17 Marc Espinosa Zamora <marc.espinosa.zamora@upcnet.es> 
			// CODI ORIGINAL 
			//<p>Copyright <?php echo delicacy_copy_date(); ?> <strong><a href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a></strong></p>
			// ***** FI
?>
			<div id="site-generator">
			
				<small><?php _e('Proudly powered by', 'delicacy'); ?> <a href="http://wordpress.org" target="_blank"><?php _e('WordPress', 'delicacy'); ?></a>. <?php _e('Design by ', 'delicacy'); ?> <a href="http://webtuts.pl/" title="<?php _e('WebTuts.pl', 'delicacy'); ?>" target="_blank">WebTuts.pl</a></small>

			</div><!-- #site-generator -->
		</div>

	<?php wp_footer(); ?>
	
	<!-- Don't forget analytics -->
	
</body>

</html>
