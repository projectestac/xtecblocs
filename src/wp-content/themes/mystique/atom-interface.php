<?php
/*
 * Administration pages (theme options interface).
 * The API is still under development, but the basic stuff is here...
 *
 * Read the documentation for more info: http://digitalnature.eu/docs/
 *
 * @revised   March 29, 2012
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */




/**
 * Creates an interface in the dashboard based on the data processed from functions.php
 *
 * @since 2.0
 */
class AtomInterface{

  protected
    $interface_id           = '',
    $menu_title             = '',
    $pages                  = array(),
    $tabs                   = array(),
    $tab_sections           = array(),
    $controls               = array(),
    $disabled_options       = array(),
    $nonce                  = '',
    $page                   = '',
    $update_info            = false,
    $options                = array(),
    $assets                 = array();



 /*
  * The constructor, sets up required hooks to create the appearance administration page
  *
  * @since    2.0
  * @param    string $id      ID of the interface
  * @param    string $title   Title of the menu / page
  */
  public function __construct($id, $title){

    $this->interface_id = $id;
    $this->menu_title = $title;

    add_action('admin_menu',                                array($this, 'addMenu'));
    add_action('admin_post_'.$this->interface_id.'_update', array($this, 'saveOptions'));

    add_action('admin_footer',                              array($this, 'footer'));

    // ajax hooks
    //add_action('wp_ajax_get_tab',                           array($this, 'getTab'));
    add_action('wp_ajax_process_upload',                    array($this, 'processUpload'));
    add_action('wp_ajax_rss_meta_box',                      array($this, 'rssMetaBox'));
    add_action('wp_ajax_force_update_check',                array($this, 'forceUpdateCheck'));

    atom()->add('settings_advanced',                        array($this, 'tabAdvancedCode'));
  }



 /*
  * Registers form fields (controls)
  *
  * @since    2.0
  * @param    array $controls
  */
  public function addControls($controls){

    foreach($controls as $id => $config){
      $config = array_merge(array(
        'location'        => '',
        'type'            => 'text',
        'depends_on'      => '',
        'conflicts_with'  => '',
        'rules'           => '',
        'label'           => '',
        'description'     => '',
        'values'          => array(),                                        // for selects
        'cap'             => '',
      ), $config);

      if(!atom()->optionExists($id))
        throw new Exception("Option '{$id}' does not exist! Please register it first");

      $this->controls[$id] = $config;

      if($config['cap'] && !current_user_can($config['cap']))
        $this->disabled_options[] = $id;

    }
  }



 /*
  * Same as the above, but can be used to register a single control
  *
  * @since    2.0
  * @param    string $id     Option ID
  * @param    array $args    Control properties
  */
  public function addControl($id, $args){
    $this->addControls(array($id => $args));
  }



 /*
  * Queues a script or style for loading in the administration area.
  * Tt accepts the same arguments as wp_enqueue_script / wp_enqueue_style (besides the 1st one - $type)
  *
  * @since    2.0
  * @param    string $type    Type, 'script' or 'style'
  */
  public function addAsset($type, $handle){

    $args = func_get_args();

    // drop $type
    array_shift($args);

    $assets = atom()->getContextArgs('interface_assets');
    $assets[$type][] = $args;

    atom()->setContextArgs('interface_assets', $assets);
  }



 /*
  * Can be used to directly insert form fields inside an existing page or section, eg. from inside a module
  *
  * @since    1.6
  * @param    string $option_id    The option ID
  * @param    mixed $html          Form fields or a callback function that outputs them
  * @param    string $location     Location where to insert it, eg. content/single
  */
  public function addFields($option_id, $html, $location){

    // split location into chunks
    $keys   = (is_array($location)) ? $location : explode('/', $location);

    // we're modifying a copy of $pages, but here we obtain a reference to it. we move the reference in order to set the values
    $pages  = &$this->pages;
    $levels = 0;

    if(is_callable($html))
      $html = $html();

    while(count($keys) > 0){
      $levels++;

      // get next first key
      $next_key = array_shift($keys);

      // if $pages isn't an array already, make it one
      if(!is_array($pages))
        $pages = array();

      // move the reference deeper
      $pages = &$pages[$next_key];
    }

    // first level, create a new section then
    if($levels < 2)
      $pages[$option_id] = array($option_id => $html);

    // 2nd level, append it to a existing section
    elseif($levels < 3)
      $pages[$option_id] = $html;

    // last level, insert it into a existing option group from within a section
    else
      $pages .= $html;

  }



 /*
  * The nonce field used by the settings. This gets created if needed
  *
  * @since 1.7
  */
  public function getNonce(){

    if(!$this->nonce)
      $this->nonce = wp_create_nonce($this->interface_id);

    return $this->nonce;
  }



  public function checkAjaxNonce(){
    return check_ajax_referer($this->interface_id);
  }



 /*
  * Inserts a tab or tab sub-section
  *
  * @since   1.6
  * @param   string $location     Tab ID / Location
  * @param   string $label        Label
  * @param   string $callback     Form
  * @param   int|bool $position   Priority; if "false" is given, the order is decided call-time of this function
  */
  public function addSection($location, $label, $callback = false, $position = false){

    // tab sub-section?
    if(strpos($location, '/') !== false){
      list($tab, $section) = explode('/', $location);

      if(!isset($this->tabs[$tab]))
        throw new Exception("Cannot create tab section '{$section}' because the '{$tab}' container does not exist!");

      $this->tab_sections[$section] = array(
        'priority' => (int)$position,
        'label'    => $label,
      );

    // no, it's just a tab...
    }else{

      if($position === false)
        $position = count($this->tabs) * 10;

      $this->tabs[$location] = array(
        'priority' => (int)$position,
        'label'    => $label,
        'callback' => $callback,
      );
    }

  }



