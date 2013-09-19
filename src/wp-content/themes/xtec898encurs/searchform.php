<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
<div><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
<input type="submit" name="searchsubmit" id="searchsubmit" value="<?php _e('Search','encurs');?>" />
</div>
</form>