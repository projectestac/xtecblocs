<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Topic form template part.
// For use with the bbPress plugin only.

?>

<?php if(!bbp_is_single_forum()) atom()->Breadcrumbs(); ?>

<?php if(bbp_is_topic_edit()): ?>
  <?php bbp_topic_tag_list(bbp_get_topic_id()); ?>
  <?php bbp_single_topic_description(array('topic_id' => bbp_get_topic_id())); ?>
<?php endif; ?>

<?php if(bbp_current_user_can_access_create_topic_form()): ?>

  <div class="comment with-avatars">

    <div class="avatar"><?php bbp_is_topic_edit() ? bbp_topic_author_avatar(bbp_get_topic_id(), 48) : bbp_current_user_avatar(48); ?></div>

    <div class="comment-head">
      <div class="ext clear-block">
        <div class="alignleft">
          <?php
           if(bbp_is_topic_edit())
             atom()->te('Now Editing &ldquo;%s&rdquo;', bbp_get_topic_title());
           else
             bbp_is_single_forum() ? atom()->te('Create New Topic in &ldquo;%s&rdquo;', bbp_get_forum_title()) : atom()->te('Create New Topic');
          ?>
        </div>
      </div>

    </div>

    <div class="comment-body bbp-topic-form" id="new-topic-<?php bbp_topic_id(); ?>">

      <form id="new-post" name="new-post" method="post" action="">

        <?php do_action('bbp_theme_before_topic_form'); ?>

        <?php do_action('bbp_theme_before_topic_form_notices'); ?>

        <?php if(!bbp_is_topic_edit() && bbp_is_forum_closed()): ?>
        <div class="bbp-template-notice">
          <p><?php atom()->te('This forum is marked as closed to new topics, however your posting capabilities still allow you to do so.'); ?></p>
        </div>
        <?php endif; ?>

        <?php if(current_user_can('unfiltered_html')): ?>
        <div class="bbp-template-notice">
          <p><?php atom()->te('Your account has the ability to post unrestricted HTML content.'); ?></p>
        </div>
        <?php endif; ?>

        <?php do_action('bbp_template_notices'); ?>

        <?php bbp_get_template_part('templates/form', 'anonymous'); ?>

        <?php do_action('bbp_theme_before_topic_form_title'); ?>

        <p>
          <label for="bbp_topic_title"><?php atom()->te('Topic Title (Maximum Length: %d):', bbp_get_title_max_length()); ?></label><br />
          <input type="text" id="bbp_topic_title" value="<?php bbp_form_topic_title(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_topic_title" maxlength="<?php bbp_title_max_length(); ?>" />
          <?php if($error = $GLOBALS['bbp']->errors->get_error_message('bbp_topic_title')): ?>
          <span class="error"><?php echo $error; ?></span>
          <?php endif; ?>
        </p>

        <?php do_action('bbp_theme_after_topic_form_title'); ?>

        <?php do_action('bbp_theme_before_topic_form_content'); ?>

        <p>
          <label for="bbp_topic_content"><?php atom()->te('Topic Description:'); ?></label><br />
          <textarea id="bbp_topic_content" tabindex="<?php bbp_tab_index(); ?>" name="bbp_topic_content" cols="51" rows="6"><?php bbp_form_topic_content(); ?></textarea>
          <?php if($error = $GLOBALS['bbp']->errors->get_error_message('bbp_topic_content')): ?>
          <span class="error"><?php echo $error; ?></span>
          <?php endif; ?>
        </p>

        <?php do_action('bbp_theme_after_topic_form_content'); ?>

        <?php if(!current_user_can('unfiltered_html')): ?>
        <p class="form-allowed-tags">
          <label><?php atom()->te('You may use these %s tags and attributes:', '<abbr title="HyperText Markup Language">HTML</abbr>'); ?></label><br />
          <code><?php bbp_allowed_tags(); ?></code>
        </p>
        <?php endif; ?>

        <?php do_action('bbp_theme_before_topic_form_tags'); ?>

        <p>
          <label for="bbp_topic_tags"><?php atom()->te('Topic Tags:'); ?></label><br />
          <input type="text" value="<?php bbp_form_topic_tags(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_topic_tags" id="bbp_topic_tags" />
        </p>

        <?php do_action('bbp_theme_after_topic_form_tags'); ?>

        <?php if(!bbp_is_single_forum()): ?>
          <?php do_action('bbp_theme_before_topic_form_forum'); ?>
          <p>
            <label for="bbp_forum_id"><?php atom()->te('Forum:'); ?></label><br />
            <?php bbp_dropdown(array('selected' => bbp_get_form_topic_forum())); ?>
          </p>
          <?php do_action('bbp_theme_after_topic_form_forum'); ?>
        <?php endif; ?>

        <?php if(current_user_can('moderate')): ?>
          <?php do_action('bbp_theme_before_topic_form_type'); ?>
          <p>
            <label for="bbp_stick_topic"><?php atom()->te('Topic Type:'); ?></label><br />
            <?php bbp_topic_type_select(); ?>
          </p>
          <?php do_action('bbp_theme_after_topic_form_type'); ?>
        <?php endif; ?>

        <?php if(bbp_is_subscriptions_active() && !bbp_is_anonymous() && (!bbp_is_topic_edit() || (bbp_is_topic_edit() && !bbp_is_topic_anonymous()))): ?>
          <?php do_action('bbp_theme_before_topic_form_subscriptions'); ?>
          <p>
            <input name="bbp_topic_subscription" id="bbp_topic_subscription" type="checkbox" value="bbp_subscribe" <?php bbp_form_topic_subscribed(); ?> tabindex="<?php bbp_tab_index(); ?>" />
            <?php if(bbp_is_topic_edit() && ($post->post_author != bbp_get_current_user_id())): ?>
            <label for="bbp_topic_subscription"><?php atom()->te('Notify the author of follow-up replies via email'); ?></label>
            <?php else: ?>
            <label for="bbp_topic_subscription"><?php atom()->te('Notify me of follow-up replies via email'); ?></label>
            <?php endif; ?>
          </p>

          <?php do_action('bbp_theme_after_topic_form_subscriptions'); ?>
        <?php endif; ?>

        <?php if(bbp_allow_revisions() && bbp_is_topic_edit()): ?>

          <?php do_action('bbp_theme_before_topic_form_revisions'); ?>

          <p>
            <input name="bbp_log_topic_edit" id="bbp_log_topic_edit" type="checkbox" value="1" <?php bbp_form_topic_log_edit(); ?> tabindex="<?php bbp_tab_index(); ?>" />
            <label for="bbp_log_topic_edit"><?php atom()->te('Keep a log of this edit:'); ?></label><br />
          </p>

          <p>
            <label for="bbp_topic_edit_reason"><?php atom()->te('Optional reason for editing:', bbp_get_current_user_name()); ?></label><br />
            <input type="text" value="<?php bbp_form_topic_edit_reason(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_topic_edit_reason" id="bbp_topic_edit_reason" />
          </p>

          <?php do_action('bbp_theme_after_topic_form_revisions'); ?>

        <?php endif; ?>

        <?php do_action('bbp_theme_before_topic_form_submit_wrapper'); ?>

        <p>
          <?php do_action('bbp_theme_before_topic_form_submit_button'); ?>
          <input type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_topic_submit" name="bbp_topic_submit" value="<?php atom()->te('Submit'); ?>" />
          <?php do_action('bbp_theme_after_topic_form_submit_button'); ?>
        </p>

        <?php do_action('bbp_theme_before_topic_form_submit_wrapper'); ?>

        <?php bbp_topic_form_fields(); ?>

        <?php do_action('bbp_theme_after_topic_form'); ?>

      </form>
   </div>

  </div>


<?php elseif(bbp_is_forum_closed()): ?>

  <div id="no-topic-<?php bbp_topic_id(); ?>" class="bbp-no-topic">
    <div class="bbp-template-notice">
      <p><?php atom()->te('The forum &#8216;%s&#8217; is closed to new topics and replies.', bbp_get_forum_title()); ?></p>
    </div>
  </div>

<?php else: ?>

  <div id="no-topic-<?php bbp_topic_id(); ?>" class="bbp-no-topic">
    <div class="bbp-template-notice">
      <p><?php is_user_logged_in() ? atom()->te('You cannot create new topics at this time.') : atom()->te('You must be logged in to create new topics.'); ?></p>
    </div>
  </div>

<?php endif; ?>