  /**
   * Initialization js
   * @todo: clean up this mess..
   *
   * @since 1.0
   */
  public function footer(){
    global $current_screen;
    if(!in_array($current_screen->id, array('appearance_page_'.ATOM))) return;
    ?>
    <script type="text/javascript">
    /*<![CDATA[*/


    /*
    function AtomEditorButton(tag){
      tb_show('<?php atom()->te('Insert Atom Element');  ?>', '<?php echo add_query_arg(array('action' => 'insert_editor_element', '_ajax_nonce' => $this->getNonce()), admin_url('admin-ajax.php')); ?>&element='+tag);
    }
    */


    jQuery(document).ready(function($){

      // @todo: organize this in a single function...
      pm.bind("themepreview-load", function(data){


        <?php if(atom()->OptionExists('layout')): ?>

        // dimensions, common variables
        // @todo: replace slider with a column splitter plugin
        var scale_grid_12 = ['|', '20', '|', '60', '|', '|', '|', '140', '|', '|', '|', '220', '|', '|', '|', '300', '|', '|', '|', '380', '|', '|', '|', '460', '|', '|', '|', '540', '|', '|', '|', '620', '|', '|', '|', '700', '|', '|', '|', '780', '|', '|', '|', '860', '|', '|', '|', '940', '|'],
            scale_fluid = ['0', '|', '10', '|', '20', '|', '30', '|', '40', '|','50', '|', '60', '|', '70', '|', '80', '|', '90', '|', '100'],
            page_width = '<?php echo(atom()->options('page_width') == 'fluid' ? 'fluid' : 'fixed'); ?>',
            layout = '<?php echo atom()->options('layout'); ?>',
            unit = '<?php echo(atom()->options('page_width') == 'fluid' ? '%' : 'px'); ?>',
            gs = <?php echo(atom()->options('page_width') == 'fluid' ? '100' : '960'); ?>,
            jstep = <?php echo(atom()->options('page_width') == 'fluid' ? '1' : '10'); ?>,
            jscale = <?php echo(atom()->options('page_width') == 'fluid' ? 'scale_fluid' : 'scale_grid_12'); ?>,
            set_slider = function(){
              $("#dimensions .current").html('');
              layout = $("input[name='layout']").val();
              if(layout != 'c1'){
                var current_dimensions = $('input#<?php echo ATOM; ?>_dimensions_'+page_width+'_'+layout).val();
                $("#dimensions .current").html('<input class="slider" type="hidden" value="'+current_dimensions+'" />');
                $("input.slider").slider({
                  from: 0,
                  to: gs,
                  step: jstep,
                  dimension: unit,
                  scale: jscale,
                  limits: false,
                  onstatechange: function(value){
                    pm({ // live preview
                      target: window.frames["themepreview"],
                      type: 'dimensions',
                      data: {layout: layout, sizes: value, unit: unit, gs: gs}
                    });
                    $('input#<?php echo ATOM; ?>_dimensions_' + page_width + '_' + layout).val(value).trigger('change');
                    return value;
                  }
                });
              }else{
                $("#dimensions .current").html('<label><small><?php esc_js(atom()->t('Nothing to configure in single column mode')); ?></small></label>');
                  pm({ // live preview
                    target: window.frames['themepreview'],
                    type: 'dimensions',
                    data: {layout: layout, sizes: '0', unit: unit, gs: gs}
                  });
              }
            };

        // dimensions (column sizes)
        set_slider();

        // page width options
        $(".atom-block input[name='page_width']").change(function(){
          page_width = $(this).val();
          unit = (page_width == 'fluid') ? '%' : 'px';
          gs = (page_width == 'fluid') ? 100 : 960;
          jstep = (page_width == 'fluid') ? 1 : 10;
          jscale = (page_width == 'fluid') ? scale_fluid : scale_grid_12;

          pm({ // live preview
            target: window.frames['themepreview'],
            type: 'page_width',
            data: page_width
          });

          set_slider();

        });

        // max page width field
        $(".atom-block input[name='page_width_max']").bind('keyup mouseup change',function(e){
          var page_width_max = $(this).val();
          pm({ // live preview
            target: window.frames['themepreview'],
            type: 'page_width_max',
            data: page_width_max
          });
          set_slider();
        });

        <?php endif; ?>

        // allow select type operations on links (nicer selects)
        $('#theme-settings a.select').click(function(){
          var button = $(this),
              option = $(this).parents("div[class*='-selector']").attr('id'),
              selected = $(this).attr('rel');

          button.parent().find('input.select').val(selected);
          button.parent().find('a').removeClass('active');
          button.addClass('active');
          if(option == 'layout') set_slider();
          pm({ // live preview
            target: window.frames['themepreview'],
            type: option,
            data: selected
          });
          return false;
        });

        // hidden input field (used for creating the nicer select inputs above)
        // not to be confused with input[type=select]
        $('#theme-settings input.select').change(function(){
          var data = $(this).val();
          $("#theme-settings a.select[rel='" + data + "']").addClass('active');
        }).change();


        $(document).bind('themepreview-loaded', function(){ set_slider(); }); // fixes slider bug   @todo: re-code slider for Atom

      });


      $(document).trigger('atom_ready');

      <?php Atom::action('admin_jquery_init'); ?>


    });


    /*]]>*/
    </script>
    <?php
  //  endif;
  }



  protected function getLanguageData($type = 'core'){

    if(!is_child_theme() && $type !== 'core') return array();

    $path = ($type !== 'user') ? TEMPLATEPATH : STYLESHEETPATH;

    if(!is_dir($path.'/lang/'))
      return array();

    $langs = get_available_languages($path.'/lang/');

    if(empty($langs))
      return array();

    $data = array();
    foreach($langs as $lang){

      $handle = @fopen($path.'/lang/'.$lang.'.po', 'r');
      $headers = array();

      // parsing headers; stop at the first empty line
      if($handle){
        while(($line = fgets($handle, 4096)) !== false){

          $line = substr(trim($line), 1, -1);
          $col_index = strpos($line, ':');

          if(empty($line))
            break;

          if($col_index === false)
            continue;

          $field = substr($line, 0, $col_index);

          // skip the white space after the colon and remove the \n at the end
          $headers[$field] = substr($line, $col_index + 1, -2);
        }
        fclose($handle);
      }

      if(!empty($headers)){

        $data[$lang][$type] = array(
          'translator' => preg_replace('/(?<!href=")http:\/\//','', make_clickable(strip_tags($headers['Last-Translator']))),
          'language'   => trim(strip_tags($headers['X-Poedit-Language'])),
          'version'    => trim(strip_tags($headers['Project-Id-Version'])),
        );

        // more accurate
        if(function_exists('format_code_lang'))
          $data[$lang][$type]['language'] = format_code_lang($lang);
      }
    }

    return $data;
  }



 /*
  * Creates the WP admin menu entry
  *
  * @since 1.6
  */
  public function addMenu(){

    // appearance menu
    $this->page = add_theme_page(
      $this->menu_title,
      $this->menu_title,
      'edit_theme_options',
      ATOM,
      array($this, 'forms') // function below
    );

    add_thickbox();

    atom()->action('add_menu', $this);
  }



