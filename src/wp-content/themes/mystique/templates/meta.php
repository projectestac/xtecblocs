<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// This template styles the meta sections on singular type pages: comments, pings, related posts and author bio.
// The template replaces the comments.php file found in generic themes, because we handle more than just comments here.
// For custom post types, a meta-post_type.php template overrides this one.


 // don't show this section if we're on a page with comments disabled and without any comments or pings in it
 // (we're assuming the site admin doesn't want this section on such pages)
 if(post_password_required() || (!comments_open() && !is_single('post') && atom()->post->getCommentCount() < 1)) return;

?>

<div class="tabs meta" id="meta" data-fx="fade">

  <ul class="navi clear-block">
    <li class="active">
      <a href="#comments"><?php atom()->te('Comments (%s)', atom()->post->getCommentCount()); ?></a>
    </li>

    <?php if(atom()->post->getPingCount() > 0): ?>
    <li><a href="#pings"><?php atom()->te('Pings (%s)', atom()->post->getPingCount()); ?></a></li>
    <?php endif; ?>

    <?php if(atom()->options('single_related')): ?>
    <li><a href="#related-posts"><?php atom()->te('Related Posts'); ?></a></li>
    <?php endif; ?>

    <?php if(atom()->options('single_author')): ?>
    <li><a href="#about-the-author"><?php atom()->te('About %s', atom()->post->author->getName()); ?></a></li>
    <?php endif; ?>

  </ul>

  <div class="sections">

    <ul class="section clear-block" id="comments">

      <?php if(get_option('comment_order') == 'desc'): ?>
      <li class="new">
        <?php atom()->template('commentform'); ?>
      </li>
      <?php endif; ?>

      <?php atom()->post->comments(); ?>

      <?php if(get_option('comment_order') != 'desc'): ?>
      <li class="new">
        <?php atom()->template('commentform'); ?>
      </li>
      <?php endif; ?>

      <li class="clear-block">
        <?php atom()->commentNavi($class = 'alignleft'); ?>
        <a class="rss-block alignright" rel="rss" href="<?php echo get_post_comments_feed_link(); ?>"><?php atom()->te('Comment Feed for this Post'); ?></a>
      </li>

      <?php // atom()->template('comments'); // @todo (use comment iterator instead) ?>
    </ul>


    <?php if(atom()->post->getPingCount() > 0): ?>
    <ul class="section hidden clear-block" id="pings">
      <?php atom()->post->comments($type = 'ping'); ?>
    </ul>
    <?php endif; ?>

    <?php if(atom()->options('single_related')): ?>
    <div class="section hidden clear-block" id="related-posts">
      <?php atom()->template('related-posts'); ?>
    </div>
    <?php endif; ?>

    <?php if(atom()->options('single_author')): ?>
    <div class="section hidden clear-block" id="about-the-author">
      <?php atom()->template('about-the-author'); ?>
    </div>
    <?php endif; ?>

  </div>
</div>