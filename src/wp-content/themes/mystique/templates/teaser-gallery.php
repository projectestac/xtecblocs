<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Post preview template for the "gallery" post format -- incomplete.
// See teaser.php for more details...

?>

<!-- post format:gallery -->
<div id="post-<?php the_ID(); ?>" <?php post_class('clear-block'); ?>>

  <?php if(atom()->options('post_title')): ?>
  <h2 class="title">
    <a href="<?php atom()->post->URL(); ?>" rel="bookmark" title="<?php atom()->te('Permanent Link: %s', atom()->post->getTitle()); ?>">
      <?php atom()->post->title(); ?>
    </a>
  </h2>
  <?php endif; ?>

  <?php
    $attachments = atom()->post->getGallery();
    foreach($attachments as $att):
      list($source, $width, $height) = wp_get_attachment_image_src($att->ID, 'post-thumbnail'); ?>
      <a class="post-thumb" href="<?php echo wp_get_attachment_url($att->ID); ?>"><img src="<?php echo $source; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" /></a>
      <?php
    endforeach;
  ?>
  <?php atom()->controls('post-edit'); ?>
</div>
<!-- /post -->
