<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// 404 page.
// This template is used as a placeholder for pages that don't exist on your site.

?>

<?php atom()->template('header'); ?>

  <!-- main content: primary + sidebar(s) -->
  <div id="mask-3" class="clear-block">
    <div id="mask-2">
      <div id="mask-1">

        <!-- primary content -->
        <div id="primary-content">
          <div class="blocks clear-block">

            <h1 class="title">404</h1>

            <?php atom()->action('before_primary'); ?>

            <p><?php atom()->te('The requested page was not found.'); ?></p>

            <?php atom()->action('after_primary'); ?>

          </div>
        </div>
        <!-- /primary content -->

      </div>
    </div>
  </div>
  <!-- /main content -->

<?php atom()->template('footer'); ?>