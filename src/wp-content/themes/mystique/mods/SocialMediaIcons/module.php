<?php
/**
 * Module Name:   Social-Media Icons
 * Description:   Adds support for social media icons in your theme. Ideas from <a href="http://marknhewitt.co.uk/my-web-portfolio/mystique-theme-edits/extra-nav-icons-for-the-mystique-theme/" target="_blank">Mark N Hewitt</a> & <a href="http://polpoinodroidi.com/">Frasten</a>.
 * Version:       2.0
 * Author:        digitalnature
 * Author URI:    http://digitalnature.eu
 * Priority:      100
 * Auto Enable:   yes
 */


// class name must follow this pattern (AtomMod + directory name)
class AtomModSocialMediaIcons extends AtomMod{

  // the folder name which stores user-uploaded icons (inside the child theme dir)
  const
    USER_ICON_DIR = 'social-media-icons';

  protected
    $defaults,
    $icon_size_x,
    $icon_size_y,
    $sprite_path,
    $sprite_url;


  public function onInit(){

    $this->defaults = array();
    
    // register extra theme options
    atom()->registerDefaultOptions(array('media_icons' => $this->defaults));

    // we're saving the sprite in /wp-content/uploads/ by default
    $upload_dir = wp_upload_dir();

    $config = atom()->getContextArgs('media_icons', array(
      'size'     => array(64, 64),                                       // icon size
      'path'     => $upload_dir['basedir'].'/'.ATOM.'_media_icons.png',  // sprite path
      'url'      => $upload_dir['baseurl'].'/'.ATOM.'_media_icons.png',  // sprite uri
      'location' => 'social_media_links',                                // atom action tag to use for output
    ));

    // compat -- older versions were using the same value for both width & height
    if(!is_array($config['size']))
      $config['size'] = array((int)$config['size'], (int)$config['size']);

    list($this->icon_size_x, $this->icon_size_y) = $config['size'];

    $this->sprite_path = $config['path'];
    $this->sprite_url  = $config['url'];

    // hooks
    atom()->add($config['location'], array($this, 'output'), 100);

    // there's very little css, not worth loading a new stylesheet so we add it inline
    atom()->add('inline_css', array($this, 'css'), 100);

    if(is_admin()){

      atom()->addContextArgs('settings_update_errors', array(
        20 => atom()->t('Failed to create sprite image with the selected icons. Make sure your upload directory is writable'),
      ));

      // insert a tab
      atom()->interface->addSection('media', atom()->t('Social-Media Icons'), array($this, 'form'), 32);
      atom()->interface->addAsset('script', 'jquery-ui-sortable');

      atom()->add('save_options', array($this, 'save'), 10, 2);

      // ajax hooks
      add_action('wp_ajax_update_media_icon_cache', array($this, 'updateIconDataCache'));
    }
  }



  public function save($status, $options){

    // icon options were already saved in the original save handler, we only need to create the sprite
    $icons_to_process = $this->getActiveIcons(); // same as array_keys($options['media_icons'])

    if(!isset($_POST['media_icons']) || empty($icons_to_process)) return;

    if(!$this->generateSprite($icons_to_process, $this->sprite_path) && !is_readable($this->sprite_path)){
      $options['media_icons'] = array();
      atom()->setOptions($options);
      $status = 20; // the error code above
    }

    // smush it installed? try to reduce the sprite size then... -- @todo: check this out because it fails on certain hosts
    //if(function_exists('wp_smushit')){
    //  list($probably_smushed_sprite, $messages) = wp_smushit($this->sprite_path);
    //  $this->sprite_path = $probably_smushed_sprite;
    //}

    return $status;
  }


  // caches filled input field values
  public function updateIconDataCache(){
    check_ajax_referer('update_media_icon_cache');

    if(isset($_GET['data']) && is_array($_GET['data'])){

      if(($cache = get_transient('media_icon_cache')) === false) $cache = array();
      delete_transient('media_icon_cache');

      $data = $_GET['data'];
      foreach($data as &$entry)
        $entry = current_user_can('unfiltered_html') ? $entry : wp_filter_post_kses($entry);

      extract($data);

      $cache[$icon][$field] = $value;
      set_transient('media_icon_cache', $cache, 60*60*24*90); // 90 days
    }

    exit;
  }



