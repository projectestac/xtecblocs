<?php
/**
 * Module Name: Advertisment Blocks
 * Description: Allows you to insert context-dependent ads into your pages
 * Version: 2.0
 * Author: digitalnature
 * Author URI: http://digitalnature.eu
 * Auto Enable: yes
 */


// @todo: http://digitalnature.eu/topic/suggestions-for-ads-settings-page/
// - add disable/enable switch to ads
// - add header ad location
// - ad rotation, maybe?



// class name must follow this pattern (AtomMod + directory name)
class AtomModAdBlocks extends AtomMod{

  // valid ads visible on the current page / to the current user
  protected $queue = array();

  // available variables from parent class:
  //
  // $this->url  - this module's url path
  // $this->dir  - this module's directory


  
 /*
  * Module init event.
  * Runs during Atom initialization (within the 'after_setup_theme' action)
  *
  * @since      1.0
  */
  public function onInit(){

     // register extra theme options
    atom()->registerDefaultOptions(array('advertisments' => array()));

    if(is_admin()){
      // insert a tab, 22 is the priority (somewhere after 'Content Options')
      atom()->interface->addSection('ad_blocks', atom()->t('Ads'), array($this, 'form'), 22);
      add_action('wp_ajax_create_ad', array($this, 'createAd'));
    }

    add_action('template_redirect', array($this, 'queueAds'));

    //add_shortcode('ad', array($this, 'shortcode'));
  }



 /*
  * Shortcode handler
  *
  * @since      1.0
  * @params     array $atts
  * @return     string
  */
  public function shortcode($atts){

    $ads = atom()->options('advertisments');

    // only continue if the ad record exists
    if(!empty($atts) && isset($ads["a{$atts[0]}"])){

      extract($ads["a{$atts[0]}"]);

      // check if the current user can see it
      $current_user = wp_get_current_user();
      $active = is_numeric($to) ? (($to && !is_user_logged_in()) || (!$to && is_user_logged_in())) : in_array($to, $current_user->roles);

      if($active)
        return $html;
    }
  }



 /*
  * Processes the registered ads.
  * Here are all the visibility checks for each ad block
  *
  * @since      1.0
  */
  public function queueAds(){
    global $current_user, $post;

    $ads = atom()->options('advertisments');
    if(!is_array($ads) || empty($ads)) return; // no ads have been set up

    $per_page = get_option('posts_per_page');

    foreach($ads as $adv){

      // reset
      $when = $page = $n = '';

      extract($adv);

      $n = empty($n) ? 1 : min(max((int)$n, 1), $per_page);

      if(empty($page)) continue;

      // page check
      $active = "is_{$page}";
      $active = $active();

      // user: all/visitor
      if(is_numeric($to))
        $active = !$active || ($active && $to && is_user_logged_in()) ? false : $active;

      // user: role
      else
        $active = $active && in_array($to, $current_user->roles);

      // comment status, on single pages
      if(!empty($when) && is_single())
        $active = $active && ($when == $post->comment_status);

      if(apply_filters('atom_ad_visibility', $active, $adv))
        $this->queue[$place][] = $adv;
    }

    foreach($this->queue as $place => $adv)
      atom()->add($place, array($this, 'output'));
  }



 /*
  * Outputs visible ads to the screen
  *
  * @since      1.0
  * @param      string $place
  */
  public function output($place){
    global $wp_query;

    // a hacky way of getting do_action's $tag argument, but it saves us a lot of lines of code;
    // the ordinary way would be to create methods for each tag...
    $place = debug_backtrace();
    $place = substr($place[2]['args'][0], strlen('atom_'));

    if(!isset($this->queue[$place])) return;

    foreach($this->queue[$place] as $key => $adv){

      // teaser
      if(in_array($place, array('before_teaser', 'after_teaser')) && ($adv['n'] !== $wp_query->current_post + 1)) continue;

      // comments
      if(($place === 'before_comment') && ($adv['n'] !== atom()->comment->getNumber())) continue;

      // all
      echo $adv['html'];
      unset($this->queue[$place][$key]);
    }
  }



