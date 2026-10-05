<?php

defined('ABSPATH') || exit;

/**
    Copyright 2007  Albert Pérez Monfort  (email : aperez16@xtec.cat)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

const XTEC_DESCRIPTORS_DB_VERSION = '1.0';

add_action('network_admin_menu', 'xtec_descriptors_network_admin_menu');
add_action('admin_menu', 'xtec_descriptors_admin_menu');
add_action('update_option_blog_public', 'xtec_descriptors_update_blog_options');
add_action('wp_head', 'xtec_descriptors_head');
add_action('wp_delete_site', 'xtec_descriptors_delete_site');
add_action('wp_ajax_xtec_descriptors_autocomp', 'xtec_descriptors_autocomp');

/**
 * Adds plugin network admin menu.
 */
function xtec_descriptors_network_admin_menu(): void
{
    add_menu_page('Descriptors', 'Descriptors', 'manage_network_options', 'ms-descriptor', 'xtec_descriptors_network_options');
}

/**
 * Displays the network options page of the plugin.
 */
function xtec_descriptors_network_options(): void
{
    global $wpdb;

    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        check_admin_referer('xtec_descriptors_delete_' . (int)$_GET['id']);
    }

    if (isset($_GET['action']) && $_GET['action'] === 'descriptors') {
        check_admin_referer('xtec_descriptors_regenerate');
    }

    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        ?>
        <div id="message" class="updated fade">
            <p><?php echo "S'ha suprimit el descriptor de tot el lloc." ?></p>
        </div>
        <?php
    }

    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'delete':
            xtec_descriptors_delete_descriptor($_GET['id']);
            break;
        case 'descriptors':
            if (isset($_GET['n']) === false) {
                $n = 0;
            } else {
                $n = (int)$_GET['n'];
            }

            $descriptors = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT id,descriptor,blogs FROM {$wpdb->descriptors} ORDER BY descriptor DESC LIMIT %d, 5",
                    $n
                ),
                ARRAY_A
            );

            if (empty($descriptors) === false) {
                print '<table border="1" cellspacing="0" width="100%">';
                foreach ($descriptors as $details) {
                    print "<tr>";
                    print '<td valign="top" width="150">' . esc_html($details['descriptor']) . '</td>';
                    $details['blogs'] = substr($details['blogs'], 0, '-1');
                    if ($details['blogs'] === '') {
                        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->descriptors} WHERE id = %d", $details['id']));
                        $actionmade = __('Deleted');
                    } else {
                        $blogs = explode('$$', $details['blogs']);
                        array_shift($blogs);
                        $descriptorsrow = '$';
                        foreach ($blogs as $blog) {
                            $blog1 = explode('-', $blog);
                            $blogDetails = get_blog_details($blog1[0]);

                            if ($blogDetails !== false) {
                                $descriptorsrow .= '$' . $blog1[0] . '-' . $blogDetails->public . '$';
                            }
                        }
                        print   '<td valign="top">
                                    <span style="background: #00ff00;">' . esc_html($descriptorsrow) . '</span>
                                    <br/>
                                    <span style="background: #ff0000;">' . esc_html($details['blogs']) . '$</span>
                                </td>';
                        $number = substr_count($descriptorsrow, '-1');
                    }
                    if ($descriptorsrow === '$') {
                        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->descriptors} WHERE id = %d", $details['id']));
                        $actionmade = __('Deleted');
                    } else {
                        if ($descriptorsrow !== $details['blogs'] . '$') {
                            $wpdb->query(
                                $wpdb->prepare(
                                    "UPDATE {$wpdb->descriptors} set number = %d, blogs = %s WHERE id = %d",
                                    $number,
                                    $descriptorsrow,
                                    $details['id']
                                )
                            );
                            $actionmade = __('Updated');
                        } else {
                            $actionmade = __('Not action');
                        }
                    }
                    print '<td valign="top" width="10">' . $number . '</td>';
                    print '<td valign="top" width="80">' . $actionmade . '</td>';
                    print '</tr>';
                }
                print '<tr>
                            <td colspan="10">
                                <span style="background: #00ff00;">Ara</span>
                                <br/>
                                <span style="background: #ff0000;">Abans</span>
                            </td>
                        </tr>';
                print '</table>';
                ?>
                <p>
                    <?php _e("If your browser doesn't start loading the next page automatically click this link:"); ?>
                    <a href="<?php echo esc_url(wp_nonce_url('?page=ms-descriptor&action=descriptors&n=' . ($n + 5), 'xtec_descriptors_regenerate')); ?>">
                        <?php _e("Next Blogs"); ?>
                    </a>
                </p>
                <?php
                xtec_descriptors_enqueue_script(
                    'xtec-descriptors-regenerate',
                    'xtecDescriptorsRegenerateNonce',
                    wp_create_nonce('xtec_descriptors_regenerate')
                );
            } else {
                _e('All Done!');
            }
            break;
    }

    if (isset($_GET['action']) && $_GET['action'] !== 'descriptors') {
        $sortida = '<div class="wrap">';
        $sortida .= '<p>' . __("Des d'aquí pots regenerar la taula de cerca dels blocs fent de manera automàtica una crida de cada bloc. Feu clic al següent enllaç per a realitzar l'actualització.") . '</p>';
        $sortida .= '<p><a href="' . esc_url(wp_nonce_url('?page=ms-descriptor&action=descriptors', 'xtec_descriptors_regenerate')) . '">' . __('Regenera la taula de descriptors') . '</a></p>';
        $sortida .= '</div>';
        echo $sortida;

        $descripts = $wpdb->get_results(
            "SELECT id,descriptor,blogs,number FROM {$wpdb->descriptors} order by descriptor"
        );
        if ($descripts) {
            print '<div class="wrap">';
            print '<table border="1" cellpadding="10" cellspacing="10">';
            foreach ($descripts as $descriptor) {
                print '<tr>
                        <td>' . esc_html($descriptor->descriptor) . '</td>
                        <td><strong>' . esc_html($descriptor->number) . '</strong></td>
                        <td>' . esc_html($descriptor->blogs) . '</td>
                        <td><a href="' . esc_url(wp_nonce_url('?page=ms-descriptor&action=delete&id=' . $descriptor->id, 'xtec_descriptors_delete_' . $descriptor->id)) . '">' . __('Delete') . '</a></td>
                    </tr>';
            }
            print '</table>';
            print '</div>';
        }
    }
}

