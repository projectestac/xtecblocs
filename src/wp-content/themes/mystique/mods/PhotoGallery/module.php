<?php
/**
 * Module Name: Featured Content / Gallery
 * Description: Gallery showcase
 * Version: 2.0
 * Author: digitalnature
 * Author URI: http://digitalnature.eu
 * Auto Enable: no
 * Priority: 100
 */




// class name must follow this pattern (AtomMod + directory name)
class AtomModPhotoGallery extends AtomMod{

  protected
    $effects  = array(),
    $sources  = array(),
    $options  = null,
    $photos   = array();

  // available public variables from parent class:
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

    $this->effects = array(
       'Fade'             => atom()->t('Fade'),
       'SlideH'           => atom()->t('Slide Horizontally'),
       'SlideV'           => atom()->t('Slide Vertically'),
       'RandomTiles'      => atom()->t('Random Tiles'),
       'HorizontalTiles'  => atom()->t('Horizontal Tiles'),
       'DiagonalTiles'    => atom()->t('Diagonal Tiles'),
       'Spiral'           => atom()->t('Spiral'),
       'RandomStripes'    => atom()->t('Random Stripes'),
       'StripeWave'       => atom()->t('Stripe Wave'),
       'Curtain'          => atom()->t('Curtain'),
       'Interweave'       => atom()->t('Interweave'),
    );

    // register extra theme options
    atom()->registerDefaultOptions(atom()->getContextArgs('photo_gallery_defaults', array(
      'gallery_dimensions'             => array(960, 320),
      'gallery_source'                 => 'posts',
      'gallery_source_nextgen'         => 0,
      'gallery_source_atom'            => 0,
      'gallery_link'                   => 'post',
      'gallery_effects'                => array_keys($this->effects),
      'gallery_count'                  => 6,
      'gallery_delay'                  => 15,
      'gallery_timeframe'              => 60,
      'gallery_caption'                => true,
      'gallery_caption_content'        => 'post_filtered',
      'gallery_navigation'             => true,
      'gallery_pager'                  => true,
      'gallery_visibility'             => array(
         'page-home'          => 1,
         'user-visitor'       => 1,
         'role-administrator' => 1,
         'role-editor'        => 1,
         'role-author'        => 1,
         'role-contributor'   => 1,
         'role-subscriber'    => 1,
      ),

      // internal, no option fields for these
      'gallery_allowed_tags'     => array('a', 'abbr', 'acronym', 'b', 'cite', 'code', 'del', 'dfn', 'em', 'i', 'ins', 'q', 'strong', 'sub', 'sup'),
      'gallery_description_size' => 250,
      'gallery_caption_position' => array('left', 'right', 'bottom'),
      'gallery_location'         => 'before_main',
    )));

    // insert a tab, 35 is the priority (somewhere between content options and ads)
    if(is_admin())
      atom()->interface->addSection('gallery', atom()->t('Featured Content'), array($this, 'form'), 35);

    // allow override of the current options
    $this->options = atom()->getContextArgs('photo_gallery', $this->getOptions('gallery'));

    // set up image size
    list($w, $h) = $this->options['gallery_dimensions'];
    add_image_size('photo_gallery', $w, $h, true);

