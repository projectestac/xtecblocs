<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Lost password forms.
// This is a custom page template that can be applied to individual pages.
//
// @todo: add login form...

/* Template Name: Password recovery */

?>

<?php

  // force gettext parsers to include this string
  if(true === false)
    atom()->t('Password recovery');

  global $wpdb, $current_site;

  // store error messages here
  $errors = new WP_Error();

  $user_login = isset($_POST['user_login']) ? trim($_POST['user_login']) : '';
  $redirect_to = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '';

  if(atom()->request('retrieve-password')){

    if(empty($user_login))
      $errors->add('user_login', atom()->t('Enter a username or e-mail address.'));

    elseif(strpos($user_login, '@')){
      $user_data = get_user_by_email($user_login);
      if(empty($user_data))
        $errors->add('user_login', atom()->t('There is no user registered with that email address.'));

    }else{
      $user_data = get_userdatabylogin($user_login);
      if(empty($user_data))
        $errors->add('user_login', atom()->t('There is no user with that name.'));
    }

    do_action('lostpassword_post');

    if(!empty($user_data) && !$errors->get_error_code()){

      // redefining user_login ensures we return the right case in the email
      $user_login = $user_data->user_login;
      $user_email = $user_data->user_email;

      do_action('retrieve_password', $user_login);

      $allow = apply_filters('allow_password_reset', true, $user_data->ID);

      if(!$allow)
        $errors->add('generic', __('Password reset is not allowed for this user'));

      elseif(is_wp_error($allow))
        $errors = $allow;
    }

    if(!$errors->get_error_code()){

      $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));

      if(empty($key)){

        // Generate something random for a key...
        $key = wp_generate_password(20, false);
        do_action('retrieve_password_key', $user_login, $key);

        // Now insert the new md5 key into the db
        $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
      }

      $message  = __('Someone requested that the password be reset for the following account:')."\r\n\r\n";
      $message .= network_site_url()."\r\n\r\n";
      $message .= sprintf(__('Username: %s'), $user_login)."\r\n\r\n";
      $message .= __('If this was a mistake, just ignore this email and nothing will happen.')."\r\n\r\n";
      $message .= __('To reset your password, visit the following address:')."\r\n\r\n";
      $message .= '<'.add_query_arg(array('atom' => 'reset-pass', 'key' => $key, 'login' => rawurlencode($user_login)), atom()->post->getURL()).">\r\n";

      if(is_multisite())
        $blog_name = $GLOBALS['current_site']->site_name;

      else

        // The blogname option is escaped with esc_html on the way into the database in sanitize_option
        // we want to reverse this for the plain text arena of emails.
        $blog_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

      $title = sprintf(__('[%s] Password Reset'), $blog_name);

      $title = apply_filters('retrieve_password_title', $title);
      $message = apply_filters('retrieve_password_message', $message, $key);

      if($message && !wp_mail($user_email, $title, $message))
        $errors->add('generic', atom()->t('Failed to send confirmation e-mail. Please contact the site admin.'));

      define('KEY_SENT', $errors->get_error_code() ? false : true);

      if(KEY_SENT && $redirect_to){
        wp_safe_redirect( $redirect_to );
        exit();
      }

    }

  }elseif(atom()->request('reset-pass')){

    $key = isset($_GET['key']) ? preg_replace('/[^a-z0-9]/i', '', $_GET['key']) : '';
    $login = isset($_GET['login']) ? sanitize_user($_GET['login']) : '';

	if(empty($key) || !is_string($key))
	  $errors->add('generic', __('Invalid key'));

	elseif(empty($login) || !is_string($login))
	  $errors->add('generic', __('Invalid login'));

	else
      $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login));

	if(empty($user))
	  $errors->add('generic', __('Invalid key'));

    define('PASSWORD_RESET', $errors->get_error_code() ? false : true);

  }

  atom()->template('header');
?>

<!-- main content: primary + sidebar(s) -->
<div id="mask-3" class="clear-block">
  <div id="mask-2">
    <div id="mask-1">

      <!-- primary content -->
      <div id="primary-content">
        <div class="blocks clear-block">

          <?php atom()->action('before_primary'); ?>

          <?php the_post(); ?>

          <?php atom()->action('before_post'); ?>

          <!-- post -->
          <div id="post-<?php the_ID(); ?>" <?php post_class('primary'); ?>>

            <?php if(!atom()->post->getMeta('hide_title')): ?>
            <h1 class="title"><?php the_title(); ?></h1>
            <?php endif; ?>

            <div class="clear-block">
              <?php the_content(); ?>

              <?php if(defined('KEY_SENT')): ?>

                <?php if(KEY_SENT): ?>
                <div class="message ok">
                  <?php _e('Check your e-mail for the confirmation link.'); ?>
                </div>

                <?php else: ?>
                <div class="message error">
                  <?php echo $errors->get_error_message('generic'); ?>
                </div>
                <?php endif; ?>


              <?php elseif(defined('PASSWORD_RESET')): ?>

                <?php if(PASSWORD_RESET): ?>
                <div class="message ok">
                  <?php _e('Your password has been reset.'); ?>
                </div>

                <?php else: ?>
                <div class="message error">
                  <?php echo $errors->get_error_message('generic'); ?>
                </div>
                <?php endif; ?>

              <?php endif; ?>

              <form name="lost-password" action="<?php atom()->post->URL(); ?>" method="post">

                <div class="clear-block <?php if($error = $errors->get_error_message('user_login')) echo 'error'; ?>">
                  <label for="user_login"><?php _e('Username or E-mail:'); ?></label>
                  <div class="input">
                    <input name="user_login" type="text" id="user_login" value="<?php esc_attr_e($user_login); ?>" maxlength="60" />
                    <?php if($error): ?>
                    <span class="help-block"><?php echo $error; ?></span>
                    <?php endif; ?>
                  </div>
                </div>

                <?php do_action('lostpassword_form'); ?>
                <input type="hidden" name="redirect_to" value="<?php esc_attr_e($redirect_to); ?>" />
                <input type="hidden" name="atom" value="retrieve-password" />

                <div class="actions">
                  <input type="submit" name="submit" class="button ok" value="<?php atom()->te('Get New Password'); ?>" />
                </div>

              </form>

            </div>

            <?php atom()->post->pagination(); ?>

            <?php atom()->controls('post-edit'); ?>

          </div>
          <!-- /post -->

          <?php atom()->action('after_post'); ?>

          <?php atom()->template('meta'); ?>

          <?php atom()->action('after_primary'); ?>

        </div>
      </div>
      <!-- /primary content -->

      <?php atom()->template('sidebar'); ?>
    </div>
  </div>
</div>
<!-- /main content -->

<?php atom()->template('footer'); ?>
