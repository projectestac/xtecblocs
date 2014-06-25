<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Single view template.
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

          <div id="bbp-view-<?php bbp_view_id(); ?>" class="bbp-view">

            <h1 class="title"><?php bbp_view_title(); ?></h1>

            <div class="entry-content">

              <?php

                atom()->action('before_primary');
                atom()->Breadcrumbs();
                bbp_set_query_name('bbp_view');

                if(bbp_view_query())                  
                  bbp_get_template_part('templates/loop', 'topics');                  

                else
                  atom()->te('No topics found.');

                bbp_reset_query_name();

                atom()->action('after_primary');
              ?>

            </div>
          </div>

        </div>
      </div>
      <!-- /primary content -->

      <?php atom()->template('sidebar'); ?>

    </div>
  </div>
</div>
<!-- /main content -->

<?php atom()->template('footer'); ?>
