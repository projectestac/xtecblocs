<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die (__('Please do not load this page directly. Thanks!','big-blue'));

        if (!empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
				?>
				
				<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.','big-blue');?><p>
				
				<?php
				return;
            }
        }

		/* This variable is for alternating comment background */
		$oddcomment = 'odd';
?>

<!-- You can start editing here. -->

<div class="boxcomments">

<?php if ($comments) : ?>

<?php 

	/* Count the totals */
	$numPingBacks = 0;
	$numComments  = 0;

	/* Loop through comments to count these totals */
	foreach ($comments as $comment) {
		if (get_comment_type() != "comment") { $numPingBacks++; }
		else { $numComments++; }
	}

?>

<?php 

	/* This is a loop for printing comments */
	if ($numComments != 0) : ?>

	<ol class="commentlist">

	<li class="commenthead"><h2 id="comments"><?php comments_number(__('No Responses','big-blue'), __('One Response','big-blue'), __('% Responses','big-blue') );?> <?php _e('to','big-blue');?> &#8220;<?php the_title(); ?>&#8221;</h2></li>
	
	<?php foreach ($comments as $comment) : ?>
	<?php if (get_comment_type()=="comment") : ?>
	
<li class="<?php if ( $comment->comment_author_email == get_the_author_email() ) echo 'mycomment'; else echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">

		<p style="margin-bottom:5px;"><?php _e('by','big-blue');?> <strong><?php comment_author_link() ?></strong> <?php _e('on','big-blue');?> <a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date('d F Y') ?></a> | <a href="#respond"><?php _e('reply','big-blue');?></a><?php edit_comment_link(__('Edit','big-blue'),' | ',''); ?></p>
		<?php if ($comment->comment_approved == '0') : ?>
		<em><?php _e('Your comment is awaiting moderation.','big-blue');?></em>
		<?php endif; ?>
		<?php comment_text() ?>
	</li>
		
	<?php /* Changes every other comment to a different class */	
	if ('alt' == $oddcomment) $oddcomment = '';
	else $oddcomment = 'odd';
	?>
	
	<?php endif; endforeach; ?>
	
	</ol>
	
	<?php endif; ?>

<?php

	/* This is a loop for printing trackbacks if there are any */
	if ($numPingBacks != 0) : ?>

	<ol class="tblist">

	<li style="background:transparent;padding-left:0;"><h2 id="trackbacks"><?php _e($numPingBacks); ?> <?php _e('Trackback(s)','big-blue');?></h2></li>
	
<?php foreach ($comments as $comment) : ?>
<?php if (get_comment_type()!="comment") : ?>

	<li id="comment-<?php comment_ID() ?>">
		<?php comment_date('d F Y') ?>: <?php comment_author_link() ?>
		<?php if ($comment->comment_approved == '0') : ?>
		<em><?php _e('Your comment is awaiting moderation.','big-blue');?></em>
		<?php endif; ?>
	</li>
	
	<?php if('odd'==$thiscomment) { $thiscomment = 'even'; } else { $thiscomment = 'odd'; } ?>
	
<?php endif; endforeach; ?>

	</ol>

<?php endif; ?>
	
<?php else : 

	/* No comments at all means a simple message instead */ 
?>

<?php endif; ?>

<?php if (comments_open()) : ?>
	
	<?php if (get_option('comment_registration') && !$user_ID ) : ?>
		<p id="comments-blocked"><?php _e('You must be','big-blue');?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=
		<?php the_permalink(); ?>"><?php _e('logged in','big-blue');?></a> <?php _e('to post a comment.','big-blue');?></p>
	<?php else : ?>

	<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

	<h3 id="respond"><?php _e('Post a Comment','big-blue');?></h3>

	<?php if ($user_ID) : ?>
	
	<p><?php _e('You are logged in as','big-blue');?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php">
		<?php echo $user_identity; ?></a>. <?php _e('To logout','big-blue');?>  <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account','big-blue');?>"><?php _e('click here','big-blue');?></a>.
	</p>
	
<?php else : ?>	
	
		<p><label for="author"><?php _e('Name','big-blue');?> <?php if ($req) _e('(required)','big-blue'); ?></label>
		<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" /></p>
				
		<p><label for="email"><?php _e('E-mail','big-blue');?> (<?php _e('will not be published','big-blue');?>) <?php if ($req) _e('(required)','big-blue'); ?></label>
		<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" tabindex="2" size="22" /></p>		
		
		<p><label for="url"><?php _e('Website','big-blue');?></label>
		<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" /></p>
	
	<?php endif; ?>

		<p><textarea name="comment" id="comment" cols="5" rows="10" tabindex="4"></textarea></p>

		<p><input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit Comment','big-blue');?>" />
		<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" /></p>
	
	<?php do_action('comment_form', $post->ID); ?>

	</form>

<?php endif; // If registration required and not logged in ?>

<?php else : // Comments are closed ?>
	<p id="comments-closed"><?php _e('Sorry, comments for this entry are closed at this time.','big-blue');?></p>
<?php endif; ?></div>