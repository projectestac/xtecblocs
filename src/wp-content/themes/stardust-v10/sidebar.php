
<!-- begin sidebar -->
<div id="menu">

<ul>
<?php 
		if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
	<?php wp_list_pages('title_li=' . __('Pages:')); ?>
	<?php wp_list_bookmarks('title_after=&title_before='); ?>
	<?php wp_list_categories('title_li=' . __('Categories:')); ?>
    
 <li id="archives"><?php _e('Archives:'); ?>
	<ul>
	 <?php wp_get_archives('type=monthly'); ?>
	</ul>
 </li>
 <li id="meta"><?php _e('Meta:'); ?>
	<ul>
		<?php wp_register(); ?>
		<li><?php wp_loginout(); ?></li>
    <?php include TEMPLATEPATH . '/rsssidebar.php' ?>
		<li><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional'); ?>"><?php _e('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>'); ?></a></li>
		<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
		<li><a href="http://wordpress.org/" title="<?php _e('Powered by WordPress, state-of-the-art semantic personal publishing platform.'); ?>"><abbr title="WordPress">WP</abbr></a></li>
		<?php wp_meta(); ?>
	</ul>
 </li>
<?php endif; ?>

</ul>

</div>
<!-- end sidebar -->
