<?php
   load_theme_textdomain( 'stardust' );

if ( function_exists('register_sidebar') )
	register_sidebar(array(
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '',
        'after_title' => '',
    ));

function stardust_comment($comment, $args, $depth) {
 $GLOBALS['comment'] = $comment; ?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
		<div id="div-comment-<?php comment_ID() ?>">
		<?php if(function_exists('get_avatar')) { echo get_avatar($comment, '32'); } ?>
			<?php printf(__('<cite class="fn">%s</cite> says:','stardust'), get_comment_author_link()) ?>
			<?php if ($comment->comment_approved == '0') : ?>
			<em><?php _e('Your comment is awaiting moderation.','stardust') ?></em>
			<?php endif; ?>
			<br />
      <small class="commentmetadata"><a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date(_c('F jS, Y|Dates','stardust')) ?> <?php _c('at|Dates','stardust') ?> <?php comment_time() ?></a> <?php edit_comment_link(__('edit','stardust'),'&nbsp;&nbsp;',''); ?></small>
			<div class="commentbody">
        <div>
  				<?php comment_text() ?>
          <div class="reply">
             <?php comment_reply_link(array_merge( $args, array('add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
          </div>				
        </div>
      </div>
    </div>
<?php
        }
?>
