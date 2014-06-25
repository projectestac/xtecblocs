<?php
/*
 * Widgets developed using Atom.
 *
 * Read the documentation for more info: http://digitalnature.eu/docs/
 *
 * @revised   February 4, 2012
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */



/*
 * Atom Widget class.
 * Makes widget development a little easier :)
 *
 * @since 1.0
 */
abstract class AtomWidget extends WP_Widget{

  const
    USER_ICON_DIR = 'widget-icons';    // the folder name which stores user-uploaded widget icons

  private
    $defaults,          // stores default options for this widget (not to be confused with the current widget instance options)
    $templates,         // widget template blocks
    $icons,             // caches user uploaded icon list
    $safe_name,         // widget class name with underscores instead of hyphens (used by filter tags and cache IDs)
    $ajax_control;      // ajax request name for the "show more" control; optional argument for set()



 /*
  * Register the widget
  *
  * @since   2.0
  * @param   array $args
  */
  final public function set($args){

    extract($args);

    // id and title are required
    if(!isset($id) || !isset($title))
      throw new Exception('Missing widget ID or title');

    $control_options = isset($width) ? array('width' => $width) : array();
    $widget_options = array('classname' => $id);

    if(isset($description))
      $widget_options['description'] = $description;

    // WP_Widget() call, sets up id_base etc.
    parent::__construct("atom-{$id}", $title, $widget_options, $control_options);

    // generate safe name, eg. "atom_name_of_the_widget"
    $this->safe_name = str_replace('-', '_', $this->id_base);

    // default options
    if(isset($defaults))
      $this->defaults = $defaults;

    // entry templates, some widgets have this feature
    if(isset($templates)){

      $is_assoc = is_array($templates) && (array_keys($templates) !== range(0, count($templates) - 1));

      // multiple templates
      if($is_assoc)
        foreach((array)$templates as $name => $template)
          $this->templates[$name] = is_array($template) ? implode("\n", $template) : $template;

      // single template
      else
        $this->templates['default'] = is_array($templates) ? implode("\n", $templates) : $templates;

      $this->templates = atom()->getContextArgs("widget_{$this->safe_name}_templates", $this->templates);
    }

    // default ajax show-more-content control
    if(isset($ajax_control)){
      $this->ajax_control = $ajax_control;
      atom()->add('requests', array($this, 'ajaxControl'));
    }

  }



 /*
  * Retrieve default widget options.
  * It will call getContextArgs() and check if user-defined options exist, and merge them.
  *
  * @since    1.7
  * @return   array   Options
  */
  final public function getDefaults(){
    return atom()->getContextArgs("widget_{$this->safe_name}_defaults", $this->defaults);
  }



 /*
  * Parses widget options by appending defaults to the current options if they are missing.
  *
  * @since    2.0
  * @param    array   Current options (reference)
  */
  final public function parseOptions(&$instance){
    $instance = atom()->getContextArgs("widget_{$this->safe_name}_options", array_merge($this->getDefaults(), $instance));
  }



  /**
   * Get all templates -- @todo
   *
   * @since 1.7
   * @return array
   */
  final public function getTemplates(){
    return $this->templates;
  }



 /*
  * Default widget ajax request handler (for the "show more" control).
  * This will run if an 'ajax_control' argument is given within set().
  * To override it we can define our own handler by implementing this method in our widget.
  *
  * Note that if this method is used, a method that pulls out the content must exist and have the camelCased name of the ajax request
  *
  * @since 2.0
  */
  public function ajaxControl(){

    if(atom()->request($this->ajax_control)){

      defined('DOING_AJAX') or define('DOING_AJAX', true);
      @header('Content-Type: application/json; charset='.get_option('blog_charset'));
      @header('X-Content-Type-Options: nosniff');

      $options = get_option($this->option_name);
      $instance = (int)$_GET['instance'];

      $options = $options[$instance];
      $this->parseOptions($options);

      // ID needed to identify post thumbnail size
      $options['id'] = "{$this->id_base}-{$instance}";

      $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

      $method = implode('', explode('_', $this->ajax_control));

      $output = $this->$method($options, $next, $offset);

      $offset = $offset + $options['number'];

      echo json_encode(array(
        'output' => $output,
        'more'   => $next,
        'offset' => $offset,
      ));

      exit;
    }
  }



 /*
  * Generates the "show more" link, used by various widget to retrieve further content
  *
  * @since    2.0
  * @param    int $offset        Offset from where to get data; this should be the widget's "number" option
  * @param    string $command    Ajax request name
  * @return   string             The link (HTML)
  */
  public function getMoreLink($offset, $command){

    $output = array();

    $attributes = array(
      'class'         => 'more',
      'href'          => '#',
      'title'         => atom()->t('Show next %d entries', $offset), // offset = number on first display
      'data-instance' => $this->number,
      'data-cmd'      => $command,
      'data-offset'   => $offset,
    );

    foreach($attributes as $key => &$value)
      $value = $key.'="'.$value.'"';

    $output[] = '<div class="fadeThis clear-block">';
    $output[] = '<a '.implode(' ', $attributes).'>'.atom()->t('Show More').'</a>';
    $output[] = '</div>';

    return implode("\n", $output);
  }



  /**
   * Retrieve a specific widget template
   * It will call getContextArgs() and check if user-defined options exist, and merge them.
   *
   * @since 1.7
   * @param $name Name of the template to get
   */
  final public function getTemplate($name = 'full'){
    return isset($this->templates[$name]) ? $this->templates[$name] : array_shift($this->templates);
  }



  /**
   * Widget form class - hides the form if the widget instance doesn't appear initialized (the wp-save bug...)
   *
   * @since 1.7
   */
  final public function formClass(){
    // hide widget if it doesn't appear initialized
    echo 'class="'.(is_numeric($this->number) ? 'atom-block' : 'hidden').'"';
  }



 /*
  * Get the output for a widget instance from the cache and display it on the screen.
  * Returns false if the widget doesn't exist in the cache
  *
  * @since    1.7
  * @param    string $instance_id     Widget instance ID; This is $args['id'] inside the widget() method
  * @return   bool                    True on success, false on failure
  */
  public function getAndDisplayCache($instance_id){

    $cache = wp_cache_get("widget_{$this->safe_name}", 'widget');

    if(isset($cache[$instance_id])){
      echo $cache[$instance_id];
      return true;
    }

    // no cache
    return false;
  }



 /*
  * Add the output of a widget instance in the cache
  *
  * @since    1.7
  * @param    string $instance_id    Widget instance ID; This is $args['id'] inside the widget() method
  * @param    string $output         Widget output
  */
  public function addCache($instance_id, $output = ''){

    $cache = (array)wp_cache_get("widget_{$this->safe_name}", 'widget');
    $cache[$instance_id] = $output;

    wp_cache_set("widget_{$this->safe_name}", $cache, 'widget');
  }



 /*
  * Removes the cache records for a widget.
  * All instances are flushed.
  *
  * @since    1.7
  */
  public function flushCache(){
    wp_cache_delete("widget_{$this->safe_name}", 'widget');
  }



  /**
   * Get the callback of a widget.
   * This function handles the "fixes" for Widget Context / Widget Logic
   * @since 1.7
   */
  public static function getCallback($widget_id){
    global $wp_registered_widgets;

    // widget logic
    if(!empty($wp_registered_widgets[$widget_id]['callback_wl_redirect']))
      return $wp_registered_widgets[$widget_id]['callback_wl_redirect'];

    // widget context
    elseif(!empty($wp_registered_widgets[$widget_id]['callback_original_wc']))
      return $wp_registered_widgets[$widget_id]['callback_original_wc'];

    // original
    elseif(!empty($wp_registered_widgets[$widget_id]['callback']))
      return $wp_registered_widgets[$widget_id]['callback'];


    return false;
  }



  /**
   * Get the callback of a widget.
   * This function handles the "fixes" for Widget Context / Widget Logic
   *
   * @since 1.7
   */
  public static function getObject($widget_id){
    $callback = self::getCallback($widget_id);

    if(isset($callback[0]))
      return $callback[0];

    return false;
  }



  /**
   * Get the default icon for the given widget
   *
   * @param string $instance Widget instance
   * @since 1.8
   */
  public function getDefaultIcon($instance){

    // default icons.png sprite (all themes should follow this order):
    //
    //  0 = default
    //  1 = star
    //  2 = comment
    //  3 = time
    //  4 = tag
    //  5 = user
    //  6 = search
    //  7 = lock
    //  8 = bird
    //  9 = calendar
    // 10 = folder

    // check if widget is registered (eg. missing widget - plugin is removed)
    $callback = self::getObject($instance);
    if(!$callback) return '';

    // get the widget instance settings
    $options = get_option($callback->option_name);
    $options = $options[$wp_registered_widgets[$instance]['params'][0]['number']];

    // parse defaults for atom widgets
    if($callback instanceof AtomWidget)
      $callback->parseOptions($options);

    // generate css classes, that can be used for styling the tabs with things like icons
    $id = $callback->id_base;

    if($id === 'atom-splitter') return ''; // no splitters here

    $icon = 0;
    $classes = array();


   // add extra relevant classes based on widget options from certain Atom widgets
        // this is to allow different styling for widgets of the same type inside the tabs (for eg. recent/popular/random posts)
        switch($id){

          // order + post type, if different than "post" + related if checked + category if different than 0 (all)
          case 'atom-posts':
            $classes[] = $options['order_by'];

            if($options['post_type'] !== 'post')
              $classes[] = $options['post_type'];

            if($options['related'])
              $classes[] = 'related';

            if($options['category'])
              $classes[] = "cat-{$options['category']}";
          break;

          // custom menus, menu id
          case 'atom-menu':
            $classes[] = $options['nav_menu'];
          break;

          // term taxonomy, if different than 'post_tag'
          case 'atom-tag-cloud':
            if($options['taxonomy'] !== 'post_tag')
              $classes[] = $options['taxonomy'];
          break;

          // term taxonomy, if different than 'category'
          case 'atom-terms':
            if($options['taxonomy'] !== 'category')
              $classes[] = $options['taxonomy'];
          break;

          // link category ID
          case 'atom-links':
            if($options['category'])
              $classes[] = $options['category'];
          break;

          // archives, type & post type
          case 'atom-archives':
            $classes[] = $options['type'];
            if($options['post_type'] !== 'post') $classes[] = $options['post_type'];
          break;

          // calendar, post type
          case 'atom-calendar':
            if($options['post_type'] !== 'post') $classes[] = $options['post_type'];
          break;

          // blogs, sort order
          case 'atom-blogs':
            $classes[] = $options['order_by'];
          break;

          // users, role (if set)
          case 'atom-users':
            if($options['role'])
              $classes[] = $options['role'];
          break;

        }

        $base = str_replace('atom-', '', strtolower(preg_replace('/[^a-z0-9\-]+/i', '-', $id)));

        foreach($classes as &$class)
          $class = 'nav-'.$base.'-'.strtolower(preg_replace('/[^a-z0-9\-]+/i', '-', $class));

        array_unshift($classes, 'nav-'.$base); // first class (the widget id_base without "atom-")
  }



  /**
   * Get the user icon list from the child theme directory
   *
   * @since 1.8
   */
  public function getIcons(){

    if(!isset($this->icons)){
      $this->icons = array();

      if(is_child_theme() && is_dir(STYLESHEETPATH.'/'.self::USER_ICON_DIR))
        foreach(glob(STYLESHEETPATH.'/'.self::USER_ICON_DIR.'/*.png') as $filename)
          $this->icons[] = basename($filename, '.png');
    }

    return $this->icons;
  }

}



/**
 * Archives Widget.
 * Similar to the default WP widget, but this one also allows the archive type / post type selection and count
 *
 * @todo maybe -- give more control over the output trough templates
 * @todo add read more link (the ajax)
 * @since 1.0
 */
class AtomWidgetArchives extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    $this->set(array(
      'id'          => 'archives',
      'title'       => atom()->t('Archives'),
      'description' => atom()->t("Archives of your site's posts"),
      'defaults'    => array(
        'title'         => atom()->t('Archives'),
        'type'          => 'monthly',
        'post_type'     => 'post',
        'limit'         => 12,
        'day_format'    => get_option('date_format'),
        'week_format'   => get_option('date_format'),
        'post_count'    => true,
        'dropdown'      => false,
      ),
    ));

    // flush cache when posts are changed
    add_action('save_post',       array($this, 'flushCache'));
    add_action('deleted_post',    array($this, 'flushCache'));

    // include CPT in main query
    add_action('pre_get_posts',   array($this, 'includeCPTinArchives'), -999);
  }



  public function includeCPTinArchives(){
    if(is_archive()){

      $post_type = get_query_var('post_type');
      $post_type = is_array($post_type) ? array_filter($post_type, 'post_type_exists') : (post_type_exists($post_type) ? $post_type : false);

      if($post_type)
        atom()->addContextArgs('main_query', array('post_type' => $post_type));

    }
  }



  public function flushCache(){
    wp_cache_delete('get_archives', 'atom');
  }



  // get results from the db; or from the cache if the query we're making was made before
  protected static function getResults($query){
    global $wpdb;

    $key = md5($query);
    $cache = wp_cache_get('get_archives', 'atom');

    if(!isset($cache[$key])){
      $entries = $wpdb->get_results($query);
      $cache[$key] = $entries;
      wp_cache_set('get_archives', $cache, 'atom');

    }else{
      $entries = $cache[$key];
    }

    return empty($entries) ? array() : (array)$entries;
  }


  // displays archive links -- replaces wp_get_archives so we can use cpt and build our own markup
  public static function getArchives($args = array()) {
    global $wpdb, $wp_locale;

    $args = wp_parse_args($args, array(
      'type'        => 'monthly',
      'post_type'   => 'post',
      'limit'       => '',
      'day_format'  => 'Y/m/d',
      'week_format' => 'Y/m/d',
    ));

    extract($args, EXTR_SKIP);

    $limit = !empty($limit) ? ' LIMIT '.(int)$limit : '';

    // filters
    $where = apply_filters('getarchives_where', $wpdb->prepare("WHERE post_type = %s AND post_status = 'publish'", $post_type), $args);
    $join = apply_filters('getarchives_join', '', $args);

    $output = array();

    // month mode
    if($type === 'monthly'){
      $query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC $limit";

      foreach(self::getResults($query) as $entry){
        $url = get_month_link($entry->year, $entry->month);

        if($post_type !== 'post')
          $url = add_query_arg('post_type', $post_type, $url);

        $output[] = array(
          'url'        => $url,
          'title'      => wptexturize(atom()->t('%1$s %2$d', $wp_locale->get_month($entry->month), $entry->year)),  // month name + 4 digit year
          'post_count' => $entry->posts,
          'context'    => strtolower(date('F', mktime(0, 0, 0, $entry->month, 1, 0))),
        );
      }

    // year mode
    }elseif($type === 'yearly'){
      $query = "SELECT YEAR(post_date) AS `year`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date) ORDER BY post_date DESC $limit";

      foreach(self::getResults($query) as $entry){
        $url = get_year_link($entry->year);
        if($post_type !== 'post') $url = add_query_arg('post_type', $post_type, $url);
        $output[] = array(
          'url'        => $url,
          'title'      => sprintf('%d', $entry->year),  // 4 digit year
          'post_count' => $entry->posts,
          'context'    => "year-{$entry->year}",
        );
      }

    // day mode
    }elseif($type === 'daily'){
      $query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date DESC $limit";

      foreach(self::getResults($query) as $entry){
        $url = get_day_link($entry->year, $entry->month, $entry->dayofmonth);
        if($post_type !== 'post') $url = add_query_arg('post_type', $post_type, $url);
        $output[] = array(
           'url'        => $url,
           'title'      => mysql2date($day_format, sprintf('%1$d-%2$02d-%3$02d 00:00:00', $entry->year, $entry->month, $entry->dayofmonth)),
           'post_count' => $entry->posts,
           'context'    => strtolower(mysql2date('l', sprintf('%1$d-%2$02d-%3$02d 00:00:00', $entry->year, $entry->month, $entry->dayofmonth))),
        );
      }

    // week mode
    }elseif($type === 'weekly'){
      $week = _wp_mysql_week('`post_date`');
      $query = "SELECT DISTINCT $week AS `week`, YEAR( `post_date` ) AS `yr`, DATE_FORMAT( `post_date`, '%Y-%m-%d' ) AS `yyyymmdd`, count( `ID` ) AS `posts` FROM `$wpdb->posts` $join $where GROUP BY $week, YEAR( `post_date` ) ORDER BY `post_date` DESC $limit";

      $arc_w_last = '';
      foreach(self::getResults($query) as $entry)
        if($entry->week != $arc_w_last){
          $arc_w_last = $entry->week;
          $arc_week = get_weekstartend($entry->yyyymmdd, get_option('start_of_week'));
          $arc_week_start = date_i18n($week_format, $arc_week['start']);
          $arc_week_end = date_i18n($week_format, $arc_week['end']);
          $url = sprintf('%1$s/%2$s%3$sm%4$s%5$s%6$sw%7$s%8$d', home_url(), '', '?', '=', $entry->yr, '&amp;', '=', $entry->week);

          if($post_type !== 'post')
            $url = add_query_arg('post_type', $post_type, $url);

          $output[] = array(
            'url'        => $url,
            'title'      => $arc_week_start.' &#8211; '.$arc_week_end,
            'post_count' => $entry->posts,
            'context'    => "week-{$entry->week}",
          );
        }

    }

    return $output;
  }



  public function widget($args, $instance){

    extract($args);

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    $results = $this->getArchives(apply_filters('widget_archives_args', $instance));

    if(!$results) return
      atom()->log("No entries of type &lt;{$instance['post_type']}&gt; were found in {$args['widget_id']} ({$args['widget_name']}). Widget marked as inactive");

    $maybe_more_link = '';

    if($page_template = atom()->getPageByTemplate('archives')){
      $page_template = atom()->post($page_template);
      $maybe_more_link = ' <small><a href="'.$page_template->getURL().'">'.atom()->t('Show All').'</a></small>';
      atom()->resetCurrentPost();
    }

    echo $before_widget;
    if($title) echo $before_title.$title.$maybe_more_link.$after_title;

    $dropdown_labels = array(
      'daily'   => atom()->t('Select Day:'),
      'weekly'  => atom()->t('Select Week:'),
      'monthly' => atom()->t('Select Month:'),
      'yearly'  => atom()->t('Select Year:'),
    );

    $count = count($results);

    if($instance['dropdown']): // dropdown? ?>
    <div class="box">
      <select class="wide" name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'>
         <option value=""><?php echo $dropdown_labels[$instance['type']]; ?></option>
         <?php foreach($results as $index => $entry): ?>
         <option value="<?php echo $entry['url']; ?>"><?php echo $entry['title']; ?> <?php if($instance['post_count']) echo "({$entry['post_count']})"; ?></option>
         <?php endforeach; ?>
      </select>
    </div>
    <?php else: ?>
    <ul class="menu fadeThis">
      <?php foreach($results as $index => $entry): ?>
      <li class="<?php echo sanitize_html_class($entry['context']); ?>">
        <a href="<?php echo esc_url($entry['url']); ?>"><?php echo $entry['title']; ?> <?php if($instance['post_count']) echo "({$entry['post_count']})"; ?></a>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif;

    echo $after_widget;
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    extract($new_instance);

    return array(
      'title'       => esc_attr($title),
      'type'        => esc_attr($type),
      'post_type'   => post_type_exists($post_type) ? $post_type : 'post',
      'limit'       => min(max((int)$limit, 1), 100),
      'day_format'  => esc_attr($day_format),
      'week_format' => esc_attr($week_format),
      'post_count'  => (bool)$post_count,
      'dropdown'    => (bool)$dropdown,

    ) + $old_instance;
  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>
      <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:'); ?></label>
       <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('type'); ?>"><?php atom()->te('Type:'); ?></label>
       <select class="wide" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
        <option value="yearly" <?php selected('yearly', esc_attr($instance['type'])) ?>><?php atom()->te('Yearly'); ?></option>
        <option value="monthly" <?php selected('monthly', esc_attr($instance['type'])) ?>><?php atom()->te('Monthly'); ?></option>
        <option value="weekly" <?php selected('weekly', esc_attr($instance['type'])) ?>><?php atom()->te('Weekly'); ?></option>
        <option value="daily" <?php selected('daily', esc_attr($instance['type'])) ?>><?php atom()->te('Daily'); ?></option>
       </select>
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('post_type'); ?>"><?php atom()->te('Post Type:'); ?></label>
       <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="wide">
         <?php foreach(get_post_types(array('public' => true)) as $post_type): ?>
          <?php $data = get_post_type_object($post_type); ?>
          <option value="<?php echo esc_attr($post_type); ?>" <?php selected($instance['post_type'], $post_type); ?>><?php echo $data->label; ?></option>
         <?php endforeach; ?>
       </select>
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('limit'); ?>"><?php atom()->te('Limit to:'); ?></label><br />
       <input size="5" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo (int)$instance['limit']; ?>" /> <small><em><?php atom()->te("Entries"); ?></em></small>
      </p>

      <hr />

      <p>
       <input type="checkbox" <?php checked(isset($instance['post_count']) ? (bool)$instance['post_count'] : false); ?> id="<?php echo $this->get_field_id('post_count'); ?>" name="<?php echo $this->get_field_name('post_count'); ?>" />
       <label for="<?php echo $this->get_field_id('post_count'); ?>"><?php atom()->te('Show post count'); ?></label>
      </p>

      <p>
       <input type="checkbox" <?php checked(isset($instance['dropdown']) ? (bool)$instance['dropdown'] : false); ?> id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" />
       <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php atom()->te('Show as dropdown'); ?></label>
      </p>

      <hr />

      <p>
       <label for="<?php echo $this->get_field_id('day_format'); ?>"><?php atom()->te('Day archive date format:'); ?></label>
       <input class="widefat" id="<?php echo $this->get_field_id('day_format'); ?>" name="<?php echo $this->get_field_name('day_format'); ?>" type="text" value="<?php echo esc_attr($instance['day_format']); ?>" />
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('week_format'); ?>"><?php atom()->te('Week archive date format:'); ?></label>
       <input class="widefat" id="<?php echo $this->get_field_id('week_format'); ?>" name="<?php echo $this->get_field_name('week_format'); ?>" type="text" value="<?php echo esc_attr($instance['week_format']); ?>" />
      </p>

    </div>
    <?php
  }

}






/**
 * Atom Blogs Widget
 *
 * A list of blogs hosted on this website.
 * This widget should only be active in MU mode on the first blog (root site)
 *
 * @since 1.3
 */
