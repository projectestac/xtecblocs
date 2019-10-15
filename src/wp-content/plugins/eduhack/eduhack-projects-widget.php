<?php
/**
Plugin Name: Eduhack Projects Widget
Plugin URI:  http://mschools.mobileworldcapital.com/es/iniciativas/edu_hack/
Description: Crea un giny sobre un projecte d'Edu_Hack
Version:     1.0
Author:      Artesans
Author URI:  https://www.artesans.eu/
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: eduhack-projects-widget
Domain Path: /languages
*/


function my_plugin_load_plugin_textdomain() {
    load_plugin_textdomain( 'eduhack-projects-widget', FALSE, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'my_plugin_load_plugin_textdomain' );



function eduhack_plugin_scripts() {
    wp_enqueue_script('eduhack-utils', plugins_url('/js/utils.js', __FILE__), array( 'eduhack-color' ), time(), false  );
    wp_enqueue_script('eduhack-ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array( 'jquery' ), time(), false  );
    wp_enqueue_script('eduhack-color', plugins_url('/js/jscolor.min.js', __FILE__), array( 'jquery' ), time(), false  );
    wp_enqueue_style('eduhack-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array( ), time(), false  );
    wp_enqueue_style('eduhack-font-a-css', plugins_url('/css/font-awesome.min.css', __FILE__), array( ), time(), false  );
    wp_enqueue_style('eduhack-admin-css', plugins_url('/css/admin-styles.css', __FILE__), array( ), time(), false  );

    wp_enqueue_script( 'wp-media-uploader', plugins_url('/js/wp_media_uploader.js', __FILE__) , array(), time(), false );
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'eduhack_plugin_scripts' );


function custom_widget_scripts(){
    wp_enqueue_style('eduhack-styles', plugins_url('/css/styles.css', __FILE__), array( ), time(), false  );
    wp_enqueue_script('eduhack-front-utils', plugins_url('/js/front-utils.js', __FILE__), array( 'jquery' ), time(), false  );
}
add_action( 'wp_enqueue_scripts', 'custom_widget_scripts' );


function create_project_posttype() {

    register_post_type( 'cpt_project',
        // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Projectes' ),
                'singular_name' => __( 'Projecte' )
            ),
            'public' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'show_in_nav_menus' => false,
            'menu_icon'   => 'dashicons-clipboard',
        )
    );

}
// Hooking up our function to theme setup
add_action( 'init', 'create_project_posttype' );

//hide content editor
add_action('init', 'hide_editor_cpt_project');
function hide_editor_cpt_project() {
    remove_post_type_support( 'cpt_project', 'editor' );
}


/**
 * Adds a meta box to the post editing screen
 */
function projects_custom_meta() {
    add_meta_box( 'projects_meta', __( 'Configuració', 'projects-textdomain' ), 'projects_meta_callback', 'cpt_project' );
}
add_action( 'add_meta_boxes', 'projects_custom_meta' );

/**
 * Outputs the content of the meta box
 */
function projects_meta_callback( $post ) {
    $stored_meta = get_post_meta( $post->ID );

    $status = unserialize($stored_meta['widget-status'][0]);
    $status = unserialize($status);

    ?>

    <div id="tabs">
        <ul>
            <li><a href="#tabs-team"><?php _e("Equip", 'eduhack-projects-widget');?></a></li>
            <li><a href="#tabs-description"><?php _e("Descripció", 'eduhack-projects-widget');?></a></li>
            <li><a href="#tabs-status"><?php _e("Estat", 'eduhack-projects-widget');?></a></li>
            <?php /*if ( current_user_can( 'manage_options' ) ) {?>
                <li><a href="#tabs-configuration">CONFIGURATION</a></li>
            <?php }*/?>
        </ul>
        <div id="tabs-team">
            <section class="team">
                <h2 class="projects-config"><?php _e("Equip", 'eduhack-projects-widget');?></h2>
                <div class="team-container">
                    <?php $i = 1;?>
                    <?php if($stored_meta['widget-team_name'][0]!=''){
                        $team_name = unserialize($stored_meta['widget-team_name'][0]);
                        $team_name = unserialize($team_name);
                        $team_img = unserialize($stored_meta['widget-team_img'][0]);
                        $team_img = unserialize($team_img);
                        $team_school = unserialize($stored_meta['widget-team_school'][0]);
                        $team_school = unserialize($team_school);

                        for ($i=0; $i<count($team_name); $i++){
                            $this_image = wp_get_attachment_image_src( $team_img[$i], 'thumbnail' );

                            $button_text = (empty($this_image)) ? __("Pujar imatge", 'eduhack-projects-widget') : __("Canviar imatge", 'eduhack-projects-widget');
                            ?>
                            <div>
                                <label for="meta-text" class="team-label"><?php _e( 'Nom', 'eduhack-projects-widget' )?></label>
                                <input type="text" name="team[name][]" value="<?php echo $team_name[$i];?>">
                                <label for="meta-text" class="team-label"><?php _e( 'Centre', 'eduhack-projects-widget' )?></label>
                                <input type="text" name="team[school][]" value="<?php echo $team_school[$i];?>">
                                <label for="meta-text" class="team-label"><?php _e( 'Imatge', 'eduhack-projects-widget' )?></label>
                                <input class="image-url-<?php echo $i;?>" type="hidden" name="team[image][]" value="<?php echo $team_img[$i];?>" />
                                <input type="button" class="button upload-button-team0 button-<?php echo $i;?>" value="<?php echo $button_text;?>" data-buttonid="<?php echo $i;?>" data-att-image="image-url-" data-img-src="image-src-"/>
                                <img src="<?php echo $this_image[0]; ?>" class="team-img image-src-<?php echo $i;?>"/>
                                <a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            </div>
                        <?php }?>
                    <?php }else{?>
                            <div>
                                <label for="meta-text" class="team-label"><?php _e( 'Nom', 'eduhack-projects-widget' )?></label>
                                <input type="text" name="team[name][]">
                                <label for="meta-text" class="team-label"><?php _e( 'Centre', 'eduhack-projects-widget' )?></label>
                                <input type="text" name="team[school][]">
                                <label for="meta-text" class="team-label"><?php _e( 'Imatge', 'eduhack-projects-widget' )?></label>
                                <input class="image-url-0" type="hidden" name="team[image][]" />
                                <img src="" class="team-img image-src-0"/>
                                <input type="button" class="button upload-button-team0" value="Pujar imatge" data-buttonid="0" data-att-image="image-url-" data-img-src="image-src-"/>

                            </div>
                    <?php }?>
                </div>
                <div class="add_form_field" data-count="<?php echo $i;?>"><?php _e("Afegir-ne més", 'eduhack-projects-widget');?>&nbsp;&nbsp;<span style="font-size:16px; font-weight:bold;">+ </span></div>
            </section>
            <section class="facilitadors">
                <h2 class="projects-config"><?php _e("Facilitadors", 'eduhack-projects-widget');?></h2>
                <div class="facilitador-container">
                    <?php if($stored_meta['widget-facilitador_name'][0]!=''){
                        $i = 0;
                        $facilitador_name = unserialize($stored_meta['widget-facilitador_name'][0]);
                        $facilitador_name = unserialize($facilitador_name);
                        $facilitador_img = unserialize($stored_meta['widget-facilitador_img'][0]);
                        $facilitador_img = unserialize($facilitador_img);
                        $facilitador_school = unserialize($stored_meta['widget-facilitador_school'][0]);
                        $facilitador_school = unserialize($facilitador_school);

                        for ($i=0; $i<count($facilitador_name); $i++){
                            $this_image = wp_get_attachment_image_src( $facilitador_img[$i], 'thumbnail' );

                            $button_text = (empty($this_image)) ? __("Pujar imatge", 'eduhack-projects-widget') : __("Canviar imatge", 'eduhack-projects-widget');
                            ?>
                            <div>
                                <label for="meta-text" class="facilitador-label"><?php _e( 'Nom', 'eduhack-projects-widget' )?></label>
                                <input type="text" name="facilitador[name][]" value="<?php echo $facilitador_name[$i];?>">
                                <label for="meta-text" class="facilitador-label"><?php _e( 'Centre', 'eduhack-projects-widget' )?></label>
                                <input type="text" name="facilitador[school][]" value="<?php echo $facilitador_school[$i];?>">
                                <label for="meta-text" class="facilitador-label"><?php _e( 'Imatge', 'eduhack-projects-widget' )?></label>
                                <input class="fac-image-url-<?php echo $i;?>" type="hidden" name="facilitador[image][]" value="<?php echo $facilitador_img[$i];?>" />
                                <input type="button" class="button upload-button-fac0 button-<?php echo $i;?>" value="<?php echo $button_text;?>" data-buttonid="<?php echo $i;?>" data-att-image="fac-image-url-" data-img-src="fac-image-src-"/>
                                <img src="<?php echo $this_image[0]; ?>" class="facilitador-img fac-image-src-<?php echo $i;?>"/>
                                <a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            </div>
                        <?php }?>

                    <?php }else{?>
                        <div>
                            <label for="meta-text" class="facilitador-label"><?php _e( 'Nom', 'eduhack-projects-widget' )?></label>
                            <input type="text" name="facilitador[name][]">
                            <label for="meta-text" class="facilitador-label"><?php _e( 'Centre', 'eduhack-projects-widget' )?></label>
                            <input type="text" name="facilitador[school][]">
                            <label for="meta-text" class="facilitador-label"><?php _e( 'Imatge', 'eduhack-projects-widget' )?></label>
                            <input class="fac-image-url-0" type="hidden" name="facilitador[image][]" />
                            <input type="button" class="button upload-button-fac0" value="Pujar imatge" data-buttonid="0" data-att-image="fac-image-url-" data-img-src="fac-image-src-"/>
                            <img src="" class="facilitador-img fac-image-src-0"/>
                        </div>
                    <?php }?>
                </div>
                <div class="add_facilitador_field" data-count="<?php echo $i;?>"><?php _e("Afegir-ne més", 'eduhack-projects-widget');?>&nbsp;&nbsp;<span style="font-size:16px; font-weight:bold;">+ </span></div>
            </section>
            <section class="project-image">
                <h2 class="projects-config"><?php _e("Imatge del projecte", 'eduhack-projects-widget');?></h2>
                <div class="project-img-container">
                    <?php if($stored_meta['widget-project_img'][0]!=''){

                            $project_img = $stored_meta['widget-project_img'][0];
                            $this_image = wp_get_attachment_image_src( $project_img, 'thumbnail' );

                            $button_text = (empty($this_image)) ? __("Pujar imatge", 'eduhack-projects-widget') : __("Canviar imatge", 'eduhack-projects-widget');
                            ?>
                            <div>
                                <label for="meta-text" class="project-img-label"><?php _e( 'Imatge', 'eduhack-projects-widget' )?></label>
                                <input class="project-image-url-0" type="hidden" name="project-img" value="<?php echo $project_img;?>" />
                                <input type="button" class="button upload-button-proj0 button-0" value="<?php echo $button_text;?>" data-buttonid="0" data-att-image="project-image-url-" data-img-src="project-image-src-"/>
                                <img src="<?php echo $this_image[0]; ?>" class="project-img project-image-src-0"/>
                            </div>

                    <?php }else{?>
                        <div>
                            <input class="project-image-url-0" type="hidden" name="project-img" />
                            <input type="button" class="button upload-button-proj0" value="Pujar imatge" data-buttonid="0" data-att-image="project-image-url-" data-img-src="project-image-src-"/>
                            <img src="" class="project-img project-image-src-0"/>
                        </div>
                    <?php }?>
                </div>
            </section>
        </div>
        <div id="tabs-description">
            <section class="description">
                <h2 class="projects-config"><?php _e("Descripció", 'eduhack-projects-widget');?></h2>
                <?php
                $content = ( isset ( $stored_meta['widget-description'] ) ) ? $stored_meta['widget-description'][0] : '';
                $editor_id = 'description';

                wp_editor( $content, $editor_id );
                ?>

            </section>
            <section>
                <h2 class="projects-config"><?php _e("Etiquetes", 'eduhack-projects-widget');?></h2>
                <?php

                $tag_category = get_category_by_slug( 'tags' );

                $args = array("hide_empty" => 0,
                    "type"      => "post",
                    "orderby"   => "name",
                    "order"     => "ASC",
                    "child_of"    => $tag_category->term_id
                );
                $tags = get_categories($args);

                foreach($tags as $tag){
                    $imatge = get_term_meta( $tag->term_id, 'xtec_image');
                    if($imatge[0] !='') $tags1[] = $tag;
                    else $tags2[] = $tag;
                }

                $selected_tags1_id = unserialize($stored_meta['widget-tags1_id'][0]);
                $selected_tags1_id = unserialize($selected_tags1_id);

                $selected_tags2_id = unserialize($stored_meta['widget-tags2_id'][0]);
                $selected_tags2_id = unserialize($selected_tags2_id);

                ?>

                <?php if(!empty($tags1)){?>
                    <div class="tags1">
                        <label for="meta-text" class="tags1-label"><?php _e( 'Etiquetes destacades', 'eduhack-projects-widget' )?></label>
                        <select>
                            <option value="" ><?php _e( 'Seleccionar etiquetes', 'eduhack-projects-widget' );?></option>
                            <?php for($i=0; $i<count($tags1); $i++){
                                $imatge = get_term_meta( $tags1[$i]->term_id, 'xtec_image');
                                $color = get_term_meta( $tags1[$i]->term_id, 'xtec_color' );
                                $disabled = ( in_array($tags1[$i]->term_id, $selected_tags1_id) )? 'disabled' : '';

                            ?>
                            <option value="<?php echo $i;?>" data-color="<?php echo $color[0];?>" data-img="<?php echo $imatge[0];?>" data-id="<?php echo $tags1[$i]->term_id;?>" <?php echo $disabled;?> ><?php echo $tags1[$i]->name;?></option>
                        <?php }?>
                        </select>
                        <div class="selected-tags1">
                            <?php if(!empty($selected_tags1_id)){
                                for($i=0; $i<count($selected_tags1_id); $i++){
                                    $tag_img = get_term_meta($selected_tags1_id[$i], 'xtec_image');
                                    $tag_color = get_term_meta($selected_tags1_id[$i], 'xtec_color');
                                    $this_tag = get_term($selected_tags1_id[$i]);
                                    $tag_text = $this_tag->name;

                            ?>
                                    <div class="choosen-tag" style="background-color:<?php echo $tag_color[0];?>" data-option="<?php echo $i;?>">
                                        <!--<span class="color-tag" style="background-color:<?php echo $tag_color;?>"></span>-->
                                        <input type="text" readonly name="selected_tags_text1[]" value="<?php echo $tag_text;?>">
                                        <!--<input type="hidden" name="selected_tags_color1[]" value="<?php echo $tag_color[0];?>">-->
                                        <input type="hidden" name="selected_tags_id1[]" value="<?php echo $selected_tags1_id[$i];?>">
                                        <!--<input class="tag-image-url-'+x+'" type="hidden" name="selected_tags_img1[]" value="<?php echo $tag_img;?>" />-->
                                        <img class="tag-image-url" src="<?php echo $tag_img[0];?>" />
                                        <a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                    </div>
                                <?php }?>
                            <?php }?>
                        </div>
                    </div>
                <?php }?>

                <?php if(!empty($tags2)){?>
                    <div class="tags2">
                        <label for="meta-text" class="tags2-label"><?php _e( 'Etiquetes', 'eduhack-projects-widget' )?></label>
                        <select>
                            <option value=""><?php _e( 'Seleccionar etiquetes', 'eduhack-projects-widget' );?></option>
                            <?php for($i=0; $i<count($tags2); $i++){
                                $imatge = get_term_meta( $tags2[$i]->term_id, 'xtec_image');
                                $color = get_term_meta( $tags2[$i]->term_id, 'xtec_color' );
                                $disabled = ( in_array($tags2[$i]->term_id, $selected_tags2_id) )? 'disabled' : '';
                                ?>
                                <option value="<?php echo $tags2[$i]->term_id;?>" data-color="<?php echo $color[0];?>" data-img="<?php echo $imatge[0];?>" data-id="<?php echo $tags2[$i]->term_id;?>" <?php echo $disabled;?> ><?php echo $tags2[$i]->name;?></option>
                            <?php }?>
                        </select>
                        <div class="selected-tags2">
                            <?php if(!empty($selected_tags2_id)){
                                for($i=0; $i<count($selected_tags2_id); $i++){
                                    $tag_color = get_term_meta($selected_tags2_id[$i], 'xtec_color');
                                    $this_tag = get_term($selected_tags2_id[$i]);
                                    $tag_text = $this_tag->name;
                            ?>
                                    <div class="choosen-tag" data-option="<?php echo $selected_tags2_id[$i];?>">
                                        <span class="color-tag" style="background-color:<?php echo $tag_color[0];?>"></span>
                                        <input type="text" readonly name="selected_tags_text2[]" value="<?php echo $tag_text;?>">
                                        <input type="hidden" name="selected_tags_color2[]" value="<?php echo $tag_color[0];?>">
                                        <input type="hidden" name="selected_tags_id2[]" value="<?php echo $selected_tags2_id[$i];?>">
                                        <a href="#" class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                    </div>
                                <?php }?>
                            <?php }?>
                        </div>
                    </div>
                <?php }?>

            </section>
            <section>
                <h2 class="projects-config"><?php _e("Botons", 'eduhack-projects-widget');?></h2>
                <div class="buttons">
                    <textarea rows="10" cols="50" name="buttons" ><?php if ( isset ( $stored_meta['widget-buttons'] ) ) echo $stored_meta['widget-buttons'][0]; ?></textarea>
                </div>
            </section>
        </div>
        <div id="tabs-status">
            <section>
                <h2 class="projects-config"><?php _e("Estat", 'eduhack-projects-widget');?></h2>
                <div class="status">
                    <?php for($s=1; $s<=4; $s++){
                        $checked = (in_array((string)$s, $status, true)) ? 'checked' : '';
                        ?>
                        <label for="meta-text" class="status-label <?php echo $s;?>"><?php _e( 'Fase', 'eduhack-projects-widget' )?> <?php echo $s;?></label>
                        <input type="checkbox" value="<?php echo $s;?>" name="status[]" <?php echo $checked;?> />
                    <?php }?>

                </div>
            </section>
        </div>
    </div>

    <?php
}

/**
 * Saves the custom meta input
 */
function projects_meta_save( $post_id ) {

    global $wpdb;
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'prfx_nonce' ] ) && wp_verify_nonce( $_POST[ 'prfx_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'status' ] ) ) {
        update_post_meta( $post_id, 'widget-status', sanitize_text_field(serialize( $_POST[ 'status' ] )) );
    }
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'description' ] ) ) {
        update_post_meta( $post_id, 'widget-description',  $_POST[ 'description' ]  );
    }
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'buttons' ] ) ) {
        update_post_meta( $post_id, 'widget-buttons',  $_POST[ 'buttons' ]  );
    }
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'team' ] ) ) {
        update_post_meta($post_id, 'widget-team_name', sanitize_text_field(serialize($_POST['team']['name'])));
        update_post_meta($post_id, 'widget-team_school', sanitize_text_field(serialize($_POST['team']['school'])));
        update_post_meta($post_id, 'widget-team_img', sanitize_text_field(serialize($_POST['team']['image'])));

    }else{
        delete_post_meta($post_id, 'widget-team_name');
        delete_post_meta($post_id, 'widget-team_school');
        delete_post_meta($post_id, 'widget-team_img');
    }
    if( isset( $_POST[ 'project-img' ] ) ) {
        update_post_meta($post_id, 'widget-project_img', sanitize_text_field($_POST['project-img']));
    }else{
        delete_post_meta($post_id, 'widget-project_img');
    }
    if( isset( $_POST[ 'facilitador' ] ) ) {
        update_post_meta($post_id, 'widget-facilitador_name', sanitize_text_field(serialize($_POST['facilitador']['name'])));
        update_post_meta($post_id, 'widget-facilitador_school', sanitize_text_field(serialize($_POST['facilitador']['school'])));
        update_post_meta($post_id, 'widget-facilitador_img', sanitize_text_field(serialize($_POST['facilitador']['image'])));

    }else{
        delete_post_meta($post_id, 'widget-facilitador_name');
        delete_post_meta($post_id, 'widget-facilitador_school');
        delete_post_meta($post_id, 'widget-facilitador_img');
    }
    if( isset( $_POST[ 'selected_tags_text1' ] ) ) {
        //update_post_meta( $post_id, 'widget-tags1_text', sanitize_text_field(serialize( $_POST[ 'selected_tags_text1' ]) ) );
        //update_post_meta( $post_id, 'widget-tags1_color', sanitize_text_field(serialize( $_POST[ 'selected_tags_color1' ]) ) );
        //update_post_meta( $post_id, 'widget-tags1_img', sanitize_text_field(serialize( $_POST[ 'selected_tags_img1' ]) ) );
        update_post_meta( $post_id, 'widget-tags1_id', sanitize_text_field(serialize( $_POST[ 'selected_tags_id1' ]) ) );
    }else{
        //delete_post_meta($post_id, 'widget-tags1_text');
        //delete_post_meta($post_id, 'widget-tags1_color');
        delete_post_meta($post_id, 'widget-tags1_id');
    }
    if( isset( $_POST[ 'selected_tags_text2' ] ) ) {
        //update_post_meta( $post_id, 'widget-tags2_text', sanitize_text_field(serialize( $_POST[ 'selected_tags_text2' ]) ) );
        //update_post_meta( $post_id, 'widget-tags2_color', sanitize_text_field(serialize( $_POST[ 'selected_tags_color2' ]) ) );
        update_post_meta( $post_id, 'widget-tags2_id', sanitize_text_field(serialize( $_POST[ 'selected_tags_id2' ]) ) );
    }else{
        //delete_post_meta($post_id, 'widget-tags2_text');
        //delete_post_meta($post_id, 'widget-tags2_color');
        delete_post_meta($post_id, 'widget-tags2_id');
    }

    if( isset( $_POST[ 'config1' ] ) ) {
        update_post_meta( $post_id, 'widget-config_tags1', sanitize_text_field(serialize( $_POST[ 'config1' ]['tag-text']) ) );
        update_post_meta($post_id, 'widget-config_tag_img', sanitize_text_field(serialize($_POST['config1']['image'])));

        //update_post_meta( $post_id, 'widget-config_color1', sanitize_text_field(serialize( $_POST[ 'config1' ]['tag-color']) ) );
    }else{
        delete_post_meta($post_id, 'widget-config_tags1');
        delete_post_meta($post_id, 'widget-config_tag_img');
        //delete_post_meta($post_id, 'widget-config_color1');
    }
    if( isset( $_POST[ 'config2' ] ) ) {
        update_post_meta( $post_id, 'widget-config_tags2', sanitize_text_field(serialize( $_POST[ 'config2' ]['tag-text']) ) );
        update_post_meta( $post_id, 'widget-config_color2', sanitize_text_field(serialize( $_POST[ 'config2' ]['tag-color']) ) );
    }else{
        delete_post_meta($post_id, 'widget-config_tags2');
        delete_post_meta($post_id, 'widget-config_color2');
    }
    if( isset( $_POST[ 'current-fase' ] ) ) {
        update_post_meta( $post_id, 'widget-config_fase',  $_POST[ 'current-fase' ]  );
    }

}
add_action( 'save_post', 'projects_meta_save' );

