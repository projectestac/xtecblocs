<div id="footer">
<p>
<?php $current_site = get_current_site(); ?>
<a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a>
&#183;
<a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional','almost-spring'); ?>">
<?php _e('<abbr title="eXtensible HyperText Markup Language">XHTML</abbr>','almost-spring'); ?></a>
&#183;
<a href="http://jigsaw.w3.org/css-validator/check/referer" title="<?php _e('This page validates as CSS','almost-spring'); ?>">
<?php _e('<abbr title="Cascading Style Sheets">CSS</abbr>','almost-spring'); ?></a>.

</p>
</div>

</div>

<?php do_action('wp_footer'); ?>

</body>
</html>
