<?php
/**
 * This is a WordPress plugin settings that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          https://developers.google.com/recaptcha/docs/php
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * @link      http://www.google.com/recaptcha
 */

if (defined('ALLOW_INCLUDE') === false)
    die('no direct access');

// XTEC ********** AFEGIT -> Update site parameters (to share them in all blogs)
// 2015.06.12 @sarjona
    if (isset($_REQUEST['settings-updated'])){
      // Move recaptcha options from current blog (wp_options) to site option (wp_sitemeta)
      update_site_option('recaptcha_options', $this->validate_options(get_option('recaptcha_options')));
      // Remove recaptcha options from current blog (wp_options)
      delete_option('recaptcha_options');
      $this->options = WPPlugin::retrieve_options('recaptcha_options');
    }
// ********** FI
?>

<div class="wrap">
   <a name="recaptcha"></a>
   <h2><?php _e('reCAPTCHA Options', 'recaptcha'); ?></h2>
   <p><?php _e('reCAPTCHA is a free, accessible CAPTCHA service that helps to block spam on your blog.', 'recaptcha'); ?></p>

   <form method="post" action="options.php">
      <?php settings_fields('recaptcha_options_group'); ?>

      <h3><?php _e('Authentication', 'recaptcha'); ?></h3>
      <p><?php _e('These keys are required. You can register them at', 'recaptcha'); ?>
      <a href="http://www.google.com/recaptcha/admin/create" title="<?php _e('Get your reCAPTCHA API Keys', 'recaptcha'); ?>"><?php _e('here', 'recaptcha'); ?></a>.</p>
      <p><?php _e('These keys should be non-global key!', 'recaptcha'); ?></p>

      <table class="form-table">
         <tr valign="top">
            <th scope="row"><?php _e('Site Key (Public Key)', 'recaptcha'); ?></th>
            <td>
               <input type="text" name="recaptcha_options[site_key]" size="40" value="<?php echo $this->options['site_key']; ?>" />
            </td>
         </tr>
         <tr valign="top">
            <th scope="row"><?php _e('Secret (Private Key)', 'recaptcha'); ?></th>
            <td>
               <input type="text" name="recaptcha_options[secret]" size="40" value="<?php echo $this->options['secret']; ?>" />
            </td>
         </tr>
      </table>

      <h3><?php _e('General Options', 'recaptcha'); ?></h3>
      <table class="form-table">
         <tr valign="top">
            <th scope="row"><?php _e('Theme', 'recaptcha'); ?></th>
            <td>
               <?php $this->theme_dropdown(); ?>
            </td>
         </tr>

         <tr valign="top">
            <th scope="row"><?php _e('Language', 'recaptcha'); ?></th>
            <td>
               <?php $this->recaptcha_language_dropdown(); ?>
            </td>
         </tr>
      </table>

      <h3><?php _e('Error Messages', 'recaptcha'); ?></h3>
      <table class="form-table">
         <tr valign="top">
            <th scope="row"><?php _e('reCAPTCHA Ignored', 'recaptcha'); ?></th>
            <td>
               <input type="text" name="recaptcha_options[no_response_error]" size="70" value="<?php echo $this->options['no_response_error']; ?>" />
            </td>
         </tr>
      </table>

      <p class="submit"><input type="submit" class="button-primary" title="<?php _e('Save reCAPTCHA Options') ?>" value="<?php _e('Save reCAPTCHA Changes') ?> &raquo;" /></p>
   </form>

   <?php do_settings_sections('recaptcha_options_page'); ?>
</div>