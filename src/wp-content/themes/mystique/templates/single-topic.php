<?php

/*
 * @template  Mystique
 * @revised   October 30, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Single topic template.
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
              if(bbp_user_can_view_forum(array('forum_id' => bbp_get_topic_forum_id()))):

                while(have_posts()):
                  the_post(); ?>

                  <div id="bbp-topic-wrapper-<?php bbp_topic_id(); ?>" class="bbp-topic-wrapper">
                    <h1 class="title"><?php bbp_topic_title(); ?></h1>
                    <div class="entry-content">

                      <?php

                       atom()->Breadcrumbs();
                       do_action('bbp_template_before_single_topic');

                       if(post_password_required()):
                         echo get_the_password_form();

                       else:
                         bbp_topic_tag_list();
                         bbp_single_topic_description();

                         if(bbp_show_lead_topic()): ?>

                          <table class="bbp-topic" id="bbp-topic-<?php bbp_topic_id(); ?>">
                            <thead>
                              <tr>
                                <th class="bbp-topic-author"><?php _ae('Creator'); ?></th>
                                <th class="bbp-topic-content">
                                  <?php _ae('Topic'); ?>
                                  <?php bbp_user_subscribe_link(); ?>
                                  <?php bbp_user_favorites_link(); ?>
                                </th>
                              </tr>
                            </thead>

                            <tfoot>
                              <tr>
                                <td colspan="2"><?php bbp_topic_admin_links(); ?></td>
                              </tr>
                            </tfoot>

                            <tbody>

                              <tr class="bbp-topic-header">
                                <td colspan="2">
                                  <?php printf(_a( '%1$s at %2$s'), get_the_date(), esc_attr(get_the_time())); ?>
                                  <a href="#bbp-topic-<?php bbp_topic_id(); ?>" title="<?php bbp_topic_title(); ?>" class="bbp-topic-permalink">#<?php bbp_topic_id(); ?></a>
                                </td>
                              </tr>

                              <tr id="post-<?php bbp_topic_id(); ?>" <?php post_class('bbp-forum-topic'); ?>>

                                <td class="bbp-topic-author">
                                  <?php bbp_topic_author_link(); ?>
                                  <?php if(is_super_admin()): ?>
                                    <div class="bbp-topic-ip"><?php bbp_author_ip(bbp_get_topic_id()); ?></div>
                                  <?php endif; ?>
                                </td>

                                <td class="bbp-topic-content">
                                  <?php bbp_topic_content(); ?>
                                </td>

                              </tr>

                            </tbody>
                          </table>

                          <?php
                         endif;


                         if(!bbp_get_query_name() && bbp_has_replies())                          
                           bbp_get_template_part('templates/loop', 'replies');                                                  

                         bbp_get_template_part('templates/form', 'reply');

                       endif;

                       do_action('bbp_template_after_single_topic');
                      ?>

                    </div>
                  </div>

                <?php endwhile;
              elseif(bbp_is_forum_private(bbp_get_topic_forum_id(), false)):

                _ae('You do not have permission to view this forum.');

              endif;
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