class AtomWidgetBlogs extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    $this->set(array(
      'id'           => 'blogs',
      'title'        => atom()->t('Network Blogs'),
      'description'  => atom()->t('A list of site blogs'),
      'width'        => 500,
      'ajax_control' => 'get_blogs',
      'defaults'     => array(
        'title'          => atom()->t('Recently updated blogs'),
        'order_by'       => 'last_updated',
        'number'         => 10,
        'exclude'        => '',
        'exclude_mature' => false,
        'avatar_size'    => 48,
        'more'           => true,
        'template'       => '',
      ),
      'templates'    => array(
        'full'     =>  "<a class=\"clear-block\" href=\"{URL}\" title=\"{TITLE}\">\n"
                      ." {AVATAR}\n"
                      ." <span class=\"base\">\n"
                      ."   <span class=\"tt\">{TITLE}</span>\n"
                      ."   <span class=\"c1\">{DESCRIPTION}</span>\n"
                      ."   <span class=\"c2\">Updated {UPDATED}</span>\n"
                      ." </span>\n"
                      ."</a>",

      ),
    ));


    // flush cache when blog is updated
    add_action('wpmu_blog_updated',  array($this, 'flushCache'));
  }



  protected function getBlogs($args, &$more, $offset = 0){
    extract($args);

    $sites = atom()->getSites(array(
      'exclude_id'     => $exclude,
      'exclude_mature' => $exclude_mature,
      'sort_column'    => $order_by,
      'order'          => ($order_by === 'id') ? 'ASC' : 'DESC',
      'limit_results'  => $number + 1,
      'start'          => $offset
     ));

    // because mysql rand() is slowwwww
    if($order_by === 'rand') shuffle($sites);

    $more = count($sites) > $number ? true : false;
    $count = 1;
    $output = '';
    foreach((array)$sites as $site){

      if($count++ === $number + 1) break;
      $output .= '<li>';

      $fields = array(
        'TITLE'        => get_blog_option($site['blog_id'], 'blogname'),
        'URL'          => get_blogaddress_by_domain($site['domain'], $site['path']), // should be faster than get_blog_option($site['blog_id'], 'siteurl'),
        'AVATAR'       => atom()->getAvatar($site['email'], $avatar_size, ''),
        'POST_COUNT'   => get_blog_option($site['blog_id'], 'post_count'),
        'DESCRIPTION'  => convert_smilies(get_blog_option($site['blog_id'], 'blogdescription')),
        'UPDATED'      => atom()->getTimeSince(abs(strtotime($site['last_updated']))),
        'REGISTERED'   => atom()->getTimeSince(abs(strtotime($site['registered']))),
        'EMAIL'        => esc_url($site['email']), // should not be used
        'ID'           => $site['blog_id'],
      );

      $fields = apply_filters('atom_widget_blogs_keywords', $fields, $site, $args);

      // output template
      $output .= atom()->getBlockTemplate($template, $fields);

      $output .= '</li>';
    }

    return $output;
  }



  public function widget($args, $instance){
    extract($args, EXTR_SKIP);

    // check for a cached instance and display it if we have it
    if($this->getAndDisplayCache($widget_id)) return;

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $blogs = $this->getBlogs($instance, $next);

    if(!$blogs)
      return atom()->log("No blogs found in {$args['widget_id']} ({$args['widget_name']}). Widget marked as inactive");

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    $output = $before_widget;
    if($title) $output .= $before_title.$title.$after_title;

    $output .= "<ul class=\"menu full fadeThis blogs\">{$blogs}</ul>";

    if($instance['more'] && $next && atom()->options('jquery'))
      $output .= $this->getMoreLink($instance['number'], 'get_blogs');

    $output .= $after_widget;

    echo $output;

    $this->addCache($widget_id, $output);
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    $this->FlushCache();
    extract($new_instance);

    return array(
      'title'          => esc_attr($title),
      'order_by'       => esc_attr($order_by),
      'number'         => min(max((int)$number, 1), 50),
      'exclude'        => esc_attr($exclude),
      'exclude_mature' => (bool)$exclude_mature,
      'avatar_size'    => (int)$avatar_size,
      'more'           => (bool)$more,
      'template'       => current_user_can('edit_themes') ? $template : $old_instance['template'],

    ) + $old_instance;
  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>

      <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:'); ?></label>
       <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php if (isset($instance['title'])) echo esc_attr($instance['title']); ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('order_by'); ?>"><?php atom()->te('Order by:') ?></label>
        <select id="<?php echo $this->get_field_id('order_by'); ?>" name="<?php echo $this->get_field_name('order_by'); ?>">
         <option value="last_updated" <?php selected($instance['order_by'], 'last_updated'); ?>><?php atom()->te('Last Update'); ?></option>
         <option value="registered" <?php selected($instance['order_by'], 'registered'); ?>><?php atom()->te('Registered Date'); ?></option>
         <option value="id" <?php selected($instance['order_by'], 'id'); ?>><?php atom()->te('Site ID'); ?></option>
         <option value="rand" <?php selected($instance['order_by'], 'rand'); ?>><?php atom()->te('Nothing, Randomize'); ?></option>
        </select>
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('number'); ?>"><?php atom()->te('How many entries to display?'); ?></label>
       <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php if (isset($instance['number'])) echo (int)$instance['number']; ?>" size="3" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php atom()->te('Exclude:'); ?></label> <input type="text" value="<?php echo esc_attr($instance['exclude']); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="widefat" />
        <br />
        <small><?php atom()->te('Site IDs, separated by commas.'); ?></small>
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('exclude_mature'); ?>">
       <input type="checkbox" id="<?php echo $this->get_field_id('exclude_mature'); ?>" name="<?php echo $this->get_field_name('exclude_mature'); ?>"<?php checked($instance['exclude_mature']); ?> />
   	   <?php atom()->te('Exclude sites marked as mature'); ?></label>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('avatar_size'); ?>"><?php atom()->te('Avatar Size:') ?></label>
        <input type="text" size="3" id="<?php echo $this->get_field_id('avatar_size'); ?>" name="<?php echo $this->get_field_name('avatar_size'); ?>" value="<?php if (isset($instance['avatar_size'])) echo esc_attr($instance['avatar_size']); ?>" /> <?php atom()->te('pixels'); ?>
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('more'); ?>" <?php if(!atom()->options('jquery')) echo "class=\"disabled\""; ?>>
       <input <?php if(!atom()->options('jquery')) echo "disabled=\"disabled\""; ?> type="checkbox" id="<?php echo $this->get_field_id('more'); ?>" name="<?php echo $this->get_field_name('more'); ?>"<?php checked($instance['more']); ?> />
       <?php atom()->te('Display %s Link', '<code>'.atom()->t('Show More').'</code>'); ?></label>
      </p>

      <?php if(current_user_can('edit_themes')): ?>
      <div class="user-template">
        <textarea class="wide code editor" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" rows="8" cols="28" data-mode="atom/html"><?php echo (empty($instance['template'])) ? format_to_edit($this->getTemplate()) : format_to_edit($instance['template']); ?></textarea>
        <small>
          <?php atom()->te('Read the %s to see all available keywords.', '<a href="'.Atom::THEME_DOC_URI.'" target="_blank">'.atom()->t('documentation').'</a>'); ?>
        </small>
      </div>
      <?php endif; ?>

    </div>
    <?php
  }

}






/*
 * Atom Calendar Widget
 *
 * A calendar widget with CSS friendly output, ajax month navigation and support for CPT
 *
 * @since 1.0
 */
class AtomWidgetCalendar extends AtomWidget{



 /*
  * Initialization
  *
  * @see AtomWidget::set and WP_Widget::__construct
  */
  public function __construct(){
    // register the widget and it's options
    $this->set(array(
      'id'           => 'calendar',
      'title'        => atom()->t('Calendar'),
      'description'  => atom()->t("A calendar of your site's posts"),
      'ajax_control' => 'get_calendar',
      'defaults'     => array(
        'initial'   => false,
        'ajax'      => true,
        'post_type' => 'post',
      ),
    ));

    // flush cache when posts are changed
    add_action('save_post',       array($this, 'flushCache'));
    add_action('deleted_post',    array($this, 'flushCache'));

    // include CPT in main query
    add_action('pre_get_posts',   array($this, 'includeCPTinArchives'), -999);
  }



  public function includeCPTinArchives(){
    if(is_archive()){

      $post_type = get_query_var('post_type');
      $post_type = is_array($post_type) ? array_filter($post_type, 'post_type_exists') : (post_type_exists($post_type) ? $post_type : false);

      if($post_type)
        atom()->addContextArgs('main_query', array('post_type' => $post_type));

    }
  }



  public function flushCache(){
    wp_cache_delete('get_calendar', 'atom');
  }



 /*
  * Defines our ajax request handler.
  *
  * @since  2.0
  * @see    AtomWidget::ajaxControl
  */
  public function ajaxControl(){
    if(atom()->request('get_calendar')){

      atom()->ajaxHeader('', 'application/json');
      $output = $this->getCalendar(array(
        'initial' => (bool)$_GET['initial'],
        'req_m'   => (int)$_GET['reqmonth'],
        'req_y'   => (int)$_GET['reqyear'],
      ));

      echo json_encode(array('output' => $output));
      exit;
    }
  }


 /*
  * Pretty much the same as the wp function, but with cpt support and mark-up changes
  *
  * @since  1.0
  */
  protected function getCalendar($args = array()){
    global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

    $args = wp_parse_args($args, array(
      'initial'   => true,
      'req_m'     => null,
      'req_y'     => null,
      'post_type' => 'post',
    ));

    extract($args, EXTR_SKIP);

    // ajax?
    if($req_m) $monthnum = $req_m;
    if($req_y) $year = $req_y;

    if(isset($_GET['w'])) $w = (int)$_GET['w'];
    if(isset($_GET['post_type']) && post_type_exists($_GET['post_type'])) $post_type = $_GET['post_type']; // validate

    $cache = array();
    $key = md5($m.$monthnum.$year.$post_type);
    if($cache = wp_cache_get('get_calendar', 'atom'))
      if(is_array($cache) && isset($cache[$key]))
        return apply_filters('get_calendar', $cache[$key]);

    if(!is_array($cache)) $cache = array();

    // Quick check. If we have no posts at all, abort!
    if(!$posts){
      $got_some = $wpdb->get_var($wpdb->prepare("SELECT 1 as test FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish' LIMIT 1", $post_type));
      if(!$got_some){
        $cache[$key] = '';
        wp_cache_set('get_calendar', $cache, 'atom'); // no point checking again
        return;
      }
    }

    // week_begins = 0 stands for Sunday
    $week_begins = (int)get_option('start_of_week');

    // Let's figure out when we are
    if(!empty($monthnum) && !empty($year)){
      $thismonth = zeroise((int)$monthnum, 2);
      $thisyear = (int)$year;

    }elseif(!empty($w)){
      // We need to get the month from MySQL
      $thisyear = (int)substr($m, 0, 4);
      $d = (($w - 1) * 7) + 6; // it seems MySQL's weeks disagree with PHP's
      $thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL {$d} DAY)), '%m')");

    }elseif(!empty($m)){
      $thisyear = ''.(int)substr($m, 0, 4);
      $thismonth = (strlen($m) < 6) ? '01' : $thismonth = ''.zeroise((int)substr($m, 4, 2), 2);

    }else{
      $thisyear = gmdate('Y', current_time('timestamp'));
      $thismonth = gmdate('m', current_time('timestamp'));
    }

    $unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);

    // Get the next and previous month and year with at least one post
    $previous = $wpdb->get_row("SELECT DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year FROM {$wpdb->posts} WHERE post_date < '{$thisyear}-{$thismonth}-01' AND post_type = '{$post_type}' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");

    $next = $wpdb->get_row("SELECT	DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year FROM {$wpdb->posts} WHERE post_date > '{$thisyear}-{$thismonth}-01' AND MONTH(post_date) != MONTH('{$thisyear}-{$thismonth}-01') AND post_type = '{$post_type}' AND post_status = 'publish' ORDER BY post_date ASC LIMIT 1");

    /* translators: Calendar caption: 1: month name, 2: 4-digit year */
    $calendar_caption = _x('%1$s %2$s', 'calendar caption');

    $caption = '<div class="top clear-block">';

    if($previous){
      $url = get_month_link($previous->year, $previous->month);
      if($post_type !== 'post') $url = add_query_arg('post_type', $post_type, $url);

      $caption .= "\n\t\t<a class=\"prev control\" rel=\"{$previous->month}-{$previous->year}\" href=\"{$url}\" title=\"".atom()->t('View posts for %1$s %2$s', $wp_locale->get_month($previous->month), date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year)))."\">&laquo; ".$wp_locale->get_month_abbrev($wp_locale->get_month($previous->month))."</a>";
    }

    $caption .= '<h4>'.sprintf($calendar_caption, $wp_locale->get_month($thismonth), date('Y', $unixmonth)).'</h4>';

    if($next){
      $url = get_month_link($next->year, $next->month);
      if($post_type !== 'post') $url = add_query_arg('post_type', $post_type, $url);

      $caption .= "\n\t\t<a class=\"next control\" rel=\"{$next->month}-{$next->year}\" href=\"{$url}\" title=\"".esc_attr(atom()->t('View posts for %1$s %2$s', $wp_locale->get_month($next->month), date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year))))."\">".$wp_locale->get_month_abbrev($wp_locale->get_month($next->month))." &raquo;</a>";
    }

    $caption .= '</div>';

    // some themes might need wrapper elements...
    $output = apply_filters('atom_calendar_caption', $caption);

    $output .= '<table class="calendar" summary="'.atom()->t('Calendar').'"><thead><tr>';

    $myweek = array();
    for($wdcount=0; $wdcount<=6; $wdcount++)
      $myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);

    foreach($myweek as $wd){
      $day_name = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
      $wd = esc_attr($wd);
      $output .= "\n\t\t<th scope=\"col\" title=\"{$wd}\">{$day_name}</th>";
    }

    $output .= '</tr></thead> <tbody><tr>';

    // Get days with posts
    $dayswithposts = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(post_date) FROM {$wpdb->posts} WHERE MONTH(post_date) = '{$thismonth}' AND YEAR(post_date) = '$thisyear' AND post_type = '{$post_type}' AND post_status = 'publish' AND post_date < '".current_time('mysql').'\'', ARRAY_N);

    if($dayswithposts)
      foreach((array) $dayswithposts as $daywith)
        $daywithpost[] = $daywith[0];
    else
      $daywithpost = array();

    if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'camino') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false)
      $ak_title_separator = "\n";
    else
      $ak_title_separator = ', ';

    $ak_titles_for_day = array();
    $ak_post_titles = $wpdb->get_results("SELECT ID, post_title, DAYOFMONTH(post_date) as dom FROM {$wpdb->posts} WHERE YEAR(post_date) = '{$thisyear}' AND MONTH(post_date) = '{$thismonth}' AND post_date < '".current_time('mysql')."' AND post_type = '{$post_type}' AND post_status = 'publish'");

    if($ak_post_titles)
      foreach((array)$ak_post_titles as $ak_post_title){
        $post_title = esc_attr(apply_filters('the_title', $ak_post_title->post_title, $ak_post_title->ID));
        if(empty($ak_titles_for_day["day_{$ak_post_title->dom}"]))
          $ak_titles_for_day["day_{$ak_post_title->dom}"] = '';

        if(empty($ak_titles_for_day[$ak_post_title->dom])) // first one
          $ak_titles_for_day[$ak_post_title->dom] = $post_title;
        else
          $ak_titles_for_day[$ak_post_title->dom] .= $ak_title_separator.$post_title;
      }

    // See how much we should pad in the beginning
    $pad = calendar_week_mod(date('w', $unixmonth) - $week_begins);
    if($pad != 0)
      $output .= '<td colspan="'.esc_attr($pad).'" class="pad">&nbsp;</td>';

    $daysinmonth = (int)date('t', $unixmonth);

    for($day = 1; $day <= $daysinmonth; ++$day){

      if(isset($newrow) && $newrow)
        $output .= "\n\t</tr>\n\t<tr>\n\t\t";
      $newrow = false;

      if($day == gmdate('j', current_time('timestamp')) && $thismonth == gmdate('m', current_time('timestamp')) && $thisyear == gmdate('Y', current_time('timestamp')))
        $output .= '<td class="today">';

      else
        $output .= '<td>';

      if(in_array($day, $daywithpost)){ // any posts today?

        $url = get_day_link($thisyear, $thismonth, $day);

        if($post_type !== 'post')
          $url = add_query_arg('post_type', $post_type, $url);

        $output .= '<a href="'.$url."\" title=\"".esc_attr($ak_titles_for_day[$day])."\">$day</a>";

      }else{
        $output .= "<span>{$day}</span>";
      }

      $output .= '</td>';
      if(calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear)) - $week_begins) == 6) $newrow = true;
    }

    $pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear)) - $week_begins);
    if($pad != 0 && $pad != 7)
      $output .= "\n\t\t".'<td class="pad" colspan="'.esc_attr($pad).'">&nbsp;</td>';

    $output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

    $cache[$key] = $output;
    wp_cache_set('get_calendar', $cache, 'atom');

    return apply_filters('atom_calendar', $output); // no point using get_calendar(), markup is different...
  }



  public function widget($args, $instance){
    extract($args);

    // merge default widget options with active widget options
    $this->parseOptions($instance);


    $initial = isset($instance['initial']) ? (int)$instance['initial'] : false;
    $ajax = isset($instance['ajax']) ? $instance['ajax'] : false;

    $calendar = $this->getCalendar($instance);

    if(!$calendar)
      return atom()->log("There are no posts for {$args['widget_id']} ({$args['widget_name']}). Widget marked as inactive");

    echo $before_widget; ?>
    <div class="calendar-block">
      <?php echo $calendar; ?>
    </div>

    <?php if($ajax):
      $block_id = "instance-{$this->id_base}-{$this->number}";

    ?>
    <script>
      /* <![CDATA[ */
      jQuery(document).ready(function($){
        $('#<?php echo $block_id; ?>').delegate('a.control', 'click', function(){
          var reqdate = $(this).attr('rel').split(/-/g);
          $.ajax({
            type: 'GET',
            url: $(this).attr('href'),
            context: this,
            data: { id: '<?php echo $block_id; ?>',
                    initial: <?php echo $initial; ?>,
                    reqmonth: reqdate[0],
                    reqyear: reqdate[1],
                    atom: 'get_calendar' },
            dataType: 'json',
            beforeSend: function() { $(this).addClass('loading'); },
            complete: function() { $(this).removeClass('loading'); },
            success: function(response){
              if(response.output != '') $('#<?php echo $block_id; ?> .calendar-block').html(response.output);
            }
          });
          return false;

        });
      });
      /* ]]> */
    </script>
    <?php endif; ?>

    <?php
    echo $after_widget;
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    extract($new_instance);

    return array(
      'initial'   => (bool)$initial,
      'ajax'      => (bool)$ajax,
      'post_type' => post_type_exists($post_type) ? $post_type : 'post',

    ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>

      <p>
       <label for="<?php echo $this->get_field_id('post_type'); ?>"><?php atom()->te('Post Type:'); ?></label>
       <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="wide">
         <?php foreach(get_post_types(array('public' => true)) as $post_type): ?>
          <?php $data = get_post_type_object($post_type); ?>
          <option value="<?php echo esc_attr($post_type); ?>" <?php selected($instance['post_type'], $post_type); ?>><?php echo $data->label; ?></option>
         <?php endforeach; ?>
       </select>
      </p>


      <p>
       <label for="<?php echo $this->get_field_id('ajax'); ?>" <?php if(!atom()->options('jquery')) echo "class=\"disabled\""; ?>>
       <input type="hidden" name="<?php echo $this->get_field_name('ajax'); ?>" value="0" />
       <input <?php if(!atom()->options('jquery')) echo "disabled=\"disabled\""; ?> type="checkbox" <?php checked($instance['ajax'], true); ?> id="<?php echo $this->get_field_id('ajax'); ?>" name="<?php echo $this->get_field_name('ajax'); ?>" /> <?php atom()->te('Use AJAX for month navigation, when possible'); ?></label>
      </p>
      <p>
       <label for="<?php echo $this->get_field_id('initial'); ?>">
       <input type="hidden" name="<?php echo $this->get_field_name('initial'); ?>" value="0" />
       <input type="checkbox" <?php checked($instance['initial'], true); ?> id="<?php echo $this->get_field_id('initial'); ?>" name="<?php echo $this->get_field_name('initial'); ?>" /> <?php atom()->te('One-letter day abbreviation'); ?></label>
      </p>
    </div>
    <?php
  }

}






/**
 * Atom Links Widget
 *
 * Displays a list of links, like the blogroll
 *
 * @since 1.0
 * @todo Add link category class to list (remove 'blogroll')
 * @todo Add RSS option
 * @todo Add template option
 * @todo maybe- group into multiple widgets when "all categories" are selected
 */
class AtomWidgetLinks extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'          => 'links',
      'title'       => atom()->t('Links'),
      'description' => atom()->t('Your blogroll/links'),
      'defaults'    => array(
        'title'          => atom()->t('Blogroll'),
        'category'       => '',
        'order_by'       => 'name',
        'hide_invisible' => true,
        'image'          => false,
        'rating'         => true,
        'description'    => true,
        'limit'          => 24,
      ),
    ));

  }



  public function widget($args, $instance){
    extract($args);

    // check for a cached instance and display it if we have it
    if($this->getAndDisplayCache($widget_id)) return;

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    $query = array(
      'category'       => $instance['category'],
      'orderby'        => $instance['order_by'],
      'order'          => ($instance['order_by'] == 'rating' ? 'DESC' : 'ASC'),
      'limit'          => (int)$instance['limit'],
      'hide_invisible' => (bool)$instance['hide_invisible'],
    );

    $links = get_bookmarks($query);

    if(empty($links))
      return atom()->log("No links found in {$args['widget_id']} ({$args['widget_name']}). Widget marked as inactive");

    $output = $before_widget;

    if($title)
      $output .= $before_title.$title.$after_title;

    $output .= '<ul class="menu fadeThis blogroll">';

    foreach($links as $link){

      $output .= '<li><a href="'.esc_url($link->link_url).'"';

      if($t = esc_attr($link->link_target))
        $output .= " target=\"{$t}\"";

      if($r = esc_attr($link->link_rel))
        $output .= " rel=\"{$r}\"";

      $output .= '>';
      $output .= '<span class="base">'.($n = esc_attr($link->link_name));

      if($instance['description'] && $link->link_description)
        $output .= '<span class="c1">'.esc_attr($link->link_description).'</span>';

      if($instance['image'] && ($i = esc_url($link->link_image)))
        $output .= '<img class="c1" src="'.$i.'" alt="'.$n.'" />';

      if($instance['rating'] && ($r = (int)$link->link_rating))
        $output .= '<span class="rating" title="'.atom()->t('%s out of 10', $r).'"><span class="bar" style="width:'.($r * 10).'%"></span></span>';

      $output .= '</span></a></li>';
    }

    $output .= '</ul>';
    $output .= $after_widget;

    echo $output;

    $this->addCache($widget_id, $output);
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    extract($new_instance);

    return array(
      'title'          => esc_attr($title),
      'category'       => esc_attr($category),
      'order_by'       => esc_attr($order_by),
      'hide_invisible' => (bool)$hide_invisible,
      'image'          => (bool)$image,
      'rating'         => (bool)$rating,
      'description'    => (bool)$description,
      'limit'          => min(max((int)$limit, 1), 100),

    ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>
      <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:'); ?></label>
       <input class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('category'); ?>"><?php atom()->te('Link Category:'); ?></label>
       <select class="wide" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
        <option value="" <?php selected('', esc_attr($instance['category'])) ?>><?php atom()->te('-- All categories --'); ?></option>
        <?php
          $categories = get_terms('link_category');
          foreach ($categories as $category)
          echo '<option value="'.(int)$category->term_id.'"'.($category->term_id == $instance['category'] ? ' selected="selected"' : '').'>'.$category->name."</option>\n";
        ?>
       </select>
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('order_by'); ?>"><?php atom()->te('Order by:'); ?></label>
       <select class="wide" id="<?php echo $this->get_field_id('order_by'); ?>" name="<?php echo $this->get_field_name('order_by'); ?>">
        <option value="name" <?php selected('name', esc_attr($instance['order_by'])) ?>><?php atom()->te("Name"); ?></option>
        <option value="rating" <?php selected('rating', esc_attr($instance['order_by'])) ?>><?php atom()->te("Rating"); ?></option>
        <option value="url" <?php selected('url', esc_attr($instance['order_by'])) ?>><?php atom()->te("URL"); ?></option>
        <option value="ID" <?php selected('ID', esc_attr($instance['order_by'])) ?>>ID</option>
        <option value="owner" <?php selected('owner', esc_attr($instance['order_by'])) ?>><?php atom()->te("Owner"); ?></option>
        <option value="notes" <?php selected('notes', esc_attr($instance['order_by'])) ?>><?php atom()->te("Notes"); ?></option>
        <option value="description" <?php selected('description', esc_attr($instance['order_by'])) ?>><?php atom()->te("Description"); ?></option>
       </select>
      </p>

      <p>
       <input type="checkbox" <?php checked(isset($instance['hide_invisible']) ? (bool)$instance['hide_invisible'] : false); ?> id="<?php echo $this->get_field_id('hide_invisible'); ?>" name="<?php echo $this->get_field_name('hide_invisible'); ?>" />
       <label for="<?php echo $this->get_field_id('hide_invisible'); ?>"><?php atom()->te('Hide Private Links'); ?></label>

       <br />

       <input type="checkbox" <?php checked(isset($instance['image']) ? (bool)$instance['image'] : false); ?> id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>" />
       <label for="<?php echo $this->get_field_id('image'); ?>"><?php atom()->te('Display Link Image'); ?></label>

       <br />

       <input type="checkbox" <?php checked(isset($instance['rating']) ? (bool)$instance['rating'] : false); ?> id="<?php echo $this->get_field_id('rating'); ?>" name="<?php echo $this->get_field_name('rating'); ?>" />
       <label for="<?php echo $this->get_field_id('rating'); ?>"><?php atom()->te('Display Link Rating'); ?></label>

       <br />

       <input type="checkbox" <?php checked(isset($instance['description']) ? (bool)$instance['description'] : false); ?> id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" />
       <label for="<?php echo $this->get_field_id('description'); ?>"><?php atom()->te('Display Link Description'); ?></label>
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('limit'); ?>"><?php atom()->te('Limit to:'); ?></label><br />
       <input size="5" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo (int)$instance['limit']; ?>" /> <small><em><?php atom()->te("Links"); ?></em></small>
      </p>
    </div>
    <?php
  }

}