/**
 * Enqueues a script of the plugin in the footer, preceded by the definition of the JavaScript variable that it uses.
 *
 * @param string $handle Name of the script, which is also the name of its file in the js directory.
 * @param string $variable Name of the JavaScript variable.
 * @param string $value Value of the JavaScript variable.
 */
function xtec_descriptors_enqueue_script(string $handle, string $variable, string $value): void
{
    $path = 'js/' . $handle . '.js';
    $version = (string)filemtime(plugin_dir_path(XTEC_PORTAL_FILE) . $path);

    wp_enqueue_script($handle, plugins_url($path, XTEC_PORTAL_FILE), [], $version, true);
    wp_add_inline_script($handle, 'var ' . $variable . ' = ' . wp_json_encode($value) . ';', 'before');
}

/**
 * Adds plugin admin menu.
 */
function xtec_descriptors_admin_menu(): void
{
    $page = add_options_page('Descriptors', 'Descriptors', 'manage_options', 'descriptors', 'xtec_descriptors_options');
    if ($page) {
        add_action('load-' . $page, 'xtec_descriptors_add_help_tab');
    }
}

/**
 * Adds the help tab to the options page of the plugin. The current screen is only available once the page is loaded.
 */
function xtec_descriptors_add_help_tab(): void
{
    get_current_screen()->add_help_tab(['title' => '', 'id' => 'id1', 'content' => __('This screen helps you manage the descriptors of your blog.')]);
}

/**
 * Displays the options page of the plugin.
 */
