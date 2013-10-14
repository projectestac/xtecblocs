<?php get_header(); ?>

<div id="content">
<?php 
    global $post;
    $weekblog = $post;
    $wb_name = get_post_meta($weekblog->ID, '_xtecweekblog-name', true);
    $wb_url = get_blogaddress_by_name($wb_name);
    $wb_blog_title = get_bloginfo('title');
    $wb_description = get_post_meta($weekblog->ID, '_xtecweekblog-description', true);
    ?>	
    <div id="box">
        <span class="contentboxheadright"></span>
        <span class="contentboxheadleft"></span>
        <h2 class="contentboxheadfons">Bloc destacat</h2>
        <div id="bloc_destacat">
            <a href="<?php echo $wb_url;?>" target="_blank" title="<?php $wb_blog_title;?>"><?php echo get_the_post_thumbnail($weekblog->ID, 'xtecweekblog', array('alt' => 'Accedeix al bloc'));?></a>
            <p><?php echo $wb_description?></p>
            <ul>
                <li><a href="<?php echo $wb_url;?>" target="_blank">Accedeix al bloc</a></li>		
            </ul>
            <div class="clear"></div>
        </div>
    </div>
</div>

<?php

get_sidebar();
get_footer();