function makeFileSafe($filePath)
{
    $fP = @fopen($filePath,'r+');
    if (!$fP)
    {
        return "Could not read file";
    }
    $contents = fread($fP,filesize($filePath));
    $contents = strip_tags($contents);
    rewind($fP);
    fwrite($fP,$contents);
    fclose($fP);
    return $contents;
}

include( plugin_dir_path( __FILE__ ) . '/eduhack-projects-custom-widget.php');


// Create WS link

// create custom plugin settings menu
add_action('admin_menu', 'eduhack_data_menu');

function eduhack_data_menu() {

    //create new top-level menu
    add_menu_page('Eduhack Data', 'Projects Data', 'administrator', __FILE__, 'eduhack_data_settings_page' , 'dashicons-migrate' );

    //call register settings function
    add_action( 'admin_init', 'eduhack_data_settings' );
}


function eduhack_data_settings() {
    //register our settings
    register_setting( 'my-cool-plugin-settings-group', 'new_option_name' );
    register_setting( 'my-cool-plugin-settings-group', 'some_other_option' );
    register_setting( 'my-cool-plugin-settings-group', 'option_etc' );
}

function eduhack_data_settings_page() {
    ?>
    <div class="wrap">
        <h1>Eduhack Projects Data</h1>
        <a href="<?php echo plugins_url('ws-eduhack-data.php', __FILE__);?>" target="_blank">Get data</a>
        <h4><?php echo plugins_url('ws-eduhack-data.php', __FILE__);?></h4>
    </div>
<?php }
