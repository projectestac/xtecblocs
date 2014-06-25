<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Template part for anonymous topic/reply form fields.
// For use with the bbPress plugin only.

?>

<?php if(bbp_is_anonymous() || (bbp_is_topic_edit() && bbp_is_topic_anonymous()) || (bbp_is_reply_edit() && bbp_is_reply_anonymous())): ?>

  <?php do_action('bbp_theme_before_anonymous_form'); ?>

  <?php do_action('bbp_theme_anonymous_form_extras_top'); ?>

  <p>
    <label for="bbp_anonymous_author"><?php atom()->te('Name (required):'); ?></label><br />
    <input type="text" id="bbp_anonymous_author" value="<?php bbp_is_topic_edit() ? bbp_topic_author() : bbp_is_reply_edit() ? bbp_reply_author() : bbp_current_anonymous_user_data('name'); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_anonymous_name" />
    <?php if($error = $GLOBALS['bbp']->errors->get_error_message('bbp_anonymous_name')): ?>
    <span class="error"><?php echo $error; ?></span>
    <?php endif; ?>
  </p>

  <p>
    <label for="bbp_anonymous_email"><?php atom()->te('Mail (will not be published) (required):'); ?></label><br />
    <input type="text" id="bbp_anonymous_email" value="<?php echo (bbp_is_topic_edit() || bbp_is_reply_edit()) ? get_post_meta($post->ID, '_bbp_anonymous_email', true) : bbp_get_current_anonymous_user_data('email'); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_anonymous_email" />
    <?php if($error = $GLOBALS['bbp']->errors->get_error_message('bbp_anonymous_email')): ?>
    <span class="error"><?php echo $error; ?></span>
    <?php endif; ?>
  </p>

  <p>
    <label for="bbp_anonymous_website"><?php atom()->te('Website:'); ?></label><br />
    <input type="text" id="bbp_anonymous_website" value="<?php bbp_is_topic_edit() ? bbp_topic_author_url() : bbp_is_reply_edit() ? bbp_reply_author_url() : bbp_current_anonymous_user_data('website'); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_anonymous_website" />
    <?php if($error = $GLOBALS['bbp']->errors->get_error_message('bbp_anonymous_email')): ?>
    <span class="error"><?php echo $error; ?></span>
    <?php endif; ?>
  </p>

  <?php do_action('bbp_theme_anonymous_form_extras_bottom'); ?>

  <?php do_action('bbp_theme_after_anonymous_form'); ?>

<?php endif; ?>
