<?php // Do not delete these lines
 if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die (__('Please do not load this page directly. Thanks!'));

        if (!empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_'.$cookiehash] != $post->post_password) {  // and it doesn't match the cookie
    ?>
    
    <p class="nocomments"><?php _e("This post is password protected. Enter the password to view comments.",'colors-idea'); ?><p>
    
    <?php
    return;
            }
        }

  /* This variable is for alternating comment background */
  $oddcomment = "graybox";
?>


<?php if ($comments) : ?>
 <a name="comments"></a><div class="commtitle"><?php comments_number(__('No Responses','colors-idea'),__('One Response','colors-idea'),__('% Responses','colors-idea') );?></div>

	<ol id="commentlist">
	<?php foreach ($comments as $comment) : ?>
		<li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">

		<!-- Gravatar -->
		<?php if (function_exists('gravatar')) { ?>
		<?php } ?>
		
		<h3 class="comment-title"><?php comment_author_link() ?> <?php _e(':'); ?></h3>
		
		<p class="comment-meta">
			<?php _e('Date:', 'colors-idea'); ?>
			<a href="#comment-<?php comment_ID() ?>">
				<?php comment_date('d F Y') ?>
				<?php _e(' @ '); ?><?php comment_time() ?>
			</a>
			<?php edit_comment_link(__("Edit",'colors-idea'), ' &#183; ', ''); ?>
		</p>
		
		<div class="comment-text"><?php comment_text() ?></div>
		</li>

		<?php 
			if ('alt' == $oddcomment) $oddcomment = '';
			else $oddcomment = 'alt';
		?>

	<?php endforeach; /* end for each comment */ ?>

	</ol>

 <?php else : // this is displayed if there are no comments so far ?>

  <?php if ('open' == $post-> comment_status) : ?>
  <!-- If comments are open, but there are no comments. -->
  
  <?php else : // comments are closed ?>
  <!-- If comments are closed. -->
  <p class="nocomments"><?php _e('Comments are closed.','colors-idea');?></p>
  
 <?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post-> comment_status) : ?>

<a name="respond"></a><div class="leavecomm"><?php _e('Leave a comment','colors-idea');?></div>
<form action="<?php echo get_settings('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<p><label for="author"><small><?php _e('Name','colors-idea');?></small></label><input type="text" name="author" id="author" class="styled" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
<input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" /></p>

<p><label for="email"><small><?php _e('Mail','colors-idea');?> (<?php _e('will not be published','colors-idea');?>)</small></label>
<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" /></p>

<p><label for="url"><small><?php _e('Website','colors-idea');?></small></label><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
</p>


<p><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>
<p><input name="submit" type="submit" class="submit1" id="submit" tabindex="5" value="<?php _e('Submit Comment','colors-idea');?>" /></p>
<p><small><?php _e('You can use these tags:', 'colors-idea');?> <?php echo allowed_tags(); ?></small></p>

<?php if ('none' != get_settings("comment_moderation")) { ?>
 <p><small><strong><?php _e('Please note:','colors-idea');?></strong> <?php _e('Comment moderation is enabled and may delay your comment. There is no need to resubmit your comment.','colors-idea');?></small></p>
<?php } ?>

<?php do_action('comment_form', $post->ID); ?>
</form>

<?php // if you delete this the sky will fall on your head
endif; ?>