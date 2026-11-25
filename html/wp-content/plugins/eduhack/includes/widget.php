<?php

defined('ABSPATH') || exit;

add_action('widgets_init', static fn() => register_widget('Eduhack_Project_Widget'));
add_action('wp_enqueue_scripts', 'eduhack_widget_scripts');

/**
 * Widget with the information of the project of the blog: status, tags, description, team, facilitators and buttons.
 * Its ID is the one of the original plugin, wpb_widget, so that the widgets of the blogs keep working.
 */
class Eduhack_Project_Widget extends WP_Widget
{
    // Names of the phases of the status
    private const PHASES = [1 => 'Empatitzar', 2 => 'Definir', 3 => 'Idear', 4 => 'Prototipar'];

    public function __construct()
    {
        parent::__construct(
            'wpb_widget',
            __('Project Widget', 'eduhack-projects-widget'),
            ['description' => __('Widget to show project information', 'eduhack-projects-widget')]
        );
    }

    /**
     * Shows the widget with the last published project.
     *
     * @param array $args The arguments of the sidebar.
     * @param array $instance The settings of the widget.
     */
    public function widget($args, $instance): void
    {
        $projects = get_posts(['post_type' => 'cpt_project', 'post_status' => 'publish', 'numberposts' => 1]);
        if (!$projects) {
            return;
        }

        $meta = get_post_meta($projects[0]->ID);
        $title = apply_filters('widget_title', $instance['title'] ?? '', $instance, $this->id_base);

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        $this->status($meta);
        $this->tags($meta);
        $this->description($meta);
        $this->team($meta);
        $this->buttons($meta);

        echo $args['after_widget'];
    }

    /**
     * Shows the phases of the status of the project.
     *
     * @param array $meta The raw metadata of the project.
     */
    private function status(array $meta): void
    {
        $status = eduhack_meta_list($meta, 'widget-status');
        $current = eduhack_meta_value($meta, 'widget-config_fase');
        ?>
        <section id="project-status" class="project-section">
            <h3><?php esc_html_e('Estat', 'eduhack-projects-widget'); ?></h3>
            <div class="checkout-wrap">
                <ul class="checkout-bar">
                    <?php foreach (self::PHASES as $phase => $name) : ?>
                        <?php
                        $done = in_array((string)$phase, $status, true);
                        $classes = ['project-fase', 'fase', $done ? 'active' : 'next'];
                        if ((string)$phase === $current) {
                            $classes[] = 'current';
                        }
                        ?>
                        <li class="<?php echo esc_attr(implode(' ', $classes)); ?>">
                            <span class="status-title"><?php echo esc_html($name); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
        <?php
    }

    /**
     * Shows the tags of the project: the highlighted ones, with their image, and the others.
     *
     * @param array $meta The raw metadata of the project.
     */
    private function tags(array $meta): void
    {
        $highlighted = $this->terms(eduhack_meta_list($meta, 'widget-tags1_id'));
        $others = $this->terms(eduhack_meta_list($meta, 'widget-tags2_id'));
        if (!$highlighted && !$others) {
            return;
        }
        ?>
        <section id="project-tags" class="project-section">
            <h3><?php esc_html_e('Etiquetes', 'eduhack-projects-widget'); ?></h3>
            <?php if ($highlighted) : ?>
                <div class="block-tags">
                    <?php foreach ($highlighted as $term) : ?>
                        <?php $color = get_term_meta($term->term_id, 'xtec_color', true); ?>
                        <div class="choosen-tag tag1" style="background-color:<?php echo esc_attr($color); ?>">
                            <img class="tag-img"
                                src="<?php echo esc_url(get_term_meta($term->term_id, 'xtec_image', true)); ?>"/>
                            <span class="text-tag"><?php echo esc_html($term->name); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($others) : ?>
                <div class="block-tags">
                    <?php foreach ($others as $term) : ?>
                        <?php $color = get_term_meta($term->term_id, 'xtec_color', true); ?>
                        <div class="choosen-tag tag2">
                            <span class="color-tag" style="background-color:<?php echo esc_attr($color); ?>"></span>
                            <span class="text-tag"><?php echo esc_html($term->name); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php
    }

    /**
     * Returns the terms of a list of IDs that exist.
     *
     * @param string[] $ids The IDs of the terms.
     * @return WP_Term[] The terms.
     */
    private function terms(array $ids): array
    {
        $terms = array_map(static fn(string $id): mixed => get_term((int)$id), $ids);

        return array_values(array_filter($terms, static fn(mixed $term): bool => $term instanceof WP_Term));
    }

