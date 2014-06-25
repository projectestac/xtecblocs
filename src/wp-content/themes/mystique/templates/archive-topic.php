<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Topic archive template.
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

          <?php do_action('bbp_template_notices'); ?>

          <h1 class="title"><?php bbp_topic_archive_title(); ?></h1>

          <?php

            atom()->action('before_primary');
            atom()->Breadcrumbs();
            do_action('bbp_template_before_topics_index');

            if(bbp_has_topics())
              bbp_get_template_part('templates/loop', 'topics');              

            else
              atom()->te('There are no forums here yet :(');
            
            do_action('bbp_template_after_topics_index');
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
