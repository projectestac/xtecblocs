</div> <!-- #content -->

<?php
// This code pulls in the sidebar:
include(get_template_directory() . '/sidebar.php');
?>
<div style="clear:both;height:1px;"> </div>
</div><!-- #wrap -->

<p class="credit">
		<?php $current_site = get_current_site(); ?>
  		<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a>
<cite><?php echo sprintf(__("Powered by <a href='http://wordpress.org' title='%s'><strong>WordPress</strong></a>",'anarchy'), __("Powered by WordPress, state-of-the-art semantic personal publishing platform.",'anarchy')); ?>.
</cite></p><?php do_action('wp_footer', ''); ?>

</body>
</html>
