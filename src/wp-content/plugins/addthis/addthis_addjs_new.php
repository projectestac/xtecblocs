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

/**
 * Class for managing AddThis script includes across all its plugins.
 */
Class AddThis_addjs_sharing_button_plugin{
    /**
    * var bool check to see if we have added our JS already.  Ensures that we don't add it twice
    */
    private $_js_added;

    private $_options;

    private $addThisConfigs;

    var $jsToAdd;

    var $jsAfterAdd;

    var $atversion;

    const addjs_version = 1;

    /**
    *
    */
    public function __construct ($addThisConfigs, $cmsConnector){
        $this->addThisConfigs = $addThisConfigs;
        $this->cmsConnector = $cmsConnector;

        if ( did_action('addthis_addjs_created') !== 0){
            //_doing_it_wrong( 'addthis_addjs', 'Only one instance of this class should be initialized.  Look for the $addthis_addjs global first',1 );
        }

        // We haven't added our JS yet. Or at least better not have.
        $this->_js_added = false;

        $this->_options = $addThisConfigs->getConfigs();

        // Version of AddThis code to use
        $this->atversion = $this->_options['atversion'];

        // If the footer option isn't set, check for it
        if (! isset($this->_options['wpfooter']) && current_user_can('manage_options'))
        {
            add_action('admin_init',array($this, 'update_wpfooter'));
        }

        // on theme swich, check for footer again
        add_action('switch_theme', array($this, 'switch_theme'),15);

        // In order for our wp_footer magic to work, we need to sometimes add our stuff
        add_action('init', array($this, 'maybe_add_footer_comment'));

        // for adding option for show/hide addthis sharing button in admin post add/edit page.
        add_action('admin_init', array($this, 'register_post_at_flag'));

        // for saving custom field value for show/hide addthis sharing button in admin post add/edit page.
        add_action('save_post', array($this, 'save_at_flag'));

        // Footer
        if ( isset($this->_options['wpfooter']) && $this->_options['wpfooter'])
            add_action('wp_footer', array($this, 'output_script') );
        else
            add_filter('the_content', array($this, 'output_script_filter') );

        //do_action('addthis_addjs_created');
    }

    function switch_theme(){
        $footer = $this->check_for_footer();
        $this->_options['wpfooter'] = $footer;
        $this->_options = $this->addThisConfigs->saveConfigs($this->_options);
    }

    function output_script(){
        if ($this->_js_added != true)
        {
            $this->wrapJs();
            $this->addWidgetToJs();
            $this->addAfterToJs();
            echo $this->jsToAdd;
            $this->_js_added = true;
            $this->jsToAdd = false;
        } else {
        	 $this->addAfterToJs();
        	 echo $this->jsToAdd;
             $this->jsToAdd = false;
        }
    }

    function output_script_filter($content){
        if ($this->_js_added != true && ! is_admin() && ! is_feed() )
        {
            $this->wrapJs();
            $this->addWidgetToJs();
            $this->addAfterToJs();
            $content = $content . $this->jsToAdd;
            $this->_js_added = true;
        }
        return $content;
    }

    function wrapJs(){
        $this->jsToAdd = '<script data-cfasync="false" type="text/javascript">' . $this->jsToAdd . '</script>';
    }

    /* testing for wp_footer in a theme stuff */
    function update_wpfooter(){
        $footer = $this->check_for_footer();
        $options = $this->_options;
        $options['wpfooter'] = $footer;
        $this->_options = $this->addThisConfigs->saveConfigs($options);
    }

    function check_for_footer(){
        $url = home_url();
        $response = wp_remote_get( $url, array( 'sslverify' => false ) );
        $code = (int) wp_remote_retrieve_response_code( $response );
            if ( $code == 200 ) {
                $html = preg_replace( '/[
                s]/', '', wp_remote_retrieve_body( $response ) );
                return (bool)( strstr( $html, '<!--wp_footer-->' ) );
            }
    }

    function maybe_add_footer_comment(){
            add_action( 'wp_footer', array($this, 'test_footer' ), 99999 ); // Some obscene priority, make sure we run last
    }

    /* END testing for wp_footer in a theme stuff */
    function addToScript($newData){
        $this->jsToAdd .= $newData;
    }

    function addAfterScript($newData){
    	if ( $this->_js_added != true )
        {
        	$this->jsAfterAdd .= $newData;
        } else {
        	$this->jsAfterAdd = $newData;
        }
    }

    function addWidgetToJs(){
        if (!is_404()) {
            //Load addthis script only if the page is not 404
            $addthis_settings_options = $this->addThisConfigs->getConfigs();

            $async = '';
            if (!empty($addthis_settings_options['addthis_asynchronous_loading'])) {
                $async = 'async="async"';
            }

            /**
             * Load client script based on the enviornment variable
             * Admin can enable debug mode in adv settings by adding url param debug=true
             */
            $script_domain = '//s7.addthis.com/js/';
            if (!empty($addthis_settings_options['addthis_environment'])) {
                $at_env = $addthis_settings_options['addthis_environment'];
                $script_domain = '//cache-'.$at_env.'.addthis.com/cachefly/js/';
            }

            $url = $script_domain .
                $this->atversion .
                '/addthis_widget.js#pubid=' .
                urlencode($this->addThisConfigs->getUsableProfileId());

            $addthis_share = $this->addThisConfigs->createAddThisShareVariable();
            $addthis_share_js = 'var addthis_share = '. json_encode($addthis_share) .';';

            $addthis_config = $this->addThisConfigs->createAddThisConfigVariable();
            $addthis_config_js = 'var addthis_config = '. json_encode($addthis_config) .';';

            $addthis_layers = $this->addThisConfigs->createAddThisLayersVariable();
            $addthis_layers_js = 'var addthis_layers = '. json_encode($addthis_layers) .';';

            $this->jsToAdd .= '
                <!-- AddThis Settings Begin -->
                <script data-cfasync="false" type="text/javascript">
                    var addthis_product = "'. $this->cmsConnector->getProductVersion() . '";
                    var wp_product_version = "' . $this->cmsConnector->getProductVersion() . '";
                    var wp_blog_version = "' . $this->cmsConnector->getCmsVersion() . '";
                    var addthis_plugin_info = ' . $this->addThisConfigs->getAddThisPluginInfoJson() . ';
                    if (typeof(addthis_config) == "undefined") {
                        ' . $addthis_config_js . '
                    }
                    if (typeof(addthis_share) == "undefined") {
                        ' . $addthis_share_js . '
                    }
                    if (typeof(addthis_layers) == "undefined") {
                        ' . $addthis_layers_js . '
                    }
                </script>
                <script
                    data-cfasync="false"
                    type="text/javascript"
                    src="' . $url . ' "
                    ' . $async . '
                >
                </script>
                <script data-cfasync="false" type="text/javascript">
                    (function() {
                        var at_interval = setInterval(function () {
                            if(window.addthis) {
                                clearInterval(at_interval);
                                addthis.layers(addthis_layers);
                            }
                        },1000)
                    }());
                </script>
                ';
        }
    }

    function addAfterToJs(){
        if (! empty($this->jsAfterAdd)) {
            $this->jsToAdd .= '<script data-cfasync="false" type="text/javascript">' . $this->jsAfterAdd . '</script>';
            $this->jsAfterAdd = NULL;
        }
    }

    function setProfileId($profile){
        $this->_options['addthis_profile'] = sanitize_text_field($profile);
        $this->_options = $this->addThisConfigs->saveConfigs($this->_options);
    }

    function test_footer(){
        echo '<!--wp_footer-->';
    }

    function getAtPluginPromoText(){
        // Included not to break the other plugins(smartlayer)
        if (! did_action('admin_init') && !  current_filter('admin_init'))
        {
            _doing_it_wrong('getAtPluginPromoText', 'This function should only be called on an admin page load and no earlier the admin_init', 1);
            return null;
        }
        return null;
    }

    /*
     * Function to add checkbox to show/hide Addthis sharing buttons in admin post add/edit page.
     */
    public function register_post_at_flag() {
        $configs = $this->addThisConfigs->getConfigs();
        if (!$configs['addthis_per_post_enabled']) {
            return;
        }

        add_meta_box('at_widget', __('AddThis Sharing Buttons'), array($this, 'add_at_flag_meta_box'), 'post', 'advanced', 'high');
        add_meta_box('at_widget', __('AddThis Sharing Buttons'), array($this, 'add_at_flag_meta_box'), 'page', 'advanced', 'high');
    }
    /*
     * Function to add checkbox to show/hide Addthis sharing buttons in admin post add/edit page.
     */
    public function add_at_flag_meta_box($post){
        $at_flag = get_post_meta($post->ID, '_at_widget', true);

        $off_checked = '';
        $on_checked = '';
        if ($at_flag == '0') {
            $off_checked = 'checked="checked"';
        } else {
            $on_checked = 'checked="checked"';
        }

        $html = '
            <label for="_at_widget_on">
                <input
                    type="radio"
                    id="_at_widget_on"
                    name="_at_widget"
                    value="1"
                    ' . $on_checked . '
                />
                <span class="addthis-checkbox-label">On</span>
            </label>
            <label for="_at_widget_off">
                <input
                    type="radio"
                    id="_at_widget_off"
                    name="_at_widget"
                    value="0"
                    ' . $off_checked . '
                />
                <span class="addthis-checkbox-label">Off</span>
            </label>
        ';

        echo $html;

    }
    /*
     * Function to save the value of checkbox to show/hide Addthis sharing buttons in admin post add/edit page.
     */
    function save_at_flag(){
        global $post;

        //Ignore if trigger is by theme specific actions without post object
        if (!isset($post, $_POST['_at_widget'])) {
            return;
        }

        if ($_POST['_at_widget'] == 1) {
            update_post_meta($post->ID, '_at_widget', '1');
        } else {
            update_post_meta($post->ID, '_at_widget', '0');
        }
    }
}
