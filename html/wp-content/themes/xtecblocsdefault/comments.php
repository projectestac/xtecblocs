<?php
// This file is loaded by comments_template() and also included directly from content.php to show the comments of the news
if (post_password_required($post)) { ?>
    <p class="nocomments">This post is password protected. Enter the password to view comments.</p>
    <?php
    return;
}

// Allow to see the comments in main page
$comments = get_comments(array('post_id' => $post->ID, 'status' => 'approve', 'order' => 'ASC'));
?>

<?php if ($comments) : ?>
    <h3 id="comments"><?php comments_number('No hi ha comentaris', 'Un comentari', '% comentaris', $post); ?> a &#8220;<?php echo get_the_title($post); ?>&#8221;</h3>

    <ol class="commentlist">
        <?php wp_list_comments(array('style' => 'ol'), $comments); ?>
    </ol>

<?php elseif (!comments_open($post)) : ?>
    <p class="nocomments">Comments are closed.</p>
<?php endif; ?>

<?php if (comments_open($post)) : ?>
    <?php if (!is_user_logged_in()) : ?>
        <h3 id="respond">Envia un comentari</h3>
        <p>Has d'estar <a href="<?php echo esc_url(site_url('index.php?a=login')); ?>">validat</a> per enviar comentaris.</p>
    <?php else :
        comment_form(array(
            'title_reply' => 'Envia un comentari',
            'logged_in_as' => '<p>T\'has identificat com a <strong>' . esc_html(wp_get_current_user()->display_name) . '</strong>.</p>',
            'comment_notes_after' => '<input type="hidden" name="redirect_to" value="index.php?msg=newComment" />',
            'label_submit' => 'Submit Comment',
        ), $post->ID);
    endif; ?>

<?php endif; ?>
