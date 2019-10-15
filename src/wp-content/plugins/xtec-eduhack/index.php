<?php
/**
 * @package XTEC Blocs
 * @subpackage Eduhack
 * @version 1.0
 */

require_once( __DIR__ . '/../../../wp-load.php' );
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Check that this plugin is active before showing the form

if ( !is_plugin_active( 'xtec-eduhack/xtec-eduhack.php' ) ) {
	wp_redirect( network_home_url() );
	exit(1);
}

// Initialize the required variables

$user = wp_get_current_user();
$has_errors = false;

// Redirect to the login page if the user is not authenticated

if ( !is_user_logged_in() || !is_main_site() ) {
	wp_redirect( wp_login_url( 'eduhack/' ) );
	exit(1);
}

// Make sure the form is shown in the user's chosen locale

switch_to_locale( get_user_locale() );

// Only super admins are allowed to create new projects

if ( is_super_admin() == false ) {
    /* translators: %s = project name, %s = log out URL */
    wp_die(sprintf(__(
      'Only super administrators can create new %s projects. Please, ' .
      '<a href="%s">log out</a> and sign in again with a different user.',
      'xtec-eduhack'), XTEH_NAME, wp_logout_url( 'eduhack/' )));
    
	exit(1);
}

// If the form was submited, try to clone the site

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = xteh_validate_form( $_POST );
    $has_errors = !empty( $errors->errors );
    
    if ($has_errors === false) {
        $message = xteh_duplicate_site( $_POST );
        
        if ( isset($message['error']) === false ) {
            wp_redirect( get_admin_url( $message['site_id'] ) );
            exit( 0 );
        }
        
        $errors->add('unknown_error', $message['error']);
    }
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= XTEH_NAME ?> | <?php esc_html_e('Project registration', 'xtec-eduhack'); ?></title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
        integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ"
        crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="content-wrapper">
    <!-- Header -->
    
    <div class="header-wrapper">
      <header class="text-center">
        <h1 class="display-4"><?= XTEH_NAME ?></h1>
        <span class="motto h5 text-muted"><?php
          esc_html_e('Project registration', 'xtec-eduhack');
        ?></span>
      </header>
    </div>
    
    <div class="form-wrapper">
      <!-- Error messages -->
      
      <?php if ( $has_errors ) : ?>
        <div class="alert alert-danger medium mb-5" role="alert">
          <?php
          
            esc_html_e(
              'There were some errors with your submission. Please, fix '.
              'them and submit the form again:', 'xtec-eduhack'
            );
          
          ?>
          <?php foreach ($errors->get_error_messages() as $message): ?>
            <?= "<strong>$message</strong> " ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <!-- Summary message -->
      
      <div class="mb-5">
        <p>
          <?php
            
            /* translators: %s = user name, %s = project name */
            printf(wp_kses(__(
              'Best regards, <strong>%s</strong>. Please fill out the '.
              'following form to create a <strong>new %s project</strong>.',
              'xtec-eduhack'), ['em' => [], 'strong' => []]),
              esc_html($user->display_name), XTEH_NAME
            );
          
          ?>
        </p>
      </div>
      
      <!-- Form -->
      
      <div class="bootstrap-iso">
        <div class="container-fluid">
          <div class="row">
            <form method="post" class="clone-form">
              <?php wp_nonce_field( 'clone_eduhack_template' ); ?>
              <div class="form-group">
                <label for="email" class="control-label">
                  <?php esc_html_e('Administrator email', 'xtec-eduhack') ?>
                </label>
                <input id="email" name="email" type="text"
                       placeholder="<?php esc_attr_e('Required', 'xtec-eduhack') ?>"
                       value="<?= esc_attr( @$_POST['email'] ?: '' ); ?>"
                       maxlength="320" required class="form-control"/>
              </div>
              <div class="form-group">
                <label for="title" class="control-label required-field">
                  <?php esc_html_e('Title', 'xtec-eduhack') ?>
                </label>
                <input id="title" name="title" type="text"
                       placeholder="<?php esc_attr_e('Required', 'xtec-eduhack') ?>"
                       value="<?= esc_attr( @$_POST['title'] ?: '' ); ?>"
                       maxlength="200" required class="form-control"/>
              </div>
              <div class="form-group ">
                <label for="description" class="control-label required-field">
                  <?php esc_html_e('Description', 'xtec-eduhack') ?>
                </label>
                <textarea id="description" name="description" rows="3"
                          maxlength="500" class="form-control"><?=
                  esc_textarea( @$_POST['description'] ?: '' );
                ?></textarea>
              </div>
              <div class="form-group ">
                <label for="url" class="control-label required-field">
                  <?php esc_html_e('Web address', 'xtec-eduhack') ?>
                </label>
                <div class="input-group">
                  <div class="input-group-addon">
                    <span style="white-space: nowrap">
                      http://blocs.xtec.cat/<b>eduhack-</b>
                    </span>
                  </div>
                  <input id="slug" name="slug" type="text" maxlength="200"
                         placeholder="<?php esc_attr_e('Required', 'xtec-eduhack') ?>"
                         value="<?= esc_attr( @$_POST['slug']  ?: '' ); ?>"
                         required class="form-control"/>
                </div>
              </div>
              <div class="form-group">
                <div class="mt-5 text-center">
                  <button class="btn btn-primary " name="submit" type="submit">
                    <?php
                    
                    /* translators: %s = project name */
                    printf(esc_html__('Create a new %s project',
                        'xtec-eduhack'), XTEH_NAME);
                    
                    ?>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</body>
</html>