/*
 * Lifestream -- under development -- will replace the twitter widget
 *
 * @since 1.0
 */
class AtomWidgetLifestream extends AtomWidget{



 /*
  * Initialization
  *
  * @see AtomWidget::init and WP_Widget::__construct
  */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'           => 'lifestream',
      'title'        => atom()->t('Lifestream'),
      'description'  => atom()->t('Your activity around social networks'),
      'width'        => 500,
      'ajax_control' => 'get_lifestream',
      'defaults'     => array(
        'services'        => array(),
        'title'           => atom()->t('My Lifestream'),
        'number'          => 10,
        'max_records'     => 100,
        'more'            => true,
        'update_interval' => 300,
      ),
    ));

  }

  protected function getLifestream($args, &$more, $offset = 0){

  }


  protected function getService($service_id = ''){
    $services = atom()->getContextArgs('lifestream_services', array(

      'delicious' => array(
                        'query'     => 'SELECT title, link, pubDate FROM delicious.feeds WHERE username = "{USER}"',
                        'template'  => 'Bookmared: <a href="{URL}">{TITLE}</a>',
                      ),

      'twitter'     => array(
                        'query'     => 'SELECT title, link, pubDate FROM twitter.user.timeline WHERE screen_name = "digitalnature"',
                        'template'  => 'Bookmared: <a href="{URL}">{TITLE}</a>',
                      ),

      'tumblr'      => array(
                        'query'     => 'SELECT title, link, pubDate FROM tumblr.posts WHERE id = "rickymontalvo"',
                        'template'  => 'Played: <a href="{URL}">{TITLE}</a>',
                      ),

      'flickr'      => array(
                        'url'       => 'http://www.flickr.com/services/feeds/photos_public.gne?id=52727640@N00&format=rss_200',
                        'template'  => 'Shared a photo <a href="{URL}">{TITLE}</a>',
                      ),

   ));

   return ($service_id && isset($services[$service_id])) ? $services[$service_id] : $services;

  }



  public function widget($args, $instance){
    extract($args);

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    $services = $this->getService();

    $urls = array();
    foreach($services as $service)
      $urls[] = $service['url'];

    $query = "SELECT title, link, pubDate, guid FROM rss WHERE url IN ('".implode("','", $urls)."')| SORT(field='pubDate', descending='true')";

    $url = Atom::YQL_URI.urlencode($query).'&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&diagnostics=false';
    $response = wp_remote_retrieve_body(wp_remote_request($url));

    if(!is_array($data = json_decode($response, true)))
      $error = true;

    $entries = $data['query']['results']['item'];
    var_dump($entries);
    foreach($entries as &$entry){
      foreach($services as $service => $properties)
        if(strpos((string)$entry['guid'], $service) !== false)
          $entry['service'] = $service;

      $entry['pubDate'] = strtotime($entry['pubDate']);
    }

    foreach($entries as $entry){
      echo '<li class="service-'.$entry['service'].'">'.$entry['link'].'</li>';
    }


    return;
    print_r($data_twitter['query']['results']['rss']['channel']['item']);




    $user = esc_attr($instance['twitter-user']);
    $type = esc_attr($instance['lastfm-get']);
//    $count = (int)$instance['count'];
//                  select * from query.multi where queries="select * from twitter.user.timeline where id='iamjpg';select * from twitter.user.timeline where id='iamjpg'"
  //                select * from twitter.user.timeline where id='iamjpg'
//                        http://twitter.com/users/show/{$user}.json
          echo '<pre>';
  return;
    $url = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20xml%20where%20url%3D%22".urlencode("http://ws.audioscrobbler.com/1.0/user/xs/recenttracks.rss")."%22&format=json";
    $response = wp_remote_retrieve_body(wp_remote_request($url));
    if(!is_array($data_lastfm = json_decode($response, true))) $error = true;
    print_r($data_lastfm['query']['results']['rss']['channel']['item']);

    echo '</pre>';

    return;



    $id = "instance-{$this->id}";

    echo $before_widget;
    if($title) echo $before_title.$title.$after_title;
    //

    echo $after_widget;
  }



 /*
  * Saves the widget options
  *
  * @see WP_Widget::update
  */
  public function update($new_instance, $old_instance){

    extract($new_instance);

    delete_transient("instance-{$this->id}");

    return array(
      'title'           => esc_attr($title),
      'number'          => min(max((int)$number, 1), 50),
      'max_records'     => min(max((int)$max_records, 1), 1000),
      'more'            => (bool)$more,
      'update_interval' => min(max((int)$update_interval, 1), 86400),

    ) + $old_instance;

  }


  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>

      <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"> <?php atom()->te('Title:'); ?></label>
       <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('number'); ?>"><?php atom()->te('How many entries to display?'); ?></label>
       <input size="3" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($instance['number']); ?>" />
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('more'); ?>" <?php if(!atom()->options('jquery')) echo "class=\"disabled\""; ?>>
       <input <?php if(!atom()->options('jquery')) echo "disabled=\"disabled\""; ?> type="checkbox" id="<?php echo $this->get_field_id('more'); ?>" name="<?php echo $this->get_field_name('more'); ?>"<?php checked($instance['more']); ?> />
   	  <?php atom()->te('Display %s Link', sprintf('<code>%s</code>', atom()->t('Show More'))); ?></label>
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('max_records'); ?>"><?php atom()->te('Max. records to keep in database'); ?></label>
       <input size="4" id="<?php echo $this->get_field_id('max_records'); ?>" name="<?php echo $this->get_field_name('max_records'); ?>" type="text" value="<?php echo esc_attr($instance['max_records']); ?>" />
      </p>

      <p>
        <?php
         $update_interval = '<input size="4" id="'.$this->get_field_id('update_interval').'" name="'.$this->get_field_name('update_interval').'" type="text" value="'.(int)$instance['update_interval'].'" />';
        ?>
        <label for="<?php echo $this->get_field_id('update_interval'); ?>"><?php atom()->te('Check for new data every %s seconds (Time-out)', $update_interval) ?></label>
      </p>

      <div class="high-priority-block">
        <strong><?php atom()->te('Services:'); ?></strong>

      </div>

    </div>



    <?php
  }
}





/**
 * Atom Login widget
 *
 * Displays the login form and lost pw/ register links.
 * If jquery/javascript is enabled it will attempt to authenticate trough ajax
 *
 * @since 1.0
 * @todo use a login/register form template file
 * @todo add lost pass / register forms...
 */
class AtomWidgetLogin extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'          => 'login',
      'title'       => atom()->t('Login'),
      'description' => atom()->t('Login and Lost Password forms'),
      'width'       => 500,
      'defaults'    => array(
        'title'      => atom()->t('Log in'),
        'text'       => atom()->t('Hello Guest. Login below if you have an account'),
        'dashboard'  => 1,
        'profile'    => 1,
        'write'      => 1,
        'comments'   => 0,
      ),
    ));

    atom()->add('requests', array($this, 'login'));
  }



  public function login(){
    if(atom()->request('login')):
      $instance = isset($_POST['instance']) ? esc_attr(strip_tags(stripslashes($_POST['instance']))) : 'LOGIN';

      $user = wp_signon('', false);
      $error = false;
      $field_to_focus = 'log';

      if(is_wp_error($user))
        if($error = $user->get_error_message()){
          if(in_array($user->get_error_code(), array('empty_password', 'incorrect_password'))) $field_to_focus = 'pwd';

        }else{
          $error = atom()->t('Please enter a valid user name and password');
        }

      if(isset($_POST['ajax'])){
        echo json_encode(array('error' => $error, 'focus_on' => $field_to_focus));
        exit;

      }elseif($error){
        define("{$instance}_ERROR", $error);

      }else{
        wp_redirect(esc_url($_POST['redirect_to']));

      }

    endif;
  }



  public function widget($args, $instance){
    extract($args);
    $block_id = "instance-{$this->id_base}-{$this->number}";

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    if(is_user_logged_in())
      $title = atom()->t('Welcome %s', atom()->user->getName());

    echo $before_widget.($title ? $before_title.$title.$after_title : null);

    echo '<div class="box fadethis login-block clear-block">';

    // the user is logged in, display the menu links
    if(is_user_logged_in()):
      echo '<div class="avatar">'.atom()->getAvatar(atom()->user->getEmail(), 96, '', atom()->user->getName()).'</div>';
      echo '<ul class="menu fadeThis">';

      if($instance['dashboard'])
        echo '<li><a class="dashboard" href="'.admin_url().'">'.atom()->t('Dashboard').'</a></li>';

      if(current_user_can('edit_posts') && $instance['write'])
        echo '<li><a class="write" href="'.admin_url('post-new.php').'">'.atom()->t('Write').'</a></li>';

      if(current_user_can('moderate_comments') && $instance['comments'])
        echo '<li><a class="edit-comments" href="'.admin_url('edit-comments.php').'">'.atom()->t('Comments').'</a></li>';

      if($instance['profile'])
        echo '<li><a class="profile" href="'.admin_url('profile.php').'">'.atom()->t('Profile').'</a></li>';

      echo '<li><a class="log-out last" id="wp-logout" href="'.wp_logout_url(atom()->getCurrentPageURL()).'">'.atom()->t('Log Out').'</a></li>';
      echo '</ul>';

    // the user is not logged in, display the login form
    else: ?>

      <?php if(defined("{$this->id}_ERROR")): ?>
      <div class="message status error clear-block"><?php echo constant("{$this->id}_ERROR"); ?></div>
      <?php elseif(!empty($instance['text'])): ?>
      <div class="message status clear-block"><?php echo $instance['text']; ?></div>
      <?php endif; ?>

      <form id="<?php echo $this->id; ?>_login" action="<?php echo atom()->currentPageURL(); ?>" method="post">

        <div>
          <p><input type="text" data-default="<?php atom()->te('User'); ?>" name="log" id="<?php echo $this->id; ?>_user" class="text clearField" value="" /></p>
          <p><input type="password" data-default="<?php atom()->te('Password'); ?>" name="pwd" id="<?php echo $this->id; ?>_pass" class="text clearField" value="" /></p>
        </div>

        <div class="clear-block">
          <input type="submit" name="wp-submit" class="button ok alignleft" value="<?php atom()->te('Log In'); ?>" tabindex="100" />

          <input type="hidden" name="redirect_to" value="<?php echo atom()->getCurrentPageURL(); ?>" />
          <input type="hidden" name="atom" value="login" />
          <input type="hidden" name="instance" value="<?php echo $this->id; ?>" />

          <label for="<?php echo $this->id; ?>_login_remember" class="remember alignleft">
            <input name="rememberme" type="checkbox" id="<?php echo $this->id; ?>_login_remember" value="forever" />
            <?php atom()->te('Remember me'); ?>
          </label>
        </div>
      </form>

      <script>
        /* <![CDATA[ */
        jQuery(document).ready(function($){

          $('#<?php echo $this->id;; ?>_login').submit(function(event){
            event.preventDefault();

            var form = $('#<?php echo $this->id; ?>_login'),
                url = form.attr('action'),
                status = $("#<?php echo $block_id; ?> .status");

            $.ajax({
              type: 'POST',
              url: url,
              data:{ _ajax_nonce: '<?php atom()->nonce('login'); ?>',
                      atom: 'login',
                      ajax: true,
                      log: $('input[name="log"]', form).val(),
                      pwd: $('input[name="pwd"]', form).val(),
                      rememberme: $('input[name="rememberme"]', form).val()
              },
              dataType: 'json',
              context: this,
              beforeSend: function(){
                status.removeClass('error').addClass('loading').text('<?php echo esc_js(atom()->t('Checking...')); ?>');
              },

              success: function(response){
                if(!response.error){
                  status.removeClass('loading error').addClass('success').html('<?php echo esc_js(atom()->t('Login Successful, redirecting...')); ?>');
                  window.location.reload();

                }else{
                  $('input', form).blur();
                  status.removeClass('loading').addClass('error').html(response.error);

                  if(response.focus_on)
                    $('input[name="' + response.focus_on + '"]', form).focus();

                };
              }

            });
          });
        });
        /* ]]> */
      </script>

      <p class="meta">

      <?php

        $pass_recovery_url = false;

        // search for page template
        if($recovery_page = atom()->getPageByTemplate('lost-pw')){
          $recovery_page = atom()->post($recovery_page);
          $pass_recovery_url = $recovery_page->getURL();
          atom()->resetCurrentPost();
        }

        if(!$pass_recovery_url)
          $pass_recovery_url = site_url('wp-login.php?action=lostpassword', 'login');

      ?>
        <a class="forgot_pass" href="<?php echo $pass_recovery_url; ?>"><?php atom()->te('Lost your password?'); ?></a>

      <?php
       if(get_option('users_can_register')):

        $register_url = false;

        // search for the page that uses the "sign-up" page template, highest priority
        if($signup_page = atom()->getPageByTemplate('sign-up')){
          $signup_page = atom()->post($signup_page);
          $register_url = $signup_page->getURL();
          atom()->resetCurrentPost();
        }

        // buddypress
        if(!$register_url && function_exists('bp_get_signup_page'))
          $register_url = bp_get_signup_page();

        // mu + wp3
        elseif(!$register_url && file_exists(ABSPATH."/wp-signup.php"))
          $register_url = site_url('wp-signup.php', 'login');

        // old wp? normally we shouldn't reach this stage...
        elseif(!$register_url)
          $register_url = site_url('wp-login.php?action=register', 'login');
        ?>
        <br />
        <a class="register" href="<?php echo $register_url; ?>"><?php atom()->te('Register'); ?></a>
      <?php endif; ?>
      </p>
      <?php
    endif;

   	echo '</div>';
	echo $after_widget;
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    extract($new_instance);

    return array(
      'title'     => esc_attr($title),
      'text'      => current_user_can('unfiltered_html') ? $text : stripslashes(wp_filter_post_kses(addslashes($text))),
      'dashboard' => (bool)$dashboard,
      'profile'   => (bool)$profile,
      'write'     => (bool)$write,
      'comments'  => (bool)$comments,

    ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:'); ?>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php if (isset($instance['title'])) echo esc_attr($instance['title']); ?>" /></label>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('text'); ?>"><?php atom()->te('Initial Status Text (or HTML):'); ?>
        <textarea class="widefat code editor" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" rows="6" cols="28" data-mode="atom/html"><?php if (isset($instance['text'])) echo format_to_edit($instance['text']); ?></textarea>
        </label>
      </p>
      <hr />
      <p><strong><?php atom()->te('Welcome screen links (if enough permissions):'); ?></strong></p>
      <p>
       <label for="<?php echo $this->get_field_id('dashboard'); ?>">
         <input id="<?php echo $this->get_field_id('dashboard'); ?>" name="<?php echo $this->get_field_name('dashboard'); ?>" type="checkbox" value="1" <?php checked(isset($instance['dashboard']) ? $instance['dashboard'] : 0); ?> />
         <?php atom()->te('Dashboard'); ?>
       </label>
       <br />

       <label for="<?php echo $this->get_field_id('profile'); ?>">
         <input id="<?php echo $this->get_field_id('profile'); ?>" name="<?php echo $this->get_field_name('profile'); ?>" type="checkbox" value="1" <?php checked(isset($instance['profile']) ? $instance['profile'] : 0); ?> />
         <?php atom()->te('Profile'); ?>
       </label>
       <br />

       <label for="<?php echo $this->get_field_id('write'); ?>">
         <input id="<?php echo $this->get_field_id('write'); ?>" name="<?php echo $this->get_field_name('write'); ?>" type="checkbox" value="1" <?php checked(isset($instance['write']) ? $instance['write'] : 0); ?> />
         <?php atom()->te('Write'); ?>
       </label>
       <br />

       <label for="<?php echo $this->get_field_id('comments'); ?>">
         <input id="<?php echo $this->get_field_id('comments'); ?>" name="<?php echo $this->get_field_name('comments'); ?>" type="checkbox" value="1" <?php checked(isset($instance['comments']) ? $instance['comments'] : 0); ?> />
         <?php atom()->te('Comments'); ?>
       </label>
       <br />

       <label>
         <input disabled="disabled" type="checkbox" value="1" checked="checked" />
         <?php atom()->te('Log out'); ?>
       </label>
      </p>
    </div>
    <?php
  }

}





/**
 * Atom Menu Widget
 *
 * Displays a custom menu as a expandable/collapsible (or collapsed) menu
 *
 * @since 1.2
 */
class AtomWidgetMenu extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'          => 'menu',
      'title'       => atom()->t('Menus'),
      'description' => atom()->t('Display custom menus'),
      'defaults'      => array(
        'title'       => '',
        'nav_menu'    => '',
        'behaviour'   => 'collapsible',
        'event'       => 'click',
      ),
    ));

  }



  public function widget($args, $instance){
    extract($args);

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    // Get menu
    $nav_menu = wp_get_nav_menu_object($instance['nav_menu']);
    if(!$nav_menu) return
      atom()->log("No menu selected in {$args['widget_id']} ({$args['widget_name']}). Widget marked as inactive");

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
    echo $before_widget;

    if(!empty($instance['title']))
      echo $before_title.$title.$after_title;

    wp_nav_menu(array(
      'menu'            => $nav_menu,
//      'container_class' => 'menu',
      'after'           => ($instance['behaviour'] !== 'expanded') ? "<span class=\"expand\"></span>" : '',
      'menu_class'      => "menu fadeThis {$instance['behaviour']} event-{$instance['event']}",
      'fallback_cb'     => '',
    ));

    echo $after_widget;
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    extract($new_instance);

    return array(
      'title'     => esc_attr($title),
      'nav_menu'  => (int)$nav_menu,
      'behaviour' => esc_attr($behaviour),
      'event'     => esc_attr($event),

    ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    // get menus
    $menus = get_terms('nav_menu', array('hide_empty' => false));

    // if no menus exist, direct the user to go and create some
    if(!$menus): ?>
      <p><?php atom()->te('No menus have been created yet.'); ?> <a href="<?php echo admin_url('nav-menus.php'); ?>"><?php atom()->te('Create some'); ?></a></p>
      <?php
      return;
    endif;
    ?>
    <div <?php $this->formClass(); ?>>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:') ?></label>
        <input type="text" class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
      </p>
      <p>
        <label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php atom()->te('Select Menu:'); ?></label><br />
        <select class="wide" id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
        <?php
         foreach($menus as $menu):
           $selected = ($instance['nav_menu'] == $menu->term_id) ? ' selected="selected"' : '';
           echo "<option{$selected} value=\"{$menu->term_id}\">{$menu->name}</option>\n";
         endforeach;
        ?>
        </select>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('behaviour'); ?>" <?php if(!atom()->options('jquery')) echo "class=\"disabled\""; ?>><?php atom()->te('Behaviour:'); ?></label><br />
        <select class="wide" <?php if(!atom()->options('jquery')) echo "disabled=\"disabled\""; ?> id="<?php echo $this->get_field_id('behaviour'); ?>" name="<?php echo $this->get_field_name('behaviour'); ?>">
          <option value="expanded" <?php selected($instance['behaviour'], 'expanded'); ?>><?php atom()->te('Expanded'); ?></option>
          <option value="collapsible" <?php selected($instance['behaviour'], 'collapsible'); ?>><?php atom()->te('Collapsible, Standard'); ?></option>
          <option value="accordion" <?php selected($instance['behaviour'], 'accordion'); ?>><?php atom()->te('Collapsible, Accordion style'); ?> (BETA)</option>
        </select>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('event'); ?>" <?php if(!atom()->options('jquery')) echo "class=\"disabled\""; ?>><?php atom()->te('Expand/Collapse Fire Event:'); ?></label><br />
        <select class="wide" <?php if(!atom()->options('jquery')) echo "disabled=\"disabled\""; ?> id="<?php echo $this->get_field_id('event'); ?>" name="<?php echo $this->get_field_name('event'); ?>">
          <option value="click" <?php selected($instance['event'], 'click'); ?>><?php atom()->te('Click'); ?></option>
          <option value="dblclick" <?php selected($instance['event'], 'dblclick'); ?>><?php atom()->te('Double-Click'); ?></option>
          <option value="mouseover" <?php selected($instance['event'], 'mouseover'); ?>><?php atom()->te('Mouse Over'); ?></option>
        </select>
      </p>
      <p><em><?php atom()->te('Note: Links with the %s URL will behave the same way as the arrows. You should use them on menus with children', '<code>#</code>'); ?></em></p>
    </div>
    <?php
  }

}




/**
 * Atom Meta Widget
 *
 * Displays login/logout/rss/credit links
 *
 * @since 1.0
 */
