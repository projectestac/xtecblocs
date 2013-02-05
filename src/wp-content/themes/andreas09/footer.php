</div>
<div class="clearingdiv">&nbsp;</div>
</div>
<?php $current_site = get_current_site(); ?>
<div id="footer">
<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a>
<br /><?php _e('Powered by <a href=\'http://wordpress.org\' title=\'%s\'><strong>WordPress</strong></a>','andreas09');?> &amp; <?php _e('was designed by','andreas09');?> <a href="http://andreasviklund.com">Andreas Viklund</a>.


</div>

<?php wp_footer(); ?>

</body>

</html>
