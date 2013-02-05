<?php /*$current_site = get_current_site(); */ ?>
<hr />
<div id="footer">
<a href="http://blocs.xtec.cat/" class="xtecblocs"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/xtecblocs.gif" alt="XTEC Blocs" class="xtec" /></a>
<!-- If you'd like to support WordPress, having the "powered by" link somewhere on your blog is the best way, it's our only promotion or advertising. -->
	<p>
		<em style="font-style: italic"><?php bloginfo('name'); ?></em> és a <a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a> i funciona amb <a href="http://mu.wordpress.org/">WordPress MU</a>
		<br />Vegeu-ne els <a href="<?php bloginfo('rss2_url'); ?>"><?php _e('Entries (RSS)','encurs');?></a>
		i els <a href="<?php bloginfo('comments_rss2_url'); ?>"><?php _e('Comments (RSS)','encurs');?></a><br />
		Theme by <a href="http://www.bezerik.net" target="_blank">Xavier Gomez (Bezerik)</a>
	</p>
	<p class="validate">
	<a href="http://jigsaw.w3.org/css-validator/validator?uri=referer"><img
        src="http://www.w3.org/Icons/valid-css2-blue"
        alt="Valid CSS level 2" height="31" width="88" /></a>
	</p>
	<p class="validate">
    <a href="http://validator.w3.org/check?uri=referer"><img
        src="http://www.w3.org/Icons/valid-xhtml10-blue"
        alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
	</p>

</div>
</div>
</div>

  <?php wp_footer(); ?>
</body>
</html>