class AtomWidgetMeta extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'          => 'meta',
      'title'       => atom()->t('Meta'),
      'description' => atom()->t('Log in/out, admin, feed and WordPress links'),
      'defaults'      => array(
        'title'        => atom()->t('Meta'),
        'login'        => true,
        'rss_posts'    => true,
        'rss_comments' => true,
        'wp'           => true,
        'dn'           => false,
      ),
    ));

  }



  public function widget($args, $instance){
    extract($args);

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    echo $before_widget;
    if($title) echo $before_title.$title.$after_title;
    ?>
    <div class="box">
      <ul>

        <?php if($instance['login']): ?>
        <?php wp_register(); ?>
        <li><?php wp_loginout(); ?></li>
        <?php endif; ?>

        <?php if($instance['rss_posts']): ?>
        <li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php atom()->te('Syndicate this site using RSS 2.0'); ?>"><?php atom()->te('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
        <?php endif; ?>

        <?php if($instance['rss_comments']): ?>
        <li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php atom()->te('The latest comments to all posts in RSS'); ?>"><?php atom()->te('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
        <?php endif; ?>

        <?php if($instance['wp']): ?>
        <li><a href="http://wordpress.org/" title="<?php atom()->te('Powered by WordPress, state-of-the-art semantic personal publishing platform.'); ?>">WordPress.org</a></li>
        <?php endif; ?>

        <?php if($instance['dn']): ?>
        <li><a href="http://digitalnature.eu/" title="<?php atom()->te('Theme developed by digitalnature'); ?>">digitalnature.eu</a></li>
        <?php endif; ?>

        <?php wp_meta(); ?>
      </ul>
    </div>
    <?php

    echo $after_widget;
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    extract($new_instance);

    return array(
      'title'         => esc_attr($title),
      'login'         => (bool)$login,
      'rss_posts'     => (bool)$rss_posts,
      'rss_comments'  => (bool)$rss_comments,
      'wp'            => (bool)$wp,
      'dn'            => (bool)$dn,

    ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>
    <p>
     <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:'); ?></label>
     <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
    </p>

    <p><strong><em><?php atom()->te("Links to display:"); ?></em></strong></p>

    <p>
     <input type="checkbox" <?php checked(isset($instance['login']) ? (bool)$instance['login'] : false); ?> id="<?php echo $this->get_field_id('login'); ?>" name="<?php echo $this->get_field_name('login'); ?>" />
     <label for="<?php echo $this->get_field_id('login'); ?>"><?php atom()->te('Login / Logout / Site Admin'); ?></label>

     <br />

     <input type="checkbox" <?php checked(isset($instance['rss_posts']) ? (bool)$instance['rss_posts'] : false); ?> id="<?php echo $this->get_field_id('rss_posts'); ?>" name="<?php echo $this->get_field_name('rss_posts'); ?>" />
     <label for="<?php echo $this->get_field_id('rss_posts'); ?>"><?php atom()->te('Post RSS'); ?></label>

     <br />

     <input type="checkbox" <?php checked(isset($instance['rss_comments']) ? (bool)$instance['rss_comments'] : false); ?> id="<?php echo $this->get_field_id('rss_comments'); ?>" name="<?php echo $this->get_field_name('rss_comments'); ?>" />
     <label for="<?php echo $this->get_field_id('rss_comments'); ?>"><?php atom()->te('Comment RSS'); ?></label>

     <br />

     <input type="checkbox" <?php checked(isset($instance['wp']) ? (bool)$instance['wp'] : false); ?> id="<?php echo $this->get_field_id('wp'); ?>" name="<?php echo $this->get_field_name('wp'); ?>" />
     <label for="<?php echo $this->get_field_id('wp'); ?>"><?php atom()->te('External: WordPress.org'); ?></label>

     <br />

     <input type="checkbox" <?php checked(isset($instance['dn']) ? (bool)$instance['dn'] : false); ?> id="<?php echo $this->get_field_id('dn'); ?>" name="<?php echo $this->get_field_name('dn'); ?>" />
     <label for="<?php echo $this->get_field_id('dn'); ?>"><?php atom()->te('External: digitalnature.eu'); ?></label>

     <br />

    </p>

    </div>
    <?php
  }

}










/**
 * Atom Pages Widget
 *
 * Show a list of pages based on the current context
 *
 * @todo add template system
 *
 * @since 1.0
 */
class AtomWidgetPages extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'          => 'pages',
      'title'       => atom()->t('Pages'),
      'description' => atom()->t('Your site&#8217;s WordPress Pages'),
      'defaults'    => array(
        'type'        => 'all',
        'root'        => true,
        'child_of'    => 0,
        'sortby'      => 'menu_order',
        'title'       => atom()->t('Pages'),
        'exclude'     => '',
        'depth'       => 0,
      ),
    ));

  }



  public function widget($args, $instance){
    extract($args);

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
    $sortby = empty($instance['sortby']) ? 'menu_order' : $instance['sortby'];
    $type = empty($instance['type']) ? 'all' : esc_attr($instance['type']);

    // newest dates first
    $order = ($sortby == 'post_date' || $sortby == 'post_modified') ? 'DESC' : 'ASC';

    if($sortby == 'menu_order')
      $sortby = 'menu_order, post_title';

    $parent = 0;
    if($type == 'sub'){
      global $post;
      $parent = $post;
      if($instance['root']){

        while($parent->post_parent != 0)
          $parent = &get_post($parent->post_parent);

        $title = '<a href="'.get_permalink($parent).'">'.$parent->post_title.'</a>';
      }
      $parent = $parent->ID;

    }elseif($type == 'child'){
      $parent = $instance['child_of'];
    }

    $out = wp_list_pages(apply_filters('widget_pages_args',
      array(
        'title_li'     => '',
        'echo'         => 0,
        'sort_order'   => $order,
        'sort_column'  => $sortby,
        'depth'        => (int)$instance['depth'],
        'exclude'      => $instance['exclude'],
        'child_of'     => $parent,
      )));

    if(empty($out)) return
      atom()->log("No pages found for the current context in {$args['widget_id']} ({$args['widget_name']}). Widget marked as inactive");

    echo $before_widget;
    if($title) echo $before_title.$title.$after_title;
    ?>
    <div class="box">
      <ul> <?php echo $out; ?> </ul>
    </div>
    <?php
    echo $after_widget;

  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    extract($new_instance);

    return array(
      'type'     => esc_attr($type),
      'root'     => (bool)$root,
      'child_of' => (int)$child_of,
      'title'    => esc_attr($title),
      'sortby'   => esc_attr($sortby),
      'exclude'  => esc_attr($exclude),
      'depth'    => (int)$depth,

    ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>
      <div class="high-priority-block">
        <p><strong><?php atom()->te("Display:"); ?></strong></p>
        <label for="<?php echo $this->get_field_id('type'); ?>_all">
          <input id="<?php echo $this->get_field_id('type'); ?>_all" name="<?php echo $this->get_field_name('type'); ?>" value="all" type="radio" <?php checked($instance['type'], 'all'); ?> />
          <?php atom()->te('All Pages'); ?>
        </label>
        <br />
        <label for="<?php echo $this->get_field_id('type'); ?>_sub">
          <input id="<?php echo $this->get_field_id('type'); ?>_sub" name="<?php echo $this->get_field_name('type'); ?>" value="sub" type="radio" <?php checked($instance['type'], 'sub'); ?> />
          <?php atom()->te('Children of the active page'); ?>
        </label>
        <br />
        <input style="margin-left: 20px;" id="<?php echo $this->get_field_id('root'); ?>" name="<?php echo $this->get_field_name('root'); ?>" type="checkbox" <?php checked(isset($instance['root']) ? $instance['root'] : 0); ?> rules="<?php echo $this->get_field_name('type'); ?>:sub" />
        <label for="<?php echo $this->get_field_id('root'); ?>"><?php atom()->te('Start from root'); ?></label>


        <br />
        <label for="<?php echo $this->get_field_id('type'); ?>_child">
          <input id="<?php echo $this->get_field_id('type'); ?>_child" name="<?php echo $this->get_field_name('type'); ?>" value="child" type="radio" <?php checked($instance['type'], 'child'); ?> />
          <?php atom()->te('Children of:'); ?>
        </label>
        <br />
        <?php
         atom()->Dropdown('page', array(
           'name'             => $this->get_field_name('child_of'),
           'id'               => $this->get_field_id('child_of'),
           'selected'         => (int)$instance['child_of'],
           'orderby'          => 'menu_order',
           'hierarchical'     => 1,
           'extra_attributes' => 'style="margin-left: 20px;" rules="'.$this->get_field_name('type').':child"',
         ));
        ?>

      </div>

      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:'); ?></label>
        <input class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('sortby'); ?>"><?php atom()->te('Sort by:'); ?></label>
        <select name="<?php echo $this->get_field_name('sortby'); ?>" id="<?php echo $this->get_field_id('sortby'); ?>" class="wide">
          <option value="post_title" <?php selected($instance['sortby'], 'post_title'); ?>><?php atom()->te('Page title'); ?></option>
          <option value="menu_order" <?php selected($instance['sortby'], 'menu_order'); ?>><?php atom()->te('Page order'); ?></option>
          <option value="ID" <?php selected($instance['sortby'], 'ID'); ?>><?php atom()->te('Page ID'); ?></option>
          <option value="post_date" <?php selected($instance['sortby'], 'post_date'); ?>><?php atom()->te('Date, created'); ?></option>
          <option value="post_modified" <?php selected($instance['sortby'], 'post_modified'); ?>><?php atom()->te('Date, modified'); ?></option>
        </select>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php atom()->te('Exclude:'); ?></label>
        <input type="text" value="<?php echo esc_attr($instance['exclude']); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="wide" />
        <br />
        <small><?php atom()->te('Page IDs, separated by commas.'); ?></small>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('depth'); ?>"><?php atom()->te('Depth:'); ?></label>
        <input type="text" value="<?php echo (int)$instance['depth']; ?>" name="<?php echo $this->get_field_name('depth'); ?>" id="<?php echo $this->get_field_id('depth'); ?>" class="wide" />
        <br />
        <small><?php atom()->te('0 = All levels'); ?></small>
      </p>
    </div>
    <?php
  }

}










/**
 * Atom Posts widget
 *
 * Displays a list of posts (recent, popular, related, random etc.)
 *
 * @since 1.0
 * @todo allow order_by meta
 * @todo maybe- allow hooks to add templates; make the mode field dynamic (do this to all widgets that use templates)...
 */
class AtomWidgetPosts extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'           => 'posts',
      'title'        => atom()->t('Posts'),
      'description'  => atom()->t('List posts based on filters you choose'),
      'width'        => 500,
      'ajax_control' => 'get_posts',
      'defaults'     => array(
        'site_wide'           => false,
        'title'               => atom()->t('Recent Posts'),
        'post_type'           => 'post',
        'mode'                => 'full',
        'order_by'            => 'date',
        'category'            => 0,
        'number'              => 5,
        'character_count'     => 140,
        'thumb_size'          => array(48, 48),
        'more'                => true,
        'related'             => false,
        'template'            => '',

        // internal settings (not worth adding forms for)
        // note that a 'atom_widget_post_defaults' filter can override these every time they are retrieved
        'allowed_tags'        => Atom::SAFE_INLINE_TAGS,
        'content_filter_more' => '[&hellip;]',
      ),
      'templates'    => array(
        'full'     =>  "<a class=\"clear-block\" href=\"{URL}\" title=\"{TITLE}\">\n"
                      ." {THUMBNAIL}\n"
                      ." <span class=\"base\">\n"
                      ."   <span class=\"tt\">{TITLE} ({COMMENT_COUNT})</span>\n"
                      ."   <span class=\"c1\">{CONTENT}</span>\n"
                      ."   <span class=\"c2\">{DATE}</span>\n"
                      ." </span>\n"
                      ."</a>",

        'images'   =>  "<a class=\"clear-block\" href=\"{URL}\" title=\"{TITLE}\">\n"
                      ." {THUMBNAIL}\n"
                      ."</a>",

        'brief'    =>  "<a class=\"clear-block\" href=\"{URL}\" title=\"{TITLE}\">\n"
                      ." <span class=\"base\">\n"
                      ."   <span class=\"tt\">{TITLE} ({COMMENT_COUNT})</span>\n"
                      ." </span>\n"
                      ."</a>",

        'detailed' =>  "<a class=\"clear-block\" href=\"{URL}\" title=\"{TITLE}\">\n"
                      ." <span class=\"base\">\n"
                      ."   <span class=\"tt\">{TITLE} ({COMMENT_COUNT})</span>\n"
                      ."   <span class=\"c1\">{CONTENT}</span>\n"
                      ."   <span class=\"c2\">{DATE}</span>\n"
                      ." </span>\n"
                      ."</a>",
      ),
    ));

    // register thumbnail size
    add_action('wp_loaded',       array($this, 'setThumbSize'));

    // flush cache when posts are changed
    add_action('save_post',       array($this, 'flushCache'));
    add_action('deleted_post',    array($this, 'flushCache'));
  }



  public function setThumbSize($sizes){

    // we need to process all instances because this function gets to run only once
    $widget_options = get_option($this->option_name);

    foreach((array)$widget_options as $instance => $options){

      // identify instance
      $id = "{$this->id_base}-{$instance}";

      // register thumb size if the widget is active
      if(!is_active_widget(false, $id, $this->id_base)) continue;

      AtomWidget::getObject($id)->parseOptions($options);

      // conditional check needed for settings saved in older versions of the "Mystique" theme, ie. 3.2.9 and below
      if(is_array($options['thumb_size'])){
        list($thumb_size_x, $thumb_size_y) = $options['thumb_size'];

      }else{
        $thumb_size_x = $thumb_size_y = $options['thumb_size'];
      }

      add_image_size($id, $thumb_size_x, $thumb_size_y, true);
    }

    return $sizes;
  }


  protected function getPosts($args, &$more, $offset = 0){
    global $post;

    extract($args);

    // build query, we get the number of posts + 1 just to check if more posts are available
    $query = array(
      'orderby'             => $order_by,
      'order'               => ($order_by !== 'title') ? 'DESC' : 'ASC',
      'post_type'           => $post_type,
      'ignore_sticky_posts' => true,
      'posts_per_page'      => $number + 1,
      'offset'              => $offset,
    );

    if($order_by === 'views'){
      $query['order']    = 'DESC';
      if($site_wide){
        $query['orderby']  = 'views';
      }else{
        $query['orderby']  = 'meta_value_num';
        $query['meta_key'] = apply_filters('post_views_meta_key', 'views');
      }
    }

    if($category != 0 && !$site_wide)
      $query['cat'] = $category;

    // exclude current post if we're on the single page
    if(is_single())
      $query['post__not_in'] = array($post->ID);

    if($related && is_single()){ // tag-related posts ?
      $tags = wp_get_post_tags($post->ID);
      if(!empty($tags)){
        $tag_ids = array();
        foreach($tags as $tag) $tag_ids[] = $tag->term_id;
        $query['tag__in'] = $tag_ids;
      }else{
        return false; // no tags = no related posts
      }

    }elseif($related){
      return false; // not a single page
    }

    $posts = atom()->get('swc') ? new AtomSWPostQuery($query) : new WP_Query($query);

    $more = ($posts->post_count > $number) ? true : false;                               // do we have more results?
    $template = ($mode === 'template') ? $args['template'] : $this->getTemplate($mode);

    $output = '';

    // output posts, minus the last one
    while($posts->have_posts() && ($posts->current_post < $number - 1)){ // @todo check $posts->current_post because it starts from -1 in some cases...

      $posts->the_post();
      atom()->setCurrentPost(false);

      $output .= '<li>';

      $fields = array(
        'TITLE'           => atom()->post->getTitle(),
        'COMMENT_COUNT'   => atom()->post->getCommentCount(),
        'THUMBNAIL'       => atom()->post->getThumbnail(str_replace('instance-', '', $id)),
        'URL'             => atom()->post->getURL(),
        'CONTENT'         => convert_smilies(atom()->post->getContent($character_count, array(
                               'allowed_tags' => $allowed_tags,
                               'more'         => $content_filter_more,
                             ))),
        'EXCERPT'         => atom()->post->getContent('e'),
        'DATE'            => atom()->post->getDate('relative'),
        'AUTHOR'          => atom()->post->author->getName(),
        'CATEGORIES'      => strip_tags(atom()->post->getTerms('category')),
        'TAGS'            => strip_tags(atom()->post->getTerms()),
        'VIEWS'           => number_format_i18n(atom()->post->getViews()),
        'INDEX'           => $posts->current_post + 1,
        'ID'              => atom()->post->getID(),
      );

      // extra fields for mu
      if($site_wide){
        $blog = get_blog_details(atom()->post->getBlogID());
        $fields = array_merge($fields, array(
          'BLOG_ID'         => $blog->blog_id,
          'BLOG_NAME'       => $blog->blogname,
          'BLOG_POST_COUNT' => $blog->post_count,
          'BLOG_URL'        => $blog->path,
        ));
      }

      $fields = apply_filters('atom_widget_posts_keywords', $fields, atom()->post, $args);

      // output template
      $output .= atom()->getBlockTemplate($template, $fields);

      $output .= '</li>';

    }

    atom()->resetCurrentPost();

    return $output;
  }



  public function widget($args, $instance){

    extract($args, EXTR_SKIP);

    // check for a cached instance and display it if we have it
    if($this->getAndDisplayCache($widget_id)) return;

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $instance['id'] = $this->id;

    // get the posts
    if(!($posts = $this->getPosts($instance, $next)))
      return atom()->log("No ".($instance['related'] ? 'related posts' : 'relevant entries')." were found in {$args['widget_id']} ({$args['widget_name']}). Widget marked as inactive");

    $output = $before_widget;

    // display "show all" link if this is a custom post type and it has an archive page
    $maybe_more_link = '';
    if(!in_array($instance['post_type'], array('post', 'page', 'media')) && ($archive = get_post_type_archive_link($instance['post_type'])))
      $maybe_more_link = ' <small><a href="'.$archive.'">'.atom()->t('Show All').'</a></small>';

    // widget title
    if($title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base))
      $output .= $before_title.$title.$maybe_more_link.$after_title;

    $output .= "<ul class=\"menu fadeThis clear-block {$instance['mode']}\">{$posts}</ul>";

    // "show more" link
    if($instance['more'] && $next && atom()->options('jquery'))
      $output .= $this->getMoreLink($instance['number'], 'get_posts');

    $output .= $after_widget;

    echo $output;

    // we can't cache random posts (they wouldn't be random :)
    if($instance['order_by'] !== 'rand')
      $this->addCache($widget_id, $output);
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    $this->FlushCache();
    extract($new_instance);

    return array(
      'title'           => esc_attr($title),
      'post_type'       => post_type_exists($post_type) ? $post_type : 'post',
      'mode'            => esc_attr($mode),
      'category'        => (int)$category,
      'order_by'        => esc_attr($order_by),
      'number'          => min(max((int)$number, 1), 50),
      'character_count' => (int)$character_count,
      'thumb_size'      => array_map('intval', $thumb_size),
      'more'            => (bool)$more,
      'related'         => (bool)$related,
      'template'        => (isset($template) && current_user_can('edit_themes')) ? $template : $old_instance['template'],
      'site_wide'       => atom()->get('swc') ? (bool)$site_wide : $old_instance['site_wide'],

    ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);
    extract($instance);

    // conditional check needed for settings saved in older versions of the "Mystique" theme, ie. 3.2.9 and below
    if(is_array($thumb_size)){
      list($thumb_size_x, $thumb_size_y) = $thumb_size;

    }else{
      $thumb_size_x = $thumb_size_y = $thumb_size;
    }

    ?>
    <div <?php $this->formClass(); ?>>
      <?php if(atom()->get('swc')): // only on mu + atom-swc ?>
      <div class="high-priority-block">

         <input type="checkbox" id="<?php echo $this->get_field_id('site_wide'); ?>" name="<?php echo $this->get_field_name('site_wide'); ?>" <?php checked($site_wide); ?> />
         <label for="<?php echo $this->get_field_id('site_wide'); ?>"><strong><?php atom()->te('Network (site-wide) content'); ?></strong></label>
         <br />
         <em style="margin-left:15px;">(<?php atom()->te('Get posts from all network blogs'); ?>)</em>

      </div>
      <?php endif; ?>
      <div class="clear-block">
        <div class="section alignleft">
          <p>
           <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:'); ?></label>
           <input class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
          </p>

          <p>
           <label for="<?php echo $this->get_field_id('post_type'); ?>"><?php atom()->te('Post Type:'); ?></label>
           <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="wide">
             <?php foreach(get_post_types(array('public' => true)) as $cpt): ?>
              <option value="<?php echo esc_attr($cpt); ?>" <?php selected($post_type, $cpt); ?>><?php echo get_post_type_object($cpt)->label; ?></option>
             <?php endforeach; ?>
           </select>
          </p>

          <p>
            <label for="<?php echo $this->get_field_id('order_by'); ?>"><?php atom()->te('Order by:') ?></label>
            <select class="wide" id="<?php echo $this->get_field_id('order_by'); ?>" name="<?php echo $this->get_field_name('order_by'); ?>">
             <option value="date" <?php selected($order_by, 'date'); ?>><?php atom()->te('Date (Recent posts)'); ?></option>
             <option value="modified" <?php selected($order_by, 'modified'); ?>><?php atom()->te('Modified date (Recently modified)'); ?></option>
             <option value="comment_count" <?php selected($order_by, 'comment_count'); ?>><?php atom()->te('Comment count'); ?></option>
             <option value="views" <?php selected($order_by, 'views'); ?>><?php atom()->te('View count'); ?></option>
             <option value="title" <?php selected($order_by, 'title'); ?>><?php atom()->te('Title, alphabetically'); ?></option>
             <option value="rand" <?php selected($order_by, 'rand'); ?>><?php atom()->te('Nothing, get random posts'); ?></option>
            </select>
          </p>

          <?php
            $categories = atom()->getDropdown('category', array(
              'name'              => $this->get_field_name('category'),
              'id'                => $this->get_field_id('category'),
              'selected'          => (int)$category,
              'show_option_all'   => atom()->t('-- All categories --'),
              'hide_empty'        => 0,
              'orderby'           => 'name',
              'show_count'        => 1,
              'class'             => 'wide',
              'hierarchical'      => 1,
              'extra_attributes'  => 'rules="'.$this->get_field_name('post_type').':post + !'.$this->get_field_name('site_wide').'"',
            ));
          ?>
          <p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php atom()->te('Show posts from:') ?></label>
            <?php echo $categories; ?>
          </p>

        </div>

        <div class="section alignright">
          <p>
           <label for="<?php echo $this->get_field_id('number'); ?>"><?php atom()->te('How many entries to display?'); ?></label><br />
           <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo (int)$number; ?>" size="3" />
          </p>

          <p>
           <?php $input = '<br /><input id="'.$this->get_field_id('character_count').'" name="'.$this->get_field_name('character_count').'" type="text" value="'.$character_count.'" size="3" />'; ?>
           <label for="<?php echo $this->get_field_id('character_count'); ?>"><?php atom()->te('Content has max %s characters', $input); ?></label>
          </p>

          <p>
            <label for="<?php echo $this->get_field_id('thumb_size'); ?>"><?php atom()->te('Thumbnail Size:') ?></label><br />
            <input type="text" size="3" id="<?php echo $this->get_field_id('thumb_size'); ?>_x" name="<?php echo $this->get_field_name('thumb_size'); ?>[]" value="<?php echo (int)$thumb_size_x; ?>" />
            &times;
            <input type="text" size="3" id="<?php echo $this->get_field_id('thumb_size'); ?>_y" name="<?php echo $this->get_field_name('thumb_size'); ?>[]" value="<?php echo (int)$thumb_size_y; ?>" /> <em><?php atom()->te('pixels'); ?></em>
          </p>

          <p>
           <input <?php if(!atom()->options('jquery')) echo "disabled=\"disabled\""; ?> type="checkbox" id="<?php echo $this->get_field_id('more'); ?>" name="<?php echo $this->get_field_name('more'); ?>"<?php checked($more); ?> />
           <label for="<?php echo $this->get_field_id('more'); ?>" <?php if(!atom()->options('jquery')) echo "class=\"disabled\""; ?>><?php atom()->te('Display %s Link', '<code>'.atom()->t('Show More').'</code>'); ?></label>
          </p>

          <p>
           <input type="checkbox" id="<?php echo $this->get_field_id('related'); ?>" name="<?php echo $this->get_field_name('related'); ?>"<?php checked($related); ?> rules="<?php echo $this->get_field_name('post_type'); ?>:post" />
           <label for="<?php echo $this->get_field_id('related'); ?>"><?php atom()->te('Get only context-related posts'); ?></label>
          </p>
        </div>
      </div>

      <div class="template-selector clear-block">
        <div class="title"><?php atom()->te('Display mode') ?></div>
        <a href="#" class="select t-full" rel="full" title="<?php atom()->te("Full"); ?>"><?php atom()->te("Full"); ?></a>
        <a href="#" class="select t-detailed" rel="detailed" title="<?php atom()->te("Details"); ?>"><?php atom()->te("Details"); ?></a>
        <a href="#" class="select t-brief" rel="brief" title="<?php atom()->te("Brief"); ?>"><?php atom()->te("Brief"); ?></a>
        <a href="#" class="select t-images" rel="images" title="<?php atom()->te("Post thumbnails"); ?>"><?php atom()->te("Post thumbnails"); ?></a>
        <a href="#" class="select t-custom" rel="template" title="<?php atom()->te("Custom Template"); ?>"><?php atom()->te("Custom Template"); ?></a>
        <input class="select" type="hidden" value="<?php echo $mode; ?>" id="<?php echo $this->get_field_id('mode'); ?>" name="<?php echo $this->get_field_name('mode'); ?>" />
      </div>

      <?php if(current_user_can('edit_themes')): ?>
      <div class="user-template <?php if($mode !== 'template') echo 'hidden'; ?>">
        <textarea class="wide code editor" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" rows="8" cols="28" data-mode="atom/html"><?php echo (empty($template)) ? format_to_edit($this->getTemplate()) : format_to_edit($template); ?></textarea>
        <small>
          <?php atom()->te('Read the %s to see all available keywords.', '<a href="'.Atom::THEME_DOC_URI.'" target="_blank">'.atom()->t('documentation').'</a>'); ?>
        </small>
      </div>
      <?php endif; ?>

      <hr />
      <p>
        <em>
          <?php
            atom()->te('<strong>Important:</strong> %1$s sized thumbnails have to be created if you just added this widget, or if you\'re changing the thumbnail size. Read more about thumbnail sizes %2$s',  (int)$thumb_size_x.'x'.(int)$thumb_size_y, '<a href="'.admin_url('themes.php?page='.ATOM.'#advanced').'">'.atom()->t('here').'</a>'); ?>
        </em>
      </p>
    </div>
    <?php
  }
}






