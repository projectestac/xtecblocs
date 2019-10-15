<?php
/**
 * @package XTEC Blocs
 * @subpackage Eduhack
 * @version 1.0
 */
/*
Plugin Name:    XTEC Eduhack
Description:    Eduhack plugin
Author:         Joan Sala Soler
Version:        1.0
Text Domain:    xtec-eduhack
Domain Path:    /languages
License:        GNU General Public License v2 or later
License URI:    http://www.gnu.org/licenses/gpl-2.0.html
*/


/** Project name */
const XTEH_NAME = 'EduHack';

/** Base path of the multisite blog */
const XTEH_BASE_PATH = 'eduhack-';


/* PUBLIC API
 * Plugin functions.
 */


/**
 * Validates the requested form and returns the found errors or an empty
 * error object if the form was valid.
 *
 * @param $form     $_POST array
 * @return          WP_Error
 *
 * @since Eduhack 1.0
 */
function xteh_validate_form($form) {
    $errors = new WP_Error();
    $fields = ['title', 'slug', 'email', '_wpnonce'];
    $site = get_current_site();
    
    // Check that all the required fields where provided
    
    foreach ($fields as $field) {
        if (isset($form[$field]) === false || trim($form[$field]) == '') {
            $errors->add('missing_field', __(
                'All the fields must be filled.', 'xtec-eduhack'));
            
            return $errors;
        }
    }
    
    // Verify that the form was submited from our web site
    
    if (!wp_verify_nonce($form['_wpnonce'], 'clone_eduhack_template')) {
        $errors->add('invalid_nonce', __(
            'Could not process the form.', 'xtec-eduhack'));
    }
    
    // Check that the slug is valid and not already taken
    
    $slug = strtolower(trim($form['slug']));
    $path = $site->path . XTEH_BASE_PATH . "$slug/";
    
    if (!preg_match('/^([a-z0-9-])+$/i', $slug)) {
        $errors->add('invalid_domain', __(
            'The web address must contain only letters and numbers.',
            'xtec-eduhack')
        );
    }
    
    if (domain_exists($site->domain, $path)) {
        $errors->add('invalid_domain', __(
            'The choosen web address already exits.',
            'xtec-eduhack')
        );
    }
    
    // Validate the blog administrator address. It must be a valid email
    // found on the users table.
    
    $email = trim($form['email']);
    
    if ( is_email( $email ) && !email_exists( $email ) ) {
        $errors->add('invalid_email', __(
            'The given email does not pertain to a registered user.',
            'xtec-eduhack')
        );
    }
    
    if ( !is_email( $email ) ) {
        $errors->add('invalid_email', __(
            'The given email address is not valid.',
            'xtec-eduhack')
        );
    }
    
    return $errors;
}


/**
 * Duplicates the template blog.
 *
 * @param $form     $_POST array
 * @return          Error or success message
 *
 * @since Eduhack 1.0
 */
function xteh_duplicate_site( $form ) {
    require_once(__DIR__ . '/../multisite-clone-duplicator/lib/duplicate.php');
    
    // Only logged in users can duplicate sites
    
    if ( ! is_user_logged_in() ) {
        exit(1);
    }
    
    // Clone the template site
    
    $template_id = get_site_option( 'eduhack_template_id' );
    $site = get_current_site();
    $title = trim($form['title']);
    $email = trim($form['email']);
    $slug = strtolower(trim($form['slug']));
    
    $message = MUCD_Duplicate::duplicate_site([
        'title' => $title,
        'email' => $email,
        'path' => $site->path . XTEH_BASE_PATH . "$slug/",
        'domain' => $site->domain,
        'newdomain' => $site->domain,
        'from_site_id' => $template_id,
        'network_id' => $site->id,
        'keep_users' => false,
        'copy_files' => true,
        'public' => true
    ]);
    
    // If the blog was cloned successfuly, update its description and
    // set the blog as public. Then reset the user permissions.
    
    if ( !isset( $message['error'] ) && isset( $message['site_id'] ) ) {
        $site_id = $message['site_id'];
        $description = sprintf(__('%s Project', 'xtec-eduhack'), XTEH_NAME);
        
        if ( isset($form['description']) && trim($form['description']) != '' ) {
            $description = trim($form['description']);
        }
        
        update_blog_option($site_id, 'blogdescription', $description);
        update_blog_option($site_id, 'blog_public', 1);
        
        switch_to_blog( $site_id );
        xteh_create_cpt_project($title, $description);
        xteh_reset_permissions();
        restore_current_blog();
    }
    
    return $message;
}


/**
 * Create the template blog that will be cloned. The created template
 * identifier will be stored on the 'eduhack_template_id' site option.
 *
 * @since Eduhack 1.0
 */