    if(!is_admin())
      add_action('wp', array($this, 'prepareImages'));
  }



 /*
  * Can be used to add another image source
  *
  * @since      2.0
  * @param      string $id       A unique identifier for the source
  * @param      string $label    Label to show in the forms
  * @param      function         Callback / function that retrieves the images
  */
  public function addSource($id, $label, $callback){
    $this->sources[$id] = array(
      'label' => $label,
      'callback' => $callback,
    );
  }



 /*
  * Gets the photos
  *
  * @since      1.0
  */
  public function prepareImages(){

    if(atom_strict_visibility_check($this->options['gallery_visibility'])){

      // featured media
      if($this->options['gallery_source'] === 'posts')
        $this->photos = $this->getFeaturedPostsPhotos($this->options);

      // featured media
      elseif($this->options['gallery_source'] === 'marked')
        $this->photos = $this->getFeaturedMediaPhotos($this->options);

      // nextgen gallery
      elseif($this->options['gallery_source'] === 'nextgen')
        $this->photos = $this->getNGGPhotos($this->options);

      // atom gallery
      elseif($this->options['gallery_source'] === 'atom')
        $this->photos = $this->getAtomPhotos($this->options);

      // other source
      elseif(in_array($this->options['gallery_source'], array_keys($this->sources)))
        $this->photos = call_user_func($this->sources[$this->options['gallery_source']]['callback'], $this->options);

      atom()->add($this->options['gallery_location'], array($this, 'output'));

      if($this->photos)
        atom()->addContextArgs('body_class', array('with-gallery'));
    }
  }



 /*
  * Tab entry in theme settings
  *
  * @since      1.0
  */
  public function form(){
    global $nggdb;
    $wp_page_types = array(
      'home'     => atom()->t('Blog Homepage'),
      'search'   => atom()->t('Search Results'),
      'author'   => atom()->t('Author Archives'),
      'date'     => atom()->t('Date-based Archives'),
      'tag'      => atom()->t('Tag Archives'),
      'category' => atom()->t('Category Archives'),
    );

    $this->options = atom()->options();

    list($width, $height) = $this->options['gallery_dimensions'];

    $ngg_installed = isset($nggdb) && ($nggdb instanceof nggdb);
    $atom_installed = class_exists('AtomGallery');
    ?>
    <!-- tab: gallery -->
    <div class="clear-block">

      <div class="clear-block">

       <div class="block alignleft">
         <h3 class="title"><?php atom()->te('Content source'); ?></h3>

         <div class="entry">
         <label for="gallery_source_posts">
           <input type="radio" value="posts" id="gallery_source_posts" name="gallery_source" <?php checked($this->options['gallery_source'], 'posts'); ?> followRules />
           <a href="<?php echo admin_url('edit.php'); ?>"><?php atom()->te('Featured posts'); ?></a>
         </label>
         </div>

         <div class="entry">
         <label for="gallery_source_marked">
           <input type="radio" value="marked" id="gallery_source_marked" name="gallery_source" <?php checked($this->options['gallery_source'], 'marked'); ?> followRules />
           <?php atom()->te('%s that I mark as featured', '<a href="'.admin_url('upload.php').'">'.atom()->t('Media items').'</a>'); ?>
         </label>
         </div>

         <div class="entry">

         <label for="gallery_source_atom" <?php if(!$atom_installed): ?> class="disabled" title="<?php atom()->te('Plugin not installed'); ?>" <?php endif;?>>
           <input id="gallery_source_atom" name="gallery_source" type="radio" value="atom" <?php checked($this->options['gallery_source'], 'atom'); ?> <?php if(!$atom_installed): ?> disabled="disabled" <?php else: ?> followRules <?php endif;?>/>
           <?php atom()->te('%s gallery', sprintf('<a href="%1$s" target="_blank">%2$s</a>', '#', 'Atom')); ?>
           <select name="gallery_source_atom" <?php if(!$atom_installed): ?> disabled="disabled"  <?php else: ?> rules="gallery_source:atom"<?php endif;?>>
             <option value="0"><?php atom()->te('-- All galleries --'); ?></option>
             <?php if($atom_installed): ?>
             <?php foreach(AtomGallery::app()->getGalleries() as $gallery): ?>             <?php /* @todo: use meta key for counts */ ?>
             <option value="<?php echo $gallery->ID; ?>" <?php selected($gallery->ID, $this->options['gallery_source_atom']); ?>><?php printf('%1$s (%2$s)', $gallery->post_title, count(get_children(array('post_type' => 'attachment', 'post_mime_type' => 'image' , 'post_parent' => $gallery->ID)))); ?></option>
             <?php endforeach; ?>
             <?php endif; ?>
           </select>
         </label>

         </div>

         <div class="entry">

         <label for="gallery_source_nextgen" <?php if(!$ngg_installed): ?> class="disabled" title="<?php atom()->te('Plugin not installed'); ?>" <?php endif;?>>
           <input id="gallery_source_nextgen" name="gallery_source" type="radio" value="nextgen" <?php checked($this->options['gallery_source'], 'nextgen'); ?> <?php if(!$ngg_installed): ?> disabled="disabled" <?php else: ?> followRules <?php endif;?>/>
           <?php atom()->te('%s gallery', sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'http://wordpress.org/extend/plugins/nextgen-gallery/', 'NextGEN')); ?>
           <select name="gallery_source_nextgen" <?php if(!$ngg_installed): ?> disabled="disabled"  <?php else: ?> followRules rules="gallery_source:nextgen"<?php endif;?>>
             <option value="0"><?php atom()->te('-- All galleries --'); ?></option>
             <?php if($ngg_installed): ?>
             <?php foreach($nggdb->find_all_galleries('name', 'ASC', true) as $gallery): ?>
             <option value="<?php echo $gallery->gid; ?>" <?php selected($gallery->gid, $this->options['gallery_source_nextgen']); ?>><?php printf('%1$s (%2$s)', $gallery->name, $gallery->counter); ?></option>
             <?php endforeach; ?>
             <?php endif; ?>
           </select>
         </label>

         </div>

         <?php foreach($this->sources as $source_id => $properties): // extra sources ?>
         <div class="entry">
         <label for="gallery_source_<?php echo $source_id; ?>">
           <input type="radio" value="<?php echo $source_id; ?>" id="gallery_source_<?php echo $source_id; ?>" name="gallery_source" <?php checked($this->options['gallery_source'], $source_id); ?> followRules />
           <?php echo $properties['label']; ?>
         </label>
         </div>
         <?php endforeach; ?>

         <h3 class="title"><?php atom()->te('Options'); ?></h3>

         <div class="entry">
          <label for="gallery_dimensions"><?php atom()->te('Dimensions:') ?></label>
          <input id="gallery_dimensions" name="gallery_dimensions[]" type="text" value="<?php echo (int)$width; ?>" size="4" /> x
          <input id="gallery_dimensions" name="gallery_dimensions[]" type="text" value="<?php echo (int)$height; ?>" size="4" />
          <?php atom()->te('pixels'); ?>
          <label class="desc"><?php atom()->te('Images of this size must already exist!'); ?></label>
         </div>

         <div class="entry">
          <label for="gallery_count"><?php atom()->te('Number of items to show:') ?></label>
          <input id="gallery_count" name="gallery_count" type="text" value="<?php echo (int)$this->options['gallery_count']; ?>" size="3" />
         </div>

         <div class="entry">
          <?php $input = '<input id="gallery_timeframe" name="gallery_timeframe" type="text" value="'.(int)$this->options['gallery_timeframe'].'" size="4" />'; ?>
          <label for="gallery_timeframe"><?php atom()->te('Exclude items older than %s day(s)', $input); ?></label>
          <label class="desc"><?php atom()->te('Set to %d to ignore date', 0); ?></label>
         </div>

         <div class="entry">
          <?php $input = '<input id="gallery_delay" name="gallery_delay" type="text" value="'.(int)$this->options['gallery_delay'].'" size="3" />'; ?>
          <label for="gallery_delay"><?php atom()->te('Auto-Cycle every %s second(s)', $input); ?></label>
          <label class="desc"><?php atom()->te('Set to %d to disable auto-play', 0); ?></label>
         </div>

         <div class="entry">
          <label for="gallery_link"><?php atom()->te('Slides link to:') ?></label>
          <select id="gallery_link" name="gallery_link" followRules rules="gallery_source:marked">
            <option value="image" <?php selected($this->options['gallery_link'], 'image'); ?>><?php atom()->te('Original image'); ?></option>
            <option value="attachment" <?php selected($this->options['gallery_link'], 'attachment'); ?>><?php atom()->te('Post URL'); ?></option>
            <option value="post" <?php selected($this->options['gallery_link'], 'post'); ?>><?php atom()->te('Attached (parent) post URL, if any'); ?></option>
            <option value="" <?php selected($this->options['gallery_link'], ''); ?>><?php atom()->te('Nothing'); ?></option>
          </select>
         </div>

         <h3 class="title"><?php atom()->te('Show'); ?></h3>
         <div class="entry">
          <input type="hidden" name="gallery_caption" value="0" />
          <input id="gallery_caption" name="gallery_caption" type="checkbox" followRules <?php checked($this->options['gallery_caption']); ?> value="1" />
          <label for="gallery_caption"><?php atom()->te('Captions:') ?></label>
          <select id="gallery_caption_content" followRules rules="gallery_caption" name="gallery_caption_content">
           <option value="image" <?php selected($this->options['gallery_caption_content'], 'image'); ?>><?php atom()->te("Image title and description"); ?></option>
           <option value="post_filtered" <?php selected($this->options['gallery_caption_content'], 'post_filtered'); ?>><?php atom()->te("Filtered content from the attached post, if any"); ?></option>
           <option value="post_excerpt" <?php selected($this->options['gallery_caption_content'], 'post_excerpt'); ?>><?php atom()->te("Excerpt from the attached post, if any"); ?></option>
          </select>
         </div>

         <div class="entry">
          <label for="gallery_navigation">
          <input type="hidden" name="gallery_navigation" value="0" />
          <input type="checkbox" id="gallery_navigation" name="gallery_navigation" <?php checked($this->options['gallery_navigation']); ?> value="1" />
   	      <?php atom()->te('Previous/Next links (on mouse over)'); ?></label>
         </div>

         <div class="entry">
          <label for="gallery_pager">
          <input type="hidden" name="gallery_pager" value="0" />
          <input type="checkbox" id="gallery_pager" name="gallery_pager" <?php checked($this->options['gallery_pager']); ?> value="1" />
   	    <?php atom()->te('Pager (on mouse over)'); ?></label>
         </div>

        <h3 class="title"><?php atom()->te('Effects'); ?></h3>
        <input type="hidden" name="gallery_effects" value="0" />
        <?php foreach($this->effects as $key => $label): ?>
         <div class="entry">
           <label for="gallery_effects_<?php echo $key; ?>">
             <input id="gallery_effects_<?php echo $key; ?>" name="gallery_effects[]" type="checkbox" value="<?php echo $key; ?>" <?php checked(true, in_array($key, (array)$this->options['gallery_effects'])); ?> />
             <?php echo $label; ?>
           </label>
         </div>
        <?php endforeach; ?>

       </div>

       <div class="block alignright">

         <h3 class="title"><?php atom()->te('Visible on:'); ?></h3>
         <?php atom()->interface->PageFields($this->options['gallery_visibility'], 'gallery_visibility'); ?>

         <h3 class="title"><?php atom()->te('To:'); ?></h3>
         <?php atom()->interface->UserRoleFields($this->options['gallery_visibility'], 'gallery_visibility'); ?>

       </div>
      </div>

    </div>
    <!-- /tab: gallery -->
    <?php
  }



 /*
  * Featured image source
  *
  * @since      1.0
  * @return     array    An array of image properties
  */
  private function getFeaturedMediaPhotos(){

    // photo sizes to retrieve
    list($gallery_width, $gallery_height) = $this->options['gallery_dimensions'];

    // get featured media records and randomize them
    $media_ids = wp_parse_id_list(get_option('featured_media'));
    shuffle($media_ids);

    $day_limit = (int)$this->options['gallery_timeframe'];
    $valid_photos = array();
    $count = 1;

    // process each image
    foreach($media_ids as $media_id){

      // make sure it exists
      $image = wp_get_attachment_image_src($media_id, 'photo_gallery');

      if(!$image) continue;

      // make sure it has the dimensions we're looking for -- @todo: auto-regenerate?
      list($src, $width, $height) = $image;
      if(($width != $gallery_width) || ($height != $gallery_height)) continue;

      // attempt to get the attached post
      $media = &get_post($media_id);
      if(!$media) continue;

      $attachment = new AtomObjectPost($media);
      $post_parent = $media->post_parent;

      $link = $desc = $title = '';

      // alt image attribute
      $alt = trim(strip_tags(get_post_meta($media_id, '_wp_attachment_image_alt', true)));

      if($this->options['gallery_caption']){
        $title = $attachment->getTitle();
        $desc = $attachment->getContent($this->options['gallery_description_size'], array('allowed_tags' => $this->options['gallery_allowed_tags']));

        $content_mode = $this->options['gallery_caption_content'];
        if($post_parent && in_array($content_mode, array('post_filtered', 'post_excerpt'))){
          $post = new AtomObjectPost($post_parent);
          $title = $post->getTitle();
          $desc = is_numeric($content_mode) ? $post->getContent($this->options['gallery_description_size'], array('allowed_tags' => $this->options['gallery_allowed_tags'])) : $post->getContent('e');
        }

        if(empty($alt)) $alt = $title;
        if(empty($alt)) $alt = $attachment->getContent('e');
      }

      // determine link URL
      if($this->options['gallery_link'] === 'image'){
        list($link, $full_width, $full_height) = wp_get_attachment_image_src($media_id, 'full');

      }elseif($post_parent && $this->options['gallery_link'] === 'attachment'){
        $link = $attachment->getURL();

      }elseif($post_parent && $this->options['gallery_link'] === 'post'){
        $post = new AtomObjectPost($post_parent);
        $link = $post->getURL();
      }

      // date must be within the timeframe set in the options
      if($day_limit && (strtotime($media->post_date) < strtotime("-{$day_limit} days"))) continue;

      // image is valid, add it to the list
      $valid_photos[] = apply_filters('atom_gallery_featured_photos', array(
        'src'    => $src,
        'link'   => $link,
        'alt'    => $alt,
        'title'  => $title,
        'desc'   => $desc,
      ), $media);

      // stop if jquery is disabled (one photo only), or if the current photo count exceeds the number limit from the options
      if(!atom()->options('jquery') || ($count++ == $this->options['gallery_count'])) break;

    }

    // reset post data
    atom()->resetCurrentPost();

    return $valid_photos;
  }





 /*
  * Featured posts source
  *
  * @since      1.0
  * @return     array    An array of image properties
  */
  private function getFeaturedPostsPhotos(){

    // photo sizes to retrieve
    list($gallery_width, $gallery_height) = $this->options['gallery_dimensions'];

    // get featured media records and randomize them
    $post_ids = wp_parse_id_list(get_option('featured_posts'));
    shuffle($post_ids);

    $day_limit = (int)$this->options['gallery_timeframe'];
    $valid_photos = array();
    $count = 1;

    // process each image
    foreach($post_ids as $post_id){

      $media_id = get_post_thumbnail_id($post_id);

      // make sure it exists
      $image = wp_get_attachment_image_src($media_id, 'photo_gallery');
      $post = new AtomObjectPost($post_id);

      if(!$post || !$image) continue;

      // make sure it has the dimensions we're looking for -- @todo: auto-regenerate?
      list($src, $width, $height) = $image;
      if(($width != $gallery_width) || ($height != $gallery_height)) continue;

      $link = $desc = $title = '';

      // alt image attribute
      $alt = $post->getTitle();

      if(empty($alt))
        $alt = $post->getContent('e');

      if($this->options['gallery_caption']){
        $title = $post->getTitle();
        $desc = $post->getContent($this->options['gallery_description_size'], array('allowed_tags' => $this->options['gallery_allowed_tags']));
      }

      // date must be within the timeframe set in the options
      if($day_limit && (strtotime($post->get('post_date')) < strtotime("-{$day_limit} days"))) continue;

      // image is valid, add it to the list
      $valid_photos[] = apply_filters('atom_gallery_featured_photos', array(
        'src'    => $src,
        'link'   => $post->getURL(),
        'alt'    => $alt,
        'title'  => $title,
        'desc'   => $desc,
      ), $post->getData());

      // stop if jquery is disabled (one photo only), or if the current photo count exceeds the number limit from the options
      if(!atom()->options('jquery') || ($count++ == $this->options['gallery_count'])) break;

    }

    // reset post data
    atom()->resetCurrentPost();

    return $valid_photos;
  }



 /*
  * Nextgen gallery photo source
  *
  * @since      1.0
  * @return     array    An array of image properties
  */
  private function getNGGPhotos(){
    global $nggdb;

    // photo sizes to retrieve
    list($gallery_width, $gallery_height) = $this->options['gallery_dimensions'];

    $gallery_id = (int)$this->options['gallery_source_nextgen'];
    $day_limit = (int)$this->options['gallery_timeframe'];
    $valid_photos = array();

    if(isset($nggdb) && ($nggdb instanceof nggdb)){

      $images = $gallery_id ? $nggdb->get_gallery($gallery_id) : $nggdb->get_random_images(100);
      $count = 1;

      foreach((array)$images as $image){

        // check dimensions
        $valid_width = isset($image->meta_data['width']) && ($image->meta_data['width'] == $gallery_width);
        $valid_height = isset($image->meta_data['height']) && ($image->meta_data['height'] == $gallery_height);
        if(!$valid_width || !$valid_height) continue;

        // is it older than what we want?
        if($day_limit && (strtotime($image->imagedate) < strtotime("-{$day_limit} days"))) continue;
        // @note: we could use sql - WHERE imagedate + INTERVAL 30 DAY < NOW() but get_random_images doesn't support the $exclude arg :(

        $valid_photos[] = apply_filters('atom_gallery_ngg_photos', array(
          'src'    => $image->imageURL,
          'link'   => $image->pageid ? get_page_link($image->pageid) : '',
          'alt'    => $image->alttext,
          'title'  => $image->title,
          'desc'   => convert_smilies(Atom::generateExcerpt($image->description, array(
                        'allowed_tags' => $this->options['gallery_allowed_tags'],
                        'limit' => $this->options['gallery_description_size']
                      ))),
        ), $image);

        // stop if jquery is disabled (one photo only), or if the current photo count exceeds the number limit from the options
        if(!atom()->options('jquery') || ($count++ == $this->options['gallery_count'])) break;

      }
    }

    return $valid_photos;
  }




 /*
  * Atom gallery photo source -- @todo
  *
  * @since      1.0
  * @return     array    An array of image properties
  */
  private function getAtomPhotos(){

    // photo sizes to retrieve
    list($gallery_width, $gallery_height) = $this->options['gallery_dimensions'];

    $valid_photos = array();

    $gallery_id = (int)$this->options['gallery_source_atom'];

    $attachments = AtomGallery::app()->getGallery($gallery_id);

    foreach($attachments as $attachment){
      list($source, $width, $height) = wp_get_attachment_image_src($attachment->ID, 'photo_gallery');

//      $gallery[] = '<a class="gallery-thumbnail" href="'.wp_get_attachment_url($attachment->ID).'"><img src="'.$source.'" width="'.$width.'" height="'.$height.'" /></a>';



        $valid_photos[] = apply_filters('atom_gallery_atom_photos', array(
          'src'    => $source,
          'link'   => '#',
          'alt'    => '#',
          'title'  => '#',
          'desc'   => '#',
        ), $image);

        // stop if jquery is disabled (one photo only), or if the current photo count exceeds the number limit from the options
        if(!atom()->options('jquery') || ($count++ == $this->options['gallery_count'])) break;



    }



    // @todo

    return $valid_photos;
  }



 /*
  * Gallery HTML output
  *
  * @since      1.0
  */
  public function output(){

    // get selected effects
    $effects = (is_array($this->options['gallery_effects']) && !empty($this->options['gallery_effects'])) ? $this->options['gallery_effects'] : array('fade');

    // remove any effects that might not exist (ie. some fx could be removed upon version change)
    $effects = array_intersect($effects, array_keys($this->effects));

    // randomize effects
    shuffle($effects);

    // get and randomize caption positions
    $caption_push = $this->options['gallery_caption_position'];
    shuffle($caption_push);

    list($gallery_width, $gallery_height) = $this->options['gallery_dimensions'];

    $output = array();

    // process each image
    foreach((array)$this->photos as $index => $photo){
      $content = '<img src="'.$photo['src'].'" alt="'.$photo['alt'].'" width="'.$gallery_width.'" height="'.$gallery_height.'" />';

      if($photo['link'])
        $content = '<a href="'.$photo['link'].'">'.$content.'</a>';

      if($this->options['gallery_caption']){
        $photo['title'] = $photo['title'] ? "<h3>{$photo['title']}</h3>" : '';
        if($photo['desc'] || $photo['title']){

          if(empty($position_history))
            $position_history = $caption_push;

          $position = array_pop($position_history);

          $content .= '<div class="caption push-'.$position.'"><div class="content">'.$photo['title'].$photo['desc'].'</div></div>';
        }
      }

      // used up all effects? reset then...
      if(empty($effect_history)) $effect_history = $effects;

      // select a random effect and remove it from the history of effects so we don't choose it again in the next loop
      $effect = array_pop($effect_history);

      $output[] = '<div class="slide '.(($index === 0) ? 'first' : '').'" data-fx="'.$effect.'" data-delay="'.(int)$this->options['gallery_delay'].'">'.$content.'</div>';

    }

    if(empty($output))
      return atom()->log("Photo gallery: No relevant images found ({$this->options['gallery_source']}, {$gallery_width}x{$gallery_height})");

    $controls = array();
    if($this->options['gallery_navigation']) $controls[] = 'arrows';
    if($this->options['gallery_pager']) $controls[] = 'pager';

    $count = count($this->photos);
    $attributes = array(
       'style'          => "width:{$gallery_width}px;height:{$gallery_height}px;",
       'class'          => ($count > 1) ? "iSlider count-{$count}" : "single",
       'data-controls'  => implode(',', $controls),
    );

    foreach($attributes as $key => &$value)
      $value = ($value) ? "{$key}=\"{$value}\"" : '';

    ?>
    <!-- gallery -->
    <div id="gallery" <?php echo implode(' ', $attributes); ?>>
      <?php echo implode("\n", $output); ?>
      <?php atom()->action('after_photo_gallery'); ?>
    </div>
    <!-- /gallery -->

    <?php
  }

}
