<?php
/*
 * Shortcodes.
 *
 * Read the documentation for more info: http://digitalnature.eu/docs/
 *
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */



class AtomShortcodes{


  public static function register(){

    // allow [shortcodes] in Text Widgets
    if(!is_admin())
      add_filter('widget_text', 'do_shortcode', 11);

    // change do_shortcode priority to 9 to disable WP formatting -- @todo: test this, it seems to be screwing with the autop filter
    //if(remove_filter('the_content','do_shortcode', 11)) add_filter('the_content','do_shortcode', 9);
    //remove_filter('the_content','shortcode_unautop');

    $shortcodes = get_class_methods(__CLASS__);
    array_shift($shortcodes);

    $shortcodes = atom()->getContextArgs('atom_shortcodes', $shortcodes);

    foreach($shortcodes as $tag){
      $our_callback = array(__CLASS__, $tag);

      if(atom()->ShortcodeExists($tag, $our_callback)){
        atom()->log("The theme [{$tag}] shortcode was disabled because the tag has already been registered by a plugin.", 1);
        continue;
      }

      add_shortcode($tag, $our_callback);
    }

  }



  // @todo: remove this nonsense and add a real column editor within the post editor
  public static function column($atts, $content = null){

    if(!isset($atts[0]))
      return '<div class="error box"><strong>[column]</strong> '.atom()->t('Please specify the column number / total column count').'</div>';

    list($index, $count) = array_map('intval', explode('/', $atts[0]));

    $classes = array('col');
    $classes[] = "c-{$count}";
    if($index == 1) $classes[] = 'first'; elseif($index == $count) $classes[] = 'last';

    $output = '';

    if($index == 1)
      $output .= '<div class="clear-block">';

    $output .= '<div class="'.implode(' ', $classes).'" '.(isset($atts['width']) ? 'style="width:'.esc_attr($atts['width']).';"' : null).'><div class="cc">'.$content.'</div></div>';

    if($index == $count)
      $output .= '</div>';

    return
      do_shortcode($output);
  }




  /**
   * Output a link. So far, valid attributes are:
   * - wp (wordpress.org link)
   * - login (login/or dashboard)
   * - rss (feed url)
   * - html validation link
   * - css (css validation, useless, css is not seen as valid because of browser-specific properties)
   * - theme (theme uri from style.css)
   *
   * usage examples:
   *   [link rss]
   *   [link login]
   *   [link wp]
   *
   * @since 1.2
   *
   * @param mixed $atts Shortcode attributes
   * @return string The Link (HTML output)
   */
  public static function link($atts){
    $links = array(
      'wp'       => array('url'   => 'http://WordPress.org/',
                          'title' => 'WordPress',
                          'rel'   => 'generator',
                          'class' => 'wp-link'
                         ),

      'rss'      => array('url'   => get_bloginfo('rss2_url'),
                          'title' => atom()->t('RSS Feeds'),
                          'rel'   => 'feed',
                          'class' => 'rss'
                         ),

      'login'    => array('url'   => is_user_logged_in() ? admin_url() : wp_login_url(),
                          'title' => is_user_logged_in() ? atom()->t('Dashboard') : atom()->t('Log in'),
                          'rel'   => 'login',
                          'class' => 'login-link'
                         ),

      'theme'    => array('url'   => atom()->get('theme_uri'),
                          'title' => atom()->get('theme_name'),
                          'rel'   => 'theme',
                          'class' => 'theme-link'
                         ),

      'html'     => array('url'   => 'http://validator.w3.org/check?uri=referer',
                          'title' => 'HTML 5',
                          'rel'   => 'external',
                          'class' => 'valid-html'
                         ),

      'css'      => array('url'   => 'http://jigsaw.w3.org/css-validator/check/referer?profile=css3',
                          'title' => 'CSS 3.0',
                          'rel'   => 'external',
                          'class' => 'valid-css'
                         ),
     );

    if(!isset($atts[0]) || !array_key_exists($atts[0], $links))
      return atom()->log("[link] Invalid link attribute.");

    $link = $links[$atts[0]];
    return "<a class=\"{$link['class']}\" rel=\"{$link['rel']}\" href=\"{$link['url']}\">{$link['title']}</a>\n";
  }



