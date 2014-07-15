<?php /* Mystique/digitalnature */ ?>

<!-- search form -->
<div class="search-form">
  <form method="get" class="search-form clear-block" name="search-form" id="search-form" action="<?php echo home_url(); ?>/">
    <a href="#" class="submit" onclick="document.getElementById('search-form').submit();"><?php _e('Search Website', 'mystique'); ?></a>
    <fieldset>
      <input type="text" name="s" alt="<?php _e('Search Website', 'mystique'); ?>" class="text alignleft" value="" placeholder="<?php _e('Search', 'mystique'); ?>" />
      <input type="hidden" value="submit" />
    </fieldset>
  </form>
</div>
<!-- /search form -->