<?php // Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die (__('Please do not load this page directly. Thanks!','stardust'));
	if ( post_password_required() ) { ?>
  <p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.','stardust') ?></p>
	<?php
		return;
	}
?>

<!-- You can start editing here. -->
<?php if ( have_comments() ) : ?>
<h3 id="comments"><?php comments_number(_c('No Responses to|Comments','stardust'), _c('One Response to|Comments','stardust'), _c('% Responses to|Comments','stardust'));?> &#8220;<?php the_title(); ?>&#8221;</h3>
	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
	<ol class="commentlist">
	<?php wp_list_comments('callback=stardust_comment'); ?>
	</ol>
	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
 <?php else : // this is displayed if there are no comments so far ?>

	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments"><?php _e('Comments are closed.','stardust') ?></p>

	<?php endif; ?>
<?php endif; ?>

<?php if ('open' == $post->comment_status) : ?>

<div id="respond">

   <h3><?php comment_form_title( _c('Leave a Reply|Comments','stardust'), _c('Leave a Reply to %s|Comments','stardust') ); ?></h3>

<div class="cancel-comment-reply">
	<small><?php cancel_comment_reply_link(); ?></small>
</div>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p><?php _e('You must be logged in to post a comment.','stardust')?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php _e('Login now.','stardust') ?></p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( $user_ID ) : ?>

<p><?php _e('Logged in as','stardust') ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account','stardust') ?>"><?php _e('Log out','stardust') ?> &raquo;</a></p>

<?php else : ?>
<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" <?php if ($req) echo "aria-required='true'"; ?> />
<label for="author"><small><?php _e('Name','stardust')?> <?php if ($req) echo "(".__('required','stardust').")"; ?></small></label></p>
<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" <?php if ($req) echo "aria-required='true'"; ?> />
<label for="email"><small><?php _e('Mail (will not be published)','stardust') ?> <?php if ($req) echo "(".__('required','stardust').")"; ?></small></label></p>
<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" />
<label for="url"><small><?php _e('Website','stardust') ?></small></label></p>
<?php endif; ?>

<p><label for="comment" class="skip"><?php _e('Comment text','stardust') ?></label><textarea name="comment" id="comment" cols="100%" rows="10"></textarea></p>
<p><input name="submit" type="submit" id="submit" value="<?php _e('Submit Comment','stardust') ?>" />
<?php comment_id_fields(); ?>
</p>
<div><?php do_action('comment_form', $post->ID); ?></div>
</form>

<?php endif; // If registration required and not logged in ?>
</div>

<?php endif; // if you delete this the sky will fall on your head ?>