 /*
  * Allows the user to insert widgets into areas that allow shortcodes
  * inspired by http://webdesign.anmari.com/shortcode-any-widget by Anna-marie Redpath
  * usage examples:
  *   [widget "ID"]
  *   [widget "Widget Name"]
  *   [widget "Widget Name" number=4]
  *
  * ID should be provided in the Widgets Dashboard page
  *
  * @since    1.0
  * @global   array $wp_registered_widgets   Registered widgets
  * @param    mixed $atts                    Shortcode attributes
  * @return   string                         The Widget (HTML output)
  */
  public static function widget($atts){
    global $wp_registered_widgets;
    extract(shortcode_atts(array(
      'number'  => false,          // only taken in consideration if the 1st argument is the "Widget Name" (not the hashed ID)
      'title'   => true,           // show titles?
      'area'    => 'arbitrary'     // sidebar to search, shoud not be changed because it might actually kill puppies
    ), $atts));

    // get 1st parameter (assuming this is the target widget id or name)
    if(!empty($atts[0]))
      $widget = esc_attr($atts[0]);

    else
      return atom()->log("[widget {$widget}] No valid widget name/or id provided");

    $sidebar = esc_attr($area);
    $number = (int)$number;

    $found = false;
    $possible_matches = array();
    $sidebars_widgets = wp_get_sidebars_widgets();
    if((empty($sidebars_widgets[$sidebar]) || empty($wp_registered_widgets)) && current_user_can('edit_themes'))
      return atom()->log("[widget {$widget}] No active widget in {$sidebar} area");

    // assuming we get the md5 hashed ID
    foreach ($wp_registered_widgets as $i => $w)
      if($widget == substr(md5($w['id']), 0, 8)){
        $found = $w['id']; // real widget ID

      // compare widget names as well, and build a array with the possible widget matches array
      // (which is used later if ID match fails)
      }elseif($widget == $w['name']){
        $possible_matches[] = $w['id'];

      }

    // nothing found, assume it's the "Widget Name".
    if(!$found){
      $valid_matches = array();
      foreach($sidebars_widgets[$sidebar] as $i => $w)
        foreach($possible_matches as $id)
          if($id == $w) $valid_matches[] = $w;

      if(!empty($valid_matches)) $found = $number ? $valid_matches[$number - 1] : $found = $valid_matches[0];
    }

    $remove_title = !$title ? 'h3' : false;
    if($found && $output = atom()->getWidget($found, $sidebar, $remove_title)) return $output;
    //else return current_user_can('edit_themes') ? '<p class="box error">'.'<strong>[Widget]</strong> '.atom()->t("Widget instance not found (%s)", esc_attr($atts[0])).'</p>' : false;
  }



