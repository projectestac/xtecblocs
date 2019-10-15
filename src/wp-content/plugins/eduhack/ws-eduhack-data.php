<?php
/**
 * Created by PhpStorm.
 * User: mireiachaler
 * Date: 26/09/2017
 * Time: 17:32
 */

include '../../../wp-load.php';

$posts = get_posts(array(
        'post_type'   => 'cpt_project',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids'
    )
);

$result = array();

//loop over each post
foreach($posts as $p){
    $stored_meta = get_post_meta( $p );

    $team_users = unserialize($stored_meta["widget-team_name"][0]);
    $team_users = unserialize($team_users);
    $result["team_name"] = $team_users;
    $team_images = unserialize($stored_meta["widget-team_img"][0]);
    $team_images = unserialize($team_images);
    foreach($team_images as $user_img){
        $image = wp_get_attachment_image_src( $user_img);
        $result["team_img"][] = $image[0];
    }

    $team_school = unserialize($stored_meta["widget-team_school"][0]);
    $team_school = unserialize($team_school);
    $result["team_school"] = $team_school;

    $facilitador_users = unserialize($stored_meta["widget-facilitador_name"][0]);
    $facilitador_users = unserialize($facilitador_users);
    $result["facilitador_name"] = $facilitador_users;

    $team_images = unserialize($stored_meta["widget-facilitador_img"][0]);
    $team_images = unserialize($team_images);
    foreach($facilitador_images as $facilitador_img){
        $image = wp_get_attachment_image_src( $facilitador_img);
        $result["facilitador_img"][] = $image[0];
    }

    $facilitador_school = unserialize($stored_meta["widget-facilitador_school"][0]);
    $facilitador_school = unserialize($facilitador_school);
    $result["facilitador_school"] = $facilitador_school;

    $result["buttons"] = $stored_meta["widget-buttons"][0];

    $status = unserialize($stored_meta["widget-status"][0]);
    $status = unserialize($status);
    $result["status"] = $status;

    $selected_tags1_id = unserialize($stored_meta['widget-tags1_id'][0]);
    $selected_tags1_id = unserialize($selected_tags1_id);
    if(!empty($selected_tags1_id)){
        foreach($selected_tags1_id as $id1){
            $tag_img = get_term_meta($id1, 'xtec_image');
            $tag_color = get_term_meta($id1, 'xtec_color');
            $this_tag = get_term($id1);
            $tag_text = $this_tag->name;
            $result["tags1_text"][] = $tag_text;
            $result["tags1_img"][] = $tag_img[0];
            $result["tags1_color"][] = $tag_color[0];
        }
    }

    $selected_tags2_id = unserialize($stored_meta['widget-tags2_id'][0]);
    $selected_tags2_id = unserialize($selected_tags2_id);
    if(!empty($selected_tags2_id)){
        foreach($selected_tags2_id as $id2){
            $tag_color = get_term_meta($id2, 'xtec_color');
            $this_tag = get_term($id2);
            $tag_text = $this_tag->name;
            $result["tags2_text"][] = $tag_text;
            $result["tags2_color"][] = $tag_color[0];
        }
    }

    /*
    $tags1_text = unserialize($stored_meta["widget-tags1_text"][0]);
    $tags1_text = unserialize($tags1_text);
    $result["tags1_text"] = $tags1_text;

    $tags1_img = unserialize($stored_meta["widget-tags1_img"][0]);
    $tags1_img = unserialize($tags1_img);
    $result["tags1_img"] = $tags1_img;

    $tags1_color = unserialize($stored_meta["widget-tags1_color"][0]);
    $tags1_color = unserialize($tags1_color);
    $result["tags1_color"] = $tags1_color;

    $tags2_text = unserialize($stored_meta["widget-tags2_text"][0]);
    $tags2_text = unserialize($tags2_text);
    $result["tags2_text"] = $tags2_text;

    $tags2_color = unserialize($stored_meta["widget-tags2_color"][0]);
    $tags2_color = unserialize($tags2_color);
    $result["tags2_color"] = $tags2_color;
    */

    $project_img = $stored_meta["widget-project_img"][0];
    $project_image = wp_get_attachment_image_src( $project_img);
    $result["project_image"][] = $project_image[0];


}

echo json_encode($result);