    /**
     * Shows the description of the project, folded, with the button to unfold it.
     *
     * @param array $meta The raw metadata of the project.
     */
    private function description(array $meta): void
    {
        $description = eduhack_meta_value($meta, 'widget-description');
        if ($description === '') {
            return;
        }
        ?>
        <section id="project-description" class="project-section">
            <h3><?php esc_html_e('Descripció', 'eduhack-projects-widget'); ?></h3>
            <div class="description"><?php echo wp_kses_post($description); ?></div>
            <span class="read-more read-button"><?php esc_html_e('Llegir més', 'eduhack-projects-widget'); ?></span>
        </section>
        <?php
    }

    /**
     * Shows the members of the team and the facilitators of the project.
     *
     * @param array $meta The raw metadata of the project.
     */
    private function team(array $meta): void
    {
        $team = eduhack_meta_list($meta, 'widget-team_name');
        $facilitators = eduhack_meta_list($meta, 'widget-facilitador_name');
        ?>
        <section id="project-team" class="project-section">
            <?php if ($team) : ?>
                <h3><?php esc_html_e('Equip', 'eduhack-projects-widget'); ?></h3>
                <?php $this->people($meta, 'widget-team_', $team, false); ?>
            <?php endif; ?>
            <?php $this->people($meta, 'widget-facilitador_', $facilitators, true); ?>
        </section>
        <?php
    }

    /**
     * Shows a list of people: the members of the team or the facilitators.
     *
     * @param array $meta The raw metadata of the project.
     * @param string $prefix The prefix of their metadata: widget-team_ or widget-facilitador_.
     * @param string[] $names The names of the people.
     * @param bool $facilitators If they are facilitators.
     */
    private function people(array $meta, string $prefix, array $names, bool $facilitators): void
    {
        $schools = eduhack_meta_list($meta, $prefix . 'school');
        $images = eduhack_meta_list($meta, $prefix . 'img');

        foreach ($names as $i => $name) {
            ?>
            <div class="team-member<?php echo $facilitators ? ' facilitador' : ''; ?>">
                <div class="column-left">
                    <img class="member-img"
                        src="<?php echo esc_url(eduhack_image_url($images[$i] ?? '', [100, 100])); ?>"/>
                </div>
                <div class="column-right">
                    <div class="member-name"><?php echo esc_html($name); ?></div>
                    <?php if ($facilitators) : ?>
                        <div><?php esc_html_e('Facilitador', 'eduhack-projects-widget'); ?></div>
                    <?php endif; ?>
                    <div class="member-school"><em><?php echo esc_html($schools[$i] ?? ''); ?></em></div>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Shows the buttons of the project. They are saved as "text,address" separated by ";;".
     *
     * @param array $meta The raw metadata of the project.
     */
    private function buttons(array $meta): void
    {
        $buttons = eduhack_meta_value($meta, 'widget-buttons');
        ?>
        <section id="project-buttons" class="project-section">
            <?php if ($buttons !== '') : ?>
                <?php foreach (explode(';;', $buttons) as $button) : ?>
                    <?php [$text, $url] = array_pad(explode(',', $button, 2), 2, ''); ?>
                    <a href="<?php echo esc_url(trim($url)); ?>" target="_blank" class="button">
                        <?php echo esc_html($text); ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
        <?php
    }

    /**
     * Shows the form of the settings of the widget: its title.
     *
     * @param array $instance The settings of the widget.
     * @return string The result of the form.
     */
    public function form($instance): string
    {
        $title = $instance['title'] ?? __('New title', 'eduhack-projects-widget');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
        return '';
    }

    /**
     * Saves the settings of the widget.
     *
     * @param array $new_instance The new settings.
     * @param array $old_instance The previous settings.
     * @return array The settings to save.
     */
    public function update($new_instance, $old_instance): array
    {
        return ['title' => sanitize_text_field($new_instance['title'] ?? '')];
    }
}

/**
 * Loads the styles and the script of the widget in the blogs that use it.
 */
function eduhack_widget_scripts(): void
{
    if (!is_active_widget(false, false, 'wpb_widget')) {
        return;
    }

    wp_enqueue_style('eduhack-styles', plugins_url('css/styles.css', EDUHACK_FILE), [], EDUHACK_VERSION);
    wp_enqueue_script(
        'eduhack-front-utils',
        plugins_url('js/front-utils.js', EDUHACK_FILE),
        ['jquery'],
        EDUHACK_VERSION,
        true
    );
}
