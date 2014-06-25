<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// "About the Author" template, optional meta tab.
// This is a template part.

?>

<div class="clear-block">
  <div class="avatar alignleft">
    <a href="<?php atom()->post->author->PostsURL(); ?>" title="<?php atom()->post->author->Name(); ?>">
      <?php atom()->post->author->Avatar($size = 160); ?>
    </a>
  </div>

  <h5>
    <?php atom()->te('About %s', atom()->post->author->getName()); ?>
    (<a href="<?php atom()->post->author->PostsURL(); ?>"><?php atom()->nte('%s post', '%s posts', atom()->post->author->getPostCount(), atom()->post->author->getPostCount()); ?></a>)
  </h5>

  <?php if(atom()->post->author->getDescription($fallback = atom()->post->getMeta('author', true))): ?>
  <div class="author-description">
    <?php atom()->post->author->Description(); ?>
  </div>

  <?php else: ?>
  <p><?php atom()->te('Nothing here :('); ?></p>
  <?php endif; ?>

</div>

<a class="rss-block alignright" rel="rss" href="<?php atom()->post->author->FeedURL(); ?>"><?php atom()->te("%s's RSS Feed", atom()->post->author->getName()); ?></a>