 /*
  * Output posts filtered by specific attributes
  *
  * Examples:
  *   List 10 most commented posts using the teaser template:
  *   [query posts_per_page=10 order="comment_count" template="teaser"] ... [/query]
  *
  *   List featured posts using a defined template
  *   [query featured=1 order="comment_count"] {TITLE} ({COMMENT_COUNT} comments) [/query]
  *
  * @since   1.0
  * @todo    add pagination
  * @todo    add more variables for user defined templates
  * @todo    handle array type arguments (explode commas?)
  * @param   mixed $atts
  * @param   string $content
  * @return  string
  */
  public static function query($atts, $content = null){

    // theme (non-wpquery) arguments
    $args = array(
      'template'         => '',   // use custom template, for eg. "teaser"
      'content_limit'    => 40,
      'thumbnail_size'   => 'post-thumbnail',
      'date_mode'        => 'relative',
      'featured'         => false,
    );

    $all_args = shortcode_atts(array_merge(array(
     // a few wp_query arguments
     'ignore_sticky_posts' => 1,                      // no sticky posts at the top by default
     'post__not_in'        => get_option('sticky_posts'), // no sticky posts in the results by default

    ), $args), $atts);

    extract($all_args);

    $query = array_merge($atts, $all_args);

    // filter out non-wpquery arguments
    foreach($args as $key => $value)
      unset($query[$key]);

    // get the featured post IDs if "featured" argument is true
    if($featured && ($featured_ids = get_option('featured_posts')))
      $query['post__in'] = wp_parse_id_list($featured_ids);

    $output = '';
    ob_start();

    $posts = new WP_Query($query);

    if($posts->have_posts())
      while($posts->have_posts()){
        $posts->the_post();

        atom()->setCurrentPost(false);

        if(!empty($template)){ // use internal template ?
          atom()->template(esc_attr($template), false);

        }else{ // use shortcode-defined template
          echo atom()->getBlockTemplate($content, array(
            'URL'              => atom()->post->getURL(),
            'TITLE'            => atom()->post->getTitle(),
            'AUTHOR'           => atom()->post->author->getName(),
            'AUTHOR_URL'       => atom()->post->author->getPostsURL(),
            'DATE'             => atom()->post->getDate($date_mode),
            'THUMBNAIL'        => atom()->post->getThumbnail($thumbnail_size),
            'TAGS'             => strip_tags(atom()->post->getTerms('post_tag'), ', '),
            'CATEGORIES'       => strip_tags(atom()->post->getTerms('category', ', ')),
            // @todo: I need to somehow add support for custom taxonomies
            'CONTENT'          => atom()->post->getContent($content_limit),
            'COMMENT_COUNT'    => atom()->post->getCommentCount(),
            'CURRENT_POST'     => $posts->current_post + 1,
          ));

        }
      }

    atom()->resetCurrentPost();
    $output = ob_get_clean();

    return $output ? "<div class=\"posts clear-block\">\n{$output}\n</div>" : atom()->log("[query] No matching posts found.");
  }



 /*
  * Member only content
  * based on: http://justintadlock.com/archives/2009/05/09/using-shortcodes-to-show-members-only-content
  * example: [member] text [/member]
  *
  * @since    1.0
  * @param    mixed $atts       Shortcode attributes
  * @param    string $content   Content to output
  * @return   string            Content after conditional check
  */
  public static function member($atts, $content = null){

    if(is_user_logged_in() && !is_null($content))
      return do_shortcode($content);

    return '';
  }




 /*
  * Visitor only content
  * based on: http://justintadlock.com/archives/2009/05/09/using-shortcodes-to-show-members-only-content
  * example: [visitor] text [/visitor]
  *
  * @since    1.0
  * @param    mixed $atts       Shortcode attributes
  * @param    string $content   Content to output
  * @return   string            Content after conditional check
  */
  public static function visitor($atts, $content = null){

    if(!is_user_logged_in() && !is_null($content))
      return do_shortcode($content);

    return '';
  }



  /**
   * Output a TinyURL link
   * example: [tinyurl url=http://google.com title="Google Homepage" rel=nofollow]
   *
   * @since 1.0
   *
   * @param mixed $atts Shortcode attributes
   * @return string The tinyURL link
   */
  public static function tinyurl($atts){

    extract(shortcode_atts(array(
      'url'   => '',
      'title' => '',
      'rel'   => 'nofollow'
    ), $atts));

    if(!$title)
      $title = $url;

    return '<a href="'.esc_attr(atom_get_tinyurl($url)).'" rel="'.esc_attr($rel).'">'.esc_attr($title).'</a>';
  }



