<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// User topic favorites template part.
// For use with the bbPress plugin only.

?>

<?php bbp_set_query_name('bbp_user_profile_favorites'); ?>

<div class="divider"></div>
<h2 class="title"><?php atom()->te('Favorite Forum Topics'); ?></h2>
<div class="entry-content">
  <?php
    if(bbp_get_user_favorites()):
      bbp_get_template_part('templates/loop', 'topics');
      
    else : ?>
     <p><?php bbp_is_user_home() ? atom()->te('You currently have no favorite topics.') : atom()->te('This user has no favorite topics.'); ?></p>

    <?php endif; ?>
</div>

<?php bbp_reset_query_name(); ?>
