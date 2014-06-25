<?php

/*
 * @template  Mystique
 * @revised   October 30, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// The home or blog page template.
// By default, the newest posts are listed here.

?>

<?php atom()->template('header'); ?>

<!-- main content (masks): primary + sidebar(s) -->
<div id="mask-3" class="clear-block">
  <div id="mask-2">
    <div id="mask-1">

      <!-- primary content -->
      <div id="primary-content">
        <div class="blocks clear-block">

          <?php atom()->action('before_primary'); ?>

          <?php if(have_posts()): ?>
          <div class="posts clear-block">
            <?php while(have_posts()) atom()->template('teaser'); ?>
          </div>

          <?php atom()->pagination(); ?>

          <?php else: ?>
          <h1 class="title error"><?php atom()->te('Oops, nothing here :('); ?></h1>
          <?php endif; ?>

          <?php atom()->action('after_primary'); ?>

        </div>
      </div>
      <!-- /primary content -->

      <?php atom()->template('sidebar'); ?>

    </div>
  </div>

</div>
<!-- /main content -->

<?php atom()->template('footer'); ?>
