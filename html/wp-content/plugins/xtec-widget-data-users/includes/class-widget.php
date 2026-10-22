<?php

/**
 * Copyright 2026 Departament d'Educació i Formació Professional
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace XtecWidgetDataUsers;

/**
 * Widget that presents the author of the blog: image, name, description, e-mail, web and user of X (Twitter).
 */
class Widget extends \WP_Widget
{
    // The same identifier as the previous version, so that the blogs keep their widgets
    public const ID_BASE = 'users_data_widget';

    // Icons of the contact details, so that no font of icons is loaded
    private const ICONS = [
        'email' => '<path d="M3 5h18v14H3z M3 6l9 7 9-7" fill="none" stroke="currentColor" stroke-width="2"/>',
        'web' => '<circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2"/>'
            . '<path d="M3 12h18M12 3c3.5 3.5 3.5 14.5 0 18M12 3c-3.5 3.5-3.5 14.5 0 18" fill="none" '
            . 'stroke="currentColor" stroke-width="2"/>',
        'twitter' => '<path fill="currentColor" d="M18.24 2.25h3.31l-7.23 8.26 8.5 11.24h-6.66l-5.21-6.82'
            . '-5.97 6.82H1.68l7.73-8.83L1.25 2.25H8.08l4.71 6.23zm-1.16 17.52h1.83L7.08 4.13H5.12z"/>',
    ];

    public function __construct()
    {
        parent::__construct(
            self::ID_BASE,
            __('Users information', 'xtec-widget-data-users'),
            [
                'classname' => 'xtec_user_data_widget',
                'description' => __('Presentation of the author of the blog', 'xtec-widget-data-users'),
            ]
        );
    }

    /**
     * Returns the URL of the image shown when none has been chosen.
     *
     * @return string The URL.
     */
    private static function defaultImage(): string
    {
        return plugin_dir_url(XTEC_WIDGET_DATA_USERS_FILE) . 'assets/images/user_default.png';
    }

    /**
     * Returns the fields of a widget, with their default values.
     *
     * @param array $instance The settings of the widget.
     * @return string[] The fields.
     */
    private static function fields(array $instance): array
    {
        $defaults = [
            'image_uri' => self::defaultImage(),
            'name' => '',
            'description' => '',
            'email' => '',
            'web' => '',
            'twitter' => '',
        ];
        $fields = [];
        foreach ($defaults as $key => $default) {
            $fields[$key] = isset($instance[$key]) && is_scalar($instance[$key]) ? (string)$instance[$key] : $default;
        }

        return $fields;
    }

    /**
     * Returns the user of X (Twitter) from what has been written: the user, with or without @, or the address of its
     * page.
     *
     * @param string $text The text written.
     * @return string The user: the characters that a user can have at the beginning, without the rest of the address.
     */
    private static function twitterUser(string $text): string
    {
        $user = (string)preg_replace('#^(https?:?/*)?(www\.)?(twitter\.com|x\.com)/*#i', '', trim($text));

        return preg_match('/^@?(\w+)/', $user, $matches) ? $matches[1] : '';
    }

    /**
     * Returns the HTML of an icon.
     *
     * @param string $field The field of the icon.
     * @return string The HTML.
     */
    private static function icon(string $field): string
    {
        return '<svg class="xtec-wdu-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" '
            . 'focusable="false">' . self::ICONS[$field] . '</svg>';
    }

