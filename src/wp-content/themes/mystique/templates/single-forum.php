<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Single forum template.
// For use with the bbPress plugin only.

?>

<?php atom()->template('header'); ?>

<!-- main content: primary + sidebar(s) -->
<div id="mask-3" class="clear-block">
  <div id="mask-2">
    <div id="mask-1">

      <!-- primary content -->
      <div id="primary-content">
        <div class="blocks clear-block">

          <?php atom()->action('before_primary'); ?>

          <?php do_action('bbp_template_notices'); ?>

          <div class="posts clear-block">

            <?php

              while(have_posts()):
                the_post();
                if(bbp_user_can_view_forum()): ?>

                <div id="forum-<?php bbp_forum_id(); ?>" class="bbp-forum-content">
                  <h1 class="title"><?php bbp_forum_title(); ?></h1>
                  <div class="entry-content">

                    <?php

                     atom()->Breadcrumbs();
                     if(post_password_required()):
                       echo get_the_password_form();

                     else:
                       bbp_single_forum_description();

                       if(bbp_get_forum_subforum_count() && bbp_has_forums())
                         bbp_get_template_part('templates/loop', 'forums');

                       if(!bbp_is_forum_category() && bbp_has_topics()):
                         bbp_get_template_part('templates/loop', 'topics');
                         bbp_get_template_part('templates/form', 'topic');

                       elseif(!bbp_is_forum_category()):
                         printf('<div class="message">%s</div>', atom()->t('There are no topics in this forum.'));
                         bbp_get_template_part('templates/form', 'topic');
                       endif;

                     endif;
                    ?>

                  </div>
                </div>
               <?php else: // Forum exists, user no access

                 atom()->te('You do not have permission to view this forum.');

               endif;
              endwhile;
            ?>

          </div>

          <?php atom()->action('after_primary'); ?>
          
        </div>
      </div>
      <!-- /primary content -->

      <?php atom()->template('sidebar'); ?>

    </div>
  </div>
</div>
<!-- /main content -->

<?php atom()->template('footer'); ?>

