<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Reply form template part.
// For use with the bbPress plugin only.

?>

<?php if(bbp_current_user_can_access_create_reply_form()): ?>

  <div class="comment with-avatars">

    <div class="avatar"><?php bbp_is_reply_edit() ? bbp_reply_author_avatar(bbp_get_reply_id(), 48) : bbp_current_user_avatar(48); ?></div>

    <div class="comment-head">
      <div class="ext clear-block">
        <div class="alignleft">
          <?php atom()->te('Reply To: %s', sprintf('<strong>%s</strong>', bbp_get_topic_title())); ?>
        </div>
      </div>

    </div>

    <div class="comment-body bbp-reply-form" id="new-reply-<?php bbp_topic_id(); ?>">

      <form id="new-post" name="new-post" method="post" action="">

        <?php do_action('bbp_theme_before_reply_form'); ?>

        <?php do_action('bbp_theme_before_reply_form_notices'); ?>

        <?php if(!bbp_is_topic_open() && !bbp_is_reply_edit()): ?>
        <div class="bbp-template-notice">
          <p><?php atom()->te('This topic is marked as closed to new replies, however your posting capabilities still allow you to do so.'); ?></p>
        </div>
        <?php endif; ?>

        <?php if(current_user_can('unfiltered_html')): ?>
        <div class="bbp-template-notice">
          <p><?php atom()->te('Your account has the ability to post unrestricted HTML content.'); ?></p>
        </div>
        <?php endif; ?>

        <?php do_action('bbp_template_notices'); ?>

        <?php bbp_get_template_part('templates/form', 'anonymous'); ?>

        <?php do_action('bbp_theme_before_reply_form_content'); ?>

        <p>
          <label for="bbp_reply_content"><?php atom()->te('Reply:'); ?></label><br />
          <textarea id="bbp_reply_content" tabindex="<?php bbp_tab_index(); ?>" name="bbp_reply_content" cols="51" rows="6"><?php bbp_form_reply_content(); ?></textarea>
          <?php if($error = $GLOBALS['bbp']->errors->get_error_message('bbp_reply_content')): ?>
          <span class="error"><?php echo $error; ?></span>
          <?php endif; ?>
        </p>

        <?php do_action('bbp_theme_after_reply_form_content'); ?>

        <?php if(!current_user_can('unfiltered_html')): ?>
        <p class="form-allowed-tags">
          <label><?php atom()->te('You may use these %s tags and attributes:', '<abbr title="HyperText Markup Language">HTML</abbr>'); ?></label><br />
          <code><?php bbp_allowed_tags(); ?></code>
        </p>
        <?php endif; ?>

        <?php do_action('bbp_theme_before_reply_form_tags'); ?>

        <p>
          <label for="bbp_topic_tags"><?php atom()->te('Tags:'); ?></label><br />
          <input id="bbp_topic_tags" type="text" value="<?php bbp_form_topic_tags(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_topic_tags" />
        </p>

        <?php do_action('bbp_theme_after_reply_form_tags'); ?>

        <?php if(bbp_is_subscriptions_active() && !bbp_is_anonymous() && (!bbp_is_reply_edit() || (bbp_is_reply_edit() && !bbp_is_reply_anonymous()))): ?>
          <?php do_action('bbp_theme_before_reply_form_subscription'); ?>
          <p>

            <input name="bbp_topic_subscription" id="bbp_topic_subscription" type="checkbox" value="bbp_subscribe"<?php bbp_form_topic_subscribed(); ?> tabindex="<?php bbp_tab_index(); ?>" />

            <?php if(bbp_is_reply_edit() && $post->post_author != bbp_get_current_user_id()) : ?>
            <label for="bbp_topic_subscription"><?php atom()->te('Notify the author of follow-up replies via email'); ?></label>
            <?php else : ?>
            <label for="bbp_topic_subscription"><?php atom()->te('Notify me of follow-up replies via email'); ?></label>
            <?php endif; ?>
          </p>
          <?php do_action('bbp_theme_after_reply_form_subscription'); ?>
        <?php endif; ?>

        <?php if(bbp_allow_revisions() && bbp_is_reply_edit()): ?>
          <?php do_action('bbp_theme_before_reply_form_revisions'); ?>
          <p>
            <input name="bbp_log_reply_edit" id="bbp_log_reply_edit" type="checkbox" value="1" <?php bbp_form_reply_log_edit(); ?> tabindex="<?php bbp_tab_index(); ?>" />
            <label for="bbp_log_reply_edit"><?php atom()->te('Keep a log of this edit:'); ?></label><br />
          </p>
          <p>
            <label for="bbp_reply_edit_reason"><?php atom()->te('Optional reason for editing:', bbp_get_current_user_name()); ?></label><br />
            <input type="text" value="<?php bbp_form_reply_edit_reason(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_reply_edit_reason" id="bbp_reply_edit_reason" />
          </p>
          <?php do_action('bbp_theme_after_reply_form_revisions'); ?>

        <?php else: ?>
          <?php bbp_topic_admin_links(); ?>

        <?php endif; ?>

        <?php do_action('bbp_theme_before_reply_form_submit_wrapper'); ?>

        <p>
          <?php do_action('bbp_theme_before_reply_form_submit_button'); ?>
          <input type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_reply_submit" name="bbp_reply_submit" value="<?php atom()->te('Submit'); ?>" />
          <?php do_action('bbp_theme_after_reply_form_submit_button'); ?>
        </p>

        <?php do_action('bbp_theme_after_reply_form_submit_wrapper'); ?>

        <?php bbp_reply_form_fields(); ?>

        <?php do_action('bbp_theme_after_reply_form'); ?>
      </form>

    </div>

  </div>


<?php elseif(bbp_is_topic_closed()): ?>

  <div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
    <div class="bbp-template-notice">
      <p><?php atom()->te('The topic &#8216;%s&#8217; is closed to new replies.', bbp_get_topic_title()); ?></p>
    </div>
  </div>

<?php elseif(bbp_is_forum_closed(bbp_get_topic_forum_id())): ?>

  <div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
    <div class="bbp-template-notice">
      <p><?php atom()->te('The forum &#8216;%s&#8217; is closed to new topics and replies.', bbp_get_forum_title(bbp_get_topic_forum_id())); ?></p>
    </div>
  </div>

<?php else: ?>

  <div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
    <div class="bbp-template-notice">
      <p><?php is_user_logged_in() ? atom()->te('You cannot reply to this topic.') : atom()->te('You must be logged in to reply to this topic.'); ?></p>
    </div>
  </div>

<?php endif; ?>