function xtec_descriptors_options(): void
{
    global $wpdb;

    if (isset($_REQUEST['del']) && $_REQUEST['del'] !== '') {
        check_admin_referer('xtec_descriptors_del_' . (int)$_REQUEST['del']);
    }

    if (isset($_POST['descriptor']) && $_POST['descriptor'] !== '') {
        check_admin_referer('xtec_descriptors_add');
    }

    xtec_descriptors_enqueue_script(
        'xtec-descriptors-autocomp',
        'xtecDescriptorsAutocompUrl',
        admin_url('admin-ajax.php?action=xtec_descriptors_autocomp')
    );

    //Admin menu in Opcions blogs
    if (isset($_REQUEST['del']) && $_REQUEST['del'] !== '') {
        //Delete blog from descriptor blogs list
        //Get blog blogs
        $descriptorBlogs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id,blogs,number,descriptor FROM {$wpdb->descriptors} where `id` = %d",
                $_REQUEST['del']
            )
        );
        if (isset($descriptorBlogs[0])) {
            $newblogs = str_replace(['$' . $wpdb->blogid . '-1$', '$' . $wpdb->blogid . '-0$'], '', $descriptorBlogs[0]->blogs);

            $public = (get_blog_details($wpdb->blogid)->public) ? 1 : 0;
            $number = xtec_descriptors_count_descriptors($descriptorBlogs[0]->descriptor) - $public;

            $sql = $wpdb->prepare(
                "UPDATE {$wpdb->descriptors} SET `blogs` = %s, `number` = %d WHERE id = %d",
                $newblogs,
                $number,
                $descriptorBlogs[0]->id
            );
            //If is the last blog that have this descriptor delete the descriptor
            if ($number === 0 && $newblogs === '$') {
                $sql = $wpdb->prepare("DELETE FROM {$wpdb->descriptors} WHERE id = %d", $descriptorBlogs[0]->id);
            }
            //Execute the SQL sentence
            $wpdb->query($sql);
        }
    }

    $descript = '';
    if (isset($_POST['descriptor']) && $_POST['descriptor'] !== '') {
        $descript = mb_strtolower(wp_unslash($_POST['descriptor']), 'UTF-8');
        // Keep only letters (\p{L}) and numbers (\p{N}) of any language
        $descript = preg_replace('/[^\p{L}\p{N}]+/u', '', $descript);
        $descript = mb_substr($descript, 0, 20, 'UTF-8');
    }

    if (!empty($descript)) {
        //Add the descriptor in descriptors table
        //Try if descriptor exists
        $descriptorId = $wpdb->get_results(
            $wpdb->prepare("SELECT id,blogs FROM {$wpdb->descriptors} where `descriptor` = %s", $descript)
        );
        //If exists add the blog in blogs list if it isn't

        $public = (get_blog_details($wpdb->blogid)->public) ? 1 : 0;
        if (!isset($descriptorId[0])) {
            //Create descriptor
            $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO {$wpdb->descriptors} (descriptor,number,blogs) VALUES (%s, %d, %s)",
                    $descript,
                    $public,
                    '$$' . $wpdb->blogid . '-' . $public . '$'
                )
            );
        } else {
            //Update the descriptor information. First check if the blog is in descriptor blogs field
            if (!strpos($descriptorId[0]->blogs, '$' . $wpdb->blogid . '-1$') && !strpos($descriptorId[0]->blogs, '$' . $wpdb->blogid . '-0$')) {
                $newblogs = $descriptorId[0]->blogs . '$' . $wpdb->blogid . '-' . $public . '$';
                $number = xtec_descriptors_count_descriptors($descript) + $public;
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->descriptors} SET `blogs` = %s, `number` = %d WHERE id = %d",
                        $newblogs,
                        $number,
                        $descriptorId[0]->id
                    )
                );
            }
        }
    }

    ?>
    <div class="wrap">
        <h2>Llista de descriptors del bloc</h2>
        <?php
        $descripts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id,descriptor FROM {$wpdb->descriptors} WHERE blogs LIKE %s OR blogs LIKE %s",
                '%$' . $wpdb->blogid . '-1$%',
                '%$' . $wpdb->blogid . '-0$%'
            )
        );
        $have = false;
        print '<table>';
        foreach ($descripts as $descript) {
            print "<tr><td width=\"150\">" . esc_html($descript->descriptor) . "</td><td><a href=\"" . esc_url(wp_nonce_url('?del=' . $descript->id . '&page=descriptors', 'xtec_descriptors_del_' . $descript->id)) . "\">Esborra</a></td></tr>";
            $have = true;
        }
        if (!$have) {
            print "<tr><td width=\"100\"><strong>No hi ha descriptors definits.</strong></td></tr>";
        }
        print '</table>';
        ?>
        <p>Afegeix un descriptor nou</p>
        <form action="#" method="post" autocomplete="on">
            <?php
            wp_nonce_field('xtec_descriptors_add'); ?>
            <input id="descriptor" type="text" name="descriptor" maxlength="20" size="20"
                   oninput="xtecDescriptorsAutocomplete(this.value)"/>
            <input type="submit" value="Crea el descriptor"/>
            <div id="autocompletediv"></div>
        </form>
    </div>
    <?php
}

