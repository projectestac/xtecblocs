<form method="get" id="searchform" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div><input type="text" value="<?php echo wp_specialchars($s, 1); ?>" name="s" id="s" />
<input type="submit" id="searchsubmit" value="<?php _e('Search','steam');?>" />
</div>
</form>
