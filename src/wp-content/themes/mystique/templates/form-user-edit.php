<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// User edit form template part.
// For use with the bbPress plugin only.

?>

<form id="bbp-your-profile" action="<?php bbp_user_profile_edit_url(bbp_get_displayed_user_id()); ?>" method="post">

  <h2 class="title"><?php atom()->te('Name') ?></h2>
  <?php do_action('bbp_user_edit_before'); ?>
  <?php do_action('bbp_user_edit_before_name'); ?>
  <p>
    <label for="first_name"><?php atom()->te('First Name') ?></label>
    <input type="text" size="40" name="first_name" id="first_name" value="<?php echo esc_attr(bbp_get_displayed_user_field('first_name')); ?>" />
  </p>
  <p>
    <label for="last_name"><?php atom()->te('Last Name') ?></label>
    <input type="text" size="40" name="last_name" id="last_name" value="<?php echo esc_attr(bbp_get_displayed_user_field('last_name')); ?>" />
  </p>
  <p>
    <label for="nickname"><?php atom()->te('Nickname'); ?></label>
    <input type="text" size="40" name="nickname" id="nickname" value="<?php echo esc_attr(bbp_get_displayed_user_field('nickname')); ?>" />
  </p>
  <p>
    <label for="display_name"><?php atom()->te('Display name publicly as') ?></label>
    <?php bbp_edit_user_display_name(); ?>
  </p>
  <?php do_action('bbp_user_edit_after_name'); ?>


  <h2 class="title"><?php atom()->te('Contact Info'); ?></h2>
  <?php do_action('bbp_user_edit_before_contact'); ?>
  <p>
    <label for="url"><?php atom()->te('Website') ?></label>
    <input type="text" size="60" name="url" id="url" value="<?php echo esc_attr(bbp_get_displayed_user_field('user_url')); ?>" />
  </p>
  <?php foreach (bbp_edit_user_contact_methods() as $name => $desc) : ?>
  <p>
    <label for="<?php echo $name; ?>"><?php echo apply_filters("user_{$name}_label", $desc); ?></label>
    <input type="text" size="20" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr(bbp_get_displayed_user_field('name')); ?>" />
  </p>
  <?php endforeach; ?>
  <?php do_action('bbp_user_edit_after_contact'); ?>


  <h2 class="title"><?php bbp_is_user_home() ? atom()->te('About Yourself') : atom()->te('About the user'); ?></h2>
  <?php do_action('bbp_user_edit_before_about'); ?>
  <p>
    <label for="description"><?php atom()->te('Biographical Info'); ?></label>
    <textarea name="description" id="description" rows="5" cols="30"><?php echo esc_attr(bbp_get_displayed_user_field('description')); ?></textarea>
    <span class="description"><?php atom()->te('Share a little biographical information to fill out your profile. This may be shown publicly.'); ?></span>
  </p>
  <?php do_action('bbp_user_edit_after_about'); ?>


  <h2 class="title"><?php atom()->te('Account') ?></h2>
  <?php do_action('bbp_user_edit_before_account'); ?>
  <p>
    <label for="user_login"><?php atom()->te('Username'); ?></label>
    <input type="text" size="40" name="user_login" id="user_login" value="<?php echo esc_attr(bbp_get_displayed_user_field('user_login')); ?>" disabled="disabled" />
    <span class="description"><?php atom()->te('Usernames cannot be changed.'); ?></span>
  </p>
  <p>
    <label for="email"><?php atom()->te('Email'); ?></label>
    <input type="text" size="40" name="email" id="email" value="<?php echo esc_attr(bbp_get_displayed_user_field('user_email')); ?>"/>

    <?php
      // Handle address change requests
      $new_email = get_option(bbp_get_displayed_user_id().'_new_email');
      if($new_email && $new_email != bbp_get_displayed_user_field('user_email')) : ?>
      <span class="updated inline">
        <?php atom()->te('There is a pending email address change to <code>%1$s</code>. <a href="%2$s">Cancel</a>', $new_email['newemail'], esc_url(self_admin_url('user.php?dismiss=' . bbp_get_current_user_id() .'_new_email'))); ?>

      </span>
      <?php endif; ?>
  </p>

  <div id="password">
    <p>
      <label for="pass1"><?php atom()->te('New Password'); ?></label>
      <input type="password" name="pass1" id="pass1" size="16" value="" autocomplete="off" />
      <span class="description"><?php atom()->te('If you would like to change the password type a new one. Otherwise leave this blank.'); ?></span>
    </p>

    <p>
      <label for="pass2"><?php atom()->te('Type your new password again'); ?></label>
      <input type="password" name="pass2" id="pass2" size="16" value="" autocomplete="off" />
      <div id="pass-strength-result"></div>
      <span class="description indicator-hint"><?php atom()->te('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp;).'); ?></span>
    </p>
  </div>

  <?php if(!bbp_is_user_home()) : ?>
  <p>
    <label for="role"><?php atom()->te('Role:') ?></label>
    <?php bbp_edit_user_role(); ?>
  </p>
  <?php endif; ?>

  <?php if(is_multisite() && is_super_admin() && current_user_can('manage_network_options')) : ?>

  <p>
    <label for="role"><?php atom()->te('Super Admin'); ?></label>
    <label>
    <input type="checkbox" id="super_admin" name="super_admin"<?php checked(is_super_admin(bbp_get_displayed_user_id())); ?> />
    <?php atom()->te('Grant this user super admin privileges for the Network.'); ?>
    </label>
  </p>

  <?php endif; ?>

  <?php do_action('bbp_user_edit_after_account'); ?>
  <?php do_action('bbp_user_edit_after'); ?>

  <?php bbp_edit_user_form_fields(); ?>

  <p>
    <input type="submit" id="bbp_user_edit_submit" name="bbp_user_edit_submit" value="<?php bbp_is_user_home() ? atom()->te('Update Profile') : atom()->te('Update User'); ?>" />
  </p>


</form>