function xteh_create_template() {
    $site = get_current_site();
    
    // Create a new template blog
    
    $blog_id = wpmu_create_blog(
        $site->domain,
        $site->path . XTEH_BASE_PATH . 'template',
        sprintf(__('%s Template', 'xtec-eduhack'), XTEH_NAME),
        wp_get_current_user()->ID,
        '',
        $site->id
    );
    
    // Initialize the template options and plugins
    
    switch_to_blog( $blog_id );
    
    activate_plugin( 'xtec-eduhack/xtec-eduhack.php', null, false, true);
    activate_plugin( 'widget-options/plugin.php', null, false, false);
    activate_plugin( 'eduhack/eduhack-projects-widget.php', null, false, false);
    
    switch_theme( 'eduhack' );
    
    wp_insert_category([
        'cat_name' => __('Home page', 'xtec-eduhack'),
        'category_nicename' => 'homepage'
    ]);
    
    wp_insert_category([
        'cat_name' => __('Tags', 'xtec-eduhack'),
        'category_nicename' => 'tags'
    ]);
    
    restore_current_blog();
    
    update_blog_status( $blod_id, 'public', 0 );
    update_site_option( 'eduhack_template_id', $blog_id );
}


/**
 * Create a new project post for the 'eduhack' plugin.
 *
 * @param $title            Project title
 * @param $description      Project description
 *
 * @since Eduhack 1.0
 */
function xteh_create_cpt_project($title, $description) {
    $project_id = wp_insert_post([
        'post_type' => 'cpt_project',
        'post_status' => 'publish',
        'comment_status' => 'closed',
        'post_title' => $title,
        'meta_input' => [
            'widget-description' => $description
        ]
    ]);
}


/**
 * Returns if the current blog is the template blog.
 *
 * @since Eduhack 1.0
 */
function xteh_is_template() {
    $template_id = get_site_option( 'eduhack_template_id' );
    $blog_id = get_current_blog_id();
    
    return ($template_id == $blog_id);
}


/**
 * Reset the default clone user permissions for the current blog.
 *
 * @since Eduhack 1.0
 */
function xteh_reset_permissions() {
    $roles = wp_roles();
    
    foreach ($roles->role_names as $slug => $name) {
        $roles->remove_cap( $slug, 'manage_categories' );
        $roles->remove_cap( $slug, 'switch_themes' );
        $roles->remove_cap( $slug, 'install_themes' );
    }
}


/* ACTIVATION AND INITIALIZATION
 * Hooks to initialize and configure this plugin.
 */

/**
 * Activate this plugin. This plugin requires the Multisite Clone Duplicator
 * to be installed and active for the main site.
 *
 * @since Eduhack 1.0
 */
register_activation_hook( __FILE__, function() {
    // Check that all the requeriments for this plugin are fullfilled
    
    $mucd_slug = 'multisite-clone-duplicator';
    
    if ( ! is_plugin_active( "$mucd_slug/$mucd_slug.php" ) ) {
        wp_die(__(
          'This plugin requires Multisite Clone Duplicator to be insalled ' .
          'and active for the main site.', 'xtec-eduhack'
        ));
    }
    
    $theme = wp_get_theme( 'fukasawa' );
    
    if ( empty($theme) ) {
        wp_die(__(
            'This plugin requires the Fukasawa theme to be insalled.',
            'xtec-eduhack'
        ));
    }
    
    // Create the template blog template if we are activating this plugin
    // on the main site and the template does not exist
    
    if ( is_user_logged_in() && is_main_site() ) {
        $id = get_site_option( 'eduhack_template_id' );
        
        if ( $id === false || get_blog_details($id)->blog_id !== $id ) {
            xteh_create_template();
        }
    }
});


/**
 * Initialize this plugin.
 *
 * @since Eduhack 1.0
 */
add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( 'xtec-eduhack', false, 'xtec-eduhack/languages' );
    load_plugin_textdomain( 'widget-options', false, 'xtec-eduhack/languages/' );
});


/**
 * Prevents access to the template site. Only members of the site and super
 * admins will be able to access the template pages.
 *
 * @since Eduhack 1.0
 */
add_action( 'wp_loaded', function() {
    if ( xteh_is_template() === true ) {
        if ( !is_super_admin() && !is_user_member_of_blog() ) {
            wp_redirect( network_home_url() );
            exit(1);
        }
    }
});


/**
 * Hides certain menus from the administration dashboard. Note that this
 * does not prevent the user from accessing the option pages directly.
 *
 * @since Eduhack 1.0
 */
add_action( 'admin_menu', function() {
    if ( !is_main_site() && !is_super_admin() ) {
        remove_submenu_page( 'options-general.php', 'widgetopts_plugin_settings' );
        
        if ( xteh_is_template() === false ) {
            remove_submenu_page( 'themes.php', 'themes.php' );
            remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
        }
    }
}, 1000);


