<?php

defined('ABSPATH') || exit;

/**
 * The post type of the projects, the box to edit their data and the page with the link to the data service.
 */

add_action('init', 'eduhack_register_post_type');
add_action('add_meta_boxes_cpt_project', 'eduhack_add_meta_box');
add_action('save_post_cpt_project', 'eduhack_save_project', 10, 2);
add_action('admin_enqueue_scripts', 'eduhack_admin_scripts');
add_action('admin_menu', 'eduhack_data_menu');

// Phases of the status of a project
const EDUHACK_PHASES = [1, 2, 3, 4];

/**
 * Registers the post type of the projects, without editor: their data is edited in the box of the project.
 */
function eduhack_register_post_type(): void
{
    register_post_type('cpt_project', [
        'labels' => [
            'name' => __('Projectes', 'eduhack-projects-widget'),
            'singular_name' => __('Projecte', 'eduhack-projects-widget'),
        ],
        'public' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'show_in_nav_menus' => false,
        'menu_icon' => 'dashicons-clipboard',
        'supports' => ['title'],
    ]);
}

/**
 * Adds the box to edit the data of a project.
 */
function eduhack_add_meta_box(): void
{
    add_meta_box(
        'projects_meta',
        __('Configuració', 'eduhack-projects-widget'),
        'eduhack_meta_box',
        'cpt_project'
    );
}

/**
 * Loads the scripts and styles of the box of the projects, only in the pages to edit them.
 *
 * @param string $hook_suffix The page of the administration.
 */
function eduhack_admin_scripts(string $hook_suffix): void
{
    $is_project_page = in_array($hook_suffix, ['post.php', 'post-new.php'], true)
        && get_current_screen()?->post_type === 'cpt_project';
    if (!$is_project_page) {
        return;
    }

    wp_enqueue_media();
    // The theme UI lightness of jQuery UI 1.11.4, which the plugin Widget Options loaded in the blogs, gives the box
    // the look that it has always had
    wp_enqueue_style('eduhack-jquery-ui', plugins_url('css/jquery-ui/jquery-ui.css', EDUHACK_FILE), [], '1.11.4');
    wp_enqueue_style('eduhack-font-awesome', plugins_url('css/font-awesome.min.css', EDUHACK_FILE), [], '4.7.0');
    wp_enqueue_style('eduhack-admin', plugins_url('css/admin-styles.css', EDUHACK_FILE), [], EDUHACK_VERSION);
    wp_enqueue_script(
        'eduhack-admin',
        plugins_url('js/utils.js', EDUHACK_FILE),
        ['jquery', 'jquery-ui-tabs'],
        EDUHACK_VERSION,
        true
    );
}

/**
 * Shows the box to edit the data of a project: the team, the facilitators, the image, the description, the tags, the
 * buttons and the status.
 *
 * @param WP_Post $post The project.
 */
function eduhack_meta_box(WP_Post $post): void
{
    $meta = get_post_meta($post->ID);
    $status = eduhack_meta_list($meta, 'widget-status');

    wp_nonce_field('eduhack_save_project_' . $post->ID, 'eduhack_project_nonce');
    ?>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-team"><?php esc_html_e('Equip', 'eduhack-projects-widget'); ?></a></li>
            <li><a href="#tabs-description"><?php esc_html_e('Descripció', 'eduhack-projects-widget'); ?></a></li>
            <li><a href="#tabs-status"><?php esc_html_e('Estat', 'eduhack-projects-widget'); ?></a></li>
        </ul>
        <div id="tabs-team">
            <?php
            eduhack_people_fields($meta, 'team', 'widget-team_', __('Equip', 'eduhack-projects-widget'));
            eduhack_people_fields(
                $meta,
                'facilitador',
                'widget-facilitador_',
                __('Facilitadors', 'eduhack-projects-widget')
            );
            eduhack_project_image_field($meta);
            ?>
        </div>
        <div id="tabs-description">
            <section class="description">
                <h2 class="projects-config"><?php esc_html_e('Descripció', 'eduhack-projects-widget'); ?></h2>
                <?php wp_editor(eduhack_meta_value($meta, 'widget-description'), 'description'); ?>
            </section>
            <?php eduhack_tags_fields($meta); ?>
            <section>
                <h2 class="projects-config"><?php esc_html_e('Botons', 'eduhack-projects-widget'); ?></h2>
                <div class="buttons">
                    <textarea rows="10" cols="50" name="buttons"><?php
                        echo esc_textarea(eduhack_meta_value($meta, 'widget-buttons'));
                    ?></textarea>
                </div>
            </section>
        </div>
        <div id="tabs-status">
            <section>
                <h2 class="projects-config"><?php esc_html_e('Estat', 'eduhack-projects-widget'); ?></h2>
                <div class="status">
                    <?php foreach (EDUHACK_PHASES as $phase) : ?>
                        <label for="meta-text" class="status-label <?php echo $phase; ?>">
                            <?php esc_html_e('Fase', 'eduhack-projects-widget'); ?> <?php echo $phase; ?>
                        </label>
                        <input type="checkbox" value="<?php echo $phase; ?>" name="status[]"
                            <?php checked(in_array((string)$phase, $status, true)); ?> />
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
    <?php
}