    public function form($instance): string
    {
        $fields = self::fields((array)$instance);
        $image = $fields['image_uri'];
        ?>
        <div class="xtec-wdu-image">
            <p><?php esc_html_e('Image', 'xtec-widget-data-users'); ?></p>
            <img src="<?php echo esc_url($image); ?>" alt="" <?php echo $image === '' ? 'hidden' : ''; ?> />
            <input type="hidden" name="<?php echo esc_attr($this->get_field_name('image_uri')); ?>"
                value="<?php echo esc_attr($image); ?>" />
            <p>
                <button type="button" class="button xtec-wdu-select">
                    <?php esc_html_e('Choose an image', 'xtec-widget-data-users'); ?>
                </button>
                <button type="button" class="button-link button-link-delete xtec-wdu-remove"
                    <?php echo $image === '' ? 'hidden' : ''; ?>>
                    <?php esc_html_e('Remove the image', 'xtec-widget-data-users'); ?>
                </button>
            </p>
        </div>
        <?php
        $inputs = [
            'name' => [__('Name and surnames', 'xtec-widget-data-users'), 'text'],
            'description' => [__('About user', 'xtec-widget-data-users'), 'textarea'],
            'email' => [__('E-mail', 'xtec-widget-data-users'), 'email'],
            'web' => [__('Web', 'xtec-widget-data-users'), 'url'],
            'twitter' => [__('User of X (Twitter)', 'xtec-widget-data-users'), 'text'],
        ];
        foreach ($inputs as $key => [$label, $type]) {
            $id = $this->get_field_id($key);
            $name = $this->get_field_name($key);
            ?>
            <p>
                <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
                <?php if ($type === 'textarea') { ?>
                    <textarea class="widefat" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>"
                        maxlength="140" rows="6"><?php echo esc_textarea($fields[$key]); ?></textarea>
                <?php } else { ?>
                    <input class="widefat" type="<?php echo esc_attr($type); ?>" id="<?php echo esc_attr($id); ?>"
                        name="<?php echo esc_attr($name); ?>" maxlength="140"
                        value="<?php echo esc_attr($fields[$key]); ?>" />
                <?php } ?>
            </p>
            <?php
        }

        return '';
    }

    public function update($new_instance, $old_instance): array
    {
        $fields = self::fields((array)$new_instance);

        return [
            'image_uri' => esc_url_raw(trim($fields['image_uri']), ['http', 'https']),
            'name' => sanitize_text_field($fields['name']),
            'description' => sanitize_textarea_field($fields['description']),
            'email' => sanitize_email($fields['email']),
            'web' => esc_url_raw(trim($fields['web']), ['http', 'https']),
            'twitter' => self::twitterUser($fields['twitter']),
        ];
    }

    public function widget($args, $instance): void
    {
        $fields = self::fields((array)$instance);
        $image = $fields['image_uri'];

        // The default image alone isn't shown, whatever the protocol of its address
        $without_protocol = static fn(string $url): string => (string)preg_replace('#^https?://#', '', $url);
        $is_default = $without_protocol($image) === $without_protocol(self::defaultImage());
        $details = array_filter(
            array_diff_key($fields, ['image_uri' => '']),
            static fn(string $value): bool => $value !== ''
        );
        if (!$details && ($image === '' || $is_default)) {
            return;
        }

        // The widget may be added in the customizer, after the stylesheet has been loaded
        [$url, $version] = xtec_widget_data_users_asset('assets/widget.css');
        wp_enqueue_style('xtec-widget-data-users', $url, [], $version);

        echo $args['before_widget'] ?? '';
        echo '<div class="widget-global-content">';

        if ($image !== '') {
            echo '<div class="widget-content-image"><img class="widget-image" src="' . esc_url($image) . '" alt="'
                . esc_attr($fields['name']) . '" /></div>';
        }
        if ($fields['name'] !== '') {
            echo '<p class="widget-name"><strong>' . esc_html($fields['name']) . '</strong></p>';
        }
        if ($fields['description'] !== '') {
            echo '<p class="widget-description">' . nl2br(esc_html($fields['description'])) . '</p>';
        }
        if (is_email($fields['email'])) {
            // The address is encoded with HTML entities, so that it is harder to take it automatically to send spam.
            // esc_url() would break the entities, and esc_attr() and esc_html() keep them.
            $email = antispambot($fields['email']);
            echo '<p class="widget-text-list">' . self::icon('email') . '<a class="widget-link" href="mailto:'
                . esc_attr($email) . '">' . esc_html($email) . '</a></p>';
        }
        if ($fields['web'] !== '') {
            // The webs of the previous version could be saved without the protocol
            $web = preg_match('#^https?://#i', $fields['web']) ? $fields['web'] : 'http://' . $fields['web'];
            echo '<p class="widget-text-list">' . self::icon('web') . '<a class="widget-link" href="' . esc_url($web)
                . '" target="_blank" rel="noopener">' . esc_html(preg_replace('#^https?://#i', '', $web)) . '</a></p>';
        }
        $twitter = self::twitterUser($fields['twitter']);
        if ($twitter !== '') {
            echo '<p class="widget-text-list">' . self::icon('twitter') . '<a class="widget-link" href="'
                . esc_url('https://x.com/' . $twitter) . '" target="_blank" rel="noopener">@' . esc_html($twitter)
                . '</a></p>';
        }

        echo '</div>';
        echo $args['after_widget'] ?? '';
    }
}