/**
 * Updates descriptors according to the privacy of the blog.
 */
function xtec_descriptors_update_blog_options(): void
{
    global $wpdb;
    $blogId = $wpdb->blogid;
    $blogs = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT blogs,descriptor,id from {$wpdb->descriptors} where `blogs` like %s or `blogs` like %s",
            '%$' . $blogId . '-1$%',
            '%$' . $blogId . '-0$%'
        )
    );

    foreach ($blogs as $blog) {
        if (get_blog_details($blogId)->public) {
            $newString = str_replace('$' . $blogId . '-0$', '$' . $blogId . '-1$', $blog->blogs);
            $number = xtec_descriptors_count_descriptors($blog->descriptor) + 1;
        } else {
            $newString = str_replace('$' . $blogId . '-1$', '$' . $blogId . '-0$', $blog->blogs);
            $number = xtec_descriptors_count_descriptors($blog->descriptor) - 1;
            if ($number < 0) {
                $number = 0;
            }
        }

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->descriptors} SET `blogs` = %s, `number` = %d WHERE id = %d",
                $newString,
                $number,
                $blog->id
            )
        );
    }
}

/**
 * Prints the descriptors of the blog as Dublin Core subjects, following the DCMI recommendation to express Dublin Core
 * metadata in HTML: the schema is declared and there is one meta element for each subject.
 *
 * @link https://www.dublincore.org/specifications/dublin-core/dc-html/
 */
function xtec_descriptors_head(): void
{
    global $wpdb;
    $descriptors = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT descriptor FROM {$wpdb->descriptors} where blogs like %s or blogs like %s",
            '%$' . $wpdb->blogid . '-1$%',
            '%$' . $wpdb->blogid . '-0$%'
        )
    );

    if (empty($descriptors)) {
        return;
    }

    echo '<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/"/>' . "\n";
    foreach ($descriptors as $descriptor) {
        echo '<meta name="DC.subject" content="' . esc_attr($descriptor) . '"/>' . "\n";
    }
}

/**
 * Deletes a deleted blog of all the descriptors.
 *
 * @param WP_Site $site The deleted blog.
 */
function xtec_descriptors_delete_site(WP_Site $site): void
{
    global $wpdb;
    $blog_id = $site->id;

    $descriptorId = $wpdb->get_results(
        $wpdb->prepare("SELECT id FROM {$wpdb->descriptors} where `blogs` like %s", '%$' . $blog_id . '-%')
    );

    foreach ($descriptorId as $id) {
        $descriptorBlogs = $wpdb->get_results(
            $wpdb->prepare("SELECT id,blogs,number FROM {$wpdb->descriptors} where `id` = %d", $id->id)
        );

        //delete de reference to the blog public or not
        $keys = ['$' . $blog_id . '-0$', '$' . $blog_id . '-1$'];
        $newblogs = str_replace($keys, '', $descriptorBlogs[0]->blogs);

        $sql = $wpdb->prepare(
            "UPDATE {$wpdb->descriptors} SET `blogs` = %s, `number` = `number` - 1 WHERE id = %d",
            $newblogs,
            $descriptorBlogs[0]->id
        );
        //If is the last blog that have this descriptor delete the descriptor
        if ($descriptorBlogs[0]->number === '1') {
            $sql = $wpdb->prepare("DELETE FROM {$wpdb->descriptors} WHERE id = %d", $descriptorBlogs[0]->id);
        }
        $wpdb->query($sql);
    }
}

