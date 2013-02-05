<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
<div><input type="text" value="<?php echo wp_specialchars($s, 1); ?>" name="s" id="s" /> 
<input type="image" id="searchsubmit" src="<?php echo bloginfo('stylesheet_directory'); ?>/images/<?php echo get_option('mandigo_scheme'); ?>/search.gif" onmouseover="hover(1,'searchsubmit','search');" onmouseout="hover(0,'searchsubmit','search')" />
</div>
</form>
