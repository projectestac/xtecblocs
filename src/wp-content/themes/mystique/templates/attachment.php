<?php

/*
 * @template  Mystique
 * @revised   April 4th, 2012
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Generic template for handling attachments.
// Image attachments should have their own template inside this theme...

?>

<?php atom()->template('header'); ?>

<!-- main content: primary + sidebar(s) -->
<div id="mask-3" class="clear-block">
  <div id="mask-2">
    <div id="mask-1">

      <!-- primary content -->
      <div id="primary-content">
        <div class="blocks clear-block">

          <?php atom()->action('before_primary'); ?>

          <?php atom()->action('before_post'); ?>

          <!-- post content -->
          <div id="post-<?php the_ID(); ?>" <?php post_class('primary'); ?>>

            <?php if(!atom()->post->getMeta('hide_title')): ?>
            <h1 class="title">
              <?php if(atom()->post->getParent()): ?>
              <a href="<?php echo get_permalink(atom()->post->getParent()); ?>"><?php echo get_the_title(atom()->post->getParent()); ?></a> &rarr;
              <?php endif; ?>
              <?php atom()->post->Title(); ?>
            </h1>
            <?php endif; ?>

            <div class="post-content clear-block">

              <?php the_content(); ?>

              <div class="divider"></div>

              <h3><?php atom()->te('Attached file:'); ?></h3>
              <a href="<?php echo wp_get_attachment_url(); ?>"><?php echo basename(wp_get_attachment_url()); ?></a>

            </div>

            <?php atom()->post->pagination(); ?>

            <?php atom()->controls('post-edit'); ?>

          </div>
          <!-- /post content -->

          <?php atom()->action('after_post'); ?>

          <?php atom()->template('meta'); ?>

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
