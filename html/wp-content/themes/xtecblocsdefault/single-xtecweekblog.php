<?php get_header(); ?>

<div id="content">
<?php
    global $post;
    $weekblog = $post;
    ['url' => $wb_url, 'blog_title' => $wb_blog_title, 'description' => $wb_description] =
        xtecweekblog_get_data($weekblog);
    $wb_image = get_the_post_thumbnail($weekblog->ID, 'xtecweekblog', array('alt' => 'Accedeix al bloc'));
?>  
    <div id="box">
        <span class="contentboxheadright"></span>
        <span class="contentboxheadleft"></span>
        <h2 class="contentboxheadfons">Bloc destacat</h2>
        <div id="bloc_destacat">
            <a href="<?php echo esc_url($wb_url); ?>" target="_blank"
                title="<?php echo esc_attr($wb_blog_title);?>"><?php echo $wb_image; ?></a>
            <?php echo wpautop(wp_kses_post($wb_description)); ?>
            <ul>
                <li><a href="<?php echo esc_url($wb_url); ?>" target="_blank">Accedeix al bloc</a></li>
            </ul>
            <div class="clear"></div>
        </div>
    </div>
</div>

<?php

get_sidebar();
get_footer();