/**
 * Atom Recent Comments Widget
 *
 * Show a list of comments order by date.
 * Can also get site-wide comments
 *
 * @since 1.0
 * @todo test cache
 * @todo test the comment content filter
 */
class AtomWidgetRecentComments extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'           => 'recent-comments',
      'title'        => atom()->t('Recent Comments'),
      'description'  => atom()->t('The most recent comments'),
      'width'        => 500,
      'ajax_control' => 'get_recent_comments',
      'defaults'     => array(
        'title'               => atom()->t('Recent Comments'),
        'number'              => 5,
        'character_count'     => 140,
        'avatar_size'         => 48,
        'more'                => true,
        'template'            => '',
        'site_wide'           => false,
        'allowed_tags'        => Atom::SAFE_INLINE_TAGS,  // internal
        'content_filter_more' => '[&hellip;]',            // internal
      ),
      'templates'    => array(
        '<a class="clear-block" href="{URL}" title="'.atom()->t('on %s', '{TITLE}').'">',
        '  {AVATAR}',
        '  <span class="base">',
        '  <span class="tt">{AUTHOR}</span>',
        '  <span class="c1">{CONTENT}</span>',
        '  <span class="c2">{DATE}</span>',
        '</span>',
        '</a>',
      ),
    ));

    // flush cache when comments are changed
    add_action('comment_post',              array($this, 'flushCache'));
    add_action('transition_comment_status', array($this, 'flushCache'));
  }



  protected function getRecentComments($args, &$more, $offset = 0){
    extract($args);

    $query = atom()->get('swc') ? new AtomSWCommentQuery(): new WP_Comment_Query();

    $comments = $query->query(array(
      'number' => $number + 1,
      'status' => 'approve',
      'offset' => (int)$offset,
    ));

    $more = (count($comments) > $number) ? true : false;
    $count = 1;
    $output = '';
    $template = $args['template'] ? $args['template'] : $this->getTemplate();

    foreach((array)$comments as $comment){

      if($count++ == $number + 1) break;
      $output .= '<li>';

      atom()->setCurrentComment($comment);

      $fields = array(
        'TITLE'     => $site_wide ? strip_tags($comment->post_title) : get_the_title($comment->comment_post_ID),
        'URL'       => atom()->comment->getURL(),
        'AVATAR'    => atom()->comment->getAvatar(),
        'AUTHOR'    => $comment->comment_author,
        'CONTENT'   => convert_smilies(atom()->generateExcerpt($comment->comment_content, array(
                         'limit'         => $character_count,
                         'allowed_tags'  => $allowed_tags,
                         'more'          => $content_filter_more
          ))),
        'DATE'      => atom()->comment->getDate('relative'),
        'EMAIL'     => esc_url($comment->comment_author_email), // should not be used
        'ID'        => atom()->comment->getID(),
      );

      // extra fields for mu
      if($site_wide){
        $blog = get_blog_details(atom()->comment->getBlogID());
        $fields = array_merge($fields, array(
          'BLOG_ID'         => $blog->blog_id,
          'BLOG_NAME'       => $blog->blogname,
          'BLOG_POST_COUNT' => $blog->post_count,
          'BLOG_URL'        => $blog->path,
        ));
      }

      $fields = apply_filters('atom_widget_recent_comments_keywords', $fields, $comment, $args);

      // output template
      $output .= atom()->getBlockTemplate($template, $fields);

      $output .= '</li>';
    }

    return $output;
  }



  public function widget($args, $instance){
    extract($args, EXTR_SKIP);

    // check for a cached instance and display it if we have it
    if($this->getAndDisplayCache($widget_id)) return;

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $comments = $this->getRecentComments($instance, $next);

    if(!$comments)
      return atom()->log("No comments were found in {$widget_id} ({$widget_name}). Widget marked as inactive");;;

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    $output = $before_widget;
    if ($title) $output .= $before_title.$title.$after_title;

    $output .= "<ul class=\"menu fadeThis full recent-comments\">{$comments}</ul>";

    if($instance['more'] && $next && atom()->options('jquery'))
      $output .= $this->getMoreLink($instance['number'], 'get_recent_comments');

    $output .= $after_widget;

    echo $output;

    $this->addCache($widget_id, $output);
  }




  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

     $this->FlushCache();
     extract($new_instance);

     return array(
      'title'           => esc_attr($title),
      'number'          => min(max((int)$number, 1), 50),
      'character_count' => (int)$character_count,
      'avatar_size'     => (int)$avatar_size,
      'more'            => (bool)$more,
      'template'        => current_user_can('edit_themes') ? $template : $old_instance['template'],
      'site_wide'       => atom()->get('swc') ? $site_wide : $old_instance['site_wide'],

     ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>

      <?php if(atom()->get('swc')): // only on mu + swc ?>
      <div class="high-priority-block">

         <input type="checkbox" id="<?php echo $this->get_field_id('site_wide'); ?>" name="<?php echo $this->get_field_name('site_wide'); ?>" <?php checked($instance['site_wide']); ?> />
         <label for="<?php echo $this->get_field_id('site_wide'); ?>"><strong><?php atom()->te('Network (site-wide) content'); ?></strong></label>
         <br />
         <em style="margin-left:15px;">(<?php atom()->te('Get comments from all network blogs'); ?>)</em>

      </div>
      <?php endif; ?>

      <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:'); ?></label>
       <input class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php if (isset($instance['title'])) echo esc_attr($instance['title']); ?>" />
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('number'); ?>"><?php atom()->te('How many entries to display?'); ?></label>
       <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php if (isset($instance['number'])) echo (int)$instance['number']; ?>" size="3" />
      </p>

      <p>
       <?php $input = '<input id="'.$this->get_field_id('character_count').'" name="'.$this->get_field_name('character_count').'" type="text" value="'.(isset($instance['character_count']) ? (int)$instance['character_count'] : '').'" size="3" />'; ?>
       <label for="<?php echo $this->get_field_id('character_count'); ?>"><?php atom()->te('Content has max %s characters', $input); ?></label>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('avatar_size'); ?>"><?php atom()->te('Avatar Size:') ?></label>
        <input type="text" size="3" id="<?php echo $this->get_field_id('avatar_size'); ?>" name="<?php echo $this->get_field_name('avatar_size'); ?>" value="<?php if (isset($instance['avatar_size'])) echo (int)$instance['avatar_size']; ?>" /> <?php atom()->te('pixels'); ?>
      </p>

      <p>
       <input <?php if(!atom()->options('jquery')) echo "disabled=\"disabled\""; ?> type="checkbox" id="<?php echo $this->get_field_id('more'); ?>" name="<?php echo $this->get_field_name('more'); ?>"<?php checked($instance['more']); ?> />
       <label for="<?php echo $this->get_field_id('more'); ?>" <?php if(!atom()->options('jquery')) echo "class=\"disabled\""; ?>><?php atom()->te('Display %s Link', '<code>'.atom()->t('Show More').'</code>'); ?></label>
      </p>

      <?php if(current_user_can('edit_themes')): ?>
      <br />
      <strong><?php atom()->te('Template:'); ?></strong>
      <div class="user-template">
        <textarea class="wide code editor" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" rows="8" cols="28" data-mode="atom/html"><?php echo (empty($instance['template'])) ? format_to_edit($this->getTemplate()) : format_to_edit($instance['template']); ?></textarea>
        <small>
          <?php atom()->te('Read the %s to see all available keywords.', '<a href="'.Atom::THEME_DOC_URI.'" target="_blank">'.atom()->t('documentation').'</a>'); ?>
        </small>
      </div>
      <?php endif; ?>
    </div>
    <?php
  }

}




/**
 * Atom Search Widget
 *
 * Displays the search form
 * Unlike the default widget this one doesn't allow titles (search in Atom-based themes should be too pretty to require titles ;)
 *
 * @since 1.0
 */
class AtomWidgetSearch extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget
    $this->set(array(
      'id'          => 'search',
      'title'       => atom()->t('Search'),
      'description' => atom()->t('A search form for your site'),
    ));

  }



  public function widget($args, $instance){
    extract($args);
    echo $before_widget;
    atom()->template('searchform');
    echo $after_widget;
  }


  // we don't have options (other than the universal visibility options)
  public function form(){}

}







/**
 * Column splitter.
 * Widgets that are surrounded by two "column splitter" widgets will be displayed in two columns
 *
 * @since 1.3
 */
class AtomWidgetSplitter extends AtomWidget{


  // strings used for replacement before / after sidebar processing
  const
    START_TAG   = "<!--start/split-->",
    END_TAG     = "<!--end/split-->",
    START_HTML  = "<li class=\"splitter\">\n<div class=\"block-content\">\n<ul class=\"split clear-block\">\n",
    END_HTML    = "</ul>\n</div>\n</li>";



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget
    $this->set(array(
      'id'          => 'splitter',
      'title'       => atom()->t('Column Splitter'),
      'description' => atom()->t('Splits area into 2 columns'),
    ));

    atom()->add('area_check', array($this, 'areaCheck'), 10, 2);
    atom()->add('widget_area', array($this, 'areaProcess'), 10, 2);
  }



  public function areaCheck($sidebar_content, $area_id){
    // no point in going further if no splitters are active
    if(!defined('SPLITTER_ACTIVE')) return $sidebar_content;

    // ignore splitters when checking for active widgets
    return str_replace(array(self::START_TAG, self::END_TAG), '', $sidebar_content);
  }



  public function areaProcess($sidebar_content, $area_id){
    // no point in going further if no splitters are active
    if(!defined('SPLITTER_ACTIVE')) return $sidebar_content;

    // replace splitter string with html and show splitters if we have any other active widgets in the sidebar
    $sidebar_content = str_replace(array(self::START_TAG, self::END_TAG), array(self::START_HTML, self::END_HTML), $sidebar_content);
    if(strpos($area_id, 'sidebar') !== false){ // check splitters
      $all_widgets = wp_get_sidebars_widgets();
      $splitters = 0;
      foreach($all_widgets as $sidebar => $sidebar_widgets)
        if($sidebar == $area_id)
          foreach($sidebar_widgets as $widget)
            if(strpos($widget, 'atom-splitter') !== false) $splitters++;

      if($splitters % 2){
        $sidebar_content .= self::END_HTML; // odd splitter count => missing a splitter.
        atom()->log("Fixed a missing closing column splitter in &lt;{$area_id}&gt;.");
      }
    }
    return $sidebar_content;
  }



  public function widget($args, $instance){
    extract($args);
    defined("SPLITTER_ACTIVE") or define("SPLITTER_ACTIVE", true);
    if(!atom()->getWidgetSplitter()){
      atom()->setWidgetSplitter(true);
      echo self::START_TAG; // this will get replaced with html when the sidebar is generated
      //atom()->log("Column splitter opened in {$args['widget_id']} ({$args['widget_name']}).");
    }else{
      atom()->setWidgetSplitter(false);
      echo self::END_TAG;
      //atom()->log("Column splitter closed in {$args['widget_id']} ({$args['widget_name']}).");
    }
  }



  public function form($instance){
    ?><p><?php atom()->te('Widgets surrounded by splitters will be grouped into columns (two per row).'); ?></p><?php
  }
}










/**
 * Atom Tabbed Widgets.
 *
 * Can group widgets from the Arbitrary area into tabs.
 * Essentially this widget processes a "sidebar".
 *
 * @link http://digitalnature.eu
 *
 * @package ATOM
 * @subpackage Template
 *
 * @since 1.0
 *
 * @todo add the possibility to drag & drop widgets inside this widget
 */
class AtomWidgetTabs extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'          => 'tabs',
      'title'       => atom()->t('Tabbed Widgets'),
      'description' => atom()->t('Group arbitrary widgets into tabs'),
      'width'       => 500,
      'defaults'    => array(
        'widgets'     => array(),
        'icons'       => array(),
        'effect'      => 'fade',
        'event'       => 'click',
      ),
    ));

  }

  protected static function getArbitraryWidgets(){
    global $wp_registered_widgets;

    $sidebars_widgets = wp_get_sidebars_widgets();

    $matches = array();
    $count = 0;
    if(!empty($sidebars_widgets['arbitrary'])){
      foreach($sidebars_widgets['arbitrary'] as $index => $widget_id){

        // check if widget is registered (eg. missing widget - plugin is removed)
        $callback = AtomWidget::getObject($widget_id);
        if(!$callback) continue;

        // get the widget instance settings
        $options = get_option($callback->option_name);
        $options = $options[$wp_registered_widgets[$widget_id]['params'][0]['number']];

        // parse defaults for atom widgets
        if($callback instanceof AtomWidget)
          $callback->parseOptions($options);

        // generate css classes, that can be used for styling the tabs with things like icons
        $id = $callback->id_base;

        if($id == 'atom-splitter') continue; // no splitters here

        $classes = array();

        // add extra relevant classes based on widget options from certain Atom widgets
        // this is to allow different styling for widgets of the same type inside the tabs (for eg. recent/popular/random posts)
        switch($id){

          // order + post type, if different than "post" + related if checked + category if different than 0 (all)
          case 'atom-posts':
            $classes[] = $options['order_by'];

            if($options['post_type'] !== 'post')
              $classes[] = $options['post_type'];

            if($options['related'])
              $classes[] = 'related';

            if($options['category'])
              $classes[] = "cat-{$options['category']}";
          break;

          // custom menus, menu id
          case 'atom-menu':
            $classes[] = $options['nav_menu'];
          break;

          // term taxonomy, if different than 'post_tag'
          case 'atom-tag-cloud':
            if($options['taxonomy'] !== 'post_tag')
              $classes[] = $options['taxonomy'];
          break;

          // term taxonomy, if different than 'category'
          case 'atom-terms':
            if($options['taxonomy'] !== 'category')
              $classes[] = $options['taxonomy'];
          break;

          // link category ID
          case 'atom-links':
            if($options['category'])
              $classes[] = $options['category'];
          break;

          // archives, type & post type
          case 'atom-archives':
            $classes[] = $options['type'];
            if($options['post_type'] !== 'post')
              $classes[] = $options['post_type'];
          break;

          // calendar, post type
          case 'atom-calendar':
            if($options['post_type'] !== 'post')
              $classes[] = $options['post_type'];
          break;

          // blogs, sort order
          case 'atom-blogs':
            $classes[] = $options['order_by'];
          break;

          // users, role (if set)
          case 'atom-users':
            if($options['role'])
              $classes[] = $options['role'];
          break;

        }

        $base = str_replace('atom-', '', strtolower(preg_replace('/[^a-z0-9\-]+/i', '-', $id)));

        foreach($classes as &$class)
          $class = 'nav-'.$base.'-'.strtolower(preg_replace('/[^a-z0-9\-]+/i', '-', $class));

        array_unshift($classes, 'nav-'.$base); // first class (the widget id_base without "atom-")

        $matches[$count] = array(
          'id'       => $widget_id,
          'number'   => $count,
          'name'     => $wp_registered_widgets[$widget_id]['name'],
          'title'    => isset($options['title']) ? $options['title'] : $wp_registered_widgets[$widget_id]['name'],
          'classes'  => apply_filters('atom_widget_tabs_classes', $classes, $options, $count),
        );

        $count++;
      }
    }

    asort($matches);

    return $matches;
  }



  public function widget($args, $instance){

    extract($args);

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    // initialize some variables
    $active_tabs = $valid_tabs = $valid_widgets = $sorted_widgets = $output = array();

    // active widgets from the options
    if(is_array($instance['widgets']))
      $active_tabs = $instance['widgets'];

    // get the widget list from the arbitrary area
    $widgets = $this->getArbitraryWidgets();

    // check if the active widgets are valid and the instances exist
    foreach($widgets as $key => $widget){
      if(!in_array($widget['id'], $active_tabs, true)){
        unset($widgets[$key]);
      }else{
        if($widget['output'] = atom()->getWidget($widget['id'])){
          $valid_tabs[] = "tab-{$widget['id']}";
          $valid_widgets[] = $widget;
        }
      }
    }

    // sort them as intended by the user
    foreach($active_tabs as $tab)
      foreach($valid_widgets as $index => $widget)
        if($widget['id'] === $tab)
          $sorted_widgets[] = $widget;

    $valid_widgets = $sorted_widgets;

    // stores the active tab state from the front-end
    $cookie = isset($_COOKIE["tabs-{$this->id}"]) && in_array($_COOKIE["tabs-{$this->id}"], $valid_tabs, true) ? $_COOKIE["tabs-{$this->id}"] : false;

    $fx = $instance['effect'] ? 'data-fx="'.$instance['effect'].'"' : '';
    $event = $instance['event'] ? 'data-event="'.$instance['event'].'"' : '';

    // generate the output
    foreach($valid_widgets as $index => $widget){

      $active = (($cookie === "tab-{$widget['id']}") || (!$cookie && $index === 0)) ? 'active' : '';

      if($active)
        $widget['classes'][] = $active;

      $title = apply_filters('atom_widget_tabs_title', ($widget['title'] ? $widget['title'] : '&nbsp;'), $widget, $instance);

      $hidden = ($cookie === "tab-{$widget['id']}") || (!$cookie && $index === 0) ? '' : 'hidden';

      $output[$index]['nav'] = '<li class="'.implode(' ', $widget['classes']).'" id="nav-'.$widget['id'].'"><a href="#tab-'.$widget['id'].'" title="'.$title.'"><span>'.$title.'</span></a></li>';
      $output[$index]['content'] = '<div class="section '.$hidden.'" id="tab-'.$widget['id'].'">'.$widget['output'].'</div>';
    }

    if(empty($valid_widgets))
      return atom()->log("Tabbed widgets are not configured in {$widget_id} ({$widget_name}). Widget marked as inactive");

    if(empty($output))
      return atom()->log("No active widget (tabs) in {$widget_id} ({$widget_name}). Widget marked as inactive");

    echo $before_widget; // <div style="position:absolute; top: 30px;border-bottom:1px solid #3d3;width:100%;height:1px;"></div>

    ?>
    <div class="tabs widgets" id="tabs-<?php echo $this->id; ?>" <?php echo $fx; ?> <?php echo $event; ?>>
      <ul class="navi clear-block">
        <?php foreach($output as $tab): ?>
          <?php echo $tab['nav']; ?>
        <?php endforeach; ?>
      </ul>
      <div class="sections">
        <?php foreach($output as $section): ?>
          <?php echo $section['content']; ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php
    echo $after_widget;

  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    extract($new_instance);

    return array(
      'widgets'  => $widgets,
      'effect'   => esc_attr($effect),
      'event'    => esc_attr($event),

    ) + $old_instance;
  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $active_tabs = is_array($instance['widgets']) ? $instance['widgets'] : array();
    $icons = is_array($instance['icons']) ? $instance['icons'] : array();
    ?>

    <div <?php $this->formClass(); ?>>
    <?php

    if(is_int($this->number)):
      $all_widgets = $this->getArbitraryWidgets();

      if(empty($all_widgets)){
        echo '<p class="widget-error">'.atom()->t('To add tabs, you must have at least one active arbitrary widget. Click save to refresh...').'</p>';

      }else{

        // sort
        $ordered_widgets = $ordered_icons = array();
        foreach($active_tabs as $w)
          foreach($all_widgets as $i => $a)
            if($w == $a['id']){
              $ordered_widgets[] = $a;
              unset($all_widgets[$i]);
            }
        $ordered_widgets = array_merge($ordered_widgets, $all_widgets);

        foreach($ordered_widgets as $w)
          $ordered_icons[$w['id']] = isset($icons[$w['id']]) ? $icons[$w['id']] : '';

        $sidebars_widgets = wp_get_sidebars_widgets();
        $output = array();

        $output[] = '<p>'.atom()->t('Create Tabs for:').'</p>';
        $output[] = '<ul id="tabs_'.$this->id.'" class="arbitrary-widget-list">';

        if(atom()->OptionExists('widget_icon_size'))
          list($icon_width, $icon_height) = explode('x', atom()->options('widget_icon_size'));

        $icon = '';

        foreach($ordered_widgets as $w){
          // is the current widget checked?
          $checked = (in_array($w['id'], $active_tabs));

          // checkbox and label
          $output[] = '<li class="entry '.($checked ? 'checked' : '').'">';
          $output[] = '<label>';
          $output[] = '<input name="'.$this->get_field_name('widgets').'[]" type="checkbox" value="'.$w['id'].'" '.($checked ? 'checked="checked"' : '').'" />';
          $output[] = '<strong>'.$w['name'].'</strong>'.($w['title'] ? ": {$w['title']}" : '');
          $output[] = '</label>';
//          $output[] = '<a class="current-icon" style="width:'.$icon_width.'px;height:'.$icon_height.'px;">'.$ordered_icons[$w['id']].'</a>';
//          $output[] = '<input type="hidden" name="'.$this->get_field_name('icons').'[]" value="'.$ordered_icons[$w['id']].'" />';
          $output[] = '</li>';
        }

        $output [] = '</ul><br />';

/*
        $output [] = '<div class="widget-icon-list">';

        $output [] = '<div class="clear-block">';

        $output [] = '<h3>'.atom()->t('Theme-default:').'</h3>';
        $output [] = '<ul>';

        for($y = 0; $y <= ($icon_height * 10); $y = $y + $icon_height)
          $output[] = '<li class="icon default" data-number="'.round($y / $icon_height).'"><span style="width:'.$icon_width.'px;height:'.$icon_height.'px;background-position:0 -'.$y.'px"></span></li>';

        $output [] = '</ul>';
        $output [] = '</div>';

        $user_icons = $this->getIcons();
        if(empty($user_icons)){

          $output[] = '<p><em>';

          if(is_child_theme())
            $output[] = atom()->t('You can also include your own icons within this list, by copying them inside this folder: %s',
              sprintf('<strong>%s</strong>', '/'.basename(WP_CONTENT_DIR).'/themes/'.basename(STYLESHEETPATH).'/'.AtomWidget::USER_ICON_DIR.'/'));

          else
            $output[] = atom()->t('Activate a child theme to add your own icons in this list.');

          $output[] = '</em></p>';

        }else{
          $icons_url = trailingslashit(atom()->get('child_theme_url').'/'.AtomWidget::USER_ICON_DIR);

          $output [] = '<h3>'.atom()->t('Your icons:').'</h3>';
          $output [] = '<ul>';

          // user icon images
          foreach($user_icons as $icon)
            $output[] = '<li class="icon user"><img src="'.$icons_url.$icon.'.png" alt="'.$icon.'" title="'.$icon.'" width="'.$icon_width.'" height="'.$icon_height.'" /></li>';

          $output [] = '</ul>';
        }

        $output [] = '</div>';
*/
        echo implode("\n", $output);

      } ?>
      <script type="text/javascript">
        //<![CDATA[

        jQuery(document).ready(function($){

          $("#tabs_<?php echo $this->id; ?>").each(function(){
            var tabs = $(this),
                icons = tabs.parent().find('.widget-icon-list');

            tabs.sortable().disableSelection();

            $('.current-icon', tabs).click(function(event){
              icons.show();

            });

          });

          $('.widget-icon-list').each(function(){
            var list = $(this);

            $('.icon').click(function(event){
              var icon = $(this).hasClass('default') ? $(this).data('number') : $('img', this).attr('src');

              list.hide();
              alert(icon);

            });

          });

        });

        //]]>
      </script>
      <?php
    endif;
    ?>

    <p>
      <label for="<?php echo $this->get_field_id('effect'); ?>"><?php atom()->te('Transition effect:') ?></label><br />
      <select class="wide" id="<?php echo $this->get_field_id('effect'); ?>" name="<?php echo $this->get_field_name('effect'); ?>">
        <option value="" <?php selected(empty($instance['effect'])); ?>><?php atom()->te('-- None --'); ?></option>
        <option value="fade" <?php selected($instance['effect'], 'fade'); ?>><?php atom()->te('Fade'); ?></option>
        <option value="height" <?php selected($instance['effect'], 'height'); ?>><?php atom()->te('Toggle Height'); ?></option>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('event'); ?>"><?php atom()->te('Switch-Tab event:'); ?></label><br />
      <select class="wide" id="<?php echo $this->get_field_id('event'); ?>" name="<?php echo $this->get_field_name('event'); ?>">
        <option value="" <?php selected(empty($instance['event'])); ?>><?php atom()->te('Click'); ?></option>
        <option value="dblclick" <?php selected($instance['event'], 'dblclick'); ?>><?php atom()->te('Double-Click'); ?></option>
        <option value="mouseover" <?php selected($instance['event'], 'mouseover'); ?>><?php atom()->te('Mouse Over'); ?></option>
      </select>
    </p>
    </div>
    <?php
  }

}





