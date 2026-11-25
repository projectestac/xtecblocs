<?php

/**
 * Data service of the projects of a blog, in JSON. It is called directly, so it loads WordPress itself.
 *
 * The response keeps the format of the original plugin: the lists of the team, the facilitators, the status and the
 * buttons are the ones of the last project, and the images and the tags are accumulated for all the projects.
 */

require_once dirname(__DIR__, 3) . '/wp-load.php';
// The functions of the metadata, also when the plugin isn't active in the blog
require_once __DIR__ . '/includes/meta.php';

$result = [];

$projects = get_posts([
    'post_type' => 'cpt_project',
    'post_status' => 'publish',
    'numberposts' => -1,
    'fields' => 'ids',
]);

foreach ($projects as $project_id) {
    $meta = get_post_meta($project_id);

    $result['team_name'] = eduhack_meta_list($meta, 'widget-team_name');
    foreach (eduhack_meta_list($meta, 'widget-team_img') as $image) {
        $result['team_img'][] = eduhack_image_url($image);
    }
    $result['team_school'] = eduhack_meta_list($meta, 'widget-team_school');

    $result['facilitador_name'] = eduhack_meta_list($meta, 'widget-facilitador_name');
    foreach (eduhack_meta_list($meta, 'widget-facilitador_img') as $image) {
        $result['facilitador_img'][] = eduhack_image_url($image);
    }
    $result['facilitador_school'] = eduhack_meta_list($meta, 'widget-facilitador_school');

    $result['buttons'] = eduhack_meta_value($meta, 'widget-buttons');
    $result['status'] = eduhack_meta_list($meta, 'widget-status');

    foreach (eduhack_meta_list($meta, 'widget-tags1_id') as $term_id) {
        $term = get_term((int)$term_id);
        $result['tags1_text'][] = $term instanceof WP_Term ? $term->name : '';
        $result['tags1_img'][] = get_term_meta((int)$term_id, 'xtec_image', true);
        $result['tags1_color'][] = get_term_meta((int)$term_id, 'xtec_color', true);
    }
    foreach (eduhack_meta_list($meta, 'widget-tags2_id') as $term_id) {
        $term = get_term((int)$term_id);
        $result['tags2_text'][] = $term instanceof WP_Term ? $term->name : '';
        $result['tags2_color'][] = get_term_meta((int)$term_id, 'xtec_color', true);
    }

    $result['project_image'][] = eduhack_image_url(eduhack_meta_value($meta, 'widget-project_img'));
}

header('Content-Type: application/json; charset=UTF-8');
echo wp_json_encode($result);