 /*
  * Display a settings page (tab section).
  * If it's called from an ajax request, the script will stop afterwards.
  *
  * @since   1.6
  * @param   string $tab_id    Tab ID (slug) to display; this is ignored on ajax requests
  */
  public function getTab($tab_id = 'welcome'){

    // use the query argument if this is an ajax call
    if(defined('DOING_AJAX') && DOING_AJAX && isset($_GET['section']) && check_ajax_referer('theme_settings_tabs'))
      $tab_id = array_key_exists($_GET['section'], $this->tabs) ? $_GET['section'] : $tab_id;

    // get current options
    $this->options = atom()->options();

    // generate interface from gathered data in $this->pages

    // determine dependency rules first
    foreach($this->controls as $id => $args){
      $rule = '';

      if($args['depends_on'])
        $rule = $args['depends_on'];

      if($args['conflicts_with'])
        $rule = "!{$args['conflicts_with']}";

      if($rule){
        $parts = explode(' ', preg_replace('/\s+/', ' ', $rule));
        $parts = array_filter($parts);
        $this->controls[$id]['rules'] = $rule;
      }
    }

    // generate html
    foreach($this->controls as $option_id => $args){

      extract($args);

      $type_mode = 'default';
      $input_size = $type_mode_extra = false;

      // check if we have an input size
      if(strpos($type, ':') !== false)
        list($type, $input_size) = explode(':', $type);

      // check if we have more type attributes
      if(strpos($type, '/') !== false){
        $type_attr = explode('/', $type);

        $type = array_shift($type_attr);
        $type_mode = array_shift($type_attr);

        if(!empty($type_attr))
          $type_mode_extra = array_shift($type_attr);

      }

      // we're using the type attribute as a class later
      $this->controls[$option_id]['type'] = $type;

      // initialize some variables
      $input = $classes = $output = array();

      // generate input classes
      if(isset($type_mode)){
        $classes[] = $type_mode;

        if($type_mode === 'code')
          $classes[] = 'editor';
      }

      // is the option disabled for the current user ?
      $disabled = in_array($option_id, $this->disabled_options);

      if($disabled){
        $classes[] = 'disabled';
        $title = atom()->t("You don't have sufficient permissions to change this option");
      }

      // generate label attributes, remove empty ones and join remaining elements into a string
      $label_attributes = implode(' ', array_filter(array(
        'for'      => 'for="atom_'.$option_id.'"',
        'class'    => $disabled ? 'class="disabled"' : NULL,         // label can only have this class, for now
        'disabled' => $disabled ? 'disabled="disabled"' : NULL,
        'title'    => isset($title) ? 'title="'.$title.'"' : NULL,
      )));

      // generate input attributes, remove empty ones and join remaining elements into a string
      $input_attributes = implode(' ', array_filter(array(
        'id'          => 'id="atom_'.$option_id.'"',
        'class'       => $classes ? 'class="'.implode(' ', $classes).'"' : NULL,
        'name'        => $disabled ? NULL : 'name="'.$option_id.'"',
        'rules'       => ($rules && !$disabled) ? 'rules="'.$rules.'"' : NULL,
        'disabled'    => $disabled ? 'disabled="disabled"' : NULL,
        'size'        => $input_size ? 'size="'.(int)$input_size.'"' : NULL,
      )));

      // determine input type
      switch($type){

        case 'text':

          switch($type_mode){

            case 'code':
              // textarea with code editor
              $language = $type_mode_extra ? $type_mode_extra : 'html';
              $input[] = '<textarea data-mode="text/'.$language.'" '.$input_attributes.'>'.$this->options[$option_id].'</textarea>';

              if($language === 'html'){
                $notice = (current_user_can('unfiltered_html')) ? atom()->t('You can add HTML code.') : atom()->t('HTML is allowed, but some tags may be filtered out for security reasons.');

                if(!empty($description))
                  $description = $notice.' '.$description;

                else
                  $description = $notice;
              }

            break;

            default:
              // simple text input
              $input[] = '<input type="text" '.$input_attributes.' value="'.$this->options[$option_id].'" />';
            break;
          }


        break;

        // normal checkbox input
        // @note:
        //   Because non-checked input field values are not sent trough $_POST, all options that have checkboxes need to have
        //   <input type="hidden" name="option_name" value="0" />
        //   before the checkbox option. This trick assumes the inputs are always processed sequentially,
        //   so the hidden input value is sent (0) if the checkbox is not checked.
        case 'checkbox':
          $input[] = '<input type="hidden" name="'.$option_id.'" value="0" />';
          $input[] = '<input type="checkbox" '.$input_attributes.' value="1" '.checked((bool)$this->options[$option_id], true, false).' />';

        break;

        // normal select field
        case 'select':
          $input[] = '<select '.$input_attributes.'>';

          // field options
          foreach($values as $value => $value_label)
            $input[] = '<option value="'.$value.'" '.selected($value, $this->options[$option_id], false).'>'.$value_label.'</option>';

          $input[] = '</select>';
        break;
      }

      // format fields
      $input = implode("\n", $input);
      $label = empty($label) ? '' : '<label '.$label_attributes.'>'.$label.'</label>';

      // check if the input need to be inside the label
      if((strpos($label, "%{$option_id}%") !== false) && $label){
        $output[] = str_replace("%{$option_id}%", $input, $label);

      // no, then check if this is not a checkbox
      }elseif($type !== 'checkbox'){
        $output[] = $label;
        $output[] = $input;

      // no to both, then reverse positions
      }else{
        $output[] = $input;
        $output[] = $label;
      }

      // description text, valid for all fields
      if(!empty($description))
        $output[] = '<span class="desc">'.$description.'</span>';

      $context     = explode('/', preg_replace('/\s+/', '', $location));    // location, should always be present
      $page        = array_shift($context);                                 // page (1st level), should always be present
      $section     = empty($context) ? $page : array_shift($context);       // section (2nd level), optional
      $option_base = empty($context) ? $option_id : array_shift($context);  // option group (3rd level), optional

      $this->pages[$page][$section][$option_base][$option_id] = implode("\n", array_filter($output));

    }

    // join option groups
    foreach($this->pages as &$sections)
      foreach($sections as &$option_groups)
        foreach($option_groups as $group_key => &$fields)
          $fields = '<div class="entry with-'.$this->controls[$group_key]['type'].' group-'.$group_key.'">'.implode("\n", $fields).'</div>';



    // plugin / module settings page
    if(!empty($this->tabs[$tab_id]['callback']))
      call_user_func($this->tabs[$tab_id]['callback'], $this->options);

    // internal page -- @todo: make these custom pages
    elseif(method_exists($this, "tab{$tab_id}")){
      $callback = "tab{$tab_id}";
      $this->$callback();

    // custom page
    }elseif(isset($this->pages[$tab_id])){

      $section_count = count($this->pages[$tab_id]);
      foreach($this->pages[$tab_id] as $section_id => $section_contents){

        echo "<div class=\"block block-{$section_id}\">\n<div class=\"inside\">\n";

        if(($section_count > 1) && isset($this->tab_sections[$section_id]))
          echo "<h3 class=\"title\">{$this->tab_sections[$section_id]['label']}</h3>";

        echo "<fieldset>\n".implode("\n", $section_contents)."</fieldset>\n";

        Atom::action("settings_{$tab_id}_{$section_id}", $this->options);

        echo "</div>\n</div>\n";
      }

      Atom::action("settings_{$tab_id}", $this->options);
    }


    if($tab_id !== 'welcome'): ?>
    <div class="save">
      <input type="hidden" name="section" value="<?php echo $tab_id; ?>" />
      <input type="submit" class="button-primary" name="submit" value="<?php atom()->te('Save Changes'); ?>" />
    </div>
    <?php endif;

    // check if this is an ajax request and exit the script
    if(defined('DOING_AJAX') && DOING_AJAX) exit;
  }


  public function tabAdvancedCode(){ ?>

    <div class="clear-block">

    <?php if(current_user_can('edit_themes') && is_child_theme()): // only show this if the user can use the theme editor (same type of operation) ?>
    <hr />
    <div class="clear-block">
     <h2 class="title"><?php atom()->te('User-defined code'); ?></h2>

     <?php
      if(WP_Filesystem() && is_child_theme()):
        global $wp_filesystem;

        if($wp_filesystem->is_readable(atom()->get('user_functions'))){
          $functions = $wp_filesystem->get_contents(atom()->get('user_functions'));
        }else{
          $default_text = atom()->t("Only edit this if you know what you're doing!");
          $functions = "<?php \n// {$default_text}\n\n\n";
        }

     ?>

     <label for="<?php echo ATOM; ?>_functions">
      <small>
        <?php
          atom()->te('The code you add here is included in the front-end on initialization. This is almost the same thing as editing the %1$s file from the theme directory, the difference is that the changes you make here are preserved after theme updates. Read the %2$s for more information.',
            '<code>functions.php</code>',
            '<a href="'.Atom::THEME_DOC_URI.'" target="_blank">'.atom()->t('documentation').'</a>'
          );

          if(is_multisite())
            printf(' <span style="color:#CC0000;">%s</span>',
              atom()->t('This will affect all blogs using the %s theme!', atom()->getThemeName())
            );
        ?>
      </small>
     </label>

     <p>
      <textarea data-mode="application/x-httpd-php" rows="26" cols="60" name="functions" id="<?php echo ATOM; ?>_functions" class="code editor widefat"><?php echo esc_textarea($functions); ?></textarea>
     </p>
     <p><?php atom()->te('Data is saved to %s.', sprintf('<code>%s</code>', atom()->get('user_functions'))); ?></p>

     <?php else: ?>
     <div class="notice"><?php atom()->te('To use this function you must have an active child theme of %s, and give WordPress write permissions to the child theme folder.', atom()->getThemeName()); ?></div>

     <?php endif; ?>

    </div>
     <?php endif; ?>

    </div>
    <?php
  }


  public function tabCSS(){
    ?>
    <!-- tab: user css -->
    <div class="clear-block">

      <div class="notice">
        <label for="<?php echo ATOM; ?>_css">
         <?php atom()->te('Check %s to see existing theme classes and properties, which you can redefine here.', '<a target="_blank" href="'.atom()->getThemeURL().'/css/core.css">css/core.css</a>'); ?>
         <br />
         <?php atom()->te('All URLs must have absolute paths. You can use the %1$s keyword to point to the %2$s.', '<code>{THEME_URL}</code>', '<abbr title="'.atom()->getThemeURL().'">'.atom()->t('parent theme URL').'</abbr>'); ?>
         <?php if(is_child_theme()): ?>
         <br />
         <?php atom()->te('To get the child theme URL use %s.', '<code>{CHILD_THEME_URL}</code>'); ?>
         <?php endif; ?>
         <?php atom()->te('Read the %s to see all available keywords.', '<a href="'.Atom::THEME_DOC_URI.'" target="_blank">'.atom()->t('documentation').'</a>'); ?>
        </label>
      </div>


      <p>
        <textarea data-mode="text/css" rows="38" cols="60" name="css" id="<?php echo ATOM; ?>_css" class="code editor widefat"><?php echo esc_html($this->options['css']); ?></textarea>
      </p>

    </div>
    <!-- /tab: user css -->
    <?php
  }