/**
 * Shows the fields of the members of the team or of the facilitators: name, school and image.
 *
 * @param array $meta The raw metadata of the project.
 * @param string $field The name of the field: team or facilitador.
 * @param string $prefix The prefix of the metadata: widget-team_ or widget-facilitador_.
 * @param string $title The title of the section.
 */
function eduhack_people_fields(array $meta, string $field, string $prefix, string $title): void
{
    // The classes of the fields are the ones that the script of the box expects
    $is_team = $field === 'team';
    $class = $is_team ? 'team' : 'facilitador';
    $image_class = $is_team ? 'image-url-' : 'fac-image-url-';
    $src_class = $is_team ? 'image-src-' : 'fac-image-src-';
    $button = $is_team ? 'team0' : 'fac0';

    $names = eduhack_meta_list($meta, $prefix . 'name');
    $schools = eduhack_meta_list($meta, $prefix . 'school');
    $images = eduhack_meta_list($meta, $prefix . 'img');

    // An empty row to fill when there is nobody
    if (!$names) {
        $names = [''];
    }
    $label = 'for="meta-text" class="' . $class . '-label"';
    ?>
    <section class="<?php echo $is_team ? 'team' : 'facilitadors'; ?>">
        <h2 class="projects-config"><?php echo esc_html($title); ?></h2>
        <div class="<?php echo $class; ?>-container">
            <?php foreach ($names as $i => $name) : ?>
                <?php
                $image = $images[$i] ?? '';
                $image_url = eduhack_image_url($image);
                ?>
                <div>
                    <label <?php echo $label; ?>><?php esc_html_e('Nom', 'eduhack-projects-widget'); ?></label>
                    <input type="text" name="<?php echo $field; ?>[name][]" value="<?php echo esc_attr($name); ?>">
                    <label <?php echo $label; ?>><?php esc_html_e('Centre', 'eduhack-projects-widget'); ?></label>
                    <input type="text" name="<?php echo $field; ?>[school][]"
                        value="<?php echo esc_attr($schools[$i] ?? ''); ?>">
                    <label <?php echo $label; ?>><?php esc_html_e('Imatge', 'eduhack-projects-widget'); ?></label>
                    <input class="<?php echo $image_class . $i; ?>" type="hidden" name="<?php echo $field; ?>[image][]"
                        value="<?php echo esc_attr($image); ?>" />
                    <input type="button" class="button upload-button-<?php echo $button; ?> button-<?php echo $i; ?>"
                        value="<?php echo esc_attr(eduhack_image_button_text($image_url)); ?>"
                        data-buttonid="<?php echo $i; ?>" data-att-image="<?php echo $image_class; ?>"
                        data-img-src="<?php echo $src_class; ?>"/>
                    <img src="<?php echo esc_url($image_url); ?>"
                        class="<?php echo $class; ?>-img <?php echo $src_class . $i; ?>"/>
                    <a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="<?php echo $is_team ? 'add_form_field' : 'add_facilitador_field'; ?>"
            data-count="<?php echo count($names); ?>">
            <?php esc_html_e('Afegir-ne més', 'eduhack-projects-widget'); ?>&nbsp;&nbsp;
            <span style="font-size:16px; font-weight:bold;">+ </span>
        </div>
    </section>
    <?php
}

/**
 * Returns the text of the button to choose an image.
 *
 * @param string $image_url The address of the current image, or empty if there isn't any.
 * @return string The text.
 */
function eduhack_image_button_text(string $image_url): string
{
    return $image_url === ''
        ? __('Pujar imatge', 'eduhack-projects-widget')
        : __('Canviar imatge', 'eduhack-projects-widget');
}

/**
 * Shows the field of the image of the project.
 *
 * @param array $meta The raw metadata of the project.
 */
