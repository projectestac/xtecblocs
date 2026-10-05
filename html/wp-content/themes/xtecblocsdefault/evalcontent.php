<?php
$action = isset($_REQUEST['a']) ? $_REQUEST['a'] : '';
switch ($action) {
    case "terms":
        include('terms.htm');
        break;
    case 'new':
        ?>
            <script>window.location = 'wp-signup.php';</script>
        <?php
        break;
    case 'newuser':
        if (!is_user_logged_in()) {
            ?>
            <iframe src="wp-signup.php" width="100%" height="700" scrolling="auto" frameborder="0"></iframe>            
            <?php
        }
        break;
    case 'login':
        ?>
        <script>window.location = 'wp-login.php?redirect_to=<?php echo site_url() ?>';</script>
        <?php
        break;
    case 'list':
        $desc = is_string($_GET['desc'] ?? null) ? $_GET['desc'] : '';
        print '<h2 style="color: #1C4387; font-size: 1.3em; background-image: none; border: none; ' .
            'margin-bottom: 1em; margin-top: 1em;">Llista de blocs que tenen el descriptor <em>' .
            esc_html($desc) . '</em>.</h2>';
        print '<div class="descriptorsById">';
        print '<table width="100%">';
        print '<th align="left" valign="top">T&iacute;tol</th><th align="left" valign="top">Propietari</th>' .
            '<th align="left" valign="top">Altres descriptors</th>';
        $blogs = xtec_descriptors_get_blogs_by_descriptor($desc, true);
        $bgcolor = '';
        foreach ($blogs as $blog) {
            $blogname = get_blog_option($blog, 'blogname');
            $siteurl = get_blog_option($blog, 'siteurl');
            $admin_email = get_blog_option($blog, 'admin_email');
            $admin_user = get_user_by('email', $admin_email);
            $admin_name = $admin_user ? $admin_user->display_name : '';
            $bgcolor = ( $bgcolor == '#ffffff' ) ? '#e5f2fe' : '#ffffff';
            ?>
            <tr bgcolor="<?php echo $bgcolor ?>">
                <td class="blogByDescriptor" valign="top" width="300">
                    <a href="<?php echo esc_url($siteurl); ?>"
                        target="_blank"><?php echo esc_html(stripslashes($blogname)); ?></a>
                    <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo xtec_favorites_url('addPrefer', $blog); ?>" title="Preferit"><img
                                src="<?php bloginfo('template_directory'); ?>/images/myblogs.gif" border="0"
                                    alt="Preferit"/></a>
                    <?php endif; ?>
                </td>
                <td valign="top" width="100"><?php echo esc_html($admin_name); ?></td>
                <td valign="top" width="200">
                    <?php
                    $other_descriptors = xtec_descriptors_get_descriptors_by_blog($blog);
                    foreach ($other_descriptors as $other_descriptor) {
                        if ($other_descriptor !== $desc) {
                            $url = get_option('home') . '/index.php?a=list&desc=' . rawurlencode($other_descriptor);
                            ?>
                            <a style="font-size:12 px; color:#0000EE; text-decoration:none;"
                                href="<?php echo esc_url($url); ?>"
                                title=""><?php echo esc_html($other_descriptor); ?></a>
                            <?php
                        }
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        print "</table>";
        print "</div>";
        break;
    case 'allDescriptors':
        print '<div class="box">';
        print '	<span class="contentboxheadright"></span>';
        print '	<span class="contentboxheadleft"></span>';
        print '	<h2 class="contentboxheadfons">Descriptors més rellevants</h2>';
        print '	<ul class="cloudtags">';

        $cloudArray = xtec_descriptors_get_descriptors_cloud(256, 12, 25);

        foreach ($cloudArray as $cloud) {
            print ("<li><a style='font-size:" . $cloud['size'] . "px; color:#1E4588;' class='tag_cloud' href='" .
                esc_url(get_option('home') . '/index.php?a=list&desc=' .
                rawurlencode(htmlspecialchars_decode($cloud['tag'], ENT_QUOTES))) . "'> ");
            print($cloud['tag']);
            print('</a></li>');
        }
        print '	</ul>';
        print '</div>';
        break;
    case 'mostActive':
        $ipp = 20;
        print '<h2 style="color: #1C4387; font-size: 1.3em; background-image: none; border: none; ' .
            'margin-bottom: 1em; margin-top: 1em;">Llista dels blocs m&eacute;s actius els darrers 60 dies.</h2>';
        $init = max(1, (int)($_REQUEST['init'] ?? 1));
        $mostActive = xtec_lastest_posts_most_active_blogs($ipp, $init - 1);
        $blogsNumber = xtec_lastest_posts_num_active_blogs();
        $pager = pager($init, $blogsNumber, 'index.php?a=mostActive&amp;init=%%', $ipp);
        $maxPosts = xtec_lastest_posts_num_posts_of_most_active_blog();
        print '<div style="text-align:right; padding-right:60px;">' . $pager . '</div><br/ >';
        print '<table>';
        print '<th align="left" valign="top">T&iacute;tol</th><th align="left" valign="top">Activitat (%)</th>' .
            '<th align="left" valign="top">Darrer article</th>';
        $bgcolor = "#e5f2fe";
        foreach ($mostActive as $active) {
            $bgcolor = ($bgcolor == '#e5f2fe') ? '#ffffff' : '#e5f2fe';
            ?>
            <tr bgcolor="<?php echo $bgcolor; ?>">
                <td width="250">
                    <a href='<?php echo esc_url($active['blog_url']); ?>' target="_blank"
                       title="Entra al bloc"><?php echo esc_html(stripslashes($active['blog_title'])); ?></a>
                    <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo xtec_favorites_url('addPrefer', $active['blogId']); ?>"
                            title="Preferit"><img
                                src="<?php bloginfo('template_directory'); ?>/images/myblogs.gif" border="0"
                                    alt="Preferit"/></a>
                    <?php endif; ?>
                </td>
                <td align="right"
                    width="100"><?php echo $maxPosts > 0 ? $active['postNumber'] / $maxPosts * 100 : 0; ?></td>
                <td width="150"><?php echo date('d/m/Y - H.i', strtotime($active['last_updated'])); ?></td>
            </tr>
            <?php // print_r($active);?>
            <?php
        }
        print '</table>';
        break;
    case 'lastCreated':
        $ipp = 20;
        print '<h2 style="color: #1C4387; font-size: 1.3em; background-image: none; border: none; ' .
            'margin-bottom: 1em; margin-top: 1em;">Llista dels darrers blocs creats.</h2>';
        $init = max(1, (int)($_REQUEST['init'] ?? 1));
        $blogs = xtec_api_lastest_blogs($ipp, 3000, 'registered', $init - 1);
        $blogsNumber = getBlogsNumber();
        $totalBlogs = $blogsNumber['blogs'] - $blogsNumber['blogsPrivate'];
        $pager = pager($init, $totalBlogs, 'index.php?a=lastCreated&amp;init=%%', $ipp);
        print '<div style="text-align:right; padding-right:60px;">' . $pager . '</div><br />';
        print '<table>';
        print '<th align="left" valign="top">T&iacute;tol</th>' .
            '<th align="left" valign="top">Data de creaci&oacute;</th>';
        $bgcolor = "#e5f2fe";
        foreach ($blogs as $blog) {
            $bgcolor = ($bgcolor == '#e5f2fe') ? '#ffffff' : '#e5f2fe';
            ?>      
            <tr bgcolor="<?php echo $bgcolor; ?>">
                <td width="300">
                    <a href='<?php echo esc_url($blog['blog_url']); ?>' target="_blank"
                       title="Entra al bloc"><?php echo esc_html(stripslashes($blog['blog_title'])); ?></a>
                    <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo xtec_favorites_url('addPrefer', $blog['blog_id']); ?>" title="Preferit"><img
                                src="<?php bloginfo('template_directory'); ?>/images/myblogs.gif" border="0"
                                    alt="Preferit"/></a>
                    <?php endif; ?>
                </td>
                <td width="150"><?php echo date('d/m/Y - H.i', strtotime($blog['registered'])); ?></td>
            </tr>
            <?php
        }
        print '</table>';
        break;
    case 'newsList':
        print '<h2 style="color: #1C4387; font-size: 1.3em; background-image: none; border: none; ' .
            'margin-bottom: 1em; margin-top: 1em;">Llista de not&iacute;cies publicades</h2>';
        $newsList = getNewsList();
        print '<div style="text-align:right; padding-right:60px;"></div><br/ >';
        print '<table>';
        print '<th align="left" valign="top">T&iacute;tol</th>' .
            '<th align="left" valign="top">Data de publicaci&oacute;</th>';
        $bgcolor = "#e5f2fe";
        foreach ($newsList as $new) {
            $bgcolor = ($bgcolor == '#e5f2fe') ? '#ffffff' : '#e5f2fe';
            ?>
            <tr bgcolor="<?php echo $bgcolor; ?>">
                <td width="250"><a href=index.php?id=<?php echo $new['newId'] ?>
                    title="V&eacute;s a la notícia"><?php echo esc_html(stripslashes($new['new_title'])); ?></a></td><td
                    width="150"><?php echo date('d/m/Y', strtotime($new['post_date'])); ?></td></tr>
            <?php // print_r($new);?>
            <?php
        }
        print '</table>';
        break;
    default:
        include('content.php');
        break;
}