  public function tabWelcome(){
    ?>
    <!-- tab: welcome -->
    <div class="clear-block">

      <div class="alignleft" style="width: 69%">
        <h3 class="title"><?php atom()->te('General Info'); ?></h3>
        <ul class="ul-disc">
          <li>
            <?php atom()->te('Core (ATOM) version: %s', sprintf('<strong>%s</strong>', Atom::VERSION)); ?>
          </li>
          <li>
            <?php atom()->te('Theme Version: %s', sprintf('<strong>%s</strong>', atom()->getThemeVersion())); ?>

            <?php if(is_child_theme()): ?>
               <?php atom()->te('(Child theme of %s)', atom()->getThemeName()); ?>
            <?php endif; ?>

            <?php if(isset($this->update_info['update_link'])): ?>
              <?php echo $this->update_info['update_link']; ?>
            <?php else: ?>
              <a id="update-check" data-nonce="<?php echo wp_create_nonce('theme_update_check'); ?>" href="#">(<?php atom()->te('Check for update'); ?>)</a>
            <?php endif; ?>
          </li>
        </ul>

        <ul class="ul-disc">
          <li><a href="<?php echo Atom::THEME_DOC_URI; ?>" target="_blank"><strong><?php atom()->te('Theme documentation'); ?></strong></a></li>
          <li><a href="<?php echo atom()->get('theme_uri'); ?>" target="_blank"><?php atom()->te('Project Homepage'); ?></a></li>
        </ul>

        <?php

          $langs = array_merge_recursive($this->getLanguageData('user'), $this->getLanguageData('core'));

          $current_lang = '';
          $locale = get_locale();
          if(!empty($langs[$locale]['core']))
            $current_lang = $langs[$locale]['core'];

          if(!empty($langs[$locale]['user']))
            $current_lang = $langs[$locale]['user'];

        ?>
        <?php if(!empty($langs)): ?>

        <h3 class="title"><?php atom()->te('Available Languages'); ?></h3>

        <ul class="ul-disc">
         <?php
           foreach($langs as $locale => $versions)
             foreach($versions as $type => $lang){
               $active = ($lang !== $current_lang) ? '' : 'style="color:#339900;font-weight:bold;"';
               $nfo = atom()->t('%1$s - Translator: %2$s (%3$s)', $lang['language'], "<strong>{$lang['translator']}</strong>", "{$lang['version']}");
               echo '<li '.$active.'>'.$nfo.'</li>';
             }
         ?>
        </ul>
       <?php endif; ?>

        <?php if(current_user_can('edit_themes')): ?>
        <h3 class="title"><?php atom()->te("Import / export / reset or backup Theme Options"); ?></h3>
        <div class="entry">
          <label for="import_data" class="desc"><?php atom()->te("If you wish to import or export theme settings from another website that's using this theme, you may do so here. Below you can copy the current settings stored as a encoded string, or paste new settings..."); ?></label>

          <?php
            // we could use base 64 encoding for this, but then we get complaints for all those retarded "theme-checking" plugins out there.
            $encoded_settings = chunk_split(bin2hex(serialize($this->options)), 90, "\n");
          ?>
          <textarea spellcheck="false" cols="6" rows="18" class="code toggle-select-all" id="import_data" name="import_data"><?php echo $encoded_settings; ?></textarea>
        </div>
        <div class="clear-block">
          <p class="alignright">
            <input type="submit" class="button-primary" name="reset" value="<?php atom()->te("Reset options to defaults"); ?>" onclick="if(confirm('<?php atom()->te("Reset all theme settings to the default values? Are you sure?"); ?>')) return true; else return false;" />
            <input type="submit" name="import" class="button-primary" value="<?php atom()->te("Import options"); ?>" onclick="if(confirm('<?php atom()->te("Import these settings? Are you sure?"); ?>')) return true; else return false;"  />
          </p>
        </div>
        <?php endif; ?>

      </div>

      <div style="width: 30%;" class="alignright rss-meta-box" data-nonce="<?php echo wp_create_nonce('rss_meta_box'); ?>" data-feed="http://digitalnature.eu/feed/">
        <!-- digitalnature.eu news go here -->
      </div>

    </div>
    <!-- /tab: welcome -->
    <?php
  }


