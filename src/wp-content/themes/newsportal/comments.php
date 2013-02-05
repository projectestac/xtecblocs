<?php // Do not delete these lines
 if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die ( __('Please do not load this page directly. Thanks!','news-portal'));

        if (!empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_'.$cookiehash] != $post->post_password) {  // and it doesn't match the cookie
    ?>
    
    <p class="nocomments"><?php _e("This post is password protected. Enter the password to view comments.",'news-portal'); ?><p>
    
    <?php
    return;
            }
        }

  /* This variable is for alternating comment background */
  $oddcomment = "graybox";
?>

<!-- You can start editing here. -->

<?php if ($comments) : ?>
 <a name="comments"></a><h2><?php comments_number( __('No Responses','news-portal'),__('One Response','news-portal'),__('% Responses','news-portal') );?></h2>

 <ol class="commentlist">

 <?php foreach ($comments as $comment) : ?>

  <li class="<?=$oddcomment;?>">
   <a name="comment-<?php comment_ID() ?>"></a><cite><?php comment_author_link() ?></cite> <?php _e('Says:','news-portal');?><br />
   <!--<small class="commentmetadata"><a href="#comment-<?php comment_ID() ?>" title="<?php comment_date('l, F jS, Y') ?> <?php _e('at','news-portal');?> <?php comment_time() ?>"><?php /* $entry_datetime = abs(strtotime($post->post_date)); $comment_datetime = abs(strtotime($comment->comment_date)); echo time_since($entry_datetime, $comment_datetime) */ ?></a> after publication. <?php edit_comment_link('e','',''); ?></small>-->
   <small class="commentmetadata"><a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date('j F Y') ?> <?php _e('at','news-portal'); ?> <?php comment_time() ?></a> <?php edit_comment_link('e','',''); ?></small>
   
   <?php comment_text() ?>
   
  </li>
  
  <?php /* Changes every other comment to a different class */ 
   if("graybox" == $oddcomment) {$oddcomment="";}
   else { $oddcomment="graybox"; }
  ?>

 <?php endforeach; /* end for each comment */ ?>

 </ol>

 <?php else : // this is displayed if there are no comments so far ?>

  <?php if ('open' == $post-> comment_status) : ?>
  <!-- If comments are open, but there are no comments. -->
  
  <?php else : // comments are closed ?>
  <!-- If comments are closed. -->
  <p class="nocomments"><?php _e('Comments are closed.','news-portal');?></p>
  
 <?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post-> comment_status) : ?>

	<h2 id="postcomment"><?php _e('Leave a comment','news-portal'); ?></h2>
	
	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
	
		<p><?php _e('You must be','news-portal'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>"><?php _e('logged in','news-portal'); ?></a> <?php _e('to post a comment.','news-portal'); ?></p>
	
	<?php else : ?>
	
		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
		
		<?php if ( $user_ID ) : ?>
		
		<p><?php _e('Logged in as','news-portal'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account','news-portal') ?>"><?php _e('Logout','news-portal'); ?> &raquo;</a></p>

		<?php else : ?>
	
		<p>
		<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="30" tabindex="1" />
		<label for="author"><?php _e('Name','news-portal'); ?><?php if ($req) _e('(required)','news-portal'); ?></label>
		</p>
		
		<p>
		<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="30" tabindex="2" />
		<label for="email"><?php _e('E-mail','news-portal'); ?> (<?php if ($req) _e('required, never displayed','news-portal') ?>)</label>
		</p>
		
		<p>
		<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="30" tabindex="3" />
		<label for="url"><?php _e('<abbr title="Uniform Resource Identifier">URI</abbr>','news-portal'); ?></label>
		</p>

		<?php endif; ?>

	<p>
	<textarea name="comment" id="comment" cols="70" rows="10" tabindex="4"></textarea>
	</p>

	<p>
	<input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit Comment','news-portal') ?>" />
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	</p>

	<?php do_action('comment_form', $post->ID); ?>

	</form>

	<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>

