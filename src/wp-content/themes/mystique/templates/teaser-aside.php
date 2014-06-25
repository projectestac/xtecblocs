<?php

/*
 * @template  Mystique
 * @revised   October 30, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Post preview template for the "aside" post format.
// See teaser.php for more details...

?>

<!-- aside post -->
<div id="post-<?php the_ID(); ?>" <?php post_class('clear-block'); ?>>

  <?php if(atom()->options('post_date')): ?>
    <p><strong><?php atom()->post->date(); ?></strong></p>
  <?php endif; ?>

  <?php the_content(); ?>
  <?php atom()->controls('post-edit'); ?>
</div>
<!-- /aside post -->