  public function tabDesign(){
    ?>
    <!-- tab: design -->
    <div class="clear-block">

      <?php if($this->options['jquery']): ?>
      <div id="themepreview-wrap" class="resizable-wrapper hide-if-no-js">
        <?php $height = isset($_COOKIE['themepreview-height']) ? 'style="height:'.(int)$_COOKIE['themepreview-height'].'px;"' : ''; ?>
        <iframe id="themepreview" name="themepreview" src="<?php echo add_query_arg('themepreview', 1, home_url()); ?>" class="resizable" <?php echo $height; ?>></iframe>
      </div>

      <div id="atom-design-panel-status">
        <p><?php atom()->te('Loading...'); ?></p>
      </div>
      <?php endif; ?>

      <div class="metabox-holder flow clear-block <?php if($this->options['jquery']): ?>hidden <?php endif; ?>" id="atom-design-panel">

        <?php if(atom()->OptionExists('layout')): ?>

        <div class="postbox wide">
           <h3><span><?php atom()->te("Layout Style (Global)"); ?></span></h3>
           <div class="inside clear-block">
             <div class="layout-selector" id="layout">
               <div class="clear-block">
                 <a href="#" rel="c1" class="select c1" title="<?php atom()->te("One column (No sidebars)"); ?>"></a>
                 <a href="#" rel="c2left" class="select c2left" title="<?php atom()->te("Two columns, left sidebar"); ?>"></a>
                 <a href="#" rel="c2right" class="select c2right" title="<?php atom()->te("Two columns, right sidebar"); ?>"></a>
                 <a href="#" rel="c3" class="select c3" title="<?php atom()->te("Three columns"); ?>"></a>
                 <a href="#" rel="c3left" class="select c3left" title="<?php atom()->te("Three columns, sidebars to the left"); ?>"></a>
                 <a href="#" rel="c3right" class="select c3right" title="<?php atom()->te("Three columns, sidebars to the right"); ?>"></a>
                 <input type="hidden" class="select" name="layout" value="<?php echo $this->options['layout']; ?>" />
               </div>
               <div class="clear-block">
                 <p>
                   <label><small><?php atom()->te("Note that widget visibility options can be used to override this setting for individual pages or posts (you can also select the appropriate template when editing pages, or use the %s custom field)", '<code>layout</code>'); ?></small></label>
                 </p>
               </div>
             </div>

           </div>
        </div>

        <div class="postbox wide">
           <h3><span><?php atom()->te('Column Sizes'); ?></span></h3>
           <div class="inside clear-block" id="dimensions">
              <input type="hidden" id="<?php echo ATOM; ?>_dimensions_fixed_c2left" name="dimensions_fixed_c2left" value="<?php echo $this->options['dimensions_fixed_c2left']; ?>" />
              <input type="hidden" id="<?php echo ATOM; ?>_dimensions_fixed_c2right" name="dimensions_fixed_c2right" value="<?php echo $this->options['dimensions_fixed_c2right']; ?>" />
              <input type="hidden" id="<?php echo ATOM; ?>_dimensions_fixed_c3" name="dimensions_fixed_c3" value="<?php echo $this->options['dimensions_fixed_c3']; ?>" />
              <input type="hidden" id="<?php echo ATOM; ?>_dimensions_fixed_c3left" name="dimensions_fixed_c3left" value="<?php echo $this->options['dimensions_fixed_c3left']; ?>" />
              <input type="hidden" id="<?php echo ATOM; ?>_dimensions_fixed_c3right" name="dimensions_fixed_c3right" value="<?php echo $this->options['dimensions_fixed_c3right']; ?>" />

              <input type="hidden" id="<?php echo ATOM; ?>_dimensions_fluid_c2left" name="dimensions_fluid_c2left" value="<?php echo $this->options['dimensions_fluid_c2left']; ?>" />
              <input type="hidden" id="<?php echo ATOM; ?>_dimensions_fluid_c2right" name="dimensions_fluid_c2right" value="<?php echo $this->options['dimensions_fluid_c2right']; ?>" />
              <input type="hidden" id="<?php echo ATOM; ?>_dimensions_fluid_c3" name="dimensions_fluid_c3" value="<?php echo $this->options['dimensions_fluid_c3']; ?>" />
              <input type="hidden" id="<?php echo ATOM; ?>_dimensions_fluid_c3left" name="dimensions_fluid_c3left" value="<?php echo $this->options['dimensions_fluid_c3left']; ?>" />
              <input type="hidden" id="<?php echo ATOM; ?>_dimensions_fluid_c3right" name="dimensions_fluid_c3right" value="<?php echo $this->options['dimensions_fluid_c3right']; ?>" />

              <div class="current"><?php // the slider input is added with jquery ?></div>
           </div>
        </div>

        <?php endif; ?>

        <div class="block alignleft">

          <?php if(atom()->OptionExists('layout')): ?>

          <div class="postbox">
             <h3><span><?php atom()->te("Page Width"); ?></span></h3>
             <div class="inside clear-block">

               <div class="entry">
                 <label for="<?php echo ATOM; ?>_page_width_fixed">
                   <input name="page_width" id="<?php echo ATOM; ?>_page_width_fixed" type="radio" class="radio" value="fixed" <?php checked('fixed', $this->options['page_width']); ?> />
                   <?php atom()->te('Fixed (%s)', '<a href="http://960.gs">960gs</a>'); ?>
                 </label>
               </div>

               <div class="entry">
                 <label for="<?php echo ATOM; ?>_page_width_fluid">
                   <input name="page_width" id="<?php echo ATOM; ?>_page_width_fluid" type="radio" class="radio" value="fluid" <?php checked('fluid', $this->options['page_width']); ?> />
                   <?php atom()->te('Fluid, but not more than %s pixels', '<label><input size="4" rules="page_width:fluid" name="page_width_max" id="'.ATOM.'_page_width_max" type="text" value="'.$this->options['page_width_max'].'" /></label>'); ?>
                 </label>
               </div>

               <div class="entry">
                 <label class="disabled" title="Not yet implemented">
                   <input disabled="disabled" type="radio" class="radio disabled" />
                   <?php atom()->te("Semi-Fluid (Fixed Sidebars)"); ?>
                 </label>
               </div>

             </div>
          </div>

          <?php endif; ?>

          <?php if(atom()->OptionExists('logo')): ?>
          <div class="postbox upload-block">
             <h3><span><?php atom()->te("Site Title (Logo)"); ?></span></h3>
             <div class="inside clear-block">

              <img id="image-logo" class="image-upload" src="<?php echo ($logo = $this->options['logo']) ? $logo : atom()->getThemeURL().'/images/admin/x.gif'; ?>" title="<?php atom()->te('Current logo image'); ?>" />
              <a class="button reset_upload alignright <?php if(!$logo): ?>hidden<?php endif; ?>" id="<?php echo ATOM; ?>_reset_logo"><?php atom()->te('Remove'); ?></a>
              <a class="button upload alignright" id="<?php echo ATOM; ?>_logo" data-nonce="<?php echo wp_create_nonce('atom_upload'); ?>"><?php $logo ? atom()->te('Change Image') : atom()->te('Upload Image'); ?></a>
              <input type="hidden" name="logo" data-title="<?php echo apply_filters('atom_logo_title', get_bloginfo('name')); ?>" value="<?php echo $logo; ?>" />
             </div>
          </div>
          <?php endif; ?>
        </div>

        <div class="block alignright">

          <?php if(atom()->OptionExists('color_scheme')): ?>
          <div class="postbox">
             <h3><span><?php atom()->te('Styles &amp; colors'); ?></span></h3>
             <div class="inside clear-block">

               <div class="clear-block colorscheme-selector" id="color-scheme">
                 <?php
                  foreach(atom()->getStyles() as $scheme):
                   echo '<a href="#" rel="'.$scheme['id'].'" class="select" style="background-color:'.$scheme['color'].'" title="'.atom()->t('%1$s by %2$s', $scheme['name'], $scheme['author']).'">'.$scheme['name'].'</a>';

                  endforeach;
                 ?>
                 <a href="#" rel="" class="select no-style" title="<?php atom()->te('Disabled'); ?> (<?php atom()->te('use custom styles'); ?>)"><?php atom()->te('Blank'); ?></a>
                 <input type="hidden" class="select" name="color_scheme" value="<?php echo $this->options['color_scheme']; ?>" />
               </div>
             </div>
          </div>
          <?php endif; ?>

          <?php if(atom()->OptionExists('background_image') || atom()->OptionExists('background_color')): ?>
          <div class="postbox upload-block">
             <h3><span><?php atom()->te('Page Background'); ?></span></h3>
             <div class="inside clear-block">

             <?php if(atom()->OptionExists('background_image')): ?>
              <img id="image-background_image" class="image-upload" src="<?php echo ($background = $this->options['background_image']) ? $background : atom()->getThemeURL().'/images/admin/x.gif'; ?>" title="<?php atom()->te("Current background image"); ?>" />

               <a class="button reset_upload alignright <?php if(!$background): ?>hidden<?php endif; ?>" id="<?php echo ATOM; ?>_reset_background"><?php atom()->te('Remove'); ?></a>
               <a class="button upload alignright" id="<?php echo ATOM; ?>_background" data-nonce="<?php echo wp_create_nonce('atom_upload'); ?>"><?php $background ? atom()->te('Change Image') : atom()->te('Upload Image'); ?></a>
               <input type="hidden" name="background_image" value="<?php echo $background; ?>" data-selector="<?php echo esc_attr($this->options['background_image_selector']); ?>" />
             <?php endif; ?>

             <?php if(atom()->OptionExists('background_color')): ?>
              <input name="background_color" class="color-selector" data-selector="<?php echo esc_attr($this->options['background_color_selector']); ?>" type="hidden" value="<?php echo esc_html($this->options['background_color']); ?>" />
             <?php endif; ?>

             </div>
          </div>
          <?php endif; ?>

          <?php if(atom()->OptionExists('background_gradient')): ?>
          <div class="postbox upload-block">
             <h3><span><?php atom()->te("Header Background"); ?></span></h3>
             <div class="inside clear-block">

             <?php if(atom()->OptionExists('background_gradient')): ?>
               <div class="alignleft" id="<?php echo ATOM; ?>_background_gradient">
                   <input name="background_gradient" class="color-selector" type="hidden" value="<?php echo esc_html($this->options['background_gradient']); ?>" />
               </div>
             <?php endif; ?>

             </div>
          </div>
          <?php endif; ?>

          <?php if(atom()->OptionExists('favicon')): ?>
          <div class="postbox upload-block">
             <h3><span><?php atom()->te("Favicon"); ?></span></h3>
             <div class="inside clear-block">

              <img id="image-favicon" class="alignleft image-upload" src="<?php echo ($favicon = $this->options['favicon']) ? $favicon : atom()->getThemeURL().'/images/admin/x.gif'; ?>" title="<?php atom()->te("Current favicon"); ?>" width="16" height="16" />

              <a class="button reset_upload alignright <?php if(!$favicon): ?>hidden<?php endif; ?>" id="<?php echo ATOM; ?>_reset_favicon"><?php atom()->te('Remove'); ?></a>
              <a class="button upload alignright" id="<?php echo ATOM; ?>_favicon" data-nonce="<?php echo wp_create_nonce('atom_upload'); ?>"><?php $favicon ? atom()->te('Change Image') : atom()->te('Upload Image'); ?></a>
              <input type="hidden" name="favicon" value="<?php echo $favicon; ?>" />
             </div>
          </div>
          <?php endif; ?>

        </div>

      </div>

    </div>
    <!-- tab: design -->
    <?php
  }


