<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Topic split template part.
// For use with the bbPress plugin only.

?>

<?php atom()->Breadcrumbs(); ?>

<?php if(is_user_logged_in() && current_user_can('edit_topic', bbp_get_topic_id())): ?>

  <div id="split-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-split">

    <form id="split_topic" name="split_topic" method="post" action="">


      <div class="bbp-template-notice info">
      <p><?php atom()->te('When you split a topic, you are slicing it in half starting with the reply you just selected. Choose to use that reply as a new topic with a new title, or merge those replies into an existing topic.'); ?></p>
      </div>

      <div class="bbp-template-notice">
        <p><?php atom()->te('If you use the existing topic option, replies within both topics will be merged chronologically. The order of the merged replies is based on the time and date they were posted.'); ?></p>
      </div>

      <p>
        <input name="bbp_topic_split_option" id="bbp_topic_split_option_reply" type="radio" checked="checked" value="reply" tabindex="<?php bbp_tab_index(); ?>" />
        <label for="bbp_topic_split_option_reply"><?php atom()->te('New topic in <strong>%s</strong> titled:', bbp_get_forum_title(bbp_get_topic_forum_id(bbp_get_topic_id()))); ?></label>
        <input type="text" id="bbp_topic_split_destination_title" value="<?php atom()->te('Split: %s', bbp_get_topic_title()); ?>" tabindex="<?php bbp_tab_index(); ?>" size="35" name="bbp_topic_split_destination_title" />
      </p>

      <?php if(bbp_has_topics(array('show_stickies' => false, 'post_parent' => bbp_get_topic_forum_id(bbp_get_topic_id()), 'post__not_in' => array(bbp_get_topic_id())))): ?>

      <p>
        <input name="bbp_topic_split_option" id="bbp_topic_split_option_existing" type="radio" value="existing" tabindex="<?php bbp_tab_index(); ?>" />
        <label for="bbp_topic_split_option_existing"><?php atom()->te('Use an existing topic in this forum:'); ?></label>

        <?php
          bbp_dropdown(array(
            'post_type'   => bbp_get_topic_post_type(),
            'post_parent' => bbp_get_topic_forum_id(bbp_get_topic_id()),
            'selected'    => -1,
            'exclude'     => bbp_get_topic_id(),
            'select_id'   => 'bbp_destination_topic',
            'none_found'  => atom()->t('No other topics found!')
          ));
        ?>

      </p>

      <?php endif; ?>


      <p>
        <?php if(bbp_is_subscriptions_active()): ?>

        <input name="bbp_topic_subscribers" id="bbp_topic_subscribers" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
        <label for="bbp_topic_subscribers"><?php atom()->te('Copy subscribers to the new topic'); ?></label><br />

        <?php endif; ?>

        <input name="bbp_topic_favoriters" id="bbp_topic_favoriters" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
        <label for="bbp_topic_favoriters"><?php atom()->te('Copy favoriters to the new topic'); ?></label><br />

        <input name="bbp_topic_tags" id="bbp_topic_tags" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
        <label for="bbp_topic_tags"><?php atom()->te('Copy topic tags to the new topic'); ?></label><br />
      </p>


      <div class="bbp-template-notice error">
        <p><?php atom()->te('<strong>WARNING:</strong> This process cannot be undone.'); ?></p>
      </div>

      <p>
        <input type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_merge_topic_submit" name="bbp_merge_topic_submit" value="<?php atom()->te('Submit'); ?>" />
      </p>

      <?php bbp_split_topic_form_fields(); ?>

    </form>
  </div>

<?php else : ?>

<div id="no-topic-<?php bbp_topic_id(); ?>" class="bbp-no-topic">
  <div class="entry-content"><?php is_user_logged_in() ? atom()->te('You do not have the permissions to edit this topic!'): atom()->te('You cannot edit this topic.'); ?></div>
</div>

<?php endif; ?>
