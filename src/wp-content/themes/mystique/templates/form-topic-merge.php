<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Topic merge template part.
// For use with the bbPress plugin only.

?>

<?php atom()->Breadcrumbs(); ?>

<?php if(is_user_logged_in() && current_user_can('edit_topic', bbp_get_topic_id())): ?>

  <h2 class="title"><?php atom()->te('Merge topic "%s"', bbp_get_topic_title()); ?></h2>

  <div id="merge-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-merge">

    <form id="merge_topic" name="merge_topic" method="post" action="">

      <div class="bbp-template-notice info">
        <p><?php atom()->te('Select the topic to merge this one into. The destination topic will remain the lead topic, and this one will change into a reply.'); ?></p>
        <p><?php atom()->te('To keep this topic as the lead, go to the other topic and use the merge tool from there instead.'); ?></p>
      </div>

      <div class="bbp-template-notice">
        <p><?php atom()->te('All replies within both topics will be merged chronologically. The order of the merged replies is based on the time and date they were posted. If the destination topic was created after this one, it\'s post date will be updated to second earlier than this one.'); ?></p>
      </div>

      <p>
        <?php if(bbp_has_topics(array('show_stickies' => false, 'post_parent' => bbp_get_topic_forum_id(bbp_get_topic_id()), 'post__not_in' => array(bbp_get_topic_id())))): ?>
        <label for="bbp_destination_topic"><?php atom()->te('Merge with this topic:'); ?></label>
        <?php
          bbp_dropdown(array(
            'post_type'   => bbp_get_topic_post_type(),
            'post_parent' => bbp_get_topic_forum_id(bbp_get_topic_id()),
            'selected'    => -1,
            'exclude'     => bbp_get_topic_id(),
            'select_id'   => 'bbp_destination_topic',
            'none_found'  => atom()->t('No topics were found to which the topic could be merged to!'),
          ));
        ?>
        <?php else : ?>
        <label><?php atom()->te('There are no other topics in this forum to merge with.'); ?></label>
        <?php endif; ?>
      </p>


      <?php if(bbp_is_subscriptions_active()): ?>
      <p>
        <input name="bbp_topic_subscribers" id="bbp_topic_subscribers" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
        <label for="bbp_topic_subscribers"><?php atom()->te('Merge topic subscribers'); ?></label><br />
      </p>
      <?php endif; ?>

      <p>
        <input name="bbp_topic_favoriters" id="bbp_topic_favoriters" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
        <label for="bbp_topic_favoriters"><?php atom()->te('Merge topic favoriters'); ?></label><br />
      </p>

      <p>
        <input name="bbp_topic_tags" id="bbp_topic_tags" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
        <label for="bbp_topic_tags"><?php atom()->te('Merge topic tags'); ?></label><br />
      </p>

      <div class="bbp-template-notice error">
        <p><?php atom()->te('<strong>WARNING:</strong> This process cannot be undone.'); ?></p>
      </div>

      <p>
        <input type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_merge_topic_submit" name="bbp_merge_topic_submit" value="<?php atom()->te('Submit'); ?>" />
      </p>

      <?php bbp_merge_topic_form_fields(); ?>

    </form>
  </div>

<?php else : ?>

  <div id="no-topic-<?php bbp_topic_id(); ?>" class="bbp-no-topic">
    <div class="entry-content"><?php is_user_logged_in() ? atom()->te('You do not have the permissions to edit this topic!') : atom()->te('You cannot edit this topic.'); ?></div>
  </div>

<?php endif; ?>
