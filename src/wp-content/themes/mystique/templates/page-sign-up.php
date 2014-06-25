<?php

/*
 * @template  Mystique
 * @revised   December 21, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// User sign-up form, includes blog registration capability as well (depends on the context, of course)
// This is a custom page template that can be applied to individual pages.

/* Template Name: Sign-up */

?>

<?php

  // force gettext parsers to include this string
  if(true === false)
    atom()->t('Sign-up');

  // store error messages here
  $errors = new WP_Error();

  // we're handling this differently if this is the main blog and multisite is enabled
  define('MU_SIGNUP', is_multisite() && is_main_site());

  if(is_user_logged_in())
    $current_user = wp_get_current_user();

  $action = isset($_POST['action']) ? $_POST['action'] : '';
  $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
  $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';

  if(MU_SIGNUP){
    global $current_site; // used later

    $active_signup = get_site_option('registration');

    if(!$active_signup)
      $active_signup = 'all';

    // return "all", "none", "blog" or "user"
    $active_signup = apply_filters('wpmu_active_signup', $active_signup);

    // Make the signup type translatable.
    $i18n_signup['all'] = _x('all', 'Multisite active signup type');
    $i18n_signup['none'] = _x('none', 'Multisite active signup type');
    $i18n_signup['blog'] = _x('blog', 'Multisite active signup type');
    $i18n_signup['user'] = _x('user', 'Multisite active signup type');

    $signup_for = isset($_POST['signup_for']) ? $_POST['signup_for'] : 'blog';

    $blog_name = isset($_GET['new']) ? strtolower(preg_replace('/^-|-$|[^-a-zA-Z0-9]/', '', $_GET['new'])) : '';
    $blog_name = isset($_POST['blog_name']) ? $_POST['blog_name'] : $blog_name;

    // not sure why isn't this included in wpmu_validate_blog_signup ?
    if(in_array($blog_name, (array)get_site_option('illegal_names'))){
      wp_redirect(network_home_url());
      exit;
    }

    $blog_title = isset($_POST['blog_title']) ? $_POST['blog_title'] : '';
    $blog_public = isset($_POST['blog_public']) ? (bool)$_POST['blog_public'] : false;

  }

  $show_user_form = (MU_SIGNUP && !is_user_logged_in() && ($active_signup == 'all' || $active_signup == 'user')) || (!MU_SIGNUP && get_option('users_can_register'));
  $show_blog_form = (MU_SIGNUP &&  is_user_logged_in() && ($active_signup == 'all' || $active_signup == 'blog'));

  switch($action){

    case 'signup-user':

      // signup from the main blog (multiuser)
      if(MU_SIGNUP){
        if($active_signup == 'all' || ($signup_for == 'blog' && $active_signup == 'blog') || ($signup_for == 'user' && $active_signup == 'user')){
          $results = wpmu_validate_user_signup($user_name, $user_email);
          extract($results);

        }else{
          wp_die(atom()->t('Srsly?'));
        }

        if(!$errors->get_error_code()){
          wpmu_signup_user($user_name, $user_email, apply_filters('add_signup_meta', array()));
          define('REGISTERED', 'user');

          // go to the next step
          if($signup_for == 'blog'){
            $show_user_form = false;
            $show_blog_form = true;
          }

        }

      // signup from one of the internal blogs
      }else{

        if(get_option('users_can_register')){

          $sanitized_user_login = sanitize_user($user_name);
          $user_email = apply_filters('user_registration_email', $user_email);

          // check user name
          if($sanitized_user_login == ''){
            $errors->add('user_name', atom()->t('You left the user name field empty.'));

          }elseif(!validate_username($user_name)){
            $errors->add('user_name', atom()->t('Invalid user name, illegal characters.'));
            $sanitized_user_login = '';

          }elseif(username_exists($sanitized_user_login)){
            $errors->add('user_name', atom()->t('User name already registered, choose another.'));
          }

          // check the e-mail address
          if($user_email == ''){
            $errors->add('user_email', atom()->t('You left the e-mail field empty.'));

          }elseif(!is_email($user_email)){
            $errors->add('user_email', atom()->t('Invalid e-mail address.'));
            $user_email = '';

          }elseif(email_exists($user_email)){
            $errors->add('user_email', atom()->t('This email is already registered, please choose another one.'));
          }

          // compat.
          do_action('register_post', $sanitized_user_login, $user_email, $errors);
          $errors = apply_filters('registration_errors', $errors, $sanitized_user_login, $user_email);

          if(!$errors->get_error_code()){


            $user_pass = wp_generate_password( 12, false);
            $created_user = wp_create_user( $sanitized_user_login, $user_pass, $user_email );

            if(!$created_user || is_wp_error($created_user)){
              // single error message
              $errors->add('generic', atom()->t('Registration failed, please contact the <a href="mailto:%s">webmaster</a>', get_option('admin_email')));

            }else{

              update_user_option($created_user, 'default_password_nag', true, true); // set up the Password change nag.
              wp_new_user_notification($created_user, $user_pass);

              define('REGISTERED', 'user');
            }
          }

        }else{
          wp_die(atom()->t('Srsly?'));
        }

      }

    break;


    case 'signup-blog':
      if(MU_SIGNUP && ($active_signup == 'all' || $active_signup == 'blog')){
        $results = wpmu_validate_blog_signup($blog_name, $blog_title);
        extract($results);

        if(!$errors->get_error_code()){
          $meta = array('lang_id' => 1, 'public' => $blog_public);
          $meta = apply_filters('add_signup_meta', $meta);
          wpmu_signup_blog($domain, $path, $blog_title, $user_name, $user_email, $meta);
          define('REGISTERED', 'blog');
        }

        // make sure we stay on the blog form, in case we have errors
        $show_user_form = false;
        $show_blog_form = true;

      }else{
        wp_die(atom()->t('Srsly?'));
      }
    break;


    case 'create-another-blog':
      if(MU_SIGNUP){

        if(!is_user_logged_in()){
          $errors->add('generic', atom()->t('You must be logged in to do this'));

        }else{
          $results = wpmu_validate_blog_signup($blog_name, $blog_title, $current_user);
          extract($results);

          if(!$errors->get_error_code()){
            $meta = apply_filters('signup_create_blog_meta', array('lang_id' => 1, 'public' => $blog_public)); // deprecated
            $meta = apply_filters('add_signup_meta', $meta);
            wpmu_create_blog($domain, $path, $blog_title, $current_user->id, $meta, $GLOBALS['wpdb']->siteid);
            define('REGISTERED', 'another-blog');

          }
        }
      }

    break;

    default:
      if((MU_SIGNUP && $active_signup == 'none') || (!MU_SIGNUP && !get_option('users_can_register')))
        $errors->add('generic', atom()->t('Registration has been disabled.'));

      elseif(MU_SIGNUP && $active_signup == 'blog' && !is_user_logged_in())
        $errors->add('generic', sprintf(__('You must first <a href="%s">log in</a>, and then you can create a new site.'), site_url('wp-login.php?redirect_to='.urlencode((is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/wp-signup.php'))));

    break;

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

              <?php if(MU_SIGNUP && is_super_admin()): // useless admin notification ?>
              <div class="message">
                <?php printf(__('Greetings Site Administrator! You are currently allowing &#8220;%s&#8221; registrations. To change or disable registration go to your <a href="%s">Options page</a>.'), $i18n_signup[$active_signup], esc_url(network_admin_url('settings.php'))); ?>
              </div>
              <?php endif; ?>

              <?php if($generic_error = $errors->get_error_message('generic')): // error not directlty related to the input fields ?>
              <div class="message error">
                <?php echo $generic_error; ?>
              </div>
              <?php endif;; ?>

              <?php if(defined('REGISTERED')): // the form was submitted successfully, display status info ?>

                <?php if(REGISTERED === 'user'): ?>
                  <h2><?php printf(__('%s is your new username'), $user_name); ?></h2>
                  <p><?php _e('But, before you can start using your new username, <strong>you must activate it</strong>.'); ?></p>
                  <p><?php printf(__('Check your inbox at <strong>%1$s</strong> and click the link given.'), $user_email); ?></p>
                  <p><?php _e('If you do not activate your username within two days, you will have to sign up again.'); ?></p>

                <?php elseif(REGISTERED === 'blog'): ?>
                  <h2><?php printf(__('Congratulations! Your new site, %s, is almost ready.'), "<a href='http://{$domain}{$path}'>{$blog_title}</a>"); ?></h2>
                  <p><?php _e('But, before you can start using your site, <strong>you must activate it</strong>.'); ?></p>
                  <p><?php printf( __('Check your inbox at <strong>%s</strong> and click the link given.'),  $user_email); ?></p>
                  <p><?php _e('If you do not activate your site within two days, you will have to sign up again.'); ?></p>
                  <h2><?php _e('Still waiting for your email?'); ?></h2>
                  <?php _e('If you haven&#8217;t received your email yet, there are a number of things you can do:'); ?>
                  <ul id="noemail-tips">
                    <li><p><strong><?php _e('Wait a little longer. Sometimes delivery of email can be delayed by processes outside of our control.'); ?></strong></p></li>
                    <li><p><?php _e('Check the junk or spam folder of your email client. Sometime emails wind up there by mistake.'); ?></p></li>
                    <li><?php printf(__('Have you entered your email correctly?  You have entered %s, if it&#8217;s incorrect, you will not receive your email.'), $user_email); ?></li>
                  </ul>

                <?php elseif(REGISTERED === 'another-blog'): ?>
                  <h2><?php atom()->te('%s is yours.', "<a href='http://{$domain}{$path}'>{$blog_title}</a>"); ?></h2>
                  <p>
                    <?php
                      printf(__('<a href="http://%1$s">http://%2$s</a> is your new site.  <a href="%3$s">Log in</a> as &#8220;%4$s&#8221; using your existing password.'),
                       $domain.$path, $domain.$path, "http://{$domain}{$path}wp-login.php", $current_user->display_name);
                    ?>
                  </p>
                <?php endif; ?>

                <?php do_action('signup_finished'); // compat.  ?>



              <?php else: // form not yet sent, or there were submission errors ?>

                <?php do_action('preprocess_signup_form'); // compat. ?>

                <?php if(MU_SIGNUP && is_user_logged_in() && ($active_signup == 'all' || $active_signup == 'blog')): ?>

                   <h2><?php printf(__('Get <em>another</em> %s site in seconds'), $current_site->site_name); ?> </h2>

                   <p>
                     <?php printf(__('Welcome back, %s. By filling out the form below, you can <strong>add another site to your account</strong>. There is no limit to the number of sites you can have, so create to your heart&#8217;s content, but write responsibly!'), $current_user->display_name); ?>
                   </p>

                   <?php $blogs = get_blogs_of_user($current_user->ID); ?>
                   <?php if(!empty($blogs)): ?>
                   <p><?php _e('Sites you are already a member of:'); ?></p>
                   <ul>
                     <?php foreach($blogs as $blog): ?>
                     <li><a href="<?php echo esc_url(get_home_url($blog->userblog_id)); ?>"><?php echo $blog->blogname; ?></a></li>
                     <?php endforeach; ?>
                   </ul>
                   <?php endif; ?>

                   <p><?php _e('If you&#8217;re not going to use a great site domain, leave it for a new user. Now have at it!'); ?></p>

                <?php endif; ?>



                <form name="sign-up" method="post" action="<?php atom()->post->URL(); ?>">

                  <?php do_action('signup_hidden_fields'); ?>

                  <?php if($show_user_form): // user registration  ?>

                    <input type="hidden" name="action" value="signup-user" />

                    <div class="clear-block <?php if($error = $errors->get_error_message('user_name')) echo 'error'; ?>">
                      <label for="user_name"><?php _e('Username:'); ?></label>
                      <div class="input">
                        <input name="user_name" type="text" id="user_name" value="<?php esc_attr_e($user_name); ?>" maxlength="60" />
                        <?php if($error): ?>
                        <span class="help-inline"><?php echo $error; ?></span>
                        <?php endif; ?>
                        <span class="help-block">
                          <?php _e('(Must be at least 4 characters, letters and numbers only.)'); ?>
                        </span>
                      </div>
                    </div>

                    <div class="clear-block <?php if($error = $errors->get_error_message('user_email')) echo 'error'; ?>">
                      <label for="user_email"><?php _e('Email Address:'); ?></label>
                      <div class="input">
                        <input name="user_email" type="text" id="user_email" value="<?php esc_attr_e($user_email); ?>" maxlength="200" />
                        <?php if($error): ?>
                        <span class="help-inline"><?php echo $error; ?></span>
                        <?php endif; ?>
                        <span class="help-block">
                          <?php _e('We send your registration email to this address. (Double-check your email address before continuing.'); ?>
                        </span>
                      </div>

                    </div>


                    <?php do_action('signup_extra_fields', $errors); ?>

                    <?php if(MU_SIGNUP): // only for wpmu signup ?>

                      <?php if($active_signup == 'blog'): ?>
                      <input type="hidden" name="signup_for" value="blog" />

                      <?php elseif($active_signup == 'user'): ?>
                      <input type="hidden" name="signup_for" value="user" />

                      <?php else: ?>
                      <div class="clear-block">

                        <div class="input">

                          <ul class="inputs-list">
                            <li>
                              <label for="signup_type_blog">
                                <input id="signup_type_blog" type="radio" name="signup_for" value="blog" <?php checked($signup_for, 'blog'); ?> />
                                <span><?php _e('Gimme a site!') ?></span>
                              </label>
                            </li>
                            <li>
                              <label for="signup_type_user">

                               <input id="signup_type_user" type="radio" name="signup_for" value="user" <?php checked($signup_for, 'user'); ?> />
                               <span><?php _e('Just a username, please.') ?></span>

                              </label>
                            </li>
                          </ul>

                        </div>

                      </div>
                    <?php endif; ?>

                  <?php endif; ?>

                    <div class="actions">
                      <input type="submit" name="submit" class="button ok" value="<?php MU_SIGNUP && in_array($active_signup, array('blog', 'all')) ?  atom()->te('Next') : atom()->te('Create User'); ?>" />
                    </div>

                  <?php elseif($show_blog_form): // blog registration ?>

                    <?php if(is_user_logged_in()): // need to pass user/email values form the previous form for non-loggedin users ?>
                      <input type="hidden" name="action" value="create-another-blog" />
                      <input type="hidden" name="user_name" value="<?php esc_attr_e($user_name); ?>" />
                      <input type="hidden" name="user_email" value="<?php esc_attr_e($user_email); ?>" />

                    <?php else: ?>
                      <input type="hidden" name="action" value="signup-blog" />

                    <?php endif; ?>

                    <div class="clear-block <?php if($error = $errors->get_error_message('blogname')) echo 'error'; ?>">
                      <label for="blogname"><?php is_subdomain_install() ? _e('Site Domain:') : _e('Site Name:'); ?></label>
                      <div class="input">
                        <div class="input-<?php echo is_subdomain_install() ? 'append' : 'prepend'; ?>">
                          <?php if(!is_subdomain_install()): ?>
                          <span class="add-on"><?php echo ($current_site->domain . $current_site->path); ?></span>
                          <?php endif; ?>

                          <input name="blog_name" type="text" id="blog_name" value="<?php esc_attr_e($blog_name); ?>" maxlength="60" />

                          <?php if(is_subdomain_install()): ?>
                          <span class="add-on"><?php echo ($site_domain = preg_replace('|^www\.|', '', $current_site->domain)); ?></span>
                          <?php endif; ?>

                        </div>

                        <?php if($error): ?>
                        <span class="help-block"><?php echo $error; ?></span>
                        <?php endif; ?>

                        <?php if(!is_user_logged_in()): ?>
                        <span class="help-block">
                          (<strong><?php printf(__('Your address will be %s.'), is_subdomain_install() ? __('domain').'.'.$site_domain.$current_site->path : $current_site->domain.$current_site->path .__('sitename')); ?></strong>
                          <?php _e('Must be at least 4 characters, letters and numbers only. It cannot be changed, so choose carefully!'); ?>
                        </span>
                        <?php endif; ?>

                      </div>

                    </div>

                    <div class="clear-block <?php if($error = $errors->get_error_message('blog_title')) echo 'error'; ?>">
                      <label for="blog_title"><?php _e('Site Title:'); ?></label>
                      <div class="input">
                        <input class="xlarge" name="blog_title" type="text" id="blog_title" value="<?php esc_attr_e($blog_title); ?>" maxlength="200" />

                        <?php if($error): ?>
                        <span class="help-block"><?php echo $error; ?></span>
                        <?php endif; ?>

                      </div>

                    </div>

                    <div class="clear-block">
                      <label><?php _e('Privacy:') ?></label>
                      <div class="input">

                        <ul class="inputs-list">
                          <li>
                            <label for="blog_public_on">
                              <input id="blog_public_on" type="checkbox" name="blog_public" value="1" <?php checked($blog_public, true); ?> />
                              <span><?php _e('Allow my site to appear in search engines like Google, Technorati, and in public listings around this network.'); ?></span>
                            </label>
                          </li>
                        </ul>
                      </div>

                    </div>
                      <?php do_action('signup_blogform', $errors); ?>

                      <div class="actions">
                        <input type="submit" name="submit" class="button ok" value="<?php _e('Create Site'); ?>" />
                      </div>

                  <?php endif; ?>

                </form>


              <?php endif; ?>

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
