<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Tag clould.
// This is a custom page template that can be applied to individual pages.


/* Template Name: Tag Cloud */

?>

<?php

  // force gettext parsers to include this string
  if(true === false)
    atom()->t('Tag Cloud');

  atom()->template('header');

  // max. # of tags
  $number = atom()->post->getMeta('number');
  $number = $number ? (int)$number : 200;

?>

<!-- main content: primary + sidebar(s) -->
<div id="mask-3" class="clear-block">
  <div id="mask-2">
    <div id="mask-1">

      <!-- primary content -->
      <div id="primary-content">
        <div class="blocks clear-block">

          <?php atom()->action('before_primary'); ?>

          <?php the_post(); ?>

          <?php atom()->action('before_post'); ?>

          <!-- post -->
          <div id="post-<?php the_ID(); ?>" <?php post_class('primary'); ?>>

            <?php if(!atom()->post->getMeta('hide_title')): ?>
            <h1 class="title"><?php the_title(); ?></h1>
            <?php endif; ?>

            <div class="clear-block">
              <?php the_content(); ?>

              <div class="tagcloud large">
                <?php
                  echo AtomWidgetTagCloud::tagCloud(array(
                    'taxonomy'       => 'post_tag',
                    'number'         => $number,
                    'smallest'       => 8,
                    'largest'        => 48,
                    'gradient_start' => 'cccccc',
                    'gradient_end'   => '333333',
                  ));
                ?>
              </div>
            </div>

            <?php atom()->post->pagination(); ?>

            <?php atom()->controls('post-edit'); ?>
          </div>
          <!-- /post -->

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
