<?php
if (isset($_GET['id'])) {
    $post = get_post((int)$_GET['id']);

    // Only published news can be shown
    if (
        $post !== null
        && ($post->post_type !== 'post' || $post->post_status !== 'publish' || post_password_required($post))
    ) {
        $post = null;
    }

    if (($_GET['msg'] ?? '') === 'newComment') {?>
        <p class="thanks">Gràcies per enviar un comentari nou. No estar&agrave; disponible fins que no sigui validat per
            un administrador/a del portal.</p>
        <?php
    }

    // notícies
    if ($post !== null) { ?>
    <br />
    <div class="box">
        <span class="contentboxheadright"></span>
        <span class="contentboxheadleft"></span>
        <h2 class="contentboxheadfons">Notícies</h2>
        <div class="article">
            <h3><?php echo esc_html($post->post_title); ?></h3>
            <p class="data">Publicat <?php echo dateText(strtotime($post->post_date));?></p>
            <?php echo apply_filters('the_content', $post->post_content); ?>
            <?php if ($post->comment_count > 0) {?>
                <p class="comentari">Aquesta notícia té <a
                    href="index.php?id=<?php echo $post->ID;?>"><?php echo (int)$post->comment_count;?>
                    Comentari/s</a></p>
            <?php } else { ?> 
                <p class="comentari">Aquesta notícia <a href="index.php?id=<?php echo $post->ID;?>">no té
                    comentaris</a></p>
            <?php } ?>
        </div> <!--end of article -->   
    </div>
        <?php
    // comments
        include_once(get_template_directory() . '/comments.php');
    }
} else {
    if (function_exists('xtecweekblog_current_weekblog')) {
        $weekblog = xtecweekblog_current_weekblog();
        if (($weekblog instanceof WP_Post) && xtecweekblog_validate($weekblog->ID)) {
            ['url' => $wb_url, 'blog_title' => $wb_blog_title, 'description' => $wb_description] =
                xtecweekblog_get_data($weekblog);
            $wb_image = get_the_post_thumbnail($weekblog->ID, 'xtecweekblog', ['alt' => 'Accedeix al bloc']);
            ?>  
                <div id="weekblog-box" class="box">
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
                <?php
        } else {
            ?>
            <div id="weekblog-box" class="box">
                <span class="contentboxheadright"></span>
                <span class="contentboxheadleft"></span>
                <h2 class="contentboxheadfons">Bloc destacat</h2>
                <div id="bloc_destacat">
                    <img src="<?php echo esc_url(xtecweekblog_default_banner_url()); ?>" alt="XTECBlocs"
                        title="XTECBlocs" />
                    <p><?php echo wp_kses_post(xtecweekblog_default_message()); ?></p>
                    <div class="clear"></div>
                </div>
            </div>
            <?php
        }
        ?> <br /> <?php
    }
    if (($_GET['msg'] ?? '') === 'newComment') {?>
        <p class="thanks">Gràcies per enviar un comentari nou. No estar&agrave; disponible fins que no sigui validat per
            un administrador/a del portal.</p>
        <?php
    }
    ?>

        <!-- Inici de notícies -->
        
        <div id="news-box" class="box">
            <span class="contentboxheadright"></span>
            <span class="contentboxheadleft"></span>
            <h2 class="contentboxheadfons">Notícies</h2>
            <?php $news_query = new WP_Query(['posts_per_page' => 2]);
            while ($news_query->have_posts()) :
                $news_query->the_post();?>
                <div class="article">
                    <h3><?php the_title(); ?></h3>
                    <p class="data">Publicat <?php echo dateText(strtotime(get_the_date('Y-m-d H:i:s')));?></p>
                    <?php the_content(); ?>
                    <div class="clear"></div>
                </div> <!--end of article -->   
            <?php endwhile;
            wp_reset_postdata(); ?>
            <div class="article">
            <p class="comentari"><a href="<?php echo esc_url(home_url('/index.php?a=newsList'));?>">Més...</a></p>
            </div> 
        </div>
    <br />
    
<div id="latestposts-box" class="box">
    <span class="contentboxheadright"></span>
    <span class="contentboxheadleft"></span>
    <h2 class="contentboxheadfons">Darrers articles als blocs</h2>
    <div class="darreres">
    <?php
    $blogs = xtec_latest_posts_latest_posts(10, 5, 0);
    if (is_array($blogs)) {
        foreach ($blogs as $blog) {
            $date = dateText(strtotime($blog['post_date']));
            //Show the content
            echo "<h3><a href=\"" . esc_url($blog['blog_url']) . "\" style=\"color:#408DD4;\" >" .
                esc_html(stripslashes($blog['blog_title'])) . "</a>";
            //si el user se ha autentificado, mostrará el icono de favoritos
            //pasar a css si es posible!
            if (is_user_logged_in()) {
                echo "&nbsp;&nbsp;<a href='" . xtec_favorites_url('addPrefer', $blog['blog_id']) .
                    "' title='Preferit'><img src='" . esc_url(get_template_directory_uri() . '/images/myblogs.gif') .
                    "' border='0' alt='Preferit'/></a>";
            }
            echo "</h3>";
            //dibuixem la caixa del darrer article
            echo "<div class=\"darrerArticle\">";
            echo "<h4><a href=\"" . esc_url($blog['guid']) . "\" style=\"color:#91beec;\">" .
                esc_html($blog['post_title']) . "</a></h4>";
            echo "<p class=\"data\">Publicat " . $date . " per " . esc_html($blog['author_name']) . "</p>";
            echo "</div>";
            //end of caixa de darrer article
        } //end of foreach
    } // end of if is_array
    ?>
    </div>
</div>
    <?php
} //end of !isset($_REQUEST['id']
?>
            
