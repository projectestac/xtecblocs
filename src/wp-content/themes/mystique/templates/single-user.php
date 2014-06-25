<?php

/*
 * @template  Mystique
 * @revised   November 16, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// User page template.
// For use with the bbPress plugin only.

?>

<?php atom()->template('header'); ?>

<!-- main content: primary + sidebar(s) -->
<div id="mask-3" class="clear-block">
  <div id="mask-2">
    <div id="mask-1">

      <!-- primary content -->
      <div id="primary-content">
        <div class="blocks clear-block">

          <?php

            atom()->action('before_primary');
            do_action('bbp_template_notices');

            // Profile details
            bbp_get_template_part('templates/user', 'details');

            // Subscriptions
            bbp_get_template_part('templates/user', 'subscriptions');

            // Favorite topics
            bbp_get_template_part('templates/user', 'favorites');

            // Topics created
            bbp_get_template_part('templates/user', 'topics-created');

            atom()->action('after_primary');
          ?>

        </div>
      </div>
      <!-- /primary content -->

      <?php atom()->template('sidebar'); ?>

    </div>
  </div>
</div>
<!-- /main content -->

<?php atom()->template('footer'); ?>
