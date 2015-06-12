<?php
/**
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          https://developers.google.com/recaptcha/docs/php
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * @link      http://www.google.com/recaptcha
 */

require_once('wp-plugin.php');

if (class_exists('ReCAPTCHAPlugin'))
{
    return;
}

class ReCAPTCHAPlugin extends WPPlugin
{
    private $_saved_error;
    private $_reCaptchaLib;

    /**
     * Php 4 Constructor.
     *
     * @param string $options_name
     */
    function ReCAPTCHAPlugin($options_name) {
        $args = func_get_args();
        call_user_func_array(array(&$this, "__construct"), $args);
    }

    /**
     * Php 5 Constructor.
     *
     * @param string $options_name
     */
    function __construct($options_name) {
        parent::__construct($options_name);
        $this->register_default_options();

        // require the recaptcha library
        $this->_require_library();

        // register the hooks
        $this->register_actions();
        $this->register_filters();
    }

    function register_actions() {
        // load the plugin's textdomain for localization
        add_action('init', array(&$this, 'load_textdomain'));

        // options
        register_activation_hook(WPPlugin::path_to_plugin_directory() .
            '/wp-recaptcha.php',
            array(&$this, 'register_default_options'));
        add_action('admin_init', array(&$this, 'register_settings_group'));

        if ($this->is_multi_blog()) {
            add_action('signup_extra_fields', array(&$this,
                'show_recaptcha_in_registration'));
        } else {
            add_action('register_form', array(&$this,
                'show_recaptcha_in_registration'));
        }

        add_action('comment_form', array(&$this, 'show_recaptcha_in_comments'));

        // recaptcha comment processing
        add_action('wp_head', array(&$this, 'saved_comment'), 0);
        add_action('preprocess_comment', array(&$this, 'check_comment'), 0);
        add_action('comment_post_redirect', array(&$this, 'relative_redirect'),
            0, 2);

        // administration (menus, pages, notifications, etc.)
        add_filter("plugin_action_links", array(&$this, 'show_settings_link'),
            10, 2);

        add_action('admin_menu', array(&$this, 'add_settings_page'));
        // admin notices
        add_action('admin_notices', array(&$this, 'missing_keys_notice'));
    }

    function register_filters() {
        if ($this->is_multi_blog()) {
            add_filter('wpmu_validate_user_signup',
                array(&$this, 'validate_recaptcha_response_wpmu'));
        } else {
            add_filter('registration_errors', array(&$this,
                'validate_recaptcha_response'));
        }
    }

    function load_textdomain() {
        load_plugin_textdomain('recaptcha', false, 'languages');
    }

    // set the default options
    function register_default_options() {
// XTEC ********** MODIFICAT -> To update new keys if they exists before. In the following version, probably this patch won't be necessary
// 2015.06.12 @mmartinez
        if ($this->options && !array_key_exists('site_key', $this->options) && array_key_exists('public_key', $this->options)){ 
            $this->options['site_key'] = $this->options['public_key'];
            $this->options['secret'] = $this->options['private_key'];
        } else {
                if ($this->options)
                    return;
        }
// *********** ORIGINAL
/*
        if ($this->options)
            return;
*/
// *********** FI
        $option_defaults = array();
        $old_options = WPPlugin::retrieve_options("recaptcha");
        if ($old_options) {
           $option_defaults['site_key'] = $old_options['pubkey'];
           $option_defaults['secret'] = $old_options['privkey'];

           // styling
           $option_defaults['recaptcha_language'] = $old_options['re_lang'];

           // error handling
           $option_defaults['no_response_error'] = $old_options['error_blank'];
        } else {
           $old_options = WPPlugin::retrieve_options($this->options_name);
           if ($old_options) {
               $option_defaults['site_key'] = $old_options['public_key'];
               $option_defaults['secret'] = $old_options['private_key'];
               $option_defaults['comments_theme'] = 'standard';
               $option_defaults['recaptcha_language'] = $old_options['recaptcha_language'];
               $option_defaults['no_response_error'] = $old_options['no_response_error'];
           } else {           
               $option_defaults['site_key'] = '';
               $option_defaults['secret'] = '';
               $option_defaults['comments_theme'] = 'standard';
               $option_defaults['recaptcha_language'] = 'en';
               $option_defaults['no_response_error'] =
                   '<strong>ERROR</strong>: Please fill in the reCAPTCHA form.';
           }
        }
        // add the option based on what environment we're in
        WPPlugin::add_options($this->options_name, $option_defaults);
    }

