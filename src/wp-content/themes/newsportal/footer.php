  <div id="footer">
	<?php $current_site = get_current_site(); ?>
      <p>
		<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a>
		<br /><?php _e('Powered by <a href=\'http://wordpress.org\' title=\'%s\'><strong>WordPress</strong></a>');?> &amp; <?php _e('was designed by','news-portal');?> <a href="http://www.unitedpunjab.com/">Gurpartap Singh</a> / 
		<a href="http://wwww.kaushalsheth.info">Kaushal Sheth</a>. 
	</p>
  </div>

  <?php //XTEC ************ AFEGIT - Standardizing theme ?>
  <?php //2011.04.07 @fbassas ?>
  
  <?php wp_footer(); ?>
  </body>
  </html>
  
  <?php //************ FI ?>