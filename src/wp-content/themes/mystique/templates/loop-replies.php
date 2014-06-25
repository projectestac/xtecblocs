<?php

/*
 * @template  Bootstrap
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Reply loop template part.
// For use with the bbPress plugin only.

?>

<?php do_action('bbp_template_before_pagination_loop'); ?>

<div class="bbp-pagination-count">
  <?php bbp_topic_pagination_count(); ?>
</div>

<?php do_action('bbp_template_after_pagination_loop'); ?>
<?php do_action('bbp_template_before_replies_loop'); ?>

<table class="bbp bbp-replies" id="topic-<?php bbp_topic_id(); ?>-replies">
  <thead>
    <tr>
      <th class="bbp-reply-author"><?php atom()->te('Author'); ?></th>
      <th class="bbp-reply-content">
        <?php if(!bbp_show_lead_topic()): ?>
          <?php atom()->te('Posts'); ?>
          <?php bbp_user_subscribe_link(); ?>
          <?php bbp_user_favorites_link(); ?>

        <?php else: ?>
          <?php atom()->te('Replies'); ?>

        <?php endif; ?>
      </th>
    </tr>
  </thead>
  
  <tfoot>
    <tr>
      <th class="bbp-reply-author"><?php atom()->te('Author'); ?></th>
      <th class="bbp-reply-content"><?php bbp_show_lead_topic() ? atom()->te('Replies') : atom()->te('Posts');  ?></th>
    </tr>
  </tfoot>  

  <tbody>

    <?php while (bbp_replies()): bbp_the_reply(); ?>

    <tr class="bbp-reply-header">
      <td colspan="2">
        <?php atom()->te('%1$s at %2$s', get_the_date(), esc_attr(get_the_time())); ?>
        <a href="<?php bbp_reply_url(); ?>" title="<?php bbp_reply_title(); ?>" class="bbp-reply-permalink">#<?php bbp_reply_id(); ?></a>
        <?php do_action('bbp_theme_before_reply_admin_links'); ?>
        <?php bbp_reply_admin_links(); ?>
        <?php do_action('bbp_theme_after_reply_admin_links'); ?>
      </td>
    </tr>

    <tr id="post-<?php bbp_reply_id(); ?>" <?php bbp_reply_class(); ?>>

      <td class="bbp-reply-author">
        <?php do_action('bbp_theme_before_reply_author_details'); ?>
        <?php bbp_reply_author_link(); ?>

        <?php if(is_super_admin()): ?>
          <?php do_action('bbp_theme_before_reply_author_admin_details'); ?>
          <div class="bbp-reply-ip"><?php bbp_author_ip(bbp_get_reply_id()); ?></div>
          <?php do_action('bbp_theme_after_reply_author_admin_details'); ?>
        <?php endif; ?>

        <?php do_action('bbp_theme_after_reply_author_details'); ?>
      </td>

      <td class="bbp-reply-content">
        <?php do_action('bbp_theme_after_reply_content'); ?>
        <?php bbp_reply_content(); ?>
        <?php do_action('bbp_theme_before_reply_content'); ?>
      </td>

    </tr>

    <?php endwhile; ?>

  </tbody>

</table>

<?php do_action('bbp_template_after_replies_loop'); ?>
<?php do_action('bbp_template_before_pagination_loop'); ?>

<div class="page-navi clear-block">
  <?php bbp_topic_pagination_links(); ?>
</div>

<?php do_action('bbp_template_after_pagination_loop'); ?>