    // require the recaptcha library
    private function _require_library() {
        require_once($this->path_to_plugin_directory() . '/recaptchalib.php');
    }

    // register the settings
    function register_settings_group() {
        register_setting("recaptcha_options_group", 'recaptcha_options',
            array(&$this, 'validate_options'));
    }

    function keys_missing() {
        return (empty($this->options['site_key']) ||
            empty($this->options['secret']));
    }

    function create_error_notice($message, $anchor = '') {
        $options_url = admin_url(
            'options-general.php?page=wp-recaptcha/recaptcha.php') . $anchor;
        $error_message = sprintf(__($message .
            ' <a href="%s" title="WP-reCAPTCHA Options">Fix this</a>',
            'recaptcha'), $options_url);
        echo '<div class="error"><p><strong>' . $error_message .
            '</strong></p></div>';
    }

    function missing_keys_notice() {
        if ($this->keys_missing()) {
            $this->create_error_notice('reCAPTCHA API Keys are missing.');
        }
    }

    function validate_dropdown($array, $key, $value) {
        if (in_array($value, $array)) {
            return $value;
        } else { // if not, load the old value
            return $this->options[$key];
        }
    }

    function validate_options($input) {
        // trim the spaces out of the key
        $validated['site_key'] = trim($input['site_key']);
        $validated['secret'] = trim($input['secret']);

        $themes = array ('standard', 'light', 'dark');
        $validated['comments_theme'] = $this->validate_dropdown($themes,
            'comments_theme', $input['comments_theme']);
        $validated['recaptcha_language'] = $input['recaptcha_language'];
        $validated['no_response_error'] = $input['no_response_error'];
        return $validated;
    }
    // display recaptcha
    function show_recaptcha_in_registration($errors) {
        $escaped_error = htmlentities($_GET['rerror'], ENT_QUOTES);

        // if it's for wordpress mu, show the errors
        if ($this->is_multi_blog()) {
            $error = $errors->get_error_message('captcha');
            echo '<label for="verification">Verification:</label>';
            echo ($error ? '<p class="error">' . $error . '</p>' : '');
            echo $this->get_recaptcha_html();
        } else {        // for regular wordpress
            echo $this->get_recaptcha_html();
        }
    }

    function validate_recaptcha_response($errors) {
        if (empty($_POST['g-recaptcha-response']) ||
            $_POST['g-recaptcha-response'] == '') {
            $errors->add('blank_captcha', $this->options['no_response_error']);
            return $errors;
        }

        if ($this->_reCaptchaLib == null) {
            $this->_reCaptchaLib = new ReCaptcha($this->options['secret']);
        }
        $response = $this->_reCaptchaLib->verifyResponse(
            $_SERVER['REMOTE_ADDR'],
            $_POST['g-recaptcha-response']);

        // response is bad, add incorrect response error
        if (!$response->success)
            $errors->add('captcha_wrong', $response->error);

        return $errors;
    }

    function validate_recaptcha_response_wpmu($result) {
        if (!$this->is_authority()) {
            // blogname in 2.6, blog_id prior to that
            // todo: why is this done?
            if (isset($_POST['blog_id']) || isset($_POST['blogname']))
                return $result;
                    // no text entered
            if (empty($_POST['g-recaptcha-response']) ||
                $_POST['g-recaptcha-response'] == '') {
                $result['errors']->add('blank_captcha',
                    $this->options['no_response_error']);
                return $result['errors'];
            }

            if ($this->_reCaptchaLib == null) {
                $this->_reCaptchaLib = new ReCaptcha($this->options['secret']);
            }
            $response = $this->_reCaptchaLib->verifyResponse(
                $_SERVER['REMOTE_ADDR'],
                $_POST['g-recaptcha-response']);

            // response is bad, add incorrect response error
            if (!$response->success) {
                $result['errors']->add('captcha_wrong', $response->error);
                echo '<div class="error">' . $response->error . '</div>';
            }
                    return $result;
        }
    }
    // utility methods
    function hash_comment($id) {
        define ("RECAPTCHA_WP_HASH_SALT", "b7e0638d85f5d7f3694f68e944136d62");
        if (function_exists('wp_hash'))
            return wp_hash(RECAPTCHA_WP_HASH_SALT . $id);
        else
            return md5(RECAPTCHA_WP_HASH_SALT . $this->options['secret'] . $id);
    }

