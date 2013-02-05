		<div id="left_sidebar">
<ul>

<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(1) ) : else : ?>

	<?php wp_list_pages('depth=3&title_li=<h2>' . __('Pages','quadruple-blue') . '</h2>'); ?>

	<?php get_links_list(); ?>


	<li><h2><?php _e('Meta','quadruple-blue'); ?></h2>
		<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS','quadruple-blue'); ?>" class="feed"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>','quadruple-blue'); ?></a></li>
			<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('Syndicate comments using RSS','quadruple-blue'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>','quadruple-blue'); ?></a></li>
			<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional"><?php _e('Valid'); ?> <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
			<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
			<?php wp_meta(); ?>
		</ul>
	</li>

<?php endif; ?>

</ul>
		</div><!-- end sidebar -->