/**
 * Creates XTEC Descriptors database tables.
 */
function xtec_descriptors_activation_hook(): void
{
    global $wpdb;

    $table_name = $wpdb->descriptors;

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        $sql = "CREATE TABLE $table_name (
                id int(11) NOT NULL AUTO_INCREMENT,
                descriptor varchar(50) NOT NULL DEFAULT '',
                number int(11) NOT NULL DEFAULT '0',
                blogs text NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY descriptor (descriptor));";
        $sql .= "CREATE TABLE {$wpdb->descriptors_pre} (
    	         id int(10) NOT NULL AUTO_INCREMENT,
                 descriptor varchar(20) NOT NULL DEFAULT '',
                 PRIMARY KEY (id),
                 UNIQUE KEY descriptor (descriptor));";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Insert the predefined descriptors
        $predefined = [
            'matemàtiques', 'socials', 'català', 'castellà', 'descoberta', 'comunicació', 'literatura', 'aranès',
            'idiomes', 'naturals', 'música', 'art', 'visual', 'plàstica', 'física', 'drets', 'ciutadania', 'tutoria',
            'religió', 'tecnologia', 'clàssiques', 'filosofia', 'història', 'biologia', 'química', 'dibuix', 'economia',
            'organització', 'empresa', 'geografia', 'grec', 'contemporani', 'món', 'electrotècnia', 'llatí',
            'industrial', 'mecànica', 'disseny', 'imatge', 'expressió', 'volum', 'recerca', 'primària', 'batxillerat',
            'secundària', 'cicles',
        ];
        foreach ($predefined as $key => $descriptor) {
            $wpdb->insert($wpdb->descriptors_pre, ['id' => $key + 1, 'descriptor' => $descriptor]);
        }
    }
    add_option('xtec_descriptors_db_version', XTEC_DESCRIPTORS_DB_VERSION);
}


/* XTEC Descriptors tags */

/**
 * Gets all the descriptors with the tag, their weight and font size for a cloud output.
 *
 * @param int $number Total number of descriptors to get.
 * @param int $min_font_size The minimum font size in pixels.
 * @param int $max_font_size The maximum font size in pixels.
 * @return array
 */
function xtec_descriptors_get_descriptors_cloud($number, $min_font_size, $max_font_size): array
{
    global $wpdb;
    $cloudArray = []; // create an array to hold tag code

    // Pull in tag data
    $tags = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT descriptor,number FROM {$wpdb->descriptors} where blogs like '%%-1$%%' ORDER BY number DESC " .
            "limit 0, %d",
            $number
        )
    );

    $arr = [];
    foreach ($tags as $iValue) {
        $arr[$iValue->descriptor] = $iValue->number;
    }
    ksort($arr);

    if (count($arr) > 0) {
        $minimum_count = min(array_values($arr));
        $maximum_count = max(array_values($arr));
        $spread = $maximum_count - $minimum_count;
        if ($spread === 0) {
            $spread = 1;
        }

        // Finally we start the HTML building process to display our tags. For this demo the tag simply searches Google using the
        // provided tag.
        foreach ($arr as $tag => $count) {
            $size = $min_font_size + ($count - $minimum_count) * ($max_font_size - $min_font_size) / $spread;
            $cloudArray[] = [
                'size' => floor($size),
                'tag' => htmlspecialchars(stripslashes($tag)),
                'count' => $count,
            ];
        }
    }

    return $cloudArray;
}

/**
 * Gets the blogs with a specific descriptor.
 *
 * @param string $descriptor The descriptor to search.
 * @param bool $public True if the search must returns only the public blogs, false if it must returns all the blogs.
 *     The deactivated, archived and spam blogs are always skipped.
 * @return array The IDs of the blogs found.
 */
