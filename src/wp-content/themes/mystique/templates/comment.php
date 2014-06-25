<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Renders a single comment.
// This is a template part.

?>

<!-- comment entry -->
<li class="entry">
  <div id="comment-<?php comment_ID(); ?>" <?php comment_class('clear-block'); ?>>

    <?php if(get_option('show_avatars')): ?>
    <div class="avatar">
      <?php atom()->comment->avatar($size = 48); ?>
    </div>
    <?php endif; ?>

    <div class="comment-head">
      <div class="ext clear-block">
        <div class="alignleft">
         <?php
          atom()->te('%1$s written by %2$s %3$s',
                 sprintf('<a class="comment-id" href="#comment-%d">#%d</a>', get_comment_ID(), atom()->comment->getNumber()),
                 atom()->comment->getAuthorAsLink(),
                 sprintf('<span class="d">%s</span>', atom()->comment->getDate()));
         ?>
        </div>
        <?php atom()->comment->karma('alignright'); ?>
      </div>
    </div>

    <?php if(!atom()->comment->isBuried()): ?>
    <div class="comment-body" id="comment-body-<?php comment_ID(); ?>">
       <div class="comment-content clear-block" id="comment-content-<?php comment_ID(); ?>">

         <?php if(!atom()->comment->isApproved()): ?>
         <p class="error">
           <em><?php if(atom()->comment->belongsToCurrentUser()) atom()->te('Your comment is awaiting moderation.'); else atom()->te('This comment is awaiting moderation.'); ?></em>
         </p>
         <?php endif; ?>

         <div class="comment-text">
           <?php comment_text(); ?>
         </div>

         <a id="comment-reply-<?php comment_ID(); ?>"></a>
       </div>

       <?php atom()->controls('comment-edit', 'comment-delete', 'comment-spam', 'comment-approve', 'comment-reply', 'comment-quote'); ?>

    </div>
    <?php endif; ?>
  </div>

  <?php // </li> is added by WP  ?>