/**
 * Removes the themes picker option from the customize manager.
 *
 * @since Eduhack 1.0
 */
add_action( 'customize_register', function( $wp_customize ) {
    if ( !is_main_site() && !is_super_admin() ) {
        $wp_customize->remove_section( 'themes' );
    }
}, 1000);


/**
 * Disable the tags taxonomy for posts.
 *
 * @since Eduhack 1.0
 */
add_action( 'init', function() {
    unregister_taxonomy_for_object_type( 'post_tag', 'post' );
});


/**
 * Register the EduHack theme with this extension.
 * 
 * @since Eduhack 1.0
 */
register_theme_directory( dirname( __FILE__ ) . '/themes' );


/* CATEGORY EDITION HOOKS
 * This hooks add custom fields to the category edition pages.
 */

/**
 * Registers a color picker for the administration pages.
 *
 * @since Eduhack 1.0
 */
add_action( 'admin_enqueue_scripts', function() {
    if ( !is_admin() )
        return;
    
    wp_enqueue_style( 'wp-color-picker' );
    
    wp_enqueue_script( 'eduhack_color_picker',
        plugins_url( 'scripts/color-picker.js', __FILE__ ),
        array('wp-color-picker'), false, true
    );
});


/**
 * Adds an image URL and color to the category edition page.
 *
 * @since Eduhack 1.0
 */
add_action( 'category_edit_form_fields', function( $term ) {
    $image = get_term_meta( $term->term_id, 'xtec_image', true);
    $color = get_term_meta( $term->term_id, 'xtec_color', true);
    
    ?>
    
    <table class="form-table">
      <tbody>
        <tr class="form-field">
          <th scope="row" valign="top">
            <label for="xtec_image">
              <?php esc_html_e('Image URL', 'xtec-eduhack') ?>
            </label>
          </th>
          <td>
            <input id="xtec_image" name="xtec_image"
                   value="<?= esc_attr( $image ) ?>"
                   placeholder="http://" type="text">
          </td>
        </tr>
        <tr class="form-field">
          <th scope="row" valign="top">
            <label for="xtec_color">
              <?php esc_html_e('Color', 'xtec-eduhack') ?>
            </label>
          </th>
          <td>
            <input id="xtec_color" name="xtec_color"
                   value="<?= esc_attr( $color ) ?>"
                   type="text" class="eduhak-color-picker">
          </td>
        </tr>
      </tbody>
    </table>
    
    <?php
}, 10, 2 );


/**
 * Save custom category fields.
 *
 * @since Eduhack 1.0
 */
add_action( 'edited_category', function( $term_id ) {
    if ( isset($_POST['xtec_image']) ) {
        $value = esc_url( $_POST['xtec_image'], ['http', 'https'] );
        update_term_meta( $term_id, 'xtec_image', $value );
    }
    
    if ( isset($_POST['xtec_color']) ) {
        $value = $_POST['xtec_color'];
        $regex = '/^#([a-f]|[0-9]){3,6}+$/i';
        
        if (empty($value) || preg_match($regex, $value) === 1) {
            update_term_meta( $term_id, 'xtec_color', $value );
        }
    }
}, 10, 2 );


/* COMMON PLUGINS HOOKS
 * This hooks remove ads, notices and premium options from the
 * required plugins.
 */

/**
 * Remove plugin actions if they are registered. The removed actions
 * usually contain only advertisings.
 *
 * @since Eduhack 1.0
 */
add_action( 'wp_loaded', function() {
    // Plugin: widget-options
    
    remove_action( 'admin_notices', 'widgetopts_admin_notices' );
    
    remove_action( 'widgetopts_module_sidebar', 'widgetopts_settings_more_plugins', 25 );
    remove_action( 'widgetopts_module_sidebar', 'widgetopts_settings_sidebar_opt_in', 20 );
    remove_action( 'widgetopts_module_sidebar', 'widgetopts_settings_support_box', 30 );
    remove_action( 'widgetopts_module_sidebar', 'widgetopts_settings_upgrade_pro', 10 );
    
    remove_action( 'extended_widget_opts_tabs', 'widgetopts_tab_gopro', 100 );
    remove_action( 'extended_widget_opts_tabcontent', 'widgetopts_tabcontent_gopro' );
    
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_animation', 130 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_cache', 175 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_columns', 90 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_columns', 90 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_dates', 110 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_disable_widgets', 150 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_fixed', 80 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_links', 70 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_links', 70 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_logic', 60 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_permissions', 160 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_roles', 100 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_shortcodes', 170 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_siteorigin', 65 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_styling', 120 );
    remove_action( 'widgetopts_module_cards', 'widgetopts_settings_taxonomies', 140 );
});
