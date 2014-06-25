<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Renders the "related posts" list (optional meta tab).
// You can use any of the AtomPost (atom()->post) methods within the iterator below, even do hierarchical related post queries...
//
// Note: The query can be altered with setContextArgs('related_posts', ...)

?>

<?php if(atom()->post->related): ?>

<ol>
  <?php foreach(atom()->post->related as $post): ?>
  <li><a href="<?php $post->URL(); ?>" title="<?php atom()->te('Permanent Link: %s', $post->getTitle()); ?>"><?php $post->Title(); ?></a></li>
  <?php endforeach; ?>
</ol>

<?php else: ?>

<p><?php atom()->te("Didn't find any related posts :("); ?></p>

<?php endif; ?>
