<!-- Start Sidebar -->

	<div class="sidebar">
<ul>

<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(2) ) : else : ?>

	<li><h2><?php _e('Calendar', 'digg-3'); ?></h2>
		<ul>
			<li><?php get_calendar(); ?></li>
		</ul>
	</li>

	<?php get_links_list(); ?>

<?php endif; ?>

	<li><h2><?php _e('Meta', 'digg-3'); ?></h2>
		<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional', 'digg-3');?>"><?php _e('Valid', 'digg-3'); ?> <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
			<li><a href="http://gmpg.org/xfn/"><?php _e('<abbr title="XHTML Friends Network">XFN</abbr>')?></a></li>
			<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
			<li><a href="http://www.wpdesigner.com/" title="Theme by WPDesigner">WPDesigner</a></li>

			<?php wp_meta(); ?>
		</ul>
	</li>

</ul>
	</div>

<!-- End Sidebar -->
