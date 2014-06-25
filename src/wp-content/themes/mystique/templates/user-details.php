<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// User details template part.
// For use with the bbPress plugin only.

?>

<div class="clear-block">
  <div class="alignright">
    <?php echo get_avatar(bbp_get_displayed_user_field('user_email'), apply_filters('twentyten_author_bio_avatar_size', 128)); ?>
  </div>

  <div class="alignleft">

    <h1 class="title">
      <?php atom()->te('Profile: %s', "<span class='vcard'><a class='url fn n' href='".bbp_get_user_profile_url()."' title='".esc_attr(bbp_get_displayed_user_field('display_name'))."' rel='me'>" . bbp_get_displayed_user_field('display_name')."</a></span>"); ?>

      <?php if(bbp_is_user_home() || current_user_can('edit_users')): ?>
      <span class="edit_user_link">
        <a href="<?php bbp_user_profile_edit_url(); ?>" title="<?php atom()->te('Edit Profile of User %s', esc_attr(bbp_get_displayed_user_field('display_name'))); ?>"><?php atom()->te('(Edit)'); ?></a>
      </span>
      <?php endif; ?>
    </h1>
        
    <p>
      <?php echo bbp_get_displayed_user_field('description'); ?>
    </p>

  </div>

</div>