 /*
  * Tab entry in theme settings
  *
  * @since      1.0
  * @param      array $options
  */
  public function form($options){
    $ads = is_array($options['advertisments']) ? $options['advertisments'] : array();
    ?>
    <!-- tab: ads -->
    <div class="clear-block">

      <?php if(defined('I_WANT_MY_ADS_MODULE')): ?>

      <div class="notice">
        <?php atom()->te('This section helps you create advertisment blocks in non-widgetized areas. For widgetized areas such as sidebars, simply use a text widget to display your ads.'); ?>
      </div>

      <?php if(atom()->shortcodeExists('ad', array($this, 'shortcode'))): ?>
      <div class="notice e">
        <?php atom()->te('It appears that a plugin has already registered the %s shortcode.', '<strong>[ad]</strong>'); ?>
      </div>
      <?php endif; ?>

      <p>
        <input class="button-secondary create-ad" type="submit" value="<?php atom()->te('Create New Ad'); ?>" />
      </p>

      <br />
      <input type="hidden" name="advertisments" value="0" />

      <div id="ad-blocks">
        <?php foreach($ads as $key => $ad) $this->advertismentForm($key, $ad); ?>
      </div>


      <script type="text/javascript">

        jQuery(document).ready(function($){

          $('#theme-settings .create-ad').click(function(event){
            event.preventDefault();

            var id = 'a' + (new Date()).getTime();

            $.ajax({
              url: ajaxurl,
              type: 'GET',
              context: this,
              data: ({
                action: 'create_ad',
                key: id,
                _ajax_nonce: '<?php echo wp_create_nonce('theme-settings-ads'); ?>'
              }),
              beforeSend: function() {
                $(this).attr('disabled', 'disabled');
                $(this).parent().addClass('loading');
              },

              success: function(data){
                $(this).removeAttr('disabled');
                $(this).parent().removeClass('loading');
                var block = $(data);
                block.filter('#ad_' + id).hide();
                $('#ad-blocks').prepend(block);
                $('#ad_' + id).animate({
                          opacity: 'show',
                          height: 'show',
                          marginTop: 'show',
                          marginBottom: 'show',
                          paddingTop: 'show',
                          paddingBottom: 'show'
                         }, 333);


                // codemirror
                $('#ad_' + id + ' .editor').each(function(){
                  CodeMirror.fromTextArea(document.getElementById($(this).attr('id')), {
                    lineNumbers: true,
                    matchBrackets: true,
                    mode: $(this).data('mode')
                  });

                });
              }
            });
          });

        });

      </script>

      <?php else: ?>
      <div class="notice e">
        <?php atom()->te('This mod is now being developed as a WordPress plugin, see %s', '<a href="http://digitalnature.eu/forum/plugins/ad-manager/">AdManager</a> (old module data will be automatically imported by the plugin)'); ?>
      </div>
      <?php endif; ?>


    </div>
    <!-- /tab: ads -->
    <?php
  }



