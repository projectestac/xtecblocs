<?php
/**
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2008-2015 AddThis, LLC                                     |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 2 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program; if not, write to the Free Software              |
 * | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
 * +--------------------------------------------------------------------------+
 */

class Addthis_Horizontal_Recommended_Content_Widget extends WP_Widget
{

    const HORIZONTAL_RECOMMENDED_CONTENT = "addthis_recommended_horizontal";

    /**
     * Register widget with WordPress.
     *
     * @return null
     */
    function __construct()
    {
        parent::__construct(
            'addthis_horizontal_recommended_content_widget', // Base ID
            __(
                'Addthis Horizontal Recommended Content',
                'hor_recomended_widget_domain'
            ), // Name
            array( 'description' =>
                __(
                    'An Addthis Widget to add horizontal recommended content',
                    'hor_recomended_widget_domain'
                )
            ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     *
     * @return null
     */
    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        $title = apply_filters('widget_title', $instance['title']);
        if (! empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo "<div class='".self::HORIZONTAL_RECOMMENDED_CONTENT."'></div>";

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @param array $instance Previously saved values from database.
     *
     * @return null
     */
    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Addthis', 'hor_recomended_widget_domain');
        }
        echo "<p>".
                '<label for="'.$this->get_field_id('title').'">'.
                    _e('Title:').
                '</label>'.
                '<input id="'.$this->get_field_id('title').'" '.
                       'name="'.$this->get_field_name('title').'" '.
                       'type="text" value="'.esc_attr($title).'">'.
             "</p>";
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $new_instance['title'] = (! empty($new_instance['title'])) ?
                                mysql_real_escape_string($new_instance['title'])
                                : '';
        return $new_instance;
    }

}

/**
 * Class for Widget for Vertical Recommended Content
 *
 * @category Class
 * @package  Wordpress_Widget
 * @author   The AddThis Team <srijith@addthis.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: 1.0
 * @link     http://www.addthis.com/blog
 */
class Addthis_Vertical_Recommended_Content_Widget extends WP_Widget
{

    const VERTICAL_RECOMMENDED_CONTENT = "addthis_recommended_vertical";

    /**
     * Register widget with WordPress.
     *
     * @return null
     */
    function __construct()
    {
        parent::__construct(
            'addthis_vertical_recommended_content_widget', // Base ID
            __(
                'Addthis Vertical Recommended Content',
                'vertical_recomended_widget_domain'
            ), // Name
            array( 'description' =>
                __(
                    'An Addthis Widget to add vertical recommended content',
                    'vertical_recomended_widget_domain'
                )
            ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     *
     * @return null
     */
    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        $title = apply_filters('widget_title', $instance['title']);
        if (! empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo "<div class='".self::VERICAL_RECOMMENDED_CONTENT."'></div>";

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @param array $instance Previously saved values from database.
     *
     * @return null
     */
    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Addthis', 'vertical_recomended_widget_domain');
        }
        echo "<p>".
                '<label for="'.$this->get_field_id('title').'">'.
                    _e('Title:').
                '</label>'.
                '<input id="'.$this->get_field_id('title').'" '.
                       'name="'.$this->get_field_name('title').'" '.
                       'type="text" value="'.esc_attr($title).'">'.
             "</p>";
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $new_instance['title'] = (! empty($new_instance['title'])) ?
                                mysql_real_escape_string($new_instance['title'])
                                : '';
        return $new_instance;
    }

}

/**
 * Register widgets
 *
 * @return null
 */
function register_addthis_widget()
{
    register_widget('Addthis_Horizontal_Recommended_Content_Widget');
    register_widget('Addthis_Vertical_Recommended_Content_Widget');
}

add_action('widgets_init', 'register_addthis_widget');