  // get the active icon list
  protected static function getActiveIcons(){
    $active_icons = atom()->options('media_icons');
    $active_icons = is_array($active_icons) ? array_keys($active_icons) : array();

    return $active_icons;
  }


  // get a list of all icons
  protected function getIconList(){
    $active_icons = $this->getActiveIcons();

    $all_icons = array();
    foreach(glob(TEMPLATEPATH.'/images/'.self::USER_ICON_DIR.'/*.png') as $filename)
      $all_icons[] = basename($filename, '.png');

    if(is_child_theme() && is_dir(STYLESHEETPATH.'/'.self::USER_ICON_DIR))
      foreach(glob(STYLESHEETPATH.'/'.self::USER_ICON_DIR.'/*.png') as $filename)
        $all_icons[] = basename($filename, '.png');

    return array_diff($all_icons, $active_icons);
  }



  // get a field like label/URI for the given icon
  protected function getIconData($icon, $field = ''){

    // looks inside the options first (only active icons store the fields here)
    $options = atom()->options('media_icons');

    $data = isset($options[$icon]) ? $options[$icon] : array('', '', '');

    // not here, icon is inactive, so try the cache
    if(empty($data) && (($cache = get_transient('media_icon_cache')) !== false) && isset($cache[$icon])) $data = $cache[$icon];

    if($field)
      return isset($data[$field]) ? $data[$field] : '';

    return $data;
  }


  // locates images and generates the icon <image> tag
  protected function getIconImage($icon){

    // module directory first
    if(is_readable(TEMPLATEPATH.'/images/'.self::USER_ICON_DIR.'/'.$icon.'.png'))
      $url = atom()->getThemeURL().'/images/'.self::USER_ICON_DIR.'/'.$icon.'.png';

    // child theme dir
    elseif((is_child_theme() && is_dir(STYLESHEETPATH.'/'.self::USER_ICON_DIR)))
      $url = atom()->get('child_theme_url').'/'.self::USER_ICON_DIR.'/'.$icon.'.png';

    $data = $this->getIconData($icon);

    $label = isset($data['label']) ? 'data-label="'.esc_html($data['label']).'"' : '';
    $uri   = isset($data['uri']) ? 'data-uri="'.esc_html($data['uri']).'"': '';
    $meta  = isset($data['meta']) ? 'data-uri="'.esc_html($data['meta']).'"': '';

    return '<img src="'.$url.'" alt="'.$icon.'" width="'.$this->icon_size_x.'" height="'.$this->icon_size_y.'" '.$label.' '.$uri.' '.$meta.' />';
  }



  // theme settings page
  public function form(){ ?>
    <style type="text/css">

      #social-media-icons ul{
        padding: 5px;
      }