function eduhack_project_image_field(array $meta): void
{
    $image = eduhack_meta_value($meta, 'widget-project_img');
    $image_url = eduhack_image_url($image);
    ?>
    <section class="project-image">
        <h2 class="projects-config"><?php esc_html_e('Imatge del projecte', 'eduhack-projects-widget'); ?></h2>
        <div class="project-img-container">
            <div>
                <label for="meta-text" class="project-img-label">
                    <?php esc_html_e('Imatge', 'eduhack-projects-widget'); ?>
                </label>
                <input class="project-image-url-0" type="hidden" name="project-img"
                    value="<?php echo esc_attr($image); ?>" />
                <input type="button" class="button upload-button-proj0 button-0"
                    value="<?php echo esc_attr(eduhack_image_button_text($image_url)); ?>"
                    data-buttonid="0" data-att-image="project-image-url-" data-img-src="project-image-src-"/>
                <img src="<?php echo esc_url($image_url); ?>" class="project-img project-image-src-0"/>
            </div>
        </div>
    </section>
    <?php
}

/**
 * Shows the fields of the tags of the project: the highlighted ones, which have an image, and the others. The tags are
 * the subcategories of the category with the slug tags.
 *
 * @param array $meta The raw metadata of the project.
 */
function eduhack_tags_fields(array $meta): void
{
    $parent = get_category_by_slug('tags');
    $categories = $parent
        ? get_categories(['hide_empty' => 0, 'orderby' => 'name', 'order' => 'ASC', 'child_of' => $parent->term_id])
        : [];

    $groups = [1 => [], 2 => []];
    foreach ($categories as $category) {
        $groups[get_term_meta($category->term_id, 'xtec_image', true) ? 1 : 2][] = $category;
    }
    $labels = [
        1 => __('Etiquetes destacades', 'eduhack-projects-widget'),
        2 => __('Etiquetes', 'eduhack-projects-widget'),
    ];
    ?>
    <section>
        <h2 class="projects-config"><?php esc_html_e('Etiquetes', 'eduhack-projects-widget'); ?></h2>
        <?php foreach ($groups as $group => $tags) : ?>
            <?php
            if (!$tags) {
                continue;
            }
            $selected = array_map('intval', eduhack_meta_list($meta, "widget-tags{$group}_id"));
            ?>
            <div class="tags<?php echo $group; ?>">
                <label for="meta-text" class="tags<?php echo $group; ?>-label">
                    <?php echo esc_html($labels[$group]); ?>
                </label>
                <select>
                    <option value=""><?php esc_html_e('Seleccionar etiquetes', 'eduhack-projects-widget'); ?></option>
                    <?php foreach ($tags as $i => $tag) : ?>
                        <option value="<?php echo $group === 1 ? $i : $tag->term_id; ?>"
                            data-color="<?php echo esc_attr(get_term_meta($tag->term_id, 'xtec_color', true)); ?>"
                            data-img="<?php echo esc_url(get_term_meta($tag->term_id, 'xtec_image', true)); ?>"
                            data-id="<?php echo $tag->term_id; ?>"
                            <?php disabled(in_array($tag->term_id, $selected, true)); ?>>
                            <?php echo esc_html($tag->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="selected-tags<?php echo $group; ?>">
                    <?php foreach ($selected as $i => $term_id) : ?>
                        <?php
                        $term = get_term($term_id);
                        $color = get_term_meta($term_id, 'xtec_color', true);
                        if (!$term instanceof WP_Term) {
                            continue;
                        }
                        ?>
                        <?php if ($group === 1) : ?>
                            <div class="choosen-tag" style="background-color:<?php echo esc_attr($color); ?>"
                                data-option="<?php echo $i; ?>">
                                <input type="text" readonly name="selected_tags_text1[]"
                                    value="<?php echo esc_attr($term->name); ?>">
                                <input type="hidden" name="selected_tags_id1[]" value="<?php echo $term_id; ?>">
                                <img class="tag-image-url"
                                    src="<?php echo esc_url(get_term_meta($term_id, 'xtec_image', true)); ?>" />
                                <a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            </div>
                        <?php else : ?>
                            <div class="choosen-tag" data-option="<?php echo $term_id; ?>">
                                <span class="color-tag" style="background-color:<?php echo esc_attr($color); ?>"></span>
                                <input type="text" readonly name="selected_tags_text2[]"
                                    value="<?php echo esc_attr($term->name); ?>">
                                <input type="hidden" name="selected_tags_color2[]"
                                    value="<?php echo esc_attr($color); ?>">
                                <input type="hidden" name="selected_tags_id2[]" value="<?php echo $term_id; ?>">
                                <a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
    <?php
}

/**
 * Saves the data of the box of a project, after checking the nonce and the permissions.
 *
 * @param int $post_id The ID of the project.
 * @param WP_Post $post The project.
 */
function eduhack_save_project(int $post_id, WP_Post $post): void
{
    $nonce = isset($_POST['eduhack_project_nonce']) && is_string($_POST['eduhack_project_nonce'])
        ? sanitize_key(wp_unslash($_POST['eduhack_project_nonce'])) : '';

    if (
        wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)
        || !wp_verify_nonce($nonce, 'eduhack_save_project_' . $post_id)
        || !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    // Phases checked: an empty list if none is
    $status = array_intersect(eduhack_post_list('status'), array_map('strval', EDUHACK_PHASES));
    eduhack_save_meta_list($post_id, 'widget-status', $status);

    update_post_meta($post_id, 'widget-description', wp_kses_post(eduhack_post_text('description')));
    update_post_meta($post_id, 'widget-buttons', sanitize_textarea_field(eduhack_post_text('buttons')));
    update_post_meta($post_id, 'widget-project_img', eduhack_attachment_id(eduhack_post_text('project-img')));

    foreach (['team', 'facilitador'] as $field) {
        $people = isset($_POST[$field]) && is_array($_POST[$field]) ? wp_unslash($_POST[$field]) : [];
        $names = eduhack_text_list($people['name'] ?? []);
        $schools = eduhack_text_list($people['school'] ?? []);
        $images = array_map('eduhack_attachment_id', eduhack_text_list($people['image'] ?? []));

        if ($names) {
            eduhack_save_meta_list($post_id, "widget-{$field}_name", $names);
            eduhack_save_meta_list($post_id, "widget-{$field}_school", $schools);
            eduhack_save_meta_list($post_id, "widget-{$field}_img", $images);
        } else {
            delete_post_meta($post_id, "widget-{$field}_name");
            delete_post_meta($post_id, "widget-{$field}_school");
            delete_post_meta($post_id, "widget-{$field}_img");
        }
    }

    foreach ([1, 2] as $group) {
        $tags = array_filter(array_map('absint', eduhack_post_list("selected_tags_id{$group}")));
        if ($tags) {
            eduhack_save_meta_list($post_id, "widget-tags{$group}_id", array_map('strval', $tags));
        } else {
            delete_post_meta($post_id, "widget-tags{$group}_id");
        }
    }
}

/**
 * Returns a text field of the form, without the slashes added by WordPress.
 *
 * @param string $name The name of the field.
 * @return string The value, or an empty string if it hasn't been sent.
 */
function eduhack_post_text(string $name): string
{
    return isset($_POST[$name]) && is_string($_POST[$name]) ? wp_unslash($_POST[$name]) : '';
}

/**
 * Returns a list field of the form, without the slashes added by WordPress and sanitized.
 *
 * @param string $name The name of the field.
 * @return string[] The values, or an empty list if it hasn't been sent.
 */
function eduhack_post_list(string $name): array
{
    return isset($_POST[$name]) && is_array($_POST[$name]) ? eduhack_text_list(wp_unslash($_POST[$name])) : [];
}

/**
 * Sanitizes a list of texts of the form. The values that aren't texts become empty, so that the positions of the
 * lists of the team (names, schools and images) still match.
 *
 * @param mixed $list The list.
 * @return string[] The sanitized texts.
 */
function eduhack_text_list(mixed $list): array
{
    if (!is_array($list)) {
        return [];
    }

    return array_values(array_map(
        static fn(mixed $value): string => is_string($value) ? sanitize_text_field($value) : '',
        $list
    ));
}

/**
 * Returns the ID of an image of the form, as it is saved: a number, or empty if there isn't any.
 *
 * @param string $value The value of the form.
 * @return string The ID of the image.
 */
function eduhack_attachment_id(string $value): string
{
    $id = absint($value);

    return $id > 0 ? (string)$id : '';
}

/**
 * Adds the page with the link to the data service of the projects.
 */
function eduhack_data_menu(): void
{
    add_menu_page(
        'Eduhack Data',
        'Projects Data',
        'manage_options',
        'eduhack-data',
        'eduhack_data_page',
        'dashicons-migrate'
    );
}

/**
 * Shows the page with the link to the data service of the projects.
 */
function eduhack_data_page(): void
{
    $url = plugins_url('ws-eduhack-data.php', EDUHACK_FILE);
    ?>
    <div class="wrap">
        <h1>Eduhack Projects Data</h1>
        <a href="<?php echo esc_url($url); ?>" target="_blank">Get data</a>
        <h4><?php echo esc_html($url); ?></h4>
    </div>
    <?php
}
