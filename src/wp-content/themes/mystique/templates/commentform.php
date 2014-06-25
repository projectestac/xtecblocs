<?php

/*
 * @template  Mystique
 * @revised   October 30, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// A nicer alternative to WP's comment_form() function, which doesn't give us full control over the output.
// This is a template part.

?>


<?php

  global $user_identity, $allowedtags;
  ksort($allowedtags);

  // get allowed tags
  $allowed = '';
  foreach($allowedtags as $tag => $attributes)
    $allowed .= sprintf('<code>%s</code> ', htmlentities("<{$tag}>"));

?>

<!-- comment form -->
<div class="comment new <?php if(get_option('show_avatars')) echo 'with-avatars'; ?>">

  <?php if(comments_open()): ?>
  <?php do_action('comment_form_before'); ?>
  <div id="respond">

    <?php if(get_option('comment_registration') && !is_user_logged_in()) : ?>
      <div class="error box"><?php sprintf(__('You must be <a href="%s">logged in</a> to post a comment.'), wp_login_url(apply_filters('the_permalink', get_permalink()))); ?></div>
    <?php do_action('comment_form_must_log_in_after'); ?>
    <?php else: ?>
     <form action="<?php echo site_url('/wp-comments-post.php'); ?>" method="post" id="commentform">

      <?php do_action('comment_form_top'); ?>

      <?php if(get_option('show_avatars')): ?>
      <div id="user-avatar" class="avatar">
        <?php echo atom()->getAvatar(atom()->commenter['comment_author_email'], 48, false, atom()->commenter['comment_author']);  ?>
      </div>
      <?php endif; ?>

      <div class="comment-head">
        <div class="ext clear-block">
        <?php if(is_user_logged_in()): // logged in ?>

           <?php atom()->te('Logged in as %s.', '<a href="'.admin_url('profile.php').'">'.$user_identity.'</a>'); ?>
           <a href="<?php echo wp_logout_url(apply_filters('the_permalink', get_permalink())); ?>" title="<?php atom()->te('Log out of this account'); ?>"><?php atom()->te('Log out?'); ?></a>

        <?php else:  // not logged in ?>

           <?php if(!empty(atom()->commenter['comment_author'])): // existing visitor ?>

             <?php atom()->te('Welcome back %s.', sprintf('<strong>%s</strong>', atom()->commenter['comment_author'])); ?>
             <?php if(atom()->options('jquery')): ?>
             <a href="#" class="toggle" data-target="comment-user-auth"><?php atom()->te('Change &raquo;'); ?></a>
             <?php endif; ?>

           <?php else: // new visitor ?>

           <?php endif; ?>

        <?php endif; ?>

        <?php atom()->CommentFormFields(); ?>
        </div>
      </div>

      <div class="comment-body">

         <div class="comment-content clear-block">

           <!-- comment input -->
           <div class="clear-block">
             <label for="comment"><?php atom()->te('Type your comment'); ?></label>
             <div class="input">
               <textarea name="comment" id="comment" class="validate required xlarge" rows="8" cols="50"></textarea>
               <span class="help-block">
                 <?php atom()->te('You may use these %1$s tags: %2$s', '<abbr title="HyperText Markup Language">HTML</abbr>', $allowed) ;?>
               </span>
             </div>
           </div>
           <!-- /comment input -->

           <div class="clear-block">
             <?php do_action('comment_form', get_the_ID()); ?>
           </div>

           <!-- comment submit -->
           <p>
             <input name="submit" type="submit" id="submit" class="button ok" value="<?php atom()->te('Post Comment') ?>" />
             <?php if(is_singular() && get_option('thread_comments') && atom()->options('jquery')): ?>
             <input name="cancel-reply" type="submit" id="cancel-reply" class="button x hidden" value="<?php atom()->te('Cancel reply') ?>" />
             <?php endif; ?>
           </p>

         </div>

      </div>
     </form>
    <?php endif; ?>
  </div>
  <?php do_action('comment_form_after'); ?>

  <?php else : ?>
    <div class="error box"><?php atom()->te('Comments are closed'); ?></div>
  <?php endif; ?>

</div>
<!-- /comment-form -->