    function get_recaptcha_html() {
        return '<div class="g-recaptcha" data-sitekey="' .
            $this->options['site_key'] .
            '" data-theme="' . $this->options['comments_theme'] .
            '"></div><script type="text/javascript"' .
            'src="https://www.google.com/recaptcha/api.js?hl=' .
            $this->options['recaptcha_language'] .
            '"></script>';
    }

    function show_recaptcha_in_comments() {
        global $user_ID;

        //modify the comment form for the reCAPTCHA widget
        add_action('wp_footer', array(&$this, 'save_comment_script'));

        $comment_string = <<<COMMENT_FORM
            <div id="recaptcha-submit-btn-area">&nbsp;</div>
            <noscript>
            <style type='text/css'>#submit {display:none;}</style>
            <input name="submit" type="submit" id="submit-alt" tabindex="6"
                value="Submit Comment"/> 
            </noscript>
COMMENT_FORM;

        $use_ssl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on");

        $escaped_error = htmlentities($_GET['rerror'], ENT_QUOTES);

        echo $this->get_recaptcha_html() . $comment_string;
    }

    // this is what does the submit-button re-ordering
    function save_comment_script() {
        $javascript = <<<JS
            <script type="text/javascript">
            var sub = document.getElementById('submit');
            document.getElementById('recaptcha-submit-btn-area').appendChild (sub);
            document.getElementById('submit').tabIndex = 6;
            if ( typeof _recaptcha_wordpress_savedcomment != 'undefined') {
                document.getElementById('comment').value = 
                    _recaptcha_wordpress_savedcomment;
            }
            </script>
JS;
        echo $javascript;
    }

    function check_comment($comment_data) {
        global $user_ID;
        // do not check trackbacks/pingbacks
        if ($comment_data['comment_type'] == '') {
            if ($this->_reCaptchaLib == null) {
                $this->_reCaptchaLib = new ReCaptcha($this->options['secret']);
            }
            $response = $this->_reCaptchaLib->verifyResponse(
                $_SERVER['REMOTE_ADDR'],
                $_POST['g-recaptcha-response']);

            if (!$response->success) {
                $this->_saved_error = $response->error;
                add_filter('pre_comment_approved',
                    create_function('$a', 'return \'spam\';'));
            }
        }
        return $comment_data;
    }

    function relative_redirect($location, $comment) {
        if ($this->_saved_error != '') {
            // replace #comment- at the end of $location with #commentform
                $location = substr($location, 0, strpos($location, '#')) .
                ((strpos($location, "?") === false) ? "?" : "&") .
                'rcommentid=' . $comment->comment_ID .
                '&rerror=' . $this->_saved_error .
                '&rchash=' . $this->hash_comment($comment->comment_ID) .
                '#commentform';
        }
        return $location;
    }

    function saved_comment() {
        if (!is_single() && !is_page())
            return;
        $comment_id = $_REQUEST['rcommentid'];
        $comment_hash = $_REQUEST['rchash'];
        if (empty($comment_id) || empty($comment_hash))
           return;
        if ($comment_hash == $this->hash_comment($comment_id)) {
           $comment = get_comment($comment_id);

           // todo: removed double quote from list of 'dangerous characters'
           $com = preg_replace('/([\\/\(\)\+\;\'])/e',
               '\'%\' . dechex(ord(\'$1\'))',
               $comment->comment_content);
               $com = preg_replace('/\\r\\n/m', '\\\n', $com);
               echo "
            <script type='text/javascript'>
            var _recaptcha_wordpress_savedcomment =  '" . $com  ."';
            _recaptcha_wordpress_savedcomment =
                unescape(_recaptcha_wordpress_savedcomment);
            </script>
            ";

            wp_delete_comment($comment->comment_ID);
        }
    }
 
   // add a settings link to the plugin in the plugin list
    function show_settings_link($links, $file) {
        if ($file == plugin_basename($this->path_to_plugin_directory() .
            '/wp-recaptcha.php')) {
            $settings_title = __('Settings for this Plugin', 'recaptcha');
            $settings = __('Settings', 'recaptcha');
            $settings_link =
               '<a href="options-general.php?page=wp-recaptcha/recaptcha.php"' .
               ' title="' . $settings_title . '">' . $settings . '</a>';
           array_unshift($links, $settings_link);
        }
        return $links;
    }

