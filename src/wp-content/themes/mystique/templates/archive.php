<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// General Archive template.
// There are quite a few templates that can override this one: http://codex.wordpress.org/Template_Hierarchy

?>

<?php atom()->template('header'); ?>

  <!-- main content: primary + sidebar(s) -->
  <div id="mask-3" class="clear-block">
    <div id="mask-2">
      <div id="mask-1">

        <!-- primary content -->
        <div id="primary-content">
          <div class="blocks clear-block">

            <?php if(is_category()): ?>
            <h1 class="title archive-category"><?php atom()->term->Title(); ?></h1>
            <?php if(atom()->term->getDescription()): ?>
            <div class="large">
              <p><em><?php atom()->term->Description(); ?></em></p>
            </div>
            <div class="divider"></div>
            <?php endif; ?>            

            <?php elseif(is_tag()): ?>
            <h1 class="title"><?php atom()->te('Posts tagged %s', sprintf('<span class="alt">%s</span>', atom()->term->getTitle())); ?></h1>
            <?php elseif(is_day()): ?>
            <h1 class="title"><?php atom()->te('Archive for %s', sprintf('<span class="alt">%s</span>', get_the_date())); ?></h1>
            <?php elseif(is_month()): ?>
            <h1 class="title"><?php atom()->te('Archive for %s', sprintf('<span class="alt">%s</span>', get_the_time('F, Y'))); ?></h1>
            <?php elseif(is_year()): ?>
            <h1 class="title"><?php atom()->te('Archive for year %s', sprintf('<span class="alt">%s</span>', get_the_time('Y'))); ?></h1>
            <?php else: ?>
            <h1 class="title"><?php atom()->te('Blog Archives'); ?></h1>
            <?php endif; ?>

            <?php atom()->action('before_primary'); ?>

            <?php if(have_posts()): ?>
            <div class="posts clear-block">
              <?php while(have_posts()) atom()->template('teaser'); ?>
            </div>

            <?php atom()->pagination(); ?>

            <?php else: ?>

             <?php if(is_category()): ?>
            <h1 class="title"> <?php atom()->te("There aren't any posts in the %s category yet :(", atom()->term->getTitle()); ?></h1>
             <?php elseif(is_date()): ?>
            <h1 class="title"> <?php atom()->te("There aren't any posts within this date :("); ?> </h1>
             <?php else: ?>
            <h1 class="title"><?php atom()->te('Nothing here :('); ?></h1>
             <?php endif; ?>

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
