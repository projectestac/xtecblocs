<div class="rsidebar">
<ul>
	<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(2) ) : else : ?>	
    <li>
				<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</li>
    <li><h2><?php _e('Recent Comments','big-blue');?></h2>
		
				     <?php
    global $wpdb;

    $sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
    comment_post_ID, comment_author, comment_date_gmt, comment_approved,
    comment_type,comment_author_url,
    SUBSTRING(comment_content,1,30) AS com_excerpt
    FROM $wpdb->comments
    LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
    $wpdb->posts.ID)
    WHERE comment_approved = '1' AND comment_type = '' AND
    post_password = ''
    ORDER BY comment_date_gmt DESC
    LIMIT 10";
    $comments = $wpdb->get_results($sql);

    $output = $pre_HTML;
    $output .= "\n<ul>";
    foreach ($comments as $comment) {

    $output .= "\n<li>".strip_tags($comment->comment_author)
    .":" . "<a href=\"" . get_permalink($comment->ID) .
    "#comment-" . $comment->comment_ID . "\" title=\"on " .
    $comment->post_title . "\">" . strip_tags($comment->com_excerpt)
    ."</a></li>";

    }
    $output .= "\n</ul>";
    $output .= $post_HTML;

    echo $output;?>

	
			</li>
    <li><h2><?php _e('Recent Posts','big-blue');?></h2>
				<ul>

<?php
$myposts = get_posts('numberposts=10&offset=1');
foreach($myposts as $post) :
?>
<li><a href="<?php the_permalink(); ?>"><?php the_title();
?></a></li>
<?php endforeach; ?>
	
		</ul>
		</li>
    
		<?php endif; ?>
		</ul>
</div>