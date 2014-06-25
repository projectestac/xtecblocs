<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// User topics template part (topics created by the user).
// For use with the bbPress plugin only.

?>

<?php bbp_set_query_name('bbp_user_profile_topics_created'); ?>


<div class="divider"></div>
<h2 class="title"><?php atom()->te('Forum Topics Created'); ?></h2>
<div class="entry-content">
  <?php
   if(bbp_get_user_topics_started()):     
     bbp_get_template_part('templates/loop', 'topics');     

    else: ?>
     <p><?php bbp_is_user_home() ? atom()->te('You have not created any topics.') : atom()->te('This user has not created any topics.'); ?></p>

  <?php endif; ?>
</div>


<?php bbp_reset_query_name(); ?>
