<?php /* Mystique/digitalnature */ ?>

<!-- comment entry -->
<li>
  <div id="comment-<?php comment_ID(); ?>" <?php comment_class('clear-block'); ?>>

    <?php if(get_option('show_avatars')): ?>
    <div class="avatar">
      <?php echo get_avatar($comment, 48); ?>
    </div>
    <?php endif; ?>

    <div class="comment-head">
      <div class="ext clear-block">
        <?php
           printf(__('%1$s %2$s', 'mystique'),
             sprintf('<span class="a">%s</span>', get_comment_author_link()),
             sprintf('<span class="d">(%s)</span>', human_time_diff(get_comment_date('U')))
          );
        ?>
       </div>
    </div>

    <div class="comment-body" id="comment-body-<?php comment_ID(); ?>">
       <div class="comment-content clear-block" id="comment-content-<?php comment_ID(); ?>">

         <?php if($comment->comment_approved == '0'): ?>
         <p class="error"><em><?php if($comment->user_id == $user_ID) _e('Your comment is awaiting moderation.', 'mystique'); else _e('This comment is awaiting moderation.', 'mystique'); ?></em></p>
         <?php endif; ?>

         <div class="comment-text">
           <?php comment_text(); ?>
         </div>

         <a id="comment-reply-<?php comment_ID(); ?>"></a>
       </div>

       <div class="controls">
         <?php edit_comment_link(); ?>
         <?php
           comment_reply_link(array(
             'reply_text' => __('Reply <span>&darr;</span>', 'mystique'),
             'depth'      => $GLOBALS['comment_depth'],
             'max_depth'  => get_option('thread_comments_depth'),
             'add_below'  => 'comment-content',
           ));
         ?>
       </div>

    </div>

  </div>
<?php // </li> is added by WP  ?>