/**
 * Atom Tag Cloud Widget
 *
 * Tag Cloud from a list of taxonomy terms
 *
 * @since 1.0
 */
class AtomWidgetTagCloud extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'          => 'tag-cloud',
      'title'       => atom()->t('Tag Cloud'),
      'description' => atom()->t('Your most used tags in cloud format'),
      'defaults'    => array(
        'title'          => atom()->t('Tags'),
        'taxonomy'       => 'post_tag',
        'number'         => 36,
        'smallest'       => 10,
        'largest'        => 22,
        'gradient_start' => 'cccccc',
        'gradient_end'   => '333333',
      ),
    ));

  }



  // calculate the tag color based on tag importance, start & end gradient colors (props to konforce from StackOverflow)
  public static function getTagColor($weight, $mincolor, $maxcolor){
    $weight = $weight/100;

    $mincolor = hexdec($mincolor);
    $maxcolor = hexdec($maxcolor);

    $r1 = ($mincolor >> 16) & 0xff;
    $g1 = ($mincolor >> 8) & 0xff;
    $b1 = $mincolor & 0xff;

    $r2 = ($maxcolor >> 16) & 0xff;
    $g2 = ($maxcolor >> 8) & 0xff;
    $b2 = $maxcolor & 0xff;

    $r = $r1 + ($r2 - $r1) * $weight;
    $g = $g1 + ($g2 - $g1) * $weight;
    $b = $b1 + ($b2 - $b1) * $weight;

    return sprintf("%06x", (($r << 16) | ($g << 8) | $b));
  }

  // almost the same as WP's function, only added color styles & removed irrelevant arguments
  public static function generateTagCloud($tags, $args = ''){
    $defaults = array(
      'smallest'                   => 8,
      'largest'                    => 22,
      'number'                     => 45,
      'gradient_start'             => false,
      'gradient_end'               => false,
      'order'                      => 'ASC',
      'topic_count_text_callback'  => 'default_topic_count_text',
      'topic_count_scale_callback' => 'default_topic_count_scale'
    );

    if(!isset($args['topic_count_text_callback']) && isset($args['single_text']) && isset($args['multiple_text'])):
      $body = 'return sprintf(_n('.var_export($args['single_text'], true).', '.var_export($args['multiple_text'], true).', $count), number_format_i18n($count));';
      $args['topic_count_text_callback'] = create_function('$count', $body);
    endif;

    $args = wp_parse_args($args, $defaults);
    extract($args);
    if(empty($tags)) return;

    $tags_sorted = apply_filters('tag_cloud_sort', $tags, $args);
    if($tags_sorted != $tags) { // the tags have been sorted by a plugin
      $tags = $tags_sorted;
      unset($tags_sorted);
    } else {
      if($order === 'RAND'){
        shuffle($tags);
      } else {
      // SQL cannot save you; this is a second (potentially different) sort on a subset of data.
        uasort($tags, create_function('$a, $b', 'return strnatcasecmp($a->name, $b->name);'));
        if($order === 'DESC') $tags = array_reverse($tags, true);
      }
    }

    if($number > 0) $tags = array_slice($tags, 0, $number);

    $counts = array();
    $real_counts = array(); // For the alt tag
    foreach((array) $tags as $key => $tag){
      $real_counts[$key] = $tag->count;
      $counts[$key] = $topic_count_scale_callback($tag->count);
    }

    $min_count = min($counts);
    $spread = max($counts) - $min_count;
    if($spread <= 0) $spread = 1;
    $font_spread = $largest - $smallest;
    if($font_spread < 0) $font_spread = 1;
    $font_step = $font_spread / $spread;

    $a = array();
    foreach($tags as $key => $tag){
      $count = $counts[$key];
      $real_count = $real_counts[$key];
      if($gradient_start && $gradient_end){
        if($largest == $smallest) $tag_weight = $largest; else $tag_weight = ($smallest+(($count-$min_count)*$font_step));
        $diff = $largest-$smallest;
        if($diff <= 0) $diff = 1;
        $color_weight = round(99*($tag_weight-$smallest)/($diff)+1);
        $tag_color = self::getTagColor($color_weight, $gradient_start, $gradient_end);
      }

      $tag_link = ('#' !== $tag->link) ? esc_url($tag->link) : '#';
      //$tag_id = isset($tags[$key]->id) ? $tags[$key]->id : $key;
      $size = ($smallest+(($count-$min_count)*$font_step));
      $name = $tags[$key]->name;
      if($size > 15) $name = "<strong>{$name}</strong>"; // bold if size > 15pt

      $a[] = "<a href=\"{$tag_link}\" class=\"tag-{$tags[$key]->slug}\" title=\"".esc_attr($topic_count_text_callback($real_count))."\" style=\"font-size:".sprintf("%01.1f", $size)."pt;".(isset($tag_color) ? "color:#{$tag_color};" : null)."\">{$name}</a>";
    }

    return apply_filters('wp_generate_tag_cloud', join("\n", $a), $tags, $args);
  }



  public static function tagCloud($args = ''){
    $defaults = array(
      'smallest'    => 8,
      'largest'     => 22,
      'number'      => 45,
      'order'       => 'ASC', // ASC/DESC/RAND -- @todo: maybe add a option for this?
      'exclude'     => '',
      'include'     => '',
      'taxonomy'    => 'post_tag',
    );

    $args = wp_parse_args($args, $defaults);
    $tags = get_terms($args['taxonomy'], array_merge($args, array('orderby' => 'count', 'order' => 'DESC'))); // Always query top tags

    if(empty($tags)) return;

    foreach($tags as $key => $tag){
      $link = get_term_link((int)$tag->term_id, $args['taxonomy']);

      if(is_wp_error($link))
       return false;

      $tags[$key]->link = $link;
      $tags[$key]->id = $tag->term_id;
    }

    $output = self::generateTagCloud($tags, $args); // Here's where those top tags get sorted according to $args
    $output = apply_filters('wp_tag_cloud', $output, $args);
    return $output;
  }



  private function getCurrentTaxonomy($instance){
    if(!empty($instance['taxonomy']) && taxonomy_exists($instance['taxonomy'])) return $instance['taxonomy'];
    return 'post_tag';
  }



  public function widget($args, $instance){
    extract($args);

    // don't show the widget on our internal "tags" page
    if(atom()->isPage('tags')) return;

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $current_taxonomy = $this->getCurrentTaxonomy($instance);
    $number = empty($instance['number']) ? 45 : $instance['number'];
    $smallest = empty($instance['smallest']) ? 8 : $instance['smallest'];
    $largest = empty($instance['largest']) ? 22 : $instance['largest'];
    $gradient_start = esc_attr($instance['gradient_start']);
    $gradient_end = esc_attr($instance['gradient_end']);
    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);


    $maybe_more_link = '';

    if($current_taxonomy === 'post_tag' && $page_template = atom()->getPageByTemplate('tags')){
      $page_template = atom()->post($page_template);
      $maybe_more_link = ' <small><a href="'.$page_template->getURL().'">'.atom()->t('Show All').'</a></small>';
      atom()->resetCurrentPost();
    }

    echo $before_widget;
    if($title) echo $before_title.$title.$maybe_more_link.$after_title;
    echo '<div class="box tagcloud">';
    echo $this->tagCloud(apply_filters('widget_tag_cloud_args', array(
      'taxonomy'       => $current_taxonomy,
      'number'         => $number,
      'smallest'       => $smallest,
      'largest'        => $largest,
      'gradient_start' => $gradient_start,
      'gradient_end'   => $gradient_end
     )));
    echo "</div>\n";
    echo $after_widget;
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    extract($new_instance);

    return array(
      'title'          => esc_attr($title),
      'taxonomy'       => esc_attr($taxonomy),
      'number'         => min(max((int)$number, 5), 1000),
      'smallest'       => min(max((int)$smallest, 1), 100),
      'largest'        => min(max((int)$largest, 1), 100),
      'gradient_start' => esc_attr($gradient_start),
      'gradient_end'   => esc_attr($gradient_end),

    ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $current_taxonomy = $this->getCurrentTaxonomy($instance);

    ?>
    <div <?php $this->formClass(); ?>>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:') ?></label>
        <input type="text" class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if(isset($instance['title'])) echo esc_attr($instance['title']); ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php atom()->te('Taxonomy:') ?></label>
        <select class="wide" id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>">
         <?php foreach (get_object_taxonomies('post') as $taxonomy):
           $tax = get_taxonomy($taxonomy);
           if(!$tax->show_tagcloud || empty($tax->labels->name)) continue; ?>
         <option value="<?php echo esc_attr($taxonomy) ?>" <?php selected($taxonomy, $current_taxonomy) ?>><?php echo $tax->labels->name; ?></option>
         <?php endforeach; ?>
        </select>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('number'); ?>"><?php atom()->te('Max. number of tags to show:') ?></label>
        <input type="text" size="4" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" value="<?php if(isset($instance['number'])) echo esc_attr($instance['number']); ?>" />
      </p>

      <p class="clear-block">
        <div class="alignleft">
          <label for="<?php echo $this->get_field_id('smallest'); ?>"><?php atom()->te('Smallest:') ?></label>
          <input type="text" size="4" id="<?php echo $this->get_field_id('smallest'); ?>" name="<?php echo $this->get_field_name('smallest'); ?>" value="<?php if(isset($instance['smallest'])) echo esc_attr($instance['smallest']); ?>" /> <small>pt</small>
        </div>
        <div class="alignright">
          <input name="<?php echo $this->get_field_name('gradient_start'); ?>" class="color-selector" type="hidden" value="<?php echo esc_attr($instance['gradient_start']); ?>" />
        </div>
      </p>

      <p class="clear-block">
        <div class="alignleft">
          <label for="<?php echo $this->get_field_id('largest'); ?>"><?php atom()->te('Largest:') ?></label>
          <input type="text" size="4" id="<?php echo $this->get_field_id('largest'); ?>" name="<?php echo $this->get_field_name('largest'); ?>" value="<?php if(isset($instance['largest'])) echo esc_attr($instance['largest']); ?>" /> <small>pt</small>
        </div>
        <div class="alignright">
          <input name="<?php echo $this->get_field_name('gradient_end'); ?>" class="color-selector" type="hidden" value="<?php echo esc_attr($instance['gradient_end']); ?>" />
        </div>
      </p>
    </div>
    <?php
  }

}





/**
 * Atom Terms Widget
 *
 * Display a list of taxonomy terms
 *
 * @since 1.0
 *
 * @todo: test custom taxanomy dropdown
 */
class AtomWidgetTerms extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'          => 'terms',
      'title'       => atom()->t('Terms'),
      'description' => atom()->t('A list of taxonomy terms like categories or post tags'),
      'width'       => 500,
      'defaults'    => array(
        'title'                  => atom()->t('Categories'),
        'taxonomy'               => 'category',
        'type'                   => 'all',
        'root'                   => true,
        'dropdown'               => 0,
        'hierarchical'           => 1,
        'hide_empty'             => 1,
        'order_by'               => 'name',
        'depth'                  => 0,
        'exclude'                => '',
        'template'               => '',

        // internal settings
        'character_limit'        => 140,
        'allowed_tags'           => Atom::SAFE_INLINE_TAGS,
        'content_filter_more'    => '[&hellip;]',
      ),
      'templates'   => array(
        'full'  =>  "<a class=\"clear-block\" href=\"{URL}\" title=\"".atom()->t('View all posts filed under %s', '{NAME}')."\">\n"
                   ." <span class=\"tt\">{NAME} <strong>({POST_COUNT})</strong></span>\n"
                   ." <span class=\"c1\">{DESCRIPTION}</span>\n"
                   ."</a>",
      ),
    ));

  }



  // gets all term ancestors (if taxonomy is hierarchical)
  // part of the code snatched from: http://core.trac.wordpress.org/attachment/ticket/12443/get_ancestors.2.12443.diff
  // the complete function should be available in 3.1
  private function getAncestors($id = 0, $taxonomy = 'category') {
    $ancestors = array();
    if(is_taxonomy_hierarchical($taxonomy)){
      $term = get_term($id, $taxonomy);
      while(!is_wp_error($term) && !empty($term->parent) && !in_array($term->parent, $ancestors)){
        $ancestors[] = (int) $term->parent;
        $term = get_term($term->parent, $taxonomy);
      }
    }

    return $ancestors;
  }



  public function termEntry($term, $args, $depth){
    $description = convert_smilies(atom()->generateExcerpt(apply_filters('category_description', $term->description, $term), array(
      'limit'        => (int)$args['character_limit'],
      'allowed_tags' => $args['allowed_tags'],
      'more'         => $args['content_filter_more']
    )));

    $fields = array(
      'NAME'        => apply_filters('list_cats', esc_attr($term->name), $term),
      'URL'         => get_term_link($term, $term->taxonomy),
      'POST_COUNT'  => (int)$term->count,
      'DESCRIPTION' => $description,
      'ID'          => $term->term_id,
      'RSS'         => !empty($feed) ? get_term_feed_link($term->term_id, $term->taxonomy, $feed_type) : NULL
    );

    $fields = apply_filters('atom_widget_terms_keywords', $fields, $term, $args);

    return atom()->getBlockTemplate($args['template'], $fields);
  }



  public function widget($args, $instance){

    extract($args);

    $this->parseOptions($instance);

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    if($instance['type'] == 'sub' && is_taxonomy_hierarchical($instance['taxonomy'])){
      // only show on pages where the current category is set

      if(((($instance['taxonomy'] == 'category') && is_category()) || is_tax($instance['taxonomy'] || is_single())) === false)
        return atom()->log("Current category doesn't have children categories requested by {$args['widget_id']} ({$args['widget_name']}). Widget marked as inactive");

      // $root = get_query_var('cat');
      $active_term = get_queried_object();
      $ancestor = end($this->getAncestors($active_term->term_id, $instance['taxonomy']));

      $root = ($instance['root'] && $ancestor) ? $ancestor : $active_term->term_id;

      $term = get_term($root, $instance['taxonomy']);

      $title = '<a href="'.get_term_link($term, $instance['taxonomy']).'">'.$term->name.'</a>';
    }

    echo $before_widget;

    if($title)
      echo $before_title.$title.$after_title;

    $query_args = array(
      'child_of'               => isset($root) ? $root : 0,
      'orderby'                => $instance['order_by'],
      'hierarchical'           => $instance['hierarchical'] ? true : false,
      'taxonomy'               => $instance['taxonomy'],
      'hide_empty'             => $instance['hide_empty'] ? true : false,
      'title_li'               => '',
      'exclude'                => $instance['exclude'],
      'depth'                  => $instance['depth'],
      'walker'                 => $instance['dropdown'] ? '' : new AtomWalkerTerms(),
      'item_display_callback'  => array($this, 'termEntry'),
      'template'               => $instance['template'],
      'character_limit'        => $instance['character_limit'],
      'allowed_tags'           => $instance['allowed_tags'],
      'content_filter_more'    => $instance['content_filter_more'],
    );
    if($instance['order_by'] == 'count')
      $query_args['order'] = 'DESC';

    $tax = get_taxonomy($instance['taxonomy']);

    if($instance['dropdown'] ? true : false):
      $query_args['show_option_none'] = "{$tax->label}:";
      $query_args['class'] = 'wide';
      $query_args['name'] = "{$this->id}_terms";
      $query_args['id'] = "{$this->id}_terms";
      wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $query_args));
    ?>

    <script type='text/javascript'>
      /* <![CDATA[ */
      var dropdown = document.getElementById("<?php echo $this->id; ?>_terms");
      function onCatChange(){
       if (dropdown.options[dropdown.selectedIndex].value > 0)
        location.href = "<?php echo home_url(); ?>/?<?php echo ($instance['taxonomy'] == 'category' ? 'cat' : 'term_id'); ?>="+dropdown.options[dropdown.selectedIndex].value;
      }
      dropdown.onchange = onCatChange;
      /* ]]> */
    </script>

    <?php else: ?>
    <ul class="menu fadeThis">
      <?php wp_list_categories(apply_filters('widget_categories_args', $query_args)); ?>
    </ul>
    <?php
    endif;

    echo $after_widget;
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    extract($new_instance);

    return array(
      'title'        => esc_attr($title),
      'taxonomy'     => esc_attr($taxonomy),
      'type'         => esc_attr($type),
      'root'         => (bool)$root,
      'hierarchical' => (bool)$hierarchical,
      'dropdown'     => (bool)$dropdown,
      'hide_empty'   => (bool)$hide_empty,
      'order_by'     => esc_attr($order_by),
      'depth'        => (int)$depth,
      'exclude'      => esc_attr($exclude),
      'template'     => current_user_can('edit_themes') ? $template : $old_instance['template'],

    ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>
      <div class="high-priority-block">
        <p><strong><?php atom()->te('Display:'); ?></strong></p>
        <label for="<?php echo $this->get_field_id('type'); ?>_all">
          <input id="<?php echo $this->get_field_id('type'); ?>_all" name="<?php echo $this->get_field_name('type'); ?>" value="all" type="radio" <?php checked($instance['type'], 'all'); ?> />
          <?php atom()->te('All Terms'); ?>
        </label>
        <br />
        <label for="<?php echo $this->get_field_id('type'); ?>_sub">
          <input id="<?php echo $this->get_field_id('type'); ?>_sub" name="<?php echo $this->get_field_name('type'); ?>" value="sub" type="radio" <?php checked($instance['type'], 'sub'); ?> />
          <?php atom()->te('Children of the active term'); ?>
        </label>
        <br />
        <input style="margin-left: 20px;" id="<?php echo $this->get_field_id('root'); ?>" name="<?php echo $this->get_field_name('root'); ?>" type="checkbox" <?php checked(isset($instance['root']) ? $instance['root'] : 0); ?> rules="<?php echo $this->get_field_name('type'); ?>:sub" />
        <label for="<?php echo $this->get_field_id('root'); ?>"><?php atom()->te('Start from root'); ?></label>

        <?php /*/ child of @todo ?>
        <br />
        <label for="<?php echo $this->get_field_id('child'); ?>_child">
          <input id="<?php echo $this->get_field_id('child'); ?>_child" name="<?php echo $this->get_field_name('child'); ?>" value="child" type="radio" <?php checked($instance['child'], 'child'); ?> />
          <?php atom()->te('Children of'); ?>
        </label>
        <br />

        <?php
         $fc = atom()->getDropdown('category', array(
           'name' => $this->get_field_name('child_of'),
           'id' => $this->get_field_id('child_of'),
           'selected' => (int)$instance['child_of'],
           'show_option_all' => atom()->t('-- All categories --'),
           'hide_empty' => 0,
           'orderby' => 'name',
           'show_count' => 1,
           'hierarchical' => 1,
           'extra_attributes' => 'rules="'.$this->get_field_name('type').':child"',
         ));
        ?>

        <input style="margin-left: 20px;" id="<?php echo $this->get_field_id('root'); ?>" name="<?php echo $this->get_field_name('root'); ?>" type="checkbox" <?php checked(isset($instance['root']) ? $instance['root'] : 0); ?> rules="<?php echo $this->get_field_name('type'); ?>:child" />
        <label for="<?php echo $this->get_field_id('root'); ?>"><?php atom()->te('Start from root'); ?></label>
        <?php //*/ ?>

      </div>

      <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:'); ?></label>
       <input class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php atom()->te('Taxonomy:'); ?></label>
       <select class="wide" id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>">
        <?php foreach(get_taxonomies(array('public' => true)) as $taxonomy):
          $tax = get_taxonomy($taxonomy);
          if (empty($tax->labels->name)) continue; ?>
        <option value="<?php echo esc_attr($taxonomy) ?>" <?php selected($taxonomy, esc_attr($instance['taxonomy'])) ?>><?php echo $tax->labels->name; ?></option>
        <?php endforeach; ?>
       </select>
      </p>

      <p>
       <input type="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" <?php checked(isset($instance['dropdown']) ? (bool)$instance['dropdown'] : false); ?> />
       <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php atom()->te('Show as dropdown'); ?></label>
       <br />
       <input type="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>" <?php checked(isset($instance['hierarchical']) ? (bool)$instance['hierarchical'] : false); ?> />
       <label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php atom()->te('Hierarchical?'); ?></label>
       <br />
       <input type="checkbox" id="<?php echo $this->get_field_id('hide_empty'); ?>" name="<?php echo $this->get_field_name('hide_empty'); ?>" <?php checked(isset($instance['hide_empty']) ? (bool)$instance['hide_empty'] : false); ?> />
       <label for="<?php echo $this->get_field_id('hide_empty'); ?>"><?php atom()->te('Hide empty?'); ?></label>
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('order_by'); ?>"><?php atom()->te('Order by:'); ?></label>
       <select class="wide" id="<?php echo $this->get_field_id('order_by'); ?>" name="<?php echo $this->get_field_name('order_by'); ?>">
        <option value="name" <?php selected('name', esc_attr($instance['order_by'])) ?>><?php atom()->te("Name"); ?></option>
        <option value="count" <?php selected('count', esc_attr($instance['order_by'])) ?>><?php atom()->te("Post count, descending"); ?></option>
        <option value="ID" <?php selected('ID', esc_attr($instance['order_by'])) ?>><?php atom()->te("ID"); ?></option>
        <option value="slug" <?php selected('slug', esc_attr($instance['order_by'])) ?>><?php atom()->te("Slug"); ?></option>
       </select>
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php atom()->te('Exclude (IDs separated by comma):'); ?></label>
       <input class="wide" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo esc_attr($instance['exclude']); ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('depth'); ?>"><?php atom()->te('Depth:'); ?></label> <input type="text" value="<?php echo (int)$instance['depth']; ?>" name="<?php echo $this->get_field_name('depth'); ?>" id="<?php echo $this->get_field_id('depth'); ?>" class="wide" />
        <br />
        <small><?php atom()->te('0 = All levels'); ?></small>
      </p>

      <?php if(current_user_can('edit_themes')): ?>
      <strong><?php atom()->te('Template:'); ?></strong>
      <div class="user-template">
        <textarea class="wide code editor" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" rows="8" cols="28" data-mode="atom/html"><?php echo (empty($instance['template'])) ? format_to_edit($this->getTemplate()) : format_to_edit($instance['template']); ?></textarea>
        <small>
          <?php atom()->te('Read the %s to see all available keywords.', '<a href="'.Atom::THEME_DOC_URI.'" target="_blank">'.atom()->t('documentation').'</a>'); ?>
        </small>
      </div>
      <?php endif; ?>
    </div>
    <?php
  }

}





