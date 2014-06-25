<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Topic tag form template part.
// For use with the bbPress plugin only.

?>

<?php if(current_user_can('edit_topic_tags')): ?>

<h2 class="title"><?php atom()->te('Manage Tag: "%s"', bbp_get_topic_tag_name()); ?></h2>

<div id="edit-topic-tag-<?php bbp_topic_tag_id(); ?>" class="bbp-topic-tag-form">



  <h3 class="title"><?php atom()->te('Rename'); ?></h3>

  <div class="bbp-template-notice info">
    <p><?php atom()->te('Leave the slug empty to have one automatically generated.'); ?></p>
  </div>

  <div class="bbp-template-notice">
    <p><?php atom()->te('Changing the slug affects its permalink. Any links to the old slug will stop working.'); ?></p>
  </div>

  <form id="rename_tag" name="rename_tag" method="post" action="">

    <p>
      <label for="tag-name"><?php atom()->te('Name:'); ?></label>
      <input type="text" name="tag-name" size="20" maxlength="40" tabindex="<?php bbp_tab_index(); ?>" value="<?php echo esc_attr(bbp_get_topic_tag_name()); ?>" />
    </p>

    <p>
      <label for="tag-name"><?php atom()->te('Slug:'); ?></label>
      <input type="text" name="tag-slug" size="20" maxlength="40" tabindex="<?php bbp_tab_index(); ?>" value="<?php echo esc_attr(apply_filters('editable_slug', bbp_get_topic_tag_slug())); ?>" />
    </p>

    <p>
      <input type="submit" name="submit" tabindex="<?php bbp_tab_index(); ?>" value="<?php atom()->te('Update'); ?>" /><br />

      <input type="hidden" name="tag-id" value="<?php bbp_topic_tag_id(); ?>" />
      <input type="hidden" name="action" value="bbp-update-topic-tag" />

      <?php wp_nonce_field('update-tag_'.bbp_get_topic_tag_id()); ?>

    </p>
  </form>


  <h3 class="title"><?php atom()->te('Merge'); ?></h3>

  <div class="bbp-template-notice">
    <p><?php atom()->te('Merging tags together cannot be undone.'); ?></p>
  </div>

  <form id="merge_tag" name="merge_tag" method="post" action="">

    <p>
      <label for="tag-name"><?php atom()->te('Existing tag:'); ?></label>
      <input type="text" name="tag-name" size="22" tabindex="<?php bbp_tab_index(); ?>" maxlength="40" />
    </p>

    <p>
      <input type="submit" name="submit" tabindex="<?php bbp_tab_index(); ?>" value="<?php atom()->te('Merge'); ?>" onclick="return confirm('<?php echo esc_js(atom()->t('Are you sure you want to merge the "%s" tag into the tag you specified?', bbp_get_topic_tag_name())); ?>');" />

      <input type="hidden" name="tag-id" value="<?php bbp_topic_tag_id(); ?>" />
      <input type="hidden" name="action" value="bbp-merge-topic-tag" />

      <?php wp_nonce_field('merge-tag_'.bbp_get_topic_tag_id()); ?>
    </p>
  </form>


  <?php if(current_user_can('delete_topic_tags')): ?>

  <h3 class="title"><?php atom()->te('Delete'); ?></h3>

  <div class="bbp-template-notice info">
    <p><?php atom()->te('This does not delete your topics. Only the tag itself is deleted.'); ?></p>
  </div>

  <div class="bbp-template-notice">
    <p><?php atom()->te('Deleting a tag cannot be undone.'); ?></p>
    <p><?php atom()->te('Any links to this tag will no longer function.'); ?></p>
  </div>

  <form id="delete_tag" name="delete_tag" method="post" action="">

    <p>
    <input type="submit" name="submit" tabindex="<?php bbp_tab_index(); ?>" value="<?php atom()->te('Delete'); ?>" onclick="return confirm('<?php echo esc_js(atom()->t('Are you sure you want to delete the "%s" tag? This is permanent and cannot be undone.', bbp_get_topic_tag_name())); ?>');" />

    <input type="hidden" name="tag-id" value="<?php bbp_topic_tag_id(); ?>" />
    <input type="hidden" name="action" value="bbp-delete-topic-tag" />

    <?php wp_nonce_field('delete-tag_'.bbp_get_topic_tag_id()); ?>
    </p>
  </form>

  <?php endif; ?>

</div>

<?php endif; ?>
