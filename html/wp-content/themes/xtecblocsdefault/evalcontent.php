<?php
$action = $_GET['a'] ?? '';
switch ($action) {
    case 'terms':
        include __DIR__ . '/terms.htm';
        break;
    case 'newuser':
        if (!is_user_logged_in()) {
            ?>
            <iframe src="wp-signup.php" class="signup-frame"></iframe>
            <?php
        }
        break;
    case 'list':
        $desc = is_string($_GET['desc'] ?? null) ? $_GET['desc'] : '';
        ?>
        <h2 class="listtitle">Llista de blocs que tenen el descriptor <em><?php echo esc_html($desc); ?></em>.</h2>
        <div class="descriptorsById">
        <table class="llistat blocs-descriptor">
            <thead>
                <tr><th>T&iacute;tol</th><th>Propietari</th><th>Altres descriptors</th></tr>
            </thead>
            <tbody>
        <?php
        $blogs = xtec_descriptors_get_blogs_by_descriptor($desc, true);
        foreach ($blogs as $blog) {
            $blogname = get_blog_option($blog, 'blogname');
            $siteurl = get_blog_option($blog, 'siteurl');
            $admin_email = get_blog_option($blog, 'admin_email');
            $admin_user = get_user_by('email', $admin_email);
            $admin_name = $admin_user ? $admin_user->display_name : '';
            ?>
            <tr>
                <td class="blogByDescriptor titol">
                    <a href="<?php echo esc_url($siteurl); ?>"
                        target="_blank"><?php echo esc_html(stripslashes($blogname)); ?></a>
                    <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo xtec_favorites_url('addPrefer', $blog); ?>" title="Preferit"><img
                                src="<?php bloginfo('template_directory'); ?>/images/myblogs.gif" alt="Preferit"/></a>
                    <?php endif; ?>
                </td>
                <td class="propietari"><?php echo esc_html($admin_name); ?></td>
                <td class="altres-descriptors">
                    <?php
                    $other_descriptors = xtec_descriptors_get_descriptors_by_blog($blog);
                    foreach ($other_descriptors as $other_descriptor) {
                        if ($other_descriptor !== $desc) {
                            $url = home_url('/index.php?a=list&desc=' . rawurlencode($other_descriptor));
                            ?>
                            <a href="<?php echo esc_url($url); ?>"
                                title=""><?php echo esc_html($other_descriptor); ?></a>
                            <?php
                        }
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
            </tbody>
        </table>
        </div>
        <?php
        break;
    case 'allDescriptors':
        ?>
        <div class="box">
            <span class="contentboxheadright"></span>
            <span class="contentboxheadleft"></span>
            <h2 class="contentboxheadfons">Descriptors més rellevants</h2>
            <ul class="cloudtags">
        <?php
        $cloudArray = xtec_descriptors_get_descriptors_cloud(256, 12, 25);

        foreach ($cloudArray as $cloud) {
            $url = home_url('/index.php?a=list&desc=') .
                rawurlencode(htmlspecialchars_decode($cloud['tag'], ENT_QUOTES));
            // The font size depends on the number of blogs with the descriptor
            echo "<li><a style='font-size:" . $cloud['size'] . "px;' class='tag_cloud' href='" . esc_url($url) . "'> ";
            echo $cloud['tag'];
            echo '</a></li>';
        }
        ?>
            </ul>
        </div>
        <?php
        break;
    case 'mostActive':
        $ipp = 20;
        $init = max(1, (int)($_GET['init'] ?? 1));
        $mostActive = xtec_latest_posts_most_active_blogs($ipp, $init - 1);
        $blogsNumber = xtec_latest_posts_num_active_blogs();
        $pager = xtec_pager($init, $blogsNumber, 'index.php?a=mostActive&amp;init=%%', $ipp);
        $maxPosts = xtec_latest_posts_num_posts_of_most_active_blog();
        ?>
        <h2 class="listtitle">Llista dels blocs m&eacute;s actius els darrers 60 dies.</h2>
        <div class="pager"><?php echo $pager; ?></div><br />
        <table class="llistat blocs-actius">
            <thead>
                <tr><th>T&iacute;tol</th><th>Activitat (%)</th><th>Darrer article</th></tr>
            </thead>
            <tbody>
        <?php
        foreach ($mostActive as $active) {
            ?>
            <tr>
                <td class="titol">
                    <a href='<?php echo esc_url($active['blog_url']); ?>' target="_blank"
                       title="Entra al bloc"><?php echo esc_html(stripslashes($active['blog_title'])); ?></a>
                    <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo xtec_favorites_url('addPrefer', $active['blogId']); ?>"
                            title="Preferit"><img
                                src="<?php bloginfo('template_directory'); ?>/images/myblogs.gif" alt="Preferit"/></a>
                    <?php endif; ?>
                </td>
                <td class="activitat"><?php echo $maxPosts > 0 ? $active['postNumber'] / $maxPosts * 100 : 0; ?></td>
                <td class="data"><?php echo date('d/m/Y - H.i', strtotime($active['last_updated'])); ?></td>
            </tr>
            <?php
        }
        ?>
            </tbody>
        </table>
        <?php
        break;
    case 'lastCreated':
        $ipp = 20;
        $init = max(1, (int)($_GET['init'] ?? 1));
        $blogs = xtec_api_latest_blogs($ipp, 3000, 'registered', $init - 1);
        $blogsNumber = xtec_api_blogs_number();
        $totalBlogs = $blogsNumber['blogs'] - $blogsNumber['blogsPrivate'];
        $pager = xtec_pager($init, $totalBlogs, 'index.php?a=lastCreated&amp;init=%%', $ipp);
        ?>
        <h2 class="listtitle">Llista dels darrers blocs creats.</h2>
        <div class="pager"><?php echo $pager; ?></div><br />
        <table class="llistat blocs-nous">
            <thead>
                <tr><th>T&iacute;tol</th><th>Data de creaci&oacute;</th></tr>
            </thead>
            <tbody>
        <?php
        foreach ($blogs as $blog) {
            ?>
            <tr>
                <td class="titol">
                    <a href='<?php echo esc_url($blog['blog_url']); ?>' target="_blank"
                       title="Entra al bloc"><?php echo esc_html(stripslashes($blog['blog_title'])); ?></a>
                    <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo xtec_favorites_url('addPrefer', $blog['blog_id']); ?>" title="Preferit"><img
                                src="<?php bloginfo('template_directory'); ?>/images/myblogs.gif" alt="Preferit"/></a>
                    <?php endif; ?>
                </td>
                <td class="data"><?php echo date('d/m/Y - H.i', strtotime($blog['registered'])); ?></td>
            </tr>
            <?php
        }
        ?>
            </tbody>
        </table>
        <?php
        break;
    case 'newsList':
        $ipp = 5;
        $init = max(1, (int)($_GET['init'] ?? 1));
        $newsList = xtec_get_news($init - 1, $ipp);
        $totalNews = (int)wp_count_posts('post')->publish;
        $pager = xtec_pager($init, $totalNews, 'index.php?a=newsList&amp;init=%%', $ipp);
        ?>
        <h2 class="listtitle">Llista de not&iacute;cies publicades</h2>
        <div class="pager"><?php echo $pager; ?></div><br />
        <table class="llistat noticies">
            <thead>
                <tr><th>T&iacute;tol</th><th>Data de publicaci&oacute;</th></tr>
            </thead>
            <tbody>
        <?php
        foreach ($newsList as $new) {
            ?>
            <tr>
                <td class="titol"><a href="index.php?id=<?php echo $new->ID ?>"
                    title="V&eacute;s a la notícia"><?php echo esc_html(stripslashes($new->post_title)); ?></a></td>
                <td class="data"><?php echo date('d/m/Y', strtotime($new->post_date)); ?></td>
            </tr>
            <?php
        }
        ?>
            </tbody>
        </table>
        <?php
        break;
    default:
        get_template_part('content');
        break;
}