  /**
   * Get the Google PageRank of a URL
   * Google Toolbar 3.0.x/4.0.x Pagerank Checksum Algorithm by http://pagerank.gamesaga.net
   * example: [pagerank url=http://digitalnature.eu]
   *
   * @since 1.0
   *
   * @param mixed $atts Shortcode attributes
   * @return string The HTML formatted page rank
   */
  public static function pagerank($atts){
    if(!function_exists('ASPCheckHash')){
      function ASPCheckHash($hashnum){
        $checkbyte = 0;
        $flag = 0;
        $hashstr = sprintf('%u', $hashnum);
        $length = strlen($hashstr);
        for($i = $length - 1;  $i >= 0;  $i--){
          $re = $hashstr{$i};
          if(($flag % 2) === 1){
            $re += $re;
            $re = (int)($re / 10) + ($re % 10);
          }
          $checkbyte += $re;
          $flag++;
        }

        $checkbyte %= 10;

        if($checkbyte !== 0){
          $checkbyte = 10 - $checkbyte;

          if(($flag % 2) === 1){

            if(($checkbyte % 2) === 1)
              $checkbyte += 9;

            $checkbyte >>= 1;
          }
        }

        return "7{$checkbyte}{$hashstr}";
      }
    }

    if(!function_exists('ASPStrToNum')){
      function ASPStrToNum($str, $check, $magic){
        $int32unit = 4294967296;  // 2^32
        $length = strlen($str);
        for($i = 0; $i < $length; $i++){
          $check *= $magic;
          // If the float is beyond the boundaries of integer (usually +/- 2.15e+9 = 2^31),
          // the result of converting to integer is undefined
          // refer to http://www.php.net/manual/en/language.types.integer.php
          if($check >= $int32unit){

            $check = ($check - $int32unit * (int)($check / $int32unit));

            //if the check less than -2^31
            $check = ($check < -2147483648) ? ($check + $int32unit) : $check;
          }
          $check += ord($str{$i});
        }
        return $check;
      }
    }

    if(!function_exists('ASPHashURL')){
      function ASPHashURL($string){

        $check1 = ASPStrToNum($string, 0x1505, 0x21);
        $check2 = ASPStrToNum($string, 0, 0x1003F);

        $check1 >>= 2;
        $check1 = (($check1 >> 4) & 0x3FFFFC0) | ($check1 & 0x3F);
        $check1 = (($check1 >> 4) & 0x3FFC00) | ($check1 & 0x3FF);
        $check1 = (($check1 >> 4) & 0x3C000) | ($check1 & 0x3FFF);

        $t1 = (((($check1 & 0x3C0) << 4) | ($check1 & 0x3C)) <<2) | ($check2 & 0xF0F);
        $t2 = (((($check1 & 0xFFFFC000) << 4) | ($check1 & 0x3C00)) << 0xA) | ($check2 & 0xF0F0000);

        return ($t1 | $t2);
      }
    }

    extract(shortcode_atts(array('url' => home_url()), $atts));

    if(isset($atts[0]))
      $url = esc_url($atts[0]);

    $pagerank = 0;
    $instance = md5($url);

    // if the transient is not present in the database
    if(($pagerank = get_transient("pagerank_{$instance}")) === false){

      // get the pr
      $query = "http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=".ASPCheckHash(ASPHashURL($url))."&features=Rank&q=info:".$url."&num=100&filter=0";
      $request = new WP_Http();
      $result = $request->request($query);

      $pos = strpos($result['body'], 'Rank_');

      if($pos !== false)
        $pagerank = (int)substr($result['body'], $pos + 9);

      set_transient("pagerank_{$instance}", $pagerank, 60*60*24); // 1 day cache
    }

    // pr green bar
    $output  = '<div class="pagerank" title="Google PageRank &trade;">'.atom()->t('PR %d', $pagerank);
    $output .= '<div class="pagerank-frame"><div class="pagerank-bar" style="width:'.(($pagerank / 10) * 100).'%"></div></div></div>';
    return $output;
  }