function xtec_descriptors_get_blogs_by_descriptor($descriptor, $public = true): array
{
    global $wpdb;
    $blogs = $wpdb->get_col(
        $wpdb->prepare("SELECT blogs from {$wpdb->descriptors} where `descriptor` = %s", $descriptor)
    );
    if (!isset($blogs[0])) {
        return [];
    }
    $blogs = explode('$$', substr($blogs[0], 0, -1));
    array_shift($blogs);

    $bbd = [];

    foreach ($blogs as $blog) {
        $blog = str_replace(['-1', '-0'], '', $blog);
        $blogDetails = get_blog_details($blog);
        if (
            $blogDetails !== false
            && (int)$blogDetails->deleted === 0
            && (int)$blogDetails->archived === 0
            && (int)$blogDetails->spam === 0
            && (!$public || (int)$blogDetails->public === 1)
        ) {
            $bbd[] = $blog;
        }
    }
    return $bbd;
}

/**
 * Gets the descriptors of a blog.
 *
 * @param int $blog_id ID of the blog.
 * @return array The descriptors of the blog.
 */
function xtec_descriptors_get_descriptors_by_blog($blog_id): array
{
    global $wpdb;
    $descriptors = $wpdb->get_results(
        $wpdb->prepare("SELECT descriptor FROM {$wpdb->descriptors} WHERE blogs LIKE %s", '%$' . $blog_id . '-1$%')
    );

    $dbb = [];
    foreach ($descriptors as $descriptor) {
        $dbb[] = $descriptor->descriptor;
    }
    return $dbb;
}

/**
 * Counts the number of descriptors of a blog.
 *
 * @param int $blogId Id of the blog.
 * @return int The number of descriptors of the blog.
 */
function xtec_descriptors_count_bloc_descriptors($blogId): int
{
    global $wpdb;
    $descripts = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT count(*) as number FROM {$wpdb->descriptors} where blogs like %s or blogs like %s",
            '%$' . $blogId . '-1$%',
            '%$' . $blogId . '-0$%'
        )
    );
    return isset($descripts[0]) ? (int)$descripts[0]->number : 0;
}

/**
 * Counts the number of blogs with the descriptor.
 *
 * @param string $descriptor The name of the descriptor.
 * @return int The number of blogs with the descriptor $desctiptor.
 */
function xtec_descriptors_count_descriptors($descriptor): int
{
    global $wpdb;

    $descriptorId = $wpdb->get_results(
        $wpdb->prepare("SELECT blogs FROM {$wpdb->descriptors} where `descriptor` = %s", $descriptor)
    );

    if (!isset($descriptorId[0])) {
        return 0;
    }

    return count(explode('-1$', $descriptorId[0]->blogs)) - 1;
}

/**
 * Deletes a descriptor.
 *
 * @param int $id Id of the descriptor.
 */
function xtec_descriptors_delete_descriptor($id): void
{
    global $wpdb;
    $wpdb->query($wpdb->prepare("DELETE from {$wpdb->descriptors} WHERE id = %d", $id));
}

/**
 * AJAX action that suggests the descriptors that start with the string entered by the user.
 * Prints a list of clickable options for the autocomplete of the options page of the plugin.
 */
function xtec_descriptors_autocomp(): void
{
    global $wpdb;

    if (!current_user_can('manage_options')) {
        wp_die('', '', ['response' => 403]);
    }

    $search = isset($_GET['sstring']) ? sanitize_text_field(wp_unslash($_GET['sstring'])) : '';
    if ($search === '') {
        wp_die();
    }

    $like = $wpdb->esc_like($search) . '%';
    $descriptors = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT descriptor FROM {$wpdb->descriptors} WHERE descriptor LIKE %s ORDER BY descriptor",
            $like
        )
    );
    $predefined = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT descriptor FROM {$wpdb->descriptors_pre} WHERE descriptor LIKE %s ORDER BY descriptor",
            $like
        )
    );

    foreach (array_unique(array_merge($descriptors, $predefined)) as $descriptor) {
        ?>
        <div style="width: 200px; padding:4px; height:14px; background:#EEEEEE;"
             onMouseOver="this.style.background='#CCCCCC'" onMouseOut="this.style.background='#EEEEEE'"
             onClick="setvalue(<?php echo esc_attr(wp_json_encode($descriptor)); ?>)">
            <?php echo esc_html($descriptor); ?>
        </div>
        <?php
    }

    wp_die();
}