  public function tabModules(){

    $modules = atom()->getModules();

    // sort by 'priority' tag
    uasort($modules, create_function('$a, $b', 'return ((int)$a["priority"] < (int)$b["priority"]);'));

    $active_modules = (array)get_option(ATOM.'-atom-mods');
    ?>
    <!-- tab: modules -->
    <div class="clear-block">


      <div class="metabox-holder clear-block">
        <div class="notice"><?php atom()->te('Modules act like plugins that can further extend %s. Keep in mind that by disabling a module your are removing its options as well (if it has any)', atom()->getThemeName()); ?></div>
        <br />
        <input type="hidden" name="mods" value="0" />

        <?php if(!empty($modules)): ?>
        <table class="widefat">
          <thead>
            <tr>
            <th class="check-column"><input type="checkbox" /></th>
            <th class="title"><?php atom()->te('Title'); ?></th>
            <th class="title"><?php atom()->te('Type'); ?></th>
            <th class="author"><?php atom()->te('Author'); ?></th>
            <th class="desc"><?php atom()->te('Description'); ?></th>
            </tr>
          </thead>

          <tbody class="plugins">

            <?php foreach($modules as $key => $module):  ?>

            <tr valign="top" class="<?php echo in_array($key, $active_modules) ? 'active' : 'inactive'; ?>">

              <th class="manage-column column-cb check-column">
                <input type="checkbox" name="mods[]" id="<?php echo sanitize_html_class($module['name']); ?>" value="<?php echo $key; ?>" <?php checked(in_array($key, $active_modules)); ?> />
              </th>

              <td class="title">
                <label for="<?php echo sanitize_html_class($module['name']); ?>"><strong><?php echo $module['name']; ?></strong></label>
              </td>

              <td class="title">
                <label for="<?php echo sanitize_title($module['type']); ?>"><?php echo $module['type']; ?></label>
              </td>

              <th class="author">
                <?php echo '<a href="'.$module['url'].'">'.$module['author'].'</a>'; ?>
              </th>

              <td class="desc" width="55%">
                <p><?php echo $module['description']; ?></p>
              </td>

            </tr>

            <?php endforeach; ?>

          </tbody>


        </table>
        <?php else: ?>
        <p><?php atom()->te('Currently there are no modules installed.'); ?></p>
        <?php endif; ?>
      </div>

    </div>
    <!-- /tab: modules -->
    <?php
  }



 /*
  * Get the new version update details
  *
  * @since   2.0
  * @return  bool  True if we have an update available, false otherwise
  */
  protected function getUpdateInfo(){
    static $themes_update;

    // make sure we get the parent theme name
    $theme = get_theme_data(TEMPLATEPATH.'/style.css');
    $theme = get_theme($theme['Name']); // need to test this

    if(!isset($themes_update))
      $themes_update = get_site_transient('update_themes');

    if(is_object($theme) && isset($theme->stylesheet))
      $stylesheet = $theme->stylesheet;

    elseif(is_array($theme) && isset($theme['Stylesheet']))
      $stylesheet = $theme['Stylesheet'];

    else
      return false;

    if(isset($themes_update->response[$stylesheet])){
      $update = $themes_update->response[$stylesheet];

      $this->update_info['theme_name'] = is_object($theme) ? $theme->name : (is_array($theme) ? $theme['Name'] : '');

      if(current_user_can('update_themes') || !empty($update->package)){
        $this->update_info['update_url']  = wp_nonce_url('update.php?action=upgrade-theme&amp;theme='.urlencode($stylesheet), 'upgrade-theme_'.$stylesheet);
        $this->update_info['update_link'] = "(<a href=\"{$update_details['update_url']}\">".atom()->t('Update')."</a>)";
      }

      $this->update_info['new_version'] = $update['new_version'];
      $this->update_info['details_url'] = add_query_arg(array('TB_iframe' => 'true', 'width' => 980, 'height' => 600), $update['url']);

      return true;
    }

    return false;
  }



