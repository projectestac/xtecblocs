<?php

	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) {
		_e( 'This post is password protected. Enter the password to view comments.', 'delicacy');

		return;
	}
?>
<?php if ( have_comments() ) : ?>
	<h2 id="comments"><?php comments_number(__('No comments','delicacy'), __('1 comment','delicacy'), __('Comments: %','delicacy')); ?></h2>
	<div class="navigation">
		<div class="next-posts"><?php previous_comments_link() ?></div>
		<div class="prev-posts"><?php next_comments_link() ?></div>
	</div>

	<ol class="commentlist">
		<?php wp_list_comments('avatar_size=60&callback=delicacy_comments'); ?>
	</ol>

	<div class="navigation">
		<div class="next-posts"><?php previous_comments_link() ?></div>
		<div class="prev-posts"><?php next_comments_link() ?></div>
	</div>
	
 <?php else : // this is displayed if there are no comments so far ?>

	<?php if ( comments_open() ) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<p><?php _e('Comments are closed.','delicacy') ?></p>

	<?php endif; ?>
	
<?php endif; ?>
<?php comment_form(
	array(
		'comment_field' => '<div><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></div>',
		'comment_notes_after' => ''
	)); ?>
