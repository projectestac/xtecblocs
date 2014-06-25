<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// User topic subscriptions template part.
// For use with the bbPress plugin only.

?>

<?php if(bbp_is_subscriptions_active()): ?>

  <?php if(bbp_is_user_home() || current_user_can('edit_users')): ?>

    <?php bbp_set_query_name('bbp_user_profile_subscriptions'); ?>

    <div class="divider"></div>
    <h2 class="title"><?php atom()->te('Subscribed Forum Topics'); ?></h2>
    <div class="entry-content">

      <?php
       if(bbp_get_user_subscriptions()):
         bbp_get_template_part('templates/loop', 'topics');
         
       else : ?>
        <p><?php bbp_is_user_home() ? atom()->te('You are not currently subscribed to any topics.'): atom()->te('This user is not currently subscribed to any topics.'); ?></p>

      <?php endif; ?>

    </div>

    <?php bbp_reset_query_name(); ?>

  <?php endif; ?>

<?php endif; ?>