  /**
   * Get the number of subscribers for a particular social media service
   * uses Yahoo Query Language - http://developer.yahoo.com/yql/
   * based on "Total Media Followers" by Melody Fassino - http://element-80.com/portfolio/plugins/total-social-followers/
   * @todo: add more services
   *
   * examples:
   *  [subscribers FeedBurner id=ourawesomeplanet]
   *  [subscribers Twitter id=digitalnature]
   *
   * @since 1.3
   *
   * @param mixed $atts Shortcode attributes
   * @return string Subscriber count
   */
  public static function subs($atts){

    extract(shortcode_atts(array('id' => false), $atts));

    $service = (isset($atts[0]) && $id) ? strtolower(esc_attr(strip_tags($atts[0]))) : false;
    $id = esc_attr(strip_tags($id));

    if($service === 'feedburner')
      $query = Atom::YQL_URI.urlencode("SELECT * FROM xml WHERE url='https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri={$id}'").'&format=json&callback=';

    elseif($service ===  'twitter')
      $query = Atom::YQL_URI.urlencode("SELECT * FROM xml WHERE url='http://twitter.com/users/show/{$id}.xml'").'&format=json&callback=';

    elseif($service === 'facebook')
      $query = Atom::YQL_URI.urlencode("SELECT * FROM facebook.graph WHERE id='{$id}'").'&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=';

    elseif($service === 'youtube')
      $query = Atom::YQL_URI.urlencode("SELECT * FROM xml WHERE url='http://gdata.youtube.com/feeds/api/users/{$id}'").'&format=json&callback=';

    else
      return "<div class=\"error box\"><strong>[subs]</strong> ".atom()->t('Please specify a valid service and user ID')."</div>\n";

    // if the transient is not present in the database make a new query
    if(($data = get_transient("subscribers_{$service}_{$id}")) === false){

      $response = wp_remote_retrieve_body(wp_remote_request($query));

      if(is_wp_error($response))
        return "<div class=\"error box\"><strong>[subs]</strong> {$response->get_error_message()}</div>\n";

      if($response){
        $response = json_decode($response, true);

        if($service === 'feedburner')
          $data = $response['query']['results']['rsp']['feed']['entry']['circulation'];
        elseif($service ===  'twitter')
          $data = $response['query']['results']['user']['followers_count'];
        elseif($service === 'facebook')
          $data = $response['query']['results']['json']['likes'];
        elseif($service === 'youtube')
          $data = $response['query']['results']['entry']['statistics']['subscriberCount'];

        // cache the result as a transient, for 6 hours
        set_transient("subscribers_{$service}_{$id}", (int)$data, 60*60*6);

      }
    }
    return (int)$data;
  }



 /*
  * Credit links (Wordpress & digitalnature)
  * example: [credit]
  *
  * @since    1.0
  * @return   string   Credit links
  */
  public static function credit(){
    return
      sprintf('<span id="site-generator">%s</span>',
        atom()->t('Powered by %1$s and %2$s theme by %3$s',
          '<a class="wp" href="http://wordpress.org/">WordPress</a>',
          atom()->get('theme_name'),
          '<a class="digitalnature" href="http://digitalnature.eu/">digitalnature</a>'
        )
      );
  }




 /*
  * Copyright info
  * example: [copyright 2001]
  *
  * @since    1.0
  * @return   string Copyright info
  */
  public static function copyright($atts){

    $atts = (array)$atts;

    if($start = array_shift($atts))
      $start = (int)$start.' &#8211; ';

    return sprintf('<span class="copyright"><span class="text">%1$s</span> <span class="the-year">'.$start.'%2$s</span> <a class="blog-title" href="%3$s" title="%4$s">%4$s</a></span>',
      atom()->t('Copyright &copy;'),
      date('Y'),
      home_url(),
      get_bloginfo('name'));
  }




 /*
  * Show the number of database queries and how much time it takes to load the current page.
  * example: [load]
  *
  * @since    1.0
  * @return   string
  */
  public static function load(){
    $memory = function_exists('memory_get_usage') ? memory_get_usage() : '';
    return atom()->t('%1$s queries in %2$s seconds (%3$sM)', get_num_queries(), timer_stop(0, 2), $memory ? number_format($memory/1024/1024, 2) : '???');
  }




 /*
  * Return the value of a custom field.
  * If used outside the loop (eg. in text widgets), you need to specify the post_id parameter
  * example: [field "Apples"]
  *
  * @since    1.0
  * @global   $post     Current post
  * @return   string    Custom field value
  */
  public static function field($atts){
    extract(shortcode_atts(array(
      'post_id' => ''
    ), $atts));

    global $post;

    $post_id = empty($post_id) ? $post->ID : $post_id;

    if(isset($atts[0]) && (($value = get_post_meta($post_id, esc_attr(strip_tags($atts[0])), true)) !== false))
      return $value;

    return '';
  }




  /**
   * Theme Author Name/Link
   * example: [theme-author]
   *
   * @since 1.0
   *
   * @return string
   */
  public static function themeauthor(){
     return atom()->get('theme_author');
  }



  /**
   * Blog title
   * example: [blog-title]
   *
   * @since 1.0
   *
   * @return string
   */
  public static function blogtitle(){
    return sprintf('<span class="blog-title">%s</span>', get_bloginfo('name'));
  }


}


