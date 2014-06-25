<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Single reply template. Normally this should never be used...
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
                the_post(); ?>

                <div id="bbp-reply-wrapper-<?php bbp_reply_id(); ?>" class="bbp-reply-wrapper">
                  <h1 class="title"><?php bbp_reply_title(); ?></h1>

                  <?php atom()->Breadcrumbs(); ?>

                  <div class="entry-content">

                    <table class="bbp-replies" id="topic-<?php bbp_topic_id(); ?>-replies">
                      <thead>
                        <tr>
                          <th class="bbp-reply-author"><?php atom()->te('Author'); ?></th>
                          <th class="bbp-reply-content"><?php atom()->te('Replies'); ?></th>
                        </tr>
                      </thead>

                      <tfoot>
                        <tr>
                          <td colspan="2"><?php bbp_topic_admin_links(); ?></td>
                        </tr>
                      </tfoot>

                      <tbody>
                        <tr class="bbp-reply-header">
                          <td class="bbp-reply-author">
                            <?php bbp_reply_author_display_name(); ?>
                          </td>
                          <td class="bbp-reply-content">
                            <a href="<?php bbp_reply_url(); ?>" title="<?php bbp_reply_title(); ?>">#</a>
                            <?php atom()->te('Posted on %1$s at %2$s', get_the_date(), get_the_time()); ?>
                            <span><?php bbp_reply_admin_links(); ?></span>
                          </td>
                        </tr>

                        <tr id="reply-<?php bbp_reply_id(); ?>" <?php bbp_reply_class(); ?>>
                          <td class="bbp-reply-author"><?php bbp_reply_author_link(array('type' => 'avatar')); ?></td>
                          <td class="bbp-reply-content"><?php bbp_reply_content(); ?> </td>
                        </tr>
                      </tbody>
                    </table>

                  </div>
                </div>

              <?php endwhile; ?>

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