      #social-media-icons li{
        float:left;
        width: <?php echo $this->icon_size_x + 2; ?>px;
        height: <?php echo $this->icon_size_y + 2; ?>px;
        margin: 2px;
        padding: 2px;
        cursor: move;
        border: 2px solid transparent;
        border-radius: 10px;
        text-align:center;
      }

      #social-media-icons li.selected{
        background: #fff;
        border-color: #ddd;
      }

    </style>
    <div class="notice">
      <?php atom()->te('Drag and drop icons below to enable or disable them.'); ?>

    </div>
    <div class="metabox-holder flow clear-block" id="social-media-icons">

      <input type="hidden" name="media_icons" value="0" />

      <div class="clear-block">
        <div class="block alignleft">
          <div class="postbox">
            <h3><span><?php atom()->te('Active icons'); ?></span></h3>
            <ul class="inside active-icons icons clear-block">
              <?php foreach($this->getActiveIcons() as $icon): ?>
              <li data-icon="<?php echo $icon; ?>">
                <?php echo $this->getIconImage($icon); ?>
                <input type="hidden" name="media_icons[<?php echo $icon; ?>][label]" value="<?php echo $this->getIconData($icon, 'label'); ?>" />
                <input type="hidden" name="media_icons[<?php echo $icon; ?>][uri]" value="<?php echo $this->getIconData($icon, 'uri'); ?>" />
                <input type="hidden" name="media_icons[<?php echo $icon; ?>][meta]" value="<?php echo $this->getIconData($icon, 'meta'); ?>" />
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

        <div class="block alignright properties">

          <div class="entry">
            <p><label for="media_label"><?php atom()->te('Label'); ?></label></p>
            <input id="media_label" size="40" type="text" value="" />
          </div>

          <div class="entry">
            <p><label for="media_uri"><?php atom()->te('Target URI'); ?></label></p>
            <input id="media_uri" size="60" type="text" value="" />
          </div>

          <div class="entry">
            <p><label for="media_meta"><?php atom()->te('Meta info (some themes may display this)'); ?></label></p>
            <textarea id="media_meta" rows="5" cols="40" type="text" value=""></textarea>
          </div>

        </div>
      </div>

      <div class="postbox wide">
        <h3><span><?php atom()->te('Inactive icons'); ?></span></h3>
        <div class="inside">
          <ul class="inactive-icons icons clear-block">
            <?php foreach($this->getIconList() as $icon): ?>
            <li data-icon="<?php echo $icon; ?>">
              <?php echo $this->getIconImage($icon); ?>
            </li>
            <?php endforeach; ?>
          </ul>

          <?php if(current_user_can('edit_themes')): ?>
          <p>
            <?php
             if(is_child_theme())
               atom()->te('You can also include your own icons within this list, by copying them in %s',
                 sprintf('<strong><em>%s</em></strong>', basename(WP_CONTENT_DIR).'/themes/'.basename(STYLESHEETPATH).'/'.self::USER_ICON_DIR.'/')
               );
             else
               atom()->te('Activate a child theme to add your own icons in this list.');
            ?>
          </p>
          <?php endif; ?>

        </div>

      </div>

    </div>

    <script type="text/javascript">
      jQuery(document).ready(function($){

        $('#social-media-icons').each(function(){
          var block = $(this),
              props = $('.properties', block);

          $('.icons', block).sortable({
            connectWith: '.icons',
            stop: function(event, ui){
              $(ui.item).trigger('click');

              if($(ui.item).parents('.inactive-icons').length > 0){

                // save input values
                $('input, textarea', $(ui.item)).each(function(){
                  var property = /\[([^\]]*)\]$/.exec($(this).attr('name'))[1]; // last [...] from the input name
                  $('img', $(ui.item)).data(property, $(this).val());
                });

                $('input, textarea', $(ui.item)).remove();
                return;
              }

              // check if new icons are being dropped, and append hidden fields
              if($('input, textarea', $(ui.item)).length < 1){
                var icon = $(ui.item).data('icon'),
                    label = $('img', ui.item).data('label'),
                    uri = $('img', ui.item).data('uri'),
                    meta = $('img', ui.item).data('meta');

                $(ui.item).append('<input type="hidden" name="media_icons[' + icon +'][label]" value="' + label + '" />');
                $(ui.item).append('<input type="hidden" name="media_icons[' + icon +'][uri]" value="' + uri + '" />');
                $(ui.item).append('<input type="hidden" name="media_icons[' + icon +'][meta]" value="' + meta + '" />');
                $('#media_label').val(label);
                $('#media_uri').val(uri);
                $('#media_meta').val(meta);

              };
            }
          }).disableSelection();

          $('.icons li', block).click(function(event){
            var icon = $(this).data('icon');

            // make sure we update the hidden fields
            $('input, textarea', props).trigger('blur');

            $('li', block).removeClass('selected');
            $(this).addClass('selected');

            if($(this).parents('.active-icons').length > 0){
              $('input, textarea', props).removeAttr('disabled');
              $('input, textarea, label', props).removeClass('disabled');

              $('input, textarea', this).each(function(){
                var property = /\[([^\]]*)\]$/.exec($(this).attr('name'))[1]; // last [...] from the input name
                $('#media_' + property).val($(this).val());
              })

            }else{

              $('input, textarea', props).attr('disabled', 'disabled').val('');
              $('input, textarea, label', props).addClass('disabled');
            }
          });

          $('input, textarea', props).bind('change blur', function(event){
            var current_value = $(this).val(),
                property = $(this).attr('id').replace('media_', '');
                icon = block.find('.active-icons li.selected').data('icon');

            $('[name="media_icons[' + icon + '][' + property + ']"]').val(current_value);

            // only update cache when the user actually changes the field
            if(event.type !== 'blur'){
              $.ajax({
                type: 'GET',
                url: ajaxurl,
                data: {
                  action: 'update_media_icon_cache',
                  data: {'icon': icon, 'field': property, 'value': current_value} ,
                  _ajax_nonce: '<?php echo wp_create_nonce('update_media_icon_cache'); ?>'
                }
              });
            }

          });

          $('.icons li:first', block).trigger('click');

        });


      });
    </script>

    <?php
  }



  // the output
  public function output($links){

    // prepare output
    $icons = array();

    foreach($this->getActiveIcons() as $icon)
      $icons[] = (object)array(
        'ID'    => $icon,
        'label' => esc_attr($this->getIconData($icon, 'label')),
        'URI'   => esc_attr($this->getIconData($icon, 'uri')),
        'meta'  => $this->getIconData($icon, 'meta'),
      );

    if($icons){

      $template = atom()->findTemplate('social-media');

      if(!$template)
        return atom()->log('Social Media Icons: no output (required template was not found)', Atom::WARNING);

      // prepare output
      $icons = array();

      foreach($this->getActiveIcons() as $icon)
        $icons[] = (object)array(
          'ID'    => $icon,
          'label' => esc_attr($this->getIconData($icon, 'label')),
          'URI'   => esc_attr($this->getIconData($icon, 'uri')),
          'meta'  => $this->getIconData($icon, 'meta'),
        );

      // we have a template, load it
      atom()->load($template, array('icons' => $icons));

    }
  }



  // css styles
  public function css($css){

    // check if these are the default settings, because the sprite doesn't exist in this case and we have to use our default image
    $image = (atom()->options('media_icons') !== $this->defaults) ? $this->sprite_url : $this->url.'/defaults.png';

    $rules = array('.media .icon{background: transparent url("'.$image.'") no-repeat center top;}');
    $icons = $this->getActiveIcons();
    $y = 0;

    foreach($icons as $icon){
      $rules[] = '.media .'.sanitize_html_class($icon).' .icon{background-position: center -'.$y.'px;}';
      $y = $y + ($this->icon_size_y - 1);
    }

    return $css."\n".implode("\n", $rules)."\n";
  }



  // generate a sprite-type image with the icons, so we don't have to load each icon separately
  protected function generateSprite($icons, $output){

    // check if we have permission to write to this directory
    if(!is_writable(dirname($output))) return false;

    $images = array();
    $height = count($icons) * $this->icon_size_y;

    $sprite = @imagecreatetruecolor($this->icon_size_x, $height);
    if(!$sprite) return false;

    // set the background to transparent
    $transparent = imagecolorallocatealpha($sprite, 0, 0, 0, 127);
    if(!imagefill($sprite, 0, 0, $transparent)) return false;
    if(!imagesavealpha($sprite, true)) return false;

    $y = 0;
    foreach($icons as $icon){

      // look in parent theme directory first
      $file = TEMPLATEPATH.'/images/'.self::USER_ICON_DIR.'/'.$icon.'.png';

      // not there, look in child theme folder, if we have a child theme
      if(!is_readable($file))
        if(is_child_theme()){
          $file = STYLESHEETPATH.'/'.self::USER_ICON_DIR.'/'.$icon.'.png';

          // fail, user has deleted it?
          if(!is_readable($file)) return false;

        }else{
          return false;
        }

      $image = @imagecreatefrompng($file);
      if(!$image) return false;

      if(!imagecopyresampled($sprite, $image, 0, $y, 0, 0, $this->icon_size_x, $this->icon_size_y, $this->icon_size_x, $this->icon_size_y)) return false;
      imagedestroy($image);
      $y = $y + ($this->icon_size_y - 1);
    }

    if(!imagepng($sprite, $output))
      return false;

    imagedestroy($sprite);

    return true;
  }

}