 /*
  * The theme settings pages
  *
  * @since 1.0
  */
  public function forms(){

    // only allow users who can at least edit_theme_options themes to view these pages
    if(!current_user_can('edit_theme_options'))
      wp_die(atom()->t('You are not authorised to perform this operation.'));

    // sort tabs by priority
    uasort($this->tabs, create_function('$a,$b', 'return $a["priority"] > $b["priority"];'));

    $active_tab = 'welcome';
    $theme = ATOM;

    // get last accessed tab
    if(isset($_GET['section']) && array_key_exists($_GET['section'], $this->tabs))
      $active_tab = esc_attr($_GET['section']);

    elseif(isset($_COOKIE["{$theme}-settings"]) && array_key_exists($_COOKIE["{$theme}-settings"], $this->tabs))
      $active_tab = esc_attr(strip_tags($_COOKIE["{$theme}-settings"]));

    $errors = atom()->getContextArgs('settings_update_errors', array(
      '1' => atom()->t('Import failed. Invalid settings. '),
      '2' => atom()->t('Settings saved, but failed to update user functions. Is your child theme directory writable?'),
    ));


    // check if we have an update (not live, just a quick look inside transients)
    $this->getUpdateInfo();

    ?>
    <div id="theme-settings" class="atom-block wrap clear-block">
      <?php screen_icon('themes'); ?><h2><?php echo get_admin_page_title(); ?></h2>
      <form id="theme-settings-form" action="<?php echo admin_url('admin-post.php?action='.$this->interface_id.'_update'); ?>" method="post" enctype="multipart/form-data">

        <?php if(isset($_GET['updated'])): // just updated? ?>
        <div class="updated fade below-h2">
          <p><?php atom()->te('Settings saved.'); ?> <a href="<?php echo home_url('/'); ?>"><?php atom()->te('View site'); ?></a></p>
        </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])):   // error? ?>
        <div class="error fade below-h2">
          <p><?php echo $errors[(int)$_GET['error']]; ?></p>
        </div>
        <?php endif; ?>

        <?php if(isset($this->update_info['new_version'])): // new theme version? ?>
        <div class="updated fade below-h2">
          <p>
           <?php
            if(!isset($this->update_info['update_url']))
              atom()->te('<strong>There is a new version of %1$s available</strong>. <a href="%2$s" class="thickbox" title="%1$s">View %1$s %3$s details</a> or notify the site administrator.', $this->update_info['theme_name'], $this->update_info['details_url'], $this->update_info['new_version']);
            else
              atom()->te('<strong>There is a new version of %1$s available</strong>. <a href="%2$s" class="thickbox" title="%1$s">View %1$s %3$s details</a> or <a href="%4$s" >upgrade automatically</a>.', $this->update_info['theme_name'], $this->update_info['details_url'], $this->update_info['new_version'], $this->update_info['update_url']);
           ?>
          </p>
        </div>
        <?php endif; ?>

        <!-- theme settings -->
        <div class="atom-tabs" data-nonce="<?php echo wp_create_nonce('theme_settings_tabs'); ?>">

          <!-- navi -->
          <ul class="nav clear-block">
            <?php foreach($this->tabs as $key => $tab): ?>
            <li class="<?php echo $key; if($active_tab === $key) echo ' active'; ?>">
              <a href="<?php echo add_query_arg('section', $key, admin_url("themes.php?page={$theme}")); ?>"><?php echo $tab['label']; ?></a>
            </li>
            <?php endforeach; ?>
          </ul>
          <!-- /navi -->

          <!-- tab sections -->
          <div class="tab-content clear-block">
            <?php $this->getTab($active_tab); ?>
          </div>
          <!-- /tab sections -->

        </div>
        <!-- /theme settings -->

        <?php wp_nonce_field($this->interface_id); ?>

      </form>

    </div>
    <?php
  }



 /*
  * Setting validation / save callback
  *
  * @since 1.0
  */
  public function saveOptions(){
    global $wp_filesystem;

    // check permissions
    if(!check_admin_referer($this->interface_id) || !current_user_can('edit_theme_options'))
      wp_die(atom()->t('You are not authorised to perform this operation.'));

    $error   = 0;
    $options = atom()->options();
    $_POST   = stripslashes_deep($_POST);  // wp slashes everything

    // update submitted options, if they are not in the "disabled options" list
    foreach(atom()->getDefaultOptions() as $key => $value)
      if(isset($_POST[$key]) && !in_array($key, $this->disabled_options))
        $options[$key] = $this->sanitizeOption($_POST[$key]);

    atom()->setOptions($options);

    // more sensitive actions; only super admins should have the 'edit_themes' capability...
    if(current_user_can('edit_themes')){

      // user-defined functions
      if(is_child_theme() && isset($_POST['functions']))
        $error = (WP_Filesystem() && $wp_filesystem->put_contents(atom()->get('user_functions'), $_POST['functions'], FS_CHMOD_FILE)) ? false : 2;

      // reset settings?
      if(isset($_POST['reset']))
        atom()->reset();

      // import existing settings?
      if(isset($_POST['import'])){

        $imported_options = unserialize(pack('H*', str_replace(array("\r", "\r\n", "\n"), '', trim($_POST['import_data']))));

        if(is_array($imported_options))
          atom()->setOptions($imported_options);

        else
          $error = 1;
      }

      // set active modules; this user should see this page, so we're assuming all modules have been deactivated if there's no 'mods' field in $_POST
      if(isset($_POST['mods'])){
        $modules = is_array($_POST['mods']) ? $_POST['mods'] : array();
        update_option(ATOM.'-atom-mods', $modules);
      }

    }

    // modules might want to do their own checks
    if(!$error)
      $error = apply_filters('atom_save_options', $error, $options);

    $url = 'themes.php?page='.ATOM.'&'.($error ?  "error={$error}" : "updated=true");

    if(isset($_POST['section']))
      $url .= '&section='.sanitize_key($_POST['section']);

    wp_cache_flush();

    // stupid WP forces us to redirect even when we have errors that need to be displayed...
    wp_redirect(admin_url($url));

    exit;
  }



 /*
  * Recursively sanitizes theme options (user input)
  *
  * @since   1.3
  * @param   mixed $option  Option value or an array of option values
  * @return  mixed          Sanitized value
  */
  protected static function sanitizeOption($option){

    // could be replaced with array_walk_recursive() in PHP > 5
    if(is_array($option)){

      foreach($option as $key => $value)
        $option[$key] = self::sanitizeOption($value);

      return $option;
    }

    // treat these as boolean true
    elseif(in_array(strtolower($option), array('on', 'yes', 'true', 'enabled'), true))
      return true;

    // treat these as boolean false
    elseif(in_array(strtolower($option), array('off', 'no', 'false', 'disabled'), true))
      return false;

    // only sanitize html if needed
    // @note: wp_filter_post_kses expects slashed content (slashes should be removed before calling this function)
    return current_user_can('unfiltered_html') ? (string)$option : stripslashes(wp_filter_post_kses(addslashes((string)$option)));
  }



 /*
  * Update check (ajax)
  *
  * @since 1.4
  */
  public function forceUpdateCheck(){

    check_ajax_referer('theme_update_check');
    set_site_transient('update_themes', null);
    wp_update_themes();

    $this->getUpdateInfo();

    if(isset($this->update_info['update_url'])): ?>
      <a style="color:red;" href="<?php echo $this->update_info['update_url']; ?>">(<?php atom()->te('%s is available', $this->update_info['new_version']); ?>)</a>
    <?php else: ?>
      <span style="color:green;">(<?php atom()->te('You have the latest version'); ?>)</span>
    <?php endif;

    exit;
  }



 /*
  * Handle PHP uploads in WordPress, sanitizes file names, checks extensions for mime type,
  * and moves the file to the appropriate directory within the appropriate directory.
  *
  * Replaces wp_handle_upload so we can use the child theme directory for storage
  *
  * @since 2.0
  */
  public static function handleUpload(&$file, $mimes = array('jpg|jpeg|jpe' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'ico' => 'image/x-icon')){
    global $wp_filesystem;

    // Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
    $upload_errors = array(
       1  => __('The uploaded file exceeds the upload_max_filesize directive in php.ini.'),
       2  => __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.'),
       3  => __('The uploaded file was only partially uploaded.'),
       4  => __('No file was uploaded.'),
       6  => __('Missing a temporary folder.'),
       7  => __('Failed to write file to disk.'),
       8  => __('File upload stopped by extension.'),
    );

    // A successful upload will pass this test. It makes no sense to override this one.
    if($file['error'] > 0)
      return new WP_Error('upload-error', $upload_errors[$file['error']]);

    if(!($file['size'] > 0)){
      if(is_multisite())
        $error_msg = __('File is empty. Please upload something more substantial.');

      else
        $error_msg = __('File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.');

      return new WP_Error('empty-file', $error_msg);
    }

    // A properly uploaded file will pass this test. There should be no reason to override this one.
    if(!@is_uploaded_file($file['tmp_name']))
      return new WP_Error('failed-test', __('Specified file failed upload test.'));

    // A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.

    $wp_filetype = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $mimes);
    extract($wp_filetype);

    // Check to see if wp_check_filetype_and_ext() determined the filename was incorrect
    if($proper_filename)
      $file['name'] = $proper_filename;

    if((!$type || !$ext) && !current_user_can('unfiltered_upload'))
      return new WP_Error('no-access', __('Sorry, this file type is not permitted for security reasons.'));

    if(!$ext)
      $ext = ltrim(strrchr($file['name'], '.'), '.');

    if(!$type)
      $type = $file['type'];


    $child_theme_uploads = false;

    if(is_child_theme())
      $child_theme_uploads = is_writable(STYLESHEETPATH.'/uploads') || (WP_Filesystem() && $wp_filesystem->mkdir(STYLESHEETPATH.'/uploads', FS_CHMOD_DIR));

    if($child_theme_uploads)
      $uploads = array(
        'basedir' => STYLESHEETPATH.'/uploads',
        'baseurl' => atom()->get('child_theme_url').'/uploads',
        'error'   => false,
      );

    else
      $uploads = wp_upload_dir();

    // A writable uploads dir will pass this test. Again, there's no point overriding this one.
    if(!($uploads && $uploads['error'] === false))
      return new WP_Error('invalid-path', $uploads['error']);

    $filename = wp_unique_filename($uploads['basedir'], $file['name']);

    // Move the file to the uploads dir
    $new_file = "{$uploads['basedir']}/{$filename}";
    if(@move_uploaded_file($file['tmp_name'], $new_file) === false)
      return new WP_Error('move-failed', sprintf(__('The uploaded file could not be moved to %s.'), $uploads['basedir']));

    // Set correct file permissions
    $stat = stat(dirname($new_file));
    @chmod($new_file, $stat['mode'] & 0000666);

    // Compute the URL
    $url = "{$uploads['baseurl']}/{$filename}";

    if(is_multisite())
      delete_transient('dirsize_cache');

    return array(
      'file' => $new_file,
      'url'  => $url,
      'type' => $type,
    );
  }



  /**
   * Image upload processing (AJAX)
   * needed for logo, background etc. settings
   *
   * @since 1.0
   *
   * @todo: add ability to the edit the image (wp crop/resize)
   */
  public function processUpload(){

    check_ajax_referer('atom_upload');

    if(!current_user_can('upload_files'))
      wp_die(atom()->t('You are not authorised to perform this operation.'));

    $error = '';
    $url = '';
    $option_name = esc_attr($_POST['option']);

    $uploaded_file = $this->handleUpload($_FILES[$option_name]);
    if(is_wp_error($uploaded_file))
      $error = atom()->t('Upload Error: %s', $uploaded_file->get_error_message());

    else
     $url = $uploaded_file['url'];

    echo json_encode(array('error' => $error, 'url' => $url));
    exit;
  }



  /**
   * Latest News RSS Feed from digitalnature.eu (AJAX)
   *
   * @since 1.0
   */
  public function rssMetaBox(){

    check_ajax_referer('rss_meta_box');

    $feed       = isset($_GET['feed']) ? esc_attr($_GET['feed']) : '';
    $site       = pathinfo($feed);
    $site       = $site['dirname'];
    $item_count = empty($_GET['items']) ? 5 : (int)$_GET['items'];

    echo '<h3 class="title">'.atom()->t('Latest News from %s', '<a href="'.$site.'">'.str_replace('http://', '', $site).'</a>').'</h3>';

    wp_widget_rss_output($feed, array(
      'show_author'  => 0,
      'show_date'    => 1,
      'show_summary' => 1,
      'items'        => $item_count,
    ));
    exit;
  }



 /*
  * Helper method, provides a set of form fields for user role selection.
  * Use atom_strict_visibility_check($instance) to verify the setting
  *
  * @since    2.0
  * @param    array $instance      Array with checked fields
  * @param    string $field_id     Input field base
  * @param    string $field_name   Input field name base
  * @param    array $args          Options
  */
  public static function UserRoleFields($instance, $field_id, $field_name = false, $args = array()){

    if(!$field_name)
      $field_name = $field_id;

    $args = wp_parse_args($args, array(
      'include_visitor' => true,
      'disabled_roles'  => array(),
    ));

    extract($args);

    if($include_visitor): ?>
      <div class="entry">
        <input type="hidden" name="<?php echo $field_name; ?>[user-visitor]" value="0" />
        <input type="checkbox" value="1" <?php checked(!empty($instance['user-visitor'])); ?> id="<?php echo $field_id; ?>[user-visitor]" name="<?php echo $field_name; ?>[user-visitor]" />
        <label for="<?php echo $field_id; ?>[user-visitor]"><?php atom()->te('Unregistered user (Visitor)'); ?></label>
      </div>
      <?php
    endif;

    $roles = new WP_Roles();

    foreach($roles->get_names() as $role => $label):
      if(in_array($role, $disabled_roles)) continue;
      ?>
      <div class="entry">
        <input type="hidden" name="<?php echo $field_name; ?>[role-<?php echo $role; ?>]" value="0" />
        <input type="checkbox" value="1" <?php checked(!empty($instance["role-{$role}"])); ?> id="<?php echo $field_id; ?>[role-<?php echo $role; ?>]" name="<?php echo $field_name; ?>[role-<?php echo $role; ?>]" />
        <label for="<?php echo $field_id; ?>[role-<?php echo $role; ?>]"><?php echo translate_user_role($label); ?></label>
      </div>
    <?php endforeach; ?>
    <?php
  }



 /*
  * Helper method, provides a set of form fields for all available pages
  * Use atom_strict_visibility_check($instance) to verify the setting
  *
  * @since   2.0
  * @param   array $instance      Array with checked fields
  * @param   string $field_id     Input field base
  * @param   string $field_name   Input field name base
  * @param   array $args          Options
  */
  public static function PageFields($instance, $field_id, $field_name = false, $args = array()){

    $args = wp_parse_args($args, array(
      'include_home'     => true,
      'include_search'   => true,
      'include_author'   => true,
      'include_date'     => true,
      'include_tag'      => true,
      'include_category' => true,
      'include_single'   => true,
      'include_tax'      => true,
      'include_page'     => true,
      'page_limit'       => 10,
    ));

    extract($args);

    if(!$field_name)
      $field_name = $field_id;

    $wp_page_types = array(
      'home'     => atom()->t('Blog'),
      'search'   => atom()->t('Search Results'),
      'author'   => atom()->t('Author Archives'),
      'date'     => atom()->t('Date-based Archives'),
      'tag'      => atom()->t('Tag Archives'),
      'category' => atom()->t('Category Archives'),
    );

    // generic wp pages
    foreach($wp_page_types as $key => $label){
      if(!$args["include_{$key}"]) continue;
      ?>
      <div class="entry">
        <input type="hidden" name="<?php echo $field_name; ?>[page-<?php echo $key; ?>]" value="0" />
        <input type="checkbox" value="1" <?php checked(!empty($instance["page-{$key}"])); ?> id="<?php echo $field_id; ?>[page-<?php echo $key; ?>]" name="<?php echo $field_name; ?>[page-<?php echo $key; ?>]" />
        <label for="<?php echo $field_id; ?>[page-<?php echo $key; ?>]"><?php echo $label; ?></label>
      </div>
      <?php
    }

    // singular posts, other than the 'page' post type
    if($include_single){
      foreach(get_post_types(array('public' => true)) as $post_type){
        $object = get_post_type_object($post_type);
        if(empty($object->labels->name) || $post_type === 'page') continue; // we handle pages separately
        ?>
        <div class="entry">
          <input type="hidden" name="<?php echo $field_name; ?>[page-singular-<?php echo $post_type; ?>]" value="0" />
          <input type="checkbox" value="1" <?php checked(!empty($instance["page-singular-{$post_type}"])); ?> id="<?php echo $field_id; ?>[page-singular-<?php echo $post_type; ?>]" name="<?php echo $field_name; ?>[page-singular-<?php echo $post_type; ?>]" />
          <label for="<?php echo $field_id; ?>[page-singular-<?php echo $post_type; ?>]"><?php atom()->te('Single: %s', $object->labels->name); ?></label>
        </div>
        <?php
      }
    }

    // custom taxonomies
    if($include_tax){
      foreach(get_taxonomies(array('public' => true, '_builtin' => false)) as $taxonomy){
        $object = get_taxonomy($taxonomy);
        if(empty($object->labels->name)) continue; ?>
        <div class="entry">
          <input type="hidden" name="<?php echo $field_name; ?>[page-tax-<?php echo $taxonomy; ?>]" value="0" />
          <input type="checkbox" value="1" <?php checked(!empty($instance["page-tax-{$taxonomy}"])); ?> id="<?php echo $field_id; ?>[page-tax-<?php echo $taxonomy; ?>]" name="<?php echo $field_name; ?>[page-tax-<?php echo $taxonomy; ?>]" />
          <label for="<?php echo $field_id; ?>[page-tax-<?php echo $taxonomy; ?>]"><?php atom()->te('Tax Archive: %s', $object->labels->name); ?></label>
        </div>
        <?php
      }
    }

    // individual pages (posts)
    if($include_page && ($pages = get_pages())){

      $checked = array();
      foreach($pages as $page)
        if(!empty($instance["page-{$page->ID}"]))
          $checked[] = $page->ID;

      $walker = new AtomWalkerPageCheckboxes();

      $walker->walk($pages, 0, array(
        'checked'    => $checked,
        'field_id'   => $field_id,
        'field_name' => $field_name,
      ));

    }
  }
}





/*
 * Generate a list of pages formatted as checkbox elements.
 *
 * @since  2.1.0
 * @uses   Walker
 */
class AtomWalkerPageCheckboxes extends Walker{

  public
    $tree_type = 'page',
    $db_fields = array('parent' => 'post_parent', 'id' => 'ID');

  public function start_el(&$output, $page, $depth, $args){
    extract($args);
    ?>

    <div class="entry level-<?php echo $depth; ?>">
      <input type="hidden" name="<?php echo $field_name; ?>[page-<?php echo $page->ID; ?>]" value="0">
      <input type="checkbox" <?php checked(in_array($page->ID, $checked)) ?> id="<?php echo $field_id; ?>[page-<?php echo $page->ID; ?>]" name="<?php echo $field_name; ?>[page-<?php echo $page->ID; ?>]" value="1" />
      <label for="<?php echo $field_id; ?>[page-<?php echo $page->ID; ?>]">
        <?php atom()->te('Page: %s', '<a href="'.get_permalink($page).'" target="_blank"><strong title="'.$page->post_title.'">'.((strlen($page->post_title) > 16) ? substr($page->post_title, 0, 16)."..." : $page->post_title).'</strong></a>'); ?>
      </label>
    </div>
    <?php
  }
}