/**
 * Atom Text Widget.
 * TinyMCE code ideas from: http://wordpress.org/extend/plugins/black-studio-tinymce-widget/
 *
 * @since 1.0
 */
class AtomWidgetText extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'          => 'text',
      'title'       => atom()->t('Text'),
      'description' => current_user_can('edit_themes') ? atom()->t('Arbitrary text, HTML/PHP or shortcodes') : atom()->t('Arbitrary text, HTML or shortcodes'),
      'width'       => 500,
      'defaults'    => array(
        'title'   => '',
        'visual'  => false,
        'text'    => '',
        'php'     => false, // IMPORTANT: must be FALSE by default, otherwise blog owners from a MU setup can eval widget content by default!
      ),
    ));

    /*
    // only load if we're one of the theme settings pages -- @todo: need to improve this
    if(is_admin() && ($GLOBALS['pagenow'] === 'widgets.php')){
      add_action('admin_head',                 create_function('', 'wp_tiny_mce(false, array());'));
      add_action('admin_print_scripts',        create_function('', 'add_thickbox();wp_enqueue_script("media-upload");'));
      add_action('admin_print_styles',         create_function('', 'wp_enqueue_style("thickbox");'));
      add_action('admin_print_footer_scripts', create_function('', 'wp_preload_dialogs(array("plugins" => "wpdialogs,wplink"));'));
      add_filter('tiny_mce_before_init',       array($this, 'tiny_mce_config'));
      add_action('admin_footer',               array($this, 'js'));
    }
    */

  }

  function js(){
    ?>

    <script>
      /* <![CDATA[ */
      // this variable is necessary to handle media inserts into textarea (html mode)
      var edCanvas;

      jQuery(document).ready(function($){

        $('div.widget[id*="atom-text-"]').each(function(){

          var widget = $(this),
              editor = $('textarea', widget).attr('id'),
              editor_on = false,

              deactivate_editor = function(){
                if(editor_on){
                  //if(typeof(tinyMCE) == 'object' && typeof(tinyMCE.execCommand ) == 'function')
                  //  if(typeof(tinyMCE.get(editor)) == 'object')
                  tinyMCE.execCommand('mceRemoveControl', false, editor);
                  $('#' + editor).focus();

                  $('.text-options', widget).show();
                  editor_on = false;
                }
              },

              activate_editor = function(){
                if(!editor_on){
                  $('#' + editor).addClass('mceEditor');
                  tinyMCE.execCommand('mceAddControl', false, editor);
                  tinyMCE.execCommand('mceFocus', false, editor);
                  tinyMCE.execCommand('mceRepaint', editor);
                  $('.text-options', widget).hide();
                  editor_on = true;
                }
              };

          // fold/unfold widget options
          $('a.widget-action', widget).click(function(){
            if($('.set-visual', widget).hasClass('active'))
              activate_editor();
          });

          // widget save button
          $('.widget-control-save', widget).click(function(){

            if($('.set-visual', widget).hasClass('active')){
              $('#' + editor).val(tinyMCE.get(editor).getContent());
              deactivate_editor();
            }

            $(this).unbind('ajaxSuccess').ajaxSuccess(function(event, xhr, settings){

              // we need to update the ID, because the widget form contents were replaced
              editor = $('textarea', widget).attr('id');

              if($('.set-visual', widget).hasClass('active'))
                activate_editor();

            });

            return true;
          });

          // live events (inside the inner form which gets updated trough ajax)
          widget.delegate('.editor_media_buttons a', 'click', function(){
            // set edCanvas variable when adding from media library (necessary when used in HTML mode)
            edCanvas = $('#' + editor).get();

          });

          widget.delegate('.set-html, .set-visual', 'click', function(){
            if(!$(this).hasClass('active')){
              var is_visual = $(this).hasClass('set-visual') ? 1 : 0;
              $(this).addClass('active').parents('.editor_toggle_buttons').find('a').not(this).removeClass('active');
              $('input.visual_mode', widget).val(is_visual);
              is_visual ? activate_editor() : deactivate_editor();
            }

          });


        });

      });
      /* ]]> */
    </script>
    <?php
  }


  public function tiny_mce_config($config){

    // remove WP fullscreen mode and set the native tinyMCE fullscreen mode
    $plugins = explode(',', $config['plugins']);

    if(isset($plugins['wpfullscreen']))
      unset($plugins['wpfullscreen']);

    if(!isset($plugins['fullscreen']))
      $plugins[] = 'fullscreen';

    $config['plugins'] = implode(',', $plugins);

    // remove the "More" toolbar button
    $config['theme_advanced_buttons1'] = str_replace(',wp_more', '', $config['theme_advanced_buttons1']);

    return $config;
  }



  public function widget($args, $instance){
    extract($args);

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
    $text = apply_filters('widget_text', $instance['text'], $instance);
    echo $before_widget;
    if(!empty($title)) echo $before_title.$title.$after_title;

    // evaluate php code, if requested
    if($instance['php']):
      ob_start();
      eval("?>{$text}");
      $text = ob_get_clean();
    endif;
    ?>

    <div class="box textwidget <?php if(!empty($title)) echo sanitize_html_class(strtolower($title)); ?>"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
    <?php
    echo $after_widget;
  }




  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

     extract($new_instance);

     return array(
       'title'        => esc_attr($title),
       'visual'       => (bool)$visual,
       'text'         => current_user_can('unfiltered_html') ? $text : stripslashes(wp_filter_post_kses(addslashes($text))),
       'filter'       => (bool)$filter,
       'php'          => current_user_can('edit_themes') ? (bool)$php : $old_instance['php'],

     ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $title = esc_attr($instance['title']);
    $visual = (int)$instance['visual'];
    $text = esc_textarea($instance['text']);
    ?>

    <div <?php $this->formClass(); ?>>
      <input id="<?php echo $this->get_field_id('visual'); ?>" class="visual_mode" name="<?php echo $this->get_field_name('visual'); ?>" type="hidden" value="<?php echo (int)$visual; ?>" />
      <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:'); ?></label>
       <input class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
      </p>

      <?php /*
      <div class="editor_toggle_buttons hide-if-no-js">
        <a class="set-html <?php if(!$visual) echo 'active'; ?>">HTML</a>
        <a class="set-visual <?php if($visual) echo 'active'; ?>"><?php atom()->te('Visual'); ?></a>
      </div>


      <div class="editor_media_buttons hide-if-no-js">
        <?php do_action('media_buttons'); ?>
      </div>
      */ ?>


        <?php
//          wp_editor($text, 'text'.$this->get_field_id('text'));
//         remove_action('admin_print_footer_scripts', array('_WP_Editors', 'editor_js', 50));
//          _WP_Editors::editor_js();
        ?>
        <textarea class="widefat code" rows="16" cols="40" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>



      <p class="text-options">

       <label for="<?php echo $this->get_field_id('filter'); ?>">
         <input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />
         <?php atom()->te('Automatically add paragraphs'); ?>
       </label>

       <?php if(current_user_can('edit_themes')): ?>
       <br />
       <label for="<?php echo $this->get_field_id('php'); ?>">
         <input id="<?php echo $this->get_field_id('php'); ?>" name="<?php echo $this->get_field_name('php'); ?>" type="checkbox" <?php checked(isset($instance['php']) ? $instance['php'] : 0); ?> />
         <?php atom()->te('Execute code within %s tags',"<code>&lt;?php ?&gt;</code>"); ?>
       </label>
       <?php endif; ?>

      </p>

   </div> <?php
  }

}





/**
 * Atom Top Commenters widget
 *
 * A list of users who frequently comment on the site
 *
 * @since 1.0
 * @todo implement 'more' link
 * @todo add SWC support
 * @todo add fade to image links in avatar-only mode
 */
class AtomWidgetTopCommenters extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'          => 'top-commenters',
      'title'       => atom()->t('Top Commenters'),
      'description' => atom()->t('People who frequently comment the blog'),
      'width'       => 500,
      'defaults'    => array(
        'title'       => atom()->t('Top Commenters'),
        'number'      => 12,
        'mode'        => 'images',
        'avatar_size' => 72,
        'exclude'     => '',
        'template'    => '',
      ),
      'templates'   => array(
        'full'     =>  "<a id=\"{ID}\" class=\"clear-block\" href=\"{URL}\" rel=\"external nofollow\">\n"
                      ." {AVATAR}\n"
                      ." <span class=\"base\">\n"
                      ."   <span class=\"tt\">{NAME}</span>\n"
                      ."   <span class=\"c1\">{COMMENT_COUNT}</span>\n"
                      ." </span>\n"
                      ."</a>",

        'images'   =>  "<a id=\"{ID}\" class=\"clear-block tt\" href=\"{URL}\" title=\"{NAME} ({COMMENT_COUNT})\" rel=\"external nofollow\">\n"
                      ." {AVATAR}\n"
                      ."</a>",

        'brief'    =>  "<a id=\"{ID}\" class=\"clear-block\" href=\"{URL}\" title=\"{NAME} ({COMMENT_COUNT})\" rel=\"external nofollow\">\n"
                      ." <span class=\"base\">\n"
                      ."   <span class=\"tt\">{NAME}</span>\n"
                      ." </span>\n"
                      ."</a>",

        'detailed' =>  "<a id=\"{ID}\" class=\"clear-block\" href=\"{URL}\" title=\"{NAME}\" rel=\"external nofollow\">\n"
                      ." <span class=\"base\">\n"
                      ."   <span class=\"tt\">{NAME}</span>\n"
                      ."   <span class=\"c1\">{COMMENT_COUNT}</span>\n"
                      ." </span>\n"
                      ."</a>",
      ),
    ));


    // flush cache when comments are changed
    add_action('comment_post', array($this, 'flushCache'));
    add_action('transition_comment_status', array($this, 'flushCache'));
  }



  public function widget($args, $instance){
    global $wpdb;

    extract($args, EXTR_SKIP);

    // check for a cached instance and display it if we have it
    if($this->getAndDisplayCache($widget_id)) return;

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
    $output = $before_widget;

    $exclude_ids = $instance['exclude'] ? wp_parse_id_list($instance['exclude']) : array();

    $query = array();

    // query (idea) slightly based on "Top Commenters Gravatar" plugin - http://suhanto.net/top-commenters-gravatar-widget-wordpress/
    $query[] = "SELECT comment_author, comment_author_email, comment_author_url, comment_author_IP, user_id, count(1) as counter from {$wpdb->comments}";
    $query[] = "WHERE comment_approved = '1' AND comment_type != 'pingback'";

    if(!empty($exclude_ids))
      $query[] = 'AND user_id NOT IN('.implode(',', $exclude_ids).')';

    // group by (only the first two are used):
    //  - comment_author (name, default)
    //  - comment_author_email (same users may have different emails)
    //  - comment_author_url (people can have multiple sites)
    //  - comment_author_IP (really useless, lots of people have dynamic IPs)
    $query[] = 'GROUP BY comment_author, comment_author_email ORDER BY counter DESC, comment_date DESC LIMIT '.(int)$instance['number'];

    $query = implode(' ', $query);

    // check cache
    $key = md5($query);
    $cache = wp_cache_get('get_top_commenters', 'atom');
    if(!isset($cache[$key])){
      $commenters = $wpdb->get_results($query);
      $cache[$key] = $commenters;
      wp_cache_set('get_top_commenters', $cache, 'atom');

    }else{
      $commenters = $cache[$key];
    }

    if(!$commenters) return atom()->log("No relevant entries found in {$args['widget_id']} ({$args['widget_name']}). Widget marked as inactive");;

    if($title) $output .= $before_title.$title.$after_title;

    $output .= "<ul class=\"menu fadeThis top-commenters {$instance['mode']}\">";
    $template = ($instance['mode'] === 'template') ? $instance['template'] : $this->getTemplate($instance['mode']);
    $count = 0;
    foreach($commenters as $commenter):
      $count++;
      $comment_count = isset($commenter->counter) ? atom()->nt('%s comment', '%s comments', $commenter->counter, number_format_i18n($commenter->counter)) : '';
      //$author_has_url = !(empty($commenter->comment_author_url) || 'http://' == $commenter->comment_author_url);
      $output .= '<li>';

      $fields = array(
        'ID'             => 'a-'.uniqid(),
        'NAME'           => $commenter->comment_author,
        'AVATAR'         => atom()->getAvatar($commenter->comment_author_email, (int)$instance['avatar_size'], '', $commenter->comment_author),
        'URL'            => esc_url($commenter->comment_author_url),
        'KARMA'          => (int)get_user_meta($commenter->user_id, "karma", true),
        'COMMENT_COUNT'  => $comment_count,
        'EMAIL'          => esc_url($commenter->comment_author_email) // should not be used
      );

      $fields = apply_filters('atom_widget_top_commenters_keywords', $fields, $commenter, $args);

      // output template
      $output .= atom()->getBlockTemplate($template, $fields);

      $output .= '</li>';
    endforeach;
    $output .= '</ul>';

    $output .= $after_widget;

    echo $output;

    $this->addCache($widget_id, $output);
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    $this->FlushCache();
     extract($new_instance);

     return array(
       'title'        => esc_attr($title),
       'number'       => min(max((int)$number, 1), 50),
       'mode'         => esc_attr($mode),
       'avatar_size'  => (int)$avatar_size,
       'exclude'      => esc_attr($exclude),
       'template'     => current_user_can('edit_themes') ? $template : $old_instance['template'],

     ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:') ?></label>
        <input type="text" class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset($instance['title'])) echo esc_attr($instance['title']); ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('number'); ?>"><?php atom()->te('How many entries to display?') ?></label>
        <input type="text" size="3" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" value="<?php if (isset($instance['number'])) echo (int)$instance['number']; ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('avatar_size'); ?>"><?php atom()->te('Avatar Size:') ?></label>
        <input type="text" size="3" id="<?php echo $this->get_field_id('avatar_size'); ?>" name="<?php echo $this->get_field_name('avatar_size'); ?>" value="<?php if (isset($instance['avatar_size'])) echo (int)$instance['avatar_size']; ?>" /> <?php atom()->te('pixels'); ?>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php atom()->te('Exclude:'); ?></label>
        <input type="text" value="<?php echo esc_attr($instance['exclude']); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="wide" />
        <br />
        <small><?php atom()->te('User IDs, separated by commas.'); ?> <?php atom()->te('Enter "0" to exclude all unregistered commenters.'); ?></small>
      </p>

      <div class="template-selector clear-block">
        <div class="title"><?php atom()->te('Display mode') ?></div>
        <a href="#" class="select t-full" rel="full" title="<?php atom()->te('Full'); ?>"><?php atom()->te('Full'); ?></a>
        <a href="#" class="select t-detailed" rel="detailed" title="<?php atom()->te('Details'); ?>"><?php atom()->te('Details'); ?></a>
        <a href="#" class="select t-brief" rel="brief" title="<?php atom()->te('Brief'); ?>"><?php atom()->te('Brief'); ?></a>
        <a href="#" class="select t-images" rel="images" title="<?php atom()->te('Post thumbnails'); ?>"><?php atom()->te('Post thumbnails'); ?></a>
        <a href="#" class="select t-custom" rel="template" title="<?php atom()->te('Custom Template'); ?>"><?php atom()->te('Custom Template'); ?></a>
        <input class="select" type="hidden" value="<?php echo $instance['mode']; ?>" id="<?php echo $this->get_field_id('mode'); ?>" name="<?php echo $this->get_field_name('mode'); ?>" />
      </div>

      <?php if(current_user_can('edit_themes')): ?>
      <div class="user-template <?php if($instance['mode'] !== 'template') echo 'hidden'; ?>">
        <textarea class="wide code editor" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" rows="8" cols="28" data-mode="atom/html"><?php echo (empty($instance['template'])) ? format_to_edit($this->getTemplate()) : format_to_edit($instance['template']); ?></textarea>
        <small>
          <?php atom()->te('Read the %s to see all available keywords.', '<a href="'.Atom::THEME_DOC_URI.'" target="_blank">'.atom()->t('documentation').'</a>'); ?>
        </small>
      </div>
      <?php endif; ?>

    </div>
    <?php
  }

}





/**
 * Twitter updates
 *
 * @since 1.0
 * @todo maybe add 'show more' functionality; might be useless because of the 20 tweet limit
 */
class AtomWidgetTwitter extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    $default_twitter_user = 'stewiegriffin';

    // attempt to get the twitter user name from the twitter media setting
    if(atom()->options('media_twitter'))
      $default_twitter_user = basename(atom()->options('media_twitter'));

    // register the widget and it's options
    $this->set(array(
      'id'           => 'twitter',
      'title'        => atom()->t('Twitter'),
      'description'  => atom()->t('Your latest Twitter updates'),
      'ajax_control' => true,
      'defaults'     => array(
        'title'       => atom()->t('Tweets'),
        'user'        => $default_twitter_user,
        'count'       => 4,
        'info'        => true,
        'cache'       => 90,
      ),
    ));
  }



  public function ajaxControl(){
    if(atom()->request('get_twitter_data')){

      atom()->ajaxHeader();
      $this->displayTweets(esc_attr(strip_tags($_GET['widget_id'])), esc_attr(strip_tags($_GET['twituser'])), (int)$_GET['twitcount'], false, (bool)$_GET['showinfo']);
      exit;
    }
  }



  private function displayTweets($id, $user, $count, $data = false, $showinfo = true, $cache = 90){
    $error = false;

    if(!$data && (($data = get_transient($id)) === false)){
      if($showinfo){
        $response = wp_remote_retrieve_body(wp_remote_request("http://twitter.com/users/show/{$user}.json"));
        if(!is_array($userdata = json_decode($response, true))) $error = true;
      }
      $response = wp_remote_retrieve_body(wp_remote_request("http://twitter.com/statuses/user_timeline/{$user}.json"));
      if(!is_array($tweets = json_decode($response, true))) $error = true;

      if(!$error){
        if(isset($tweets['error'])){
          $error = esc_attr($tweets['error']);

        }else{
          $data = array();
          if($showinfo){
            $data['user']['profile_image_url'] = esc_url($userdata['profile_image_url']);
            $data['user']['name'] = esc_attr(strip_tags($userdata['name']));
            $data['user']['screen_name'] = esc_attr(strip_tags($userdata['screen_name']));
            $data['user']['followers_count'] = (int)$userdata['followers_count'];
            $data['user']['statuses_count'] = (int)$userdata['statuses_count'];
            $data['user']['description'] = esc_attr(strip_tags($userdata['description']));
          }

          $i = 0;
          foreach($tweets as $tweet){
            $data['tweets'][$i]['text'] = esc_attr(strip_tags($tweet['text']));

            $tweet_time = esc_attr(strip_tags($tweet['created_at']));
            $data['tweets'][$i]['created_at'] = abs(strtotime("{$tweet_time} UTC"));

            // unfortunately JSON_BIGINT_AS_STRING option doesn't seem to work with json_decode :(
            // we can just hope that the float conversion is right here...
            // $data['tweets'][$i]['id'] = esc_attr(strip_tags($tweet['id']));
            $data['tweets'][$i]['id'] = number_format($tweet['id'], 0, '.', '');
            $i++;
          }

          set_transient($id, $data, (int)$cache); // keep the data cached
        }
      }
    }


    if(!$error && is_array($data['tweets'])): ?>
     <?php if($showinfo): ?>
     <div class="info box clear-block">
       <div class="avatar">
         <img width="48" height="48" src="<?php echo $data['user']['profile_image_url']; ?>" alt="<?php echo $data['user']['name']; ?>" />
       </div>
       <div class="details">
         <a href="http://www.twitter.com/<?php echo $user; ?>/" title="<?php echo $data['user']['description']; ?>"><?php echo $data['user']['name']; ?> </a>
         <span class="followers"> <?php atom()->te('%s followers', $data['user']['followers_count']); ?></span>
         <span class="count"> <?php atom()->te('%s tweets', $data['user']['statuses_count']); ?></span>
       </div>
     </div>
     <?php endif; ?>
     <ul class="tweets box">
      <?php
        $i = 0;
        foreach($data['tweets'] as $tweet){
          $i++;
          $pattern = '/\@(\w+)/';
          $replace = '<a rel="nofollow" href="http://twitter.com/$1">@$1</a>';
          $tweet['text'] = preg_replace($pattern, $replace, $tweet['text']);
          $tweet['text'] = convert_smilies(make_clickable($tweet['text']));

          $link = "http://twitter.com/{$user}/statuses/{$tweet['id']}";
          echo '<li class="entry">'.$tweet['text'].'<a class="date" href="'.$link.'" rel="nofollow">'.atom()->getTimeSince($tweet['created_at'], time()).'</a></li>';
          if ($i == $count) break;
        }
      ?>
     </ul>

    <?php else:
      if(current_user_can('edit_theme_options')){
        echo '<div class="box error">';
        if(is_string($error)) atom()->te('Twitter returned error: %s', $error);
        else atom()->te('Could not retrieve tweets.');
        echo '</div>';

        if(!is_string($error)){
          echo '<p>'.atom()->t('Possible reasons:').'</p>';
          echo '<ul>';
          echo '<li>'.atom()->t("Your host doesn't allow outgoing connections").'</li>';
          echo '<li>'.atom()->t("Your server has made too many requests and is being limited (try increasing the cache value)").'</li>';
          echo '<li>'.atom()->t("The user has protected his/her tweets").'</li>';
          echo '<li>'.atom()->t("Twitter may be down").'</li>';
          echo '</ul';
        }
      }else{
        echo '<div class="box"><em>'.atom()->t('Unavailable for the moment').'</em></div>';

      }
    endif;
  }



  public function widget($args, $instance){
    extract($args);

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $block_id = "instance-{$this->id_base}-{$this->number}";

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
    $user = esc_attr(strip_tags($instance['user']));
    $count = (int)$instance['count'];

    $id = "instance-{$this->id}";

    echo $before_widget;
    if ($title) echo $before_title.$title.$after_title;

    echo '<div class="latest-tweets">';

    if(($data = get_transient($id)) === false && atom()->options('jquery')): ?>
      <div class="box loading"></div>
      <script>
        /* <![CDATA[ */
        jQuery(document).ready(function($){
          $.ajax({
            type: 'GET',
            url: atom_config.blog_url,
            data: { widget_id: '<?php echo $block_id; ?>',
                    twituser: '<?php echo $user; ?>',
                    twitcount: <?php echo $count; ?>,
                    showinfo: <?php echo (bool)$instance['info']; ?>,
                    atom: 'get_twitter_data'
                  },
            beforeSend: function() { },
            complete: function() { },
            success: function(response){
              var block = $('#<?php echo $block_id; ?>');
              $('.latest-tweets', block).html(response);

              <?php if(atom()->options('effects')): // animate list ?>
              $('.latest-tweets li', block).hide().each(function(i){
                 $(this).delay(333*i).animate(
                    {
                      "opacity": "show",
                      "height": "show",
                      "marginTop": "show",
                      "marginBottom": "show",
                      "paddingTop": "show",
                      "paddingBottom": "show"
                    },
                    { duration: 333,
                      step: function(now, fx){
                      $('.latest-tweets', block).parents('li.block .sections').height((block.height()) + 5);
                    }
                 });
              });
              <?php endif; ?>

            }
         });
        });
        /*]]>*/
      </script>

    <?php else:
      $this->displayTweets($id, $user, $count, $data, (bool)$instance['info'], (int)$instance['cache']);
    endif;

    echo '</div>';
    echo $after_widget;
  }




  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    delete_transient("instance-{$this->id}");
    extract($new_instance);

    return array(
      'title'  => esc_attr(strip_tags($title)),
      'user'   => esc_attr(strip_tags($user)),
      'count'  => min(max((int)$count, 1), 20),
      'info'   => (bool)$info,
      'cache'  => (int)$cache * 60, // turn into seconds (compatibility with Atom < 1.7)

    ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>
    <p>
     <label for="<?php echo $this->get_field_id('title'); ?>"> <?php atom()->te('Title:'); ?></label>
     <input class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
    </p>

    <p>
     <label for="<?php echo $this->get_field_id('user'); ?>"><?php atom()->te('%s user name:', 'Twitter'); ?></label>
     <input class="wide" id="<?php echo $this->get_field_id('user'); ?>" name="<?php echo $this->get_field_name('user'); ?>" type="text" value="<?php echo esc_attr($instance['user']); ?>" />
    </p>

    <p>
     <label for="<?php echo $this->get_field_id('count'); ?>"><?php atom()->te('How many entries to display?'); ?></label>
     <input size="3" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr($instance['count']); ?>" />
    </p>

    <p>
     <label for="<?php echo $this->get_field_id('info'); ?>">
     <input type="checkbox" id="<?php echo $this->get_field_id('info'); ?>" name="<?php echo $this->get_field_name('info'); ?>"<?php checked($instance['info']); ?> />
 	   <?php atom()->te('Show profile info'); ?></label>
    </p>

    <p>
     <?php
      $cache_input = '<input size="4" id="'.$this->get_field_id('cache').'" name="'.$this->get_field_name('cache').'" type="text" value="'.(round((int)$instance['cache'] / 60)).'" />';
     ?>
     <label for="<?php echo $this->get_field_id('cache'); ?>"><?php atom()->te('Keep data cached for %s minute(s)', $cache_input) ?></label>

    </p>

    </div>
    <?php
  }
}




