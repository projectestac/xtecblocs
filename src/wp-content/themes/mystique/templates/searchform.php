<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Search form, mainly used by the search widget.
// This is a template part.

?>

<!-- search form -->
<div class="search-form" role="search">
  <form method="get" class="search-form clear-block" action="<?php echo home_url('/'); ?>">
    <input type="submit" class="submit" title="<?php atom()->te('Search Website'); ?>" value="" />
    <fieldset>
      <input type="text" name="s" data-default="<?php atom()->te('Search Website'); ?>" class="text alignleft clearField suggestTerms" value="" />
      <input type="hidden" value="submit" />
    </fieldset>
 </form>
</div>
<!-- /search form -->