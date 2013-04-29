<?php /* Mystique/digitalnature */ ?>

<?php if(post_password_required()): ?>
  <p class="error"><?php _e('This post is password protected. Enter the password to view any comments.',  'mystique'); ?></p>
  <?php return; ?>
<?php endif; ?>

<?php if(have_comments()): ?>
<h3 class="title">
  <?php printf(_n('One comment', '%s comments', get_comments_number(), 'mystique'), number_format_i18n(get_comments_number())); ?>
</h3>

<ul id="comments">
  <?php wp_list_comments(array('callback' => 'mystique_comment')); ?>
</ul>

<?php if(get_comment_pages_count() > 1 && get_option('page_comments')): ?>
<div class="page-navi clear-block">
  <div class="alignleft"><?php previous_comments_link(__('&laquo; Older Comments', 'mystique')); ?></div>
  <div class="alignright"><?php next_comments_link(__('Newer Comments &raquo;', 'mystique')); ?></div>
</div>
<?php endif; ?>

<?php elseif(!comments_open() && !is_page() && post_type_supports(get_post_type(), 'comments')): ?>
<p class="error"><?php _e('Comments are closed.', 'mystique'); ?></p>
<?php endif; ?>

<?php comment_form(); ?>