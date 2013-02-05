<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die (__('Please do not load this page directly. Thanks!', 'freshy'));

        if (!empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
				?>
				
				<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments', 'freshy'); ?><p>
				
				<?php
				return;
            }
        }

		/* This variable is for alternating comment background */
		$oddcomment = 'alt';
		$gravatar_size= get_option('gravatar_options');
		$gravatar_size= $gravatar_size['gravatar_size'];
?>

<!-- You can start editing here. -->

<?php if ($comments) : ?><?php _e('No responses', 'freshy'); ?>
	<h3 id="comments"><?php comments_number(__('No responses', 'freshy'), __('One response', 'freshy'), __('% responses', 'freshy'));?> <?php _e('to', 'freshy'); ?> &#8220;<?php the_title(); ?>&#8221;</h3>

	<dl class="commentlist">

	<?php foreach ($comments as $comment) : ?>
		
	<?php
	$author_comment_class=' none';
	if($comment->comment_author_email == get_the_author_email()) $author_comment_class=' author_comment';

	?>
	
		<dt class="<?php echo $author_comment_class; ?>">
				<small class="date">
					<span class="date_day"><?php comment_date('j') ?></span>
					<span class="date_month"><?php comment_date('m') ?></span>
					<span class="date_year"><?php comment_date('Y') ?></span>
				</small>
		</dt>

		<dd class="commentlist_item <?php echo $oddcomment; echo $author_comment_class; ?>" id="comment-<?php comment_ID() ?>">			

			<div class="comment">
				<strong class="author" style="height:<?php echo $gravatar_size; ?>px;line-height:<?php echo $gravatar_size; ?>px;">
				<?php // gravatars
				if (function_exists('gravatar')) { 
					if ('' != get_comment_author_url()) {
				  		echo "<a href='$comment->comment_author_url' title='Visit $comment->comment_author'>";
					} else { 
				  		echo "<a href='http://www.gravatar.com' title='Create your own gravatar at gravatar.com!'>";
					}
				echo "<img src='";
				if ('' == $comment->comment_type) {
					echo gravatar($comment->comment_author_email);
				} elseif ( ('trackback' == $comment->comment_type) || ('pingback' == $comment->comment_type) ) {
					echo gravatar($comment->comment_author_url);
				}
				echo "' alt='' class='gravatar' width='".$gravatar_size."' height='".$gravatar_size."' /></a>";

				} ?>
				<?php comment_author_link() ?></strong> <small>(<?php comment_time('H:i:s'); ?>)</small> : <?php edit_comment_link(__('edit', 'freshy'),'',''); ?>
				<?php if ($comment->comment_approved == '0') : ?>
				<small><?php _e('Your comment is awaiting moderation'); ?></small>
				<?php endif; ?>
				
				<br style="display:none;"/>
			
				<div class="comment_text">				
				<?php comment_text(); ?>
				</div>
			</div>
		</dd>


	<?php /* Changes every other comment to a different class */	
		if ('alt' == $oddcomment) $oddcomment = '';
		else $oddcomment = 'alt';
	?>

	<?php endforeach; /* end for each comment */ ?>

	</dl>

<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

<h3 id="respond"><?php _e('Leave a comment', 'freshy'); ?></h3>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p><?php _e('You must be', 'freshy'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>"><?php _e('logged in', 'freshy'); ?></a> <?php _e('to post a comment', 'freshy'); ?></p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( $user_ID ) : ?>

<p><?php _e('Logged in as', 'freshy'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account"><?php _e('Logout', 'freshy'); ?> &raquo;</a></p>

<?php else : ?>

<p>
	<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
	<label for="author"><?php _e('Name', 'freshy'); ?> <?php if ($req) _e('(required)', 'freshy'); ?></label>
</p>
<p>
	<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
	<label for="email"><?php _e('Mail', 'freshy'); ?> <?php _e('(will not be published)', 'freshy'); ?> <?php if ($req) _e('(required)', 'freshy'); ?></label>
</p>
<p>
	<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
	<label for="url"><?php _e('Website', 'freshy'); ?></label>
</p>

<?php endif; ?>

<p><small><?php _e('You can use these tags', 'freshy'); ?> : <?php echo allowed_tags(); ?></small></p>

<p>
<textarea name="comment" id="comment" cols="60" rows="10" tabindex="4"></textarea>
</p>
<p>
<input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit comment', 'freshy'); ?>" />
<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
</p>

<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>
