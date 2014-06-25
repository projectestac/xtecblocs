<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Topic tag edit template.
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

          <h1 class="title"><?php atom()->te('Topic Tag: %s', sprintf('<span>%s<span>', bbp_get_topic_tag_name())); ?></h1>

          <?php
           atom()->action('before_primary');
           atom()->Breadcrumbs();
           bbp_topic_tag_description();
           do_action('bbp_template_before_topic_tag_edit');
           bbp_get_template_part('templates/form', 'topic-tag');
           do_action('bbp_template_after_topic_tag_edit');
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