    // add the settings page
    function add_settings_page() {
        // add the options page
// XTEC ********** MODIFICAT -> Added WordPressMS to share site_options
// 2015.06.12 @sarjona
        // At the moment options can be edited only from main blog
        if ( ($this->environment == Environment::WordPressMU || ( $this->environment == Environment::WordPressMS && get_current_blog_id() === 1) ) &&
            $this->is_authority()) {
            add_options_page('WP-reCAPTCHA', 'WP-reCAPTCHA', 'manage_options',
                 __FILE__, array(&$this, 'show_settings_page'));
        }
// *********** ORIGINAL
/*
        if ($this->environment == Environment::WordPressMU &&
            $this->is_authority())
            add_submenu_page('wpmu-admin.php', 'WP-reCAPTCHA', 'WP-reCAPTCHA',
                'manage_options', __FILE__, array(&$this, 'show_settings_page'));
            add_options_page('WP-reCAPTCHA', 'WP-reCAPTCHA', 'manage_options',
                 __FILE__, array(&$this, 'show_settings_page'));
*/
// *********** FI
        }

    // store the xhtml in a separate file and use include on it
    function show_settings_page() {
        include("settings.php");
    }

    function build_dropdown($name, $keyvalue, $checked_value) {
        echo '<select name="' . $name . '" id="' . $name . '">' . "\n";
        foreach ($keyvalue as $key => $value) {
            $checked = ($value == $checked_value) ?
                ' selected="selected" ' : '';
            echo '\t <option value="' . $value . '"' . $checked .
                ">$key</option> \n";
            $checked = NULL;
        }
        echo "</select> \n";
    }

    function theme_dropdown() {
        $themes = array (
            __('Standard', 'recaptcha') => 'standard',
            __('Light', 'recaptcha') => 'light',
            __('Dark', 'recaptcha') => 'dark'
        );
        $this->build_dropdown('recaptcha_options[comments_theme]', $themes,
            $this->options['comments_theme']);
    }

    function recaptcha_language_dropdown() {
        $languages = array (
            __('English', 'recaptcha') => 'en',
            __('Arabic', 'recaptcha') => 'ar',
            __('Bulgarian', 'recaptcha') => 'bg',
            __('Catalan Valencian', 'recaptcha') => 'ca',
            __('Czech', 'recaptcha') => 'cs',
            __('Danish', 'recaptcha') => 'da',
            __('German', 'recaptcha') => 'de',
            __('Greek', 'recaptcha') => 'el',
            __('British English', 'recaptcha') => 'en_gb',
            __('Spanish', 'recaptcha') => 'es',
            __('Persian', 'recaptcha') => 'fa',
            __('French', 'recaptcha') => 'fr',
            __('Canadian French', 'recaptcha') => 'fr_ca',
            __('Hindi', 'recaptcha') => 'hi',
            __('Croatian', 'recaptcha') => 'hr',
            __('Hungarian', 'recaptcha') => 'hu',
            __('Indonesian', 'recaptcha') => 'id',
            __('Italian', 'recaptcha') => 'it',
            __('Hebrew', 'recaptcha') => 'iw',
            __('Jananese', 'recaptcha') => 'ja',
            __('Korean', 'recaptcha') => 'ko',
            __('Lithuanian', 'recaptcha') => 'lt',
            __('Latvian', 'recaptcha') => 'lv',
            __('Dutch', 'recaptcha') => 'nl',
            __('Norwegian', 'recaptcha') => 'no',
            __('Polish', 'recaptcha') => 'pl',
            __('Portuguese', 'recaptcha') => 'pt',
            __('Romanian', 'recaptcha') => 'ro',
            __('Russian', 'recaptcha') => 'ru',
            __('Slovak', 'recaptcha') => 'sk',
            __('Slovene', 'recaptcha') => 'sl',
            __('Serbian', 'recaptcha') => 'sr',
            __('Swedish', 'recaptcha') => 'sv',
            __('Thai', 'recaptcha') => 'th',
            __('Turkish', 'recaptcha') => 'tr',
            __('Ukrainian', 'recaptcha') => 'uk',
            __('Vietnamese', 'recaptcha') => 'vi',
            __('Simplified Chinese', 'recaptcha') => 'zh_cn',
            __('Traditional Chinese', 'recaptcha') => 'zh_tw'
        );

        $this->build_dropdown('recaptcha_options[recaptcha_language]',
            $languages, $this->options['recaptcha_language']);
    }
} // end class declaration

?>