 /*
  * Single ad form entry in the theme settings
  *
  * @since      1.0
  * @param      string $id
  * @param      array $add
  */
  private function advertismentForm($id, $ad = array()){

    // defaults
    $ad = wp_parse_args($ad, array(
      // defaults (note that keys can change depending on context)
      'page'   => 'home',
      'place'  => 'after_teaser',
      'n'      => 1,
      'to'     => 1,
      'html'   => "<div class=\"ad-block aligncenter\">\n  <a href=\"http://google.com\"><img src=\"http://dummyimage.com/468x60/000/fff\" alt=\"Test Ad\" /></a>\n</div>",
      'when'   => 0, // 0 = anytime...
    ));

    // makre sure the ad position doesn't exceed the # if items it's relative to
    $ad['n'] = ($ad['place'] !== 'before_comment') ? min(max((int)$ad['n'], 1), get_option('posts_per_page')) : min(max((int)$ad['n'], 1), get_option('comments_per_page'));

    $contexts = apply_filters('atom_ad_contexts', array(
      'pages' => array(
        'home'            => atom()->t('Blog Homepage'),
        'single'          => atom()->t('Single Post Pages'),
        'category'        => atom()->t('Category Archives'),
        'tag'             => atom()->t('Tag Archives'),
        'author'          => atom()->t('Author Archives'),
        'date'            => atom()->t('Date-based Archives'),
        'search'          => atom()->t('Search Results'),
      ),

      'places' => array(
        'single' => array(
          'before_main'     => atom()->t('After Header'),
          'before_primary'  => atom()->t('Before Main'),
          'after_primary'   => atom()->t('After Main'),
          'before_comment'  => atom()->t('Before Comment'),
         ),

        'generic' => array(
          'before_main'     => atom()->t('After Header'),
          'before_primary'  => atom()->t('Before Main'),
          'after_primary'   => atom()->t('After Main'),
          'before_teaser'   => atom()->t('Before Post Teaser'),
          'after_teaser'    => atom()->t('After Post Teaser'),
        ),
      ),

    ));

    $wp_roles = new WP_Roles();
   ?>

   <div class="ad-block" id="ad_<?php echo $id; ?>">
     <div class="clear-block">
       <div class="alignleft">

          <label for="ad_<?php echo $id; ?>_page"><?php atom()->te('Visibility:'); ?></label>
          <select rel="page" id="ad_<?php echo $id; ?>_page" name="advertisments[<?php echo $id; ?>][page]">
            <optgroup label="<?php atom()->te('Automatic'); ?>">
              <?php foreach($contexts['pages'] as $page => $label): ?>
              <option value="<?php echo $page; ?>" <?php selected($page, $ad['page']) ?>><?php echo $label; ?></option>
              <?php endforeach; ?>
            </optgroup>
            <optgroup label="<?php atom()->te('Manual'); ?>">
              <option value="0" <?php selected(0, $ad['page']) ?>><?php atom()->te('Use shortcode'); ?></option>
            </optgroup>
          </select>

          <input type="text" size="24" class="code" rel="shortcode" ad-rules="page:0" value="<?php echo '[ad '.substr($id, 1).']'; ?>" />

          <select ad-rules="page:!single|!0" rel="generic" name="advertisments[<?php echo $id; ?>][place]">
            <?php foreach($contexts['places']['generic'] as $place => $label): ?>
            <option value="<?php echo $place; ?>" <?php selected($place, $ad['place']) ?>><?php echo $label; ?></option>
            <?php endforeach; ?>
          </select>

          <select ad-rules="page:single" rel="single" name="advertisments[<?php echo $id; ?>][place]">
            <?php foreach($contexts['places']['single'] as $place => $label): ?>
            <option value="<?php echo $place; ?>" <?php selected($place, $ad['place']) ?>><?php echo $label; ?></option>
            <?php endforeach; ?>
          </select>

          <label for="ad_<?php echo $id; ?>_ng">#</label>
          <input rel="ns" id="ad_<?php echo $id; ?>_ng" type="text" size="2" value="<?php echo (int)$ad['n']; ?>" ad-rules="generic:before_teaser|after_teaser + page:!0|!single" name="advertisments[<?php echo $id; ?>][n]" />

          <label for="ad_<?php echo $id; ?>_ns">#</label>
          <input rel="ng" id="ad_<?php echo $id; ?>_ns" type="text" size="2" value="<?php echo (int)$ad['n']; ?>" ad-rules="single:before_comment + page:!0|!generic" name="advertisments[<?php echo $id; ?>][n]" />

          <label for="ad_<?php echo $id; ?>_time"><?php atom()->te('When:'); ?></label>
          <select ad-rules="page:single" rel="when" id="ad_<?php echo $id; ?>_time" name="advertisments[<?php echo $id; ?>][when]">
            <option value="0" <?php selected(empty($ad['when'])) ?>><?php atom()->te("Anytime"); ?></option>
            <option value="closed" <?php selected('closed', $ad['when']) ?>><?php atom()->te("Comments are closed") ?></option>
            <option value="open" <?php selected('open', $ad['when']) ?>><?php atom()->te("Comments are open") ?></option>
          </select>

          <label for="ad_<?php echo $id; ?>_to"><?php atom()->te('To:'); ?></label>
          <select rel="to" id="ad_<?php echo $id; ?>_to" name="advertisments[<?php echo $id; ?>][to]">
            <option value="0" <?php selected(empty($ad['to'])) ?>><?php atom()->te("Anyone") ?></option>
            <option value="1" <?php selected(1, $ad['to']) ?>><?php atom()->te("Visitor only") ?></option>
            <?php foreach($wp_roles->get_names() as $role => $label): ?>
            <option value="<?php echo $role; ?>"  <?php selected($role, $ad['to']) ?>><?php echo translate_user_role($label); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <p class="alignright">
          <input class="button-secondary alignright remove-ad" type="button" value="<?php atom()->te('Remove'); ?>" />
        </p>

      </div>

      <div>
        <textarea data-mode="text/html" rows="4" class="code editor widefat" id="ad_html_<?php echo $id; ?>" name="advertisments[<?php echo $id; ?>][html]"><?php echo $ad['html']; ?></textarea>
      </div>

      <script type="text/javascript">
       /*<![CDATA[*/
       jQuery(document).ready(function($){
         $('#ad_<?php echo $id; ?>').FormDependencies({hide_inactive:true, clear_inactive:false, identify_by:'rel', attribute:'ad-rules'});

         $("#ad_<?php echo $id; ?> .remove-ad").click(function(){
           $("#ad_<?php echo $id; ?>").animate({
             opacity: 'hide',
             height: 'hide',
             marginTop: 'hide',
             marginBottom: 'hide',
             paddingTop: 'hide',
             paddingBottom: 'hide'
           }, 333, function(){ $(this).remove(); });
         });
       });
       /*]]>*/
      </script>
   </div>

   <?php
  }


 /*
  * Ajax event for adding a new ad from the theme settings
  *
  * @since      1.0
  */
  public function createAd(){
    check_ajax_referer('theme-settings-ads');
    $this->advertismentForm(strip_tags($_GET['key']));
    exit;
  }


}