/**
 * Atom Users Widget
 *
 * A list of Users (or Authors, depending on role selection)
 *
 * @since 1.0
 * @todo add user feed link option
 */


class AtomWidgetUsers extends AtomWidget{



  /**
   * Initialization
   *
   * @see AtomWidget::init and WP_Widget::__construct
   */
  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'           => 'users',
      'title'        => atom()->t('Users'),
      'description'  => atom()->t('A list of users/authors from your blog'),
      'width'        => 500,
      'ajax_control' => 'get_users',
      'defaults'     => array(
        'title'         => atom()->t('Authors'),
        'role'          => 'author',
        'mode'          => 'full',
        'avatar_size'   => 48,
        'sort_by'       => 'post_count',
        'hide_empty'    => true,
        'exclude'       => '',
        'number'        => 5,
        'more'          => true,
        'template'      => '',
      ),
      'templates'    => array(
        'full'     =>  "<a class=\"clear-block\" href=\"{URL}\" title=\"{NAME}\">\n"
                      ." {AVATAR}\n"
                      ." <span class=\"base\">\n"
                      ."   <span class=\"tt\">{ONLINE_STATUS} {NAME}</span>\n"
                      ."   <span class=\"c1\">{POST_COUNT}</span>\n"
                      ." </span>\n"
                      ."</a>",

        'images'   =>  "<a class=\"clear-block tt\" href=\"{URL}\" title=\"{NAME} ({POST_COUNT})\">\n"
                      ." {AVATAR}\n"
                      ."</a>",

        'brief'    =>  "<a class=\"clear-block\" href=\"{URL}\" title=\"{NAME} ({POST_COUNT})\">\n"
                      ." <span class=\"base\">\n"
                      ."   <span class=\"tt\">{NAME}</span>\n"
                      ." </span>\n"
                      ."</a>",

        'detailed' => "<a class=\"clear-block\" href=\"{URL}\" title=\"{NAME}\">\n"
                     ." <span class=\"base\">\n"
                     ."   <span class=\"tt\">{NAME}</span>\n"
                     ."   <span class=\"c1\">{POST_COUNT}</span>\n"
                     ." </span>\n"
                     ."</a>",
      ),
    ));

    // flush cache when posts or users change
    add_action('save_post',      array($this, 'flushCache'));
    add_action('deleted_post',   array($this, 'flushCache'));
    add_action('user_register',  array($this, 'flushCache'));
    add_action('delete_user',    array($this, 'flushCache'));
  }



  protected function getUsers($args, &$more, $offset = 0){
    extract($args);
    $users = get_users(array(
      //'blog_id'    => $GLOBALS['blog_id'],

      'role'         => $role,
      'exclude'      => wp_parse_id_list($exclude),
      'orderby'      => $sort_by,
      'order'        => $sort_by != 'display_name' ? 'DESC' : 'ASC',
      'offset'       => $offset,
      'number'       => $number + 1, // +1 because we need to find out if there are more results (to determine if 'show more' should be displayed)
      'count_total'  => false,
      'fields'       => 'all_with_meta', // it seems we get less queries if we retrieve meta too, instead of just array('ID', 'user_email', 'user_url', 'user_registered', 'display_name') ...wtf?
    ));

    // attempt to count each user's posts (heavy db usage here)
    // sort by post count should be handled by get_users()...
    $user_ids = array();

    foreach($users as $user)
      $user_ids[] = $user->ID;

    $user_post_count = count_many_users_posts($user_ids); // much faster than counting them individually
    foreach($users as $user)
      $user->post_count = $user_post_count[$user->ID];  // $user->getCount() uses it if available

    $output = '';
    $count = 1;
    $more = (count($users) > $number) ? true : false;
    $template = ($mode != 'template') ? $this->getTemplate($mode) : $template;
    foreach($users as $user){

      $user = new AtomObjectUser($user);

      if($count++ == $number + 1) break; // -1 (see above)

      $output .= '<li>';
      $title = ($mode === 'images' || $mode === 'brief') ? atom()->t('%1$s (%2$s posts)', $user->getName(), $user->getPostCount()) : atom()->t('Posts by %s', $user->getName());

      $fields = array(
        'NAME'          => $user->getName(),
        'URL'           => $user->getPostsURL(),
        'AVATAR'        => $user->getAvatar((int)$avatar_size),
        'KARMA'         => $user->getKarma(),
        'POST_COUNT'    => atom()->nt('%s post', '%s posts', $user->getPostCount(), number_format_i18n($user->getPostCount())),
        'ONLINE_STATUS' => $user->isOnline() ? '<span class="online"></span>' : '<span class="offline"></span>',

         // not used by default
        'REGISTERED'    => atom()->getTimeSince(abs(strtotime($user->get('user_registered').' GMT'))),
        'RSS'           => $user->getFeedURL(),
        'ID'            => $user->get('ID'),
        'EMAIL'         => $user->get('user_email'),
        'SITE_URL'      => $user->get('user_url'),

        // meta
        'BIO'           => $user->getDescription(),
        'ROLE'          => $user->getRole(),
        'AIM'           => $user->get('aim'),
        'YIM'           => $user->get('yim'),
        'JABBER'        => $user->get('jabber'),

      );

      $fields = apply_filters('atom_widget_users_keywords', $fields, $user, $args);

      // output template
      $output .= atom()->getBlockTemplate($template, $fields);

      $output .= '</li>';
    }

    return $output;
  }



  public function widget($args, $instance){
    extract($args);

    // check for a cached instance and display it if we have it
    if($this->getAndDisplayCache($widget_id)) return;

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    $users = $this->getUsers($instance, $next);
    if(empty($users))
      return atom()->log("No &lt;{$instance['role']}&gt; users found in {$args['widget_id']} ({$args['widget_name']}). Widget marked as inactive");

    $maybe_more_link = '';

    if(empty($instance['role']) && ($page_template = atom()->getPageByTemplate('users')))
      $maybe_more_link = ' <small><a href="'.atom()->post($page_template)->getURL().'">'.atom()->t('Show All').'</a></small>';

    $output = $before_widget;

    if($title) $output .= $before_title.$title.$maybe_more_link.$after_title;

    $output .= "<ul class=\"menu fadeThis users clear-block {$instance['mode']}\">\n{$users}\n</ul>\n";

    if($instance['more'] && $next && atom()->options('jquery'))
      $output .= $this->getMoreLink($instance['number'], 'get_users');

    $output .= $after_widget;

    echo $output;

    $this->addCache($widget_id, $output);
  }



  /**
   * Saves the widget options
   *
   * @see WP_Widget::update
   */
  public function update($new_instance, $old_instance){

    $this->FlushCache();
    extract($new_instance);

    return array(
      'title'         => esc_attr($title),
      'role'          => esc_attr($role),
      'mode'          => esc_attr($mode),
      'sort_by'       => esc_attr($sort_by),
      'exclude_admin' => (bool)$exclude_admin,
      'avatar_size'   => (int)$avatar_size,
      'number'        => min(max((int)$number, 1), 100),
      'exclude'       => esc_attr($exclude),
      'more'          => (bool)$more,
      'template'      => current_user_can('edit_themes') ? $template : $old_instance['template'],

    ) + $old_instance;

  }




  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $wp_roles = new WP_Roles();
    ?>
    <div <?php $this->formClass(); ?>>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:') ?></label>
        <input type="text" class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset($instance['title'])) echo esc_attr($instance['title']); ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('role'); ?>"><?php atom()->te('Role') ?></label>
        <select id="<?php echo $this->get_field_id('role'); ?>" name="<?php echo $this->get_field_name('role'); ?>">
           <option value="" <?php selected(empty($instance['role'])); ?>><?php atom()->te('-- Any --'); ?></option>
           <?php foreach($wp_roles->role_names as $role => $label): ?>
           <option value="<?php echo $role; ?>"  <?php selected($instance['role'], $role) ?>><?php echo $label; ?></option>
           <?php endforeach; ?>
        </select>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('sort_by'); ?>"><?php atom()->te('Sort by') ?></label>
        <select id="<?php echo $this->get_field_id('sort_by'); ?>" name="<?php echo $this->get_field_name('sort_by'); ?>">
         <option value="display_name" <?php selected($instance['sort_by'], "display_name"); ?>><?php atom()->te("Name"); ?></option>
         <option value="post_count" <?php selected($instance['sort_by'], "post_count"); ?>><?php atom()->te("Post count, descending"); ?></option>
         <option value="registered" <?php selected($instance['sort_by'], "registered"); ?>><?php atom()->te("Date registered, newest first"); ?></option>
        </select>
      </p>


      <?php /* @todo: find a way to re-implement this (excludes must be done within get_users()
      <p>
        <label for="<?php echo $this->get_field_id('hide_empty'); ?>">
          <input type="checkbox" id="<?php echo $this->get_field_id('hide_empty'); ?>" name="<?php echo $this->get_field_name('hide_empty'); ?>"<?php checked($instance['hide_empty']); ?> />
          <?php atom()->te('Only get users with posts'); ?>
        </label>
      </p>
      */
      ?>

      <p>
        <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php atom()->te('Exclude:'); ?></label>
        <input type="text" value="<?php echo esc_attr($instance['exclude']); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="wide" />
        <br />
        <small><?php atom()->te('User IDs, separated by commas.'); ?></small>
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('number'); ?>"><?php atom()->te('How many entries to display?'); ?></label>
       <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php if (isset($instance['number'])) echo (int)$instance['number']; ?>" size="3" />
      </p>

      <p>
       <input <?php if(!atom()->options('jquery')) echo "disabled=\"disabled\""; ?> type="checkbox" id="<?php echo $this->get_field_id('more'); ?>" name="<?php echo $this->get_field_name('more'); ?>"<?php checked($instance['more']); ?> />
       <label for="<?php echo $this->get_field_id('more'); ?>" <?php if(!atom()->options('jquery')) echo "class=\"disabled\""; ?>><?php atom()->te('Display %s Link', '<code>'.atom()->t('Show More').'</code>'); ?></label>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('avatar_size'); ?>"><?php atom()->te('Avatar Size:') ?></label>
        <input type="text" size="3" id="<?php echo $this->get_field_id('avatar_size'); ?>" name="<?php echo $this->get_field_name('avatar_size'); ?>" value="<?php if (isset($instance['avatar_size'])) echo esc_attr($instance['avatar_size']); ?>" /> <?php atom()->te('pixels'); ?>
      </p>

      <div class="template-selector clear-block">
        <div class="title"><?php atom()->te('Display mode') ?></div>
        <a href="#" class="select t-full" rel="full" title="<?php atom()->te("Full"); ?>"><?php atom()->te("Full"); ?></a>
        <a href="#" class="select t-detailed" rel="detailed" title="<?php atom()->te("Details"); ?>"><?php atom()->te("Details"); ?></a>
        <a href="#" class="select t-brief" rel="brief" title="<?php atom()->te("Brief"); ?>"><?php atom()->te("Brief"); ?></a>
        <a href="#" class="select t-images" rel="images" title="<?php atom()->te("Avatar thumbnails"); ?>"><?php atom()->te("Avatars thumbnails"); ?></a>
        <a href="#" class="select t-custom" rel="template" title="<?php atom()->te("Custom Template"); ?>"><?php atom()->te("Custom Template"); ?></a>
        <input class="select" type="hidden" value="<?php echo $instance['mode']; ?>" id="<?php echo $this->get_field_id('mode'); ?>" name="<?php echo $this->get_field_name('mode'); ?>" />
      </div>

      <?php if(current_user_can('edit_themes')): ?>
      <div class="user-template <?php if($instance['mode'] !== 'template') echo 'hidden'; ?>">
        <textarea class="wide code editor" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" rows="8" cols="28" data-mode="atom/html"><?php echo (empty($instance['template'])) ? format_to_edit($this->getTemplate()) : format_to_edit($instance['template']); ?></textarea>
        <small>
          <?php atom()->te('Read the %s to see all available keywords.', '<a href="'.Atom::THEME_DOC_URI.'" target="_blank">'.atom()->t('documentation').'</a>'); ?>
        </small>
      </div>
      <?php endif; ?>
    </div>
    <?php
  }
}







/**
 * bbPress widget
 *
 * Replaces default widgets
 *
 * @since 1.0
 * @todo test cache
 * @todo test the comment content filter
 *
class AtomWidgetBBPress extends AtomWidget{



  public function __construct(){

    // register the widget and it's options
    $this->set(array(
      'id'          => 'bbpress',
      'title'       => atom()->t('bbPress'),
      'description' => atom()->t('List bbPress forums, topics or replies'),
      'width'       => 500,
      'defaults'    => array(
        'title'               => atom()->t('Forum Activity'),
        'number'              => 5,
        'character_count'     => 140,
        'avatar_size'         => 48,
        'more'                => true,
        'template'            => '',
        'allowed_tags'        => Atom::SAFE_INLINE_TAGS,  // internal
        'content_filter_more' => '[&hellip;]',            // internal
      ),
      'templates'   => array(
        '<a class="clear-block" href="{URL}" title="'.atom()->t('on %s', '{TITLE}').'">',
        '  {AVATAR}',
        '  <span class="base">',
        '  <span class="tt">{AUTHOR}</span>',
        '  <span class="c1">{CONTENT}</span>',
        '  <span class="c2">{DATE}</span>',
        '</span>',
        '</a>',
      ),
    ));

    atom()->add('requests',                   array($this, 'ajax'));

    // flush cache when comments are changed
    add_action('comment_post',              array($this, 'flushCache'));
    add_action('transition_comment_status', array($this, 'flushCache'));
  }


  private function getComments($args, &$more, $offset = 0){
    extract($args);

    $app = &Atom::app();

    $query = $app->get('swc') ? new AtomSWCommentQuery(): new WP_Comment_Query();

    $comments = $query->query(array(
      'number' => $number + 1,
      'status' => 'approve',
      'offset' => (int)$offset,
    ));

    $more = (count($comments) > $number) ? true : false;
    $count = 1;
    $output = '';
    $template = $args['template'] ? $args['template'] : $this->getTemplate();

    foreach((array)$comments as $comment){

      if($count++ == $number + 1) break;
      $output .= '<li>';

      $app->setCurrentComment($comment);

      $fields = array(
        'TITLE'     => $site_wide ? strip_tags($comment->post_title) : get_the_title($comment->comment_post_ID),
        'URL'       => $app->comment->getURL(),
        'AVATAR'    => $app->comment->getAvatar(),
        'AUTHOR'    => $comment->comment_author,
        'CONTENT'   => convert_smilies($app->generateExcerpt($comment->comment_content, array(
                         'limit'         => $character_count,
                         'allowed_tags'  => $allowed_tags,
                         'more'          => $content_filter_more
          ))),
        'DATE'      => $app->comment->getDate('relative'),
        'EMAIL'     => esc_url($comment->comment_author_email), // should not be used
        'ID'        => $app->comment->getID(),
      );


      $fields = apply_filters('atom_widget_recent_comments_keywords', $fields, $comment, $args);

      // output template
      $output .= $app->getBlockTemplate($template, $fields);

      $output .= '</li>';
    }

    return $output;
  }



  public function widget($args, $instance){
    extract($args, EXTR_SKIP);

    // check for a cached instance and display it if we have it
    if($this->getAndDisplayCache($widget_id)) return;

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    $comments = $this->getComments($instance, $next);
    if(!$comments) return atom()->log("No comments were found in {$widget_id} ({$widget_name}). Widget marked as inactive");;;

    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    $output = $before_widget;
    if ($title) $output .= $before_title.$title.$after_title;

    $output .= "<ul class=\"menu fadeThis full recent-comments\">{$comments}</ul>";

    if($instance['more'] && $next && atom()->options('jquery'))
      $output .= $this->getMoreLink($instance['number'], 'get_recent_comments');

    $output .= $after_widget;

    echo $output;

    $this->addCache($widget_id, $output);
  }


  public function update($new_instance, $old_instance){

     $this->FlushCache();
     extract($new_instance);

     return array(
      'title'           => esc_attr($title),
      'number'          => min(max((int)$number, 1), 50),
      'character_count' => (int)$character_count,
      'avatar_size'     => (int)$avatar_size,
      'more'            => (bool)$more,
      'template'        => current_user_can('edit_themes') ? $template : $old_instance['template'],
      'site_wide'       => atom()->get('swc') ? $site_wide : $old_instance['site_wide'],

     ) + $old_instance;

  }



  public function form($instance){

    // merge default widget options with active widget options
    $this->parseOptions($instance);

    ?>
    <div <?php $this->formClass(); ?>>

      <div class="high-priority-block">
         <label for="<?php echo $this->get_field_id('what'); ?>"><?php atom()->te('Show:'); ?></label>
         <select id="<?php echo $this->get_field_id('what'); ?>" name="<?php echo $this->get_field_name('what'); ?>">
          <option value="forums" <?php selected('forums', esc_attr($instance['what'])) ?>><?php atom()->te("Forum list"); ?></option>
          <option value="topics" <?php selected('topics', esc_attr($instance['what'])) ?>><?php atom()->te("Recent topic activity"); ?></option>
          <option value="replies" <?php selected('replies', esc_attr($instance['what'])) ?>><?php atom()->te("Latest replies"); ?></option>
         </select>
      </div>

      <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php atom()->te('Title:'); ?></label>
       <input class="wide" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php if (isset($instance['title'])) echo esc_attr($instance['title']); ?>" />
      </p>

      <p>
       <label for="<?php echo $this->get_field_id('number'); ?>"><?php atom()->te('How many entries to display?'); ?></label>
       <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php if (isset($instance['number'])) echo (int)$instance['number']; ?>" size="3" />
      </p>

      <p>
       <?php $input = '<input id="'.$this->get_field_id('character_count').'" name="'.$this->get_field_name('character_count').'" type="text" value="'.(isset($instance['character_count']) ? (int)$instance['character_count'] : '').'" size="3" />'; ?>
       <label for="<?php echo $this->get_field_id('character_count'); ?>"><?php atom()->te('Content has max %s characters', $input); ?></label>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('avatar_size'); ?>"><?php atom()->te('Avatar Size:') ?></label>
        <input type="text" size="3" id="<?php echo $this->get_field_id('avatar_size'); ?>" name="<?php echo $this->get_field_name('avatar_size'); ?>" value="<?php if (isset($instance['avatar_size'])) echo (int)$instance['avatar_size']; ?>" /> <?php atom()->te('pixels'); ?>
      </p>

      <p>
       <input <?php if(!atom()->options('jquery')) echo "disabled=\"disabled\""; ?> type="checkbox" id="<?php echo $this->get_field_id('more'); ?>" name="<?php echo $this->get_field_name('more'); ?>"<?php checked($instance['more']); ?> />
       <label for="<?php echo $this->get_field_id('more'); ?>" <?php if(!atom()->options('jquery')) echo "class=\"disabled\""; ?>><?php atom()->te('Display %s Link', '<code>'.atom()->t('Show More').'</code>'); ?></label>
      </p>

      <?php if(current_user_can('edit_themes')): ?>
      <br />
      <strong><?php atom()->te('Template:'); ?></strong>
      <div class="user-template">
        <textarea class="wide code editor" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" rows="8" cols="28" data-mode="atom/html"><?php echo (empty($instance['template'])) ? format_to_edit($this->getTemplate()) : format_to_edit($instance['template']); ?></textarea>
        <small>
          <?php atom()->te('Read the %s to see all available keywords.', '<a href="'.Atom::THEME_DOC_URI.'" target="_blank">'.atom()->t('documentation').'</a>'); ?>
        </small>
      </div>
      <?php endif; ?>
    </div>
    <?php
  }

}
*/