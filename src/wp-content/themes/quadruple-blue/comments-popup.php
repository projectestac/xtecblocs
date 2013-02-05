<?php 
/* Don't remove these lines. */
add_filter('comment_text', 'popuplinks');
foreach ($posts as $post) { start_wp();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
     <title><?php echo get_settings('blogname'); ?> - Comments on <?php the_title(); ?></title>

	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />
	<style type="text/css" media="screen">
		@import url( <?php bloginfo('template_url'); ?>/style_comments_popup.css );
	</style>

</head>
<body id="commentspopup">

<h1 id="header"><a href="" title="<?php echo get_settings('blogname'); ?>"><?php echo get_settings('blogname'); ?></a></h1>

<h2 id="comments"><?php _e('Comments','quadruple-blue');?></h2>

<p><a href="<?php echo get_settings('siteurl'); ?>/wp-commentsrss2.php?p=<?php echo $post->ID; ?>"><?php _e('<abbr title="Really Simple Syndication">RSS</abbr> feed for comments on this post.','quadruple-blue');?></a></p>

<?php if ('open' == $post->ping_status) { ?>
<p><a href="<?php trackback_url() ?>" title="trackback URI to this entry"><?php _e('TrackBack <acronym title="Uniform Resource Identifier">URI</acronym>','quadruple-blue'); ?></a></p>
<?php } ?>

<?php
// this line is WordPress' motor, do not delete it.
$commenter = wp_get_current_commenter();
extract($commenter);
$comments = get_approved_comments($id);
$post = get_post($id);
if (!empty($post->post_password) && $_COOKIE['wp-postpass_'. COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
	echo(get_the_password_form());
} else { ?>

<?php if ($comments) { ?>
<ol id="commentlist">
<?php foreach ($comments as $comment) { ?>
	<li id="comment-<?php comment_ID() ?>">
	<cite><?php comment_type(__('Comment','quadruple-blue'), __('Trackback','quadruple-blue'), __('Pingback','quadruple-blue')); ?> <?php _e('by','quadruple-blue');?> <strong><?php comment_author_link() ?></strong> &#8212; <?php comment_date() ?> @ <a href="#comment-<?php comment_ID() ?>"><?php comment_time() ?></a></cite>
	<?php comment_text() ?>
	</li>

<?php } // end for each comment ?>
</ol>
<?php } else { // this is displayed if there are no comments so far ?>
	<p><?php _e('No comments yet.','quadruple-blue');?></p>
<?php } ?>

<?php if ('open' == $post->comment_status) { ?>
<h2><?php _e('Leave a comment','quadruple-blue');?></h2>
<p><?php _e('Line and paragraph breaks automatic, e-mail address never displayed, <acronym title="Hypertext Markup Language">HTML</acronym> allowed:','quadruple-blue');?> <code><?php echo allowed_tags(); ?></code></p>

<form action="<?php echo get_settings('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
	<p>
	  <input type="text" name="author" id="author" class="textarea" value="<?php echo $comment_author; ?>" size="28" tabindex="1" />
	   <label for="author"><?php _e('Name','quadruple-blue');?></label>
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	<input type="hidden" name="redirect_to" value="<?php echo wp_specialchars($_SERVER["REQUEST_URI"]); ?>" />
	</p>

	<p>
	  <input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="28" tabindex="2" />
	   <label for="email"><?php _e('E-mail','quadruple-blue');?></label>
	</p>

	<p>
	  <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="28" tabindex="3" />
	   <label for="url"><acronym title="Uniform Resource Identifier">URI</acronym></label>
	</p>

	<p>
	  <label for="comment"><?php _e('Your Comment','quadruple-blue');?></label>
	<br />
	  <textarea name="comment" id="comment" cols="40" rows="4" tabindex="4"></textarea>
	</p>

	<p>
	  <input name="submit" type="submit" tabindex="5" value="<?php _e('Say It!','quadruple-blue');?>" />
	</p>
	<?php do_action('comment_form', $post->ID); ?>
</form>
<?php } else { // comments are closed ?>
<p><?php _e('Sorry, the comment form is closed at this time.','quadruple-blue');?></p>
<?php }
} // end password check
?>

<div><strong><a href="javascript:window.close()">Close this window.</a></strong></div>

<?php // if you delete this the sky will fall on your head
}
?>

<!-- // this is just the end of the motor - don't touch that line either :) -->
<?php //} ?> 
<p class="credit"><?php timer_stop(1); ?> <cite>Powered by <a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform"><strong>Wordpress</strong></a></cite></p>
<?php // Seen at http://www.mijnkopthee.nl/log2/archive/2003/05/28/esc(18) ?>
<script type="text/javascript">
<!--
document.onkeypress = function esc(e) {
	if(typeof(e) == "undefined") { e=event; }
	if (e.keyCode == 27) { self.close(); }
}
// -->
</script>
</body>
</html>
