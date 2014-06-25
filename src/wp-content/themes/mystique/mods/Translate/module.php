<?php
/**
 * Module Name:   Translate
 * Description:   Allows you to easily translate the theme from the dashboard
 * Version:       0.9.8
 * Author:        digitalnature
 * Author URI:    http://digitalnature.eu
 * Auto Enable:   no
 */



// class name must follow this pattern (AtomMod + directory name)
class AtomModTranslate extends AtomMod{

  protected
    $cache             = array(),
    $locale            = 'en_US',
    $plural_count      = 2,
    $has_translation   = false,
    $current_po        = null,
    $original_po       = null;

  // available protected variables from parent class:
  //
  // $this->url  - this module's url path
  // $this->dir  - this module's directory



 /*
  * Initialization, will be called by Atom when the module needs to be loaded (replaces the __construct method)
  *
  * @since    0.9
  * @return   object   instance
  */
  public function onInit(){

    // this is an administration-only interface for super admins
    if(is_admin() && current_user_can('update_core')){

      atom()->interface->addSection('translate', atom()->t('Translate'), array($this, 'form'), 38);

      if(is_child_theme()){
        atom()->add('save_options', array($this, 'save'));

        atom()->addContextArgs('settings_update_errors', array(
          30 => atom()->t('Failed to save .mo / .po files. Is your child theme directory writable?'),
        ));

        add_action('wp_ajax_cache_translation_string', array($this, 'updateStringCache'));
      }

    }

    return $this;
  }



 /*
  * Get the requested Po translation, original strings (pot template) or translated strings.
  * If there's no translation the pot template will be considered the "current" po.
  *
  * @since    0.9.4
  * @param    string $which   Original or current
  * @return   mixed           Po object, or false on fail
  */
  protected function getPo($which = 'original'){

    $po = "{$which}_po";

    if(isset($this->$po))
      return $this->$po;

    // set up related variables first
    $this->locale = get_locale();
    $this->has_translation = true;
    $this->plural_count = $GLOBALS['l10n']['default']->get_plural_forms_count();

    require_once ABSPATH.'/wp-includes/pomo/po.php';

    $this->$po = new PO();

    $loaded = true;

    // original strings requested
    if($which !== 'current'){
       // load original pot template
       if(file_exists(TEMPLATEPATH.'/lang/_default.pot'))
         $loaded = $this->$po->import_from_file(TEMPLATEPATH.'/lang/_default.pot');

       // compat with older Atom versions
       else
         $loaded = $this->$po->import_from_file(TEMPLATEPATH.'/lang/en_US.po');

    // current language strings
    }else{

      // locate and import the .po file matching the locale - child theme first
      if(is_child_theme() && file_exists(STYLESHEETPATH.'/lang/'.$this->locale.'.po'))
        $loaded = $this->$po->import_from_file(STYLESHEETPATH.'/lang/'.$this->locale.'.po');

      // parent theme
      elseif(file_exists(TEMPLATEPATH.'/lang/'.$this->locale.'.po'))
        $loaded = $this->$po->import_from_file(TEMPLATEPATH.'/lang/'.$this->locale.'.po');

      // not found, return original pot
      else{
        $this->$po = $this->getPo('original');
        $this->has_translation = ($this->locale == 'en_US');
        $loaded = true;
      }

    }

    if(($cache = get_transient('atom_translation_cache')) !== false)
      $this->cache = $cache;

    return $loaded ? $this->$po : false;
  }



 /*
  * Replaces WP's format_code_lang() because that function is only available in multisite mode
  *
  * @since    0.9
  * @param    string $code   Language code, eg. fr_FR
  * @return   string
  */
  protected function getLanguageByCode($code = false){

    $code = $code ? $code : $this->locale;

    $code = strtolower(substr($code, 0, 2));
    $lang_codes = array(
      'aa' => 'Afar',
      'ab' => 'Abkhazian',
      'af' => 'Afrikaans',
      'ak' => 'Akan',
      'sq' => 'Albanian',
      'am' => 'Amharic',
      'ar' => 'Arabic',
      'an' => 'Aragonese',
      'hy' => 'Armenian',
      'as' => 'Assamese',
      'av' => 'Avaric',
      'ae' => 'Avestan',
      'ay' => 'Aymara',
      'az' => 'Azerbaijani',
      'ba' => 'Bashkir',
      'bm' => 'Bambara',
      'eu' => 'Basque',
      'be' => 'Belarusian',
      'bn' => 'Bengali',
      'bh' => 'Bihari',
      'bi' => 'Bislama',
      'bs' => 'Bosnian',
      'br' => 'Breton',
      'bg' => 'Bulgarian',
      'my' => 'Burmese',
      'ca' => 'Catalan; Valencian',
      'ch' => 'Chamorro',
      'ce' => 'Chechen',
      'zh' => 'Chinese',
      'cu' => 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic',
      'cv' => 'Chuvash',
      'kw' => 'Cornish',
      'co' => 'Corsican',
      'cr' => 'Cree',
      'cs' => 'Czech',
      'da' => 'Danish',
      'dv' => 'Divehi; Dhivehi; Maldivian',
      'nl' => 'Dutch; Flemish',
      'dz' => 'Dzongkha',
      'en' => 'English',
      'eo' => 'Esperanto',
      'et' => 'Estonian',
      'ee' => 'Ewe',
      'fo' => 'Faroese',
      'fj' => 'Fijjian',
      'fi' => 'Finnish',
      'fr' => 'French',
      'fy' => 'Western Frisian',
      'ff' => 'Fulah',
      'ka' => 'Georgian',
      'de' => 'German',
      'gd' => 'Gaelic; Scottish Gaelic',
      'ga' => 'Irish',
      'gl' => 'Galician',
      'gv' => 'Manx',
      'el' => 'Greek, Modern',
      'gn' => 'Guarani',
      'gu' => 'Gujarati',
      'ht' => 'Haitian; Haitian Creole',
      'ha' => 'Hausa',
      'he' => 'Hebrew',
      'hz' => 'Herero',
      'hi' => 'Hindi',
      'ho' => 'Hiri Motu',
      'hu' => 'Hungarian',
      'ig' => 'Igbo',
      'is' => 'Icelandic',
      'io' => 'Ido',
      'ii' => 'Sichuan Yi',
      'iu' => 'Inuktitut',
      'ie' => 'Interlingue',
      'ia' => 'Interlingua (International Auxiliary Language Association)',
      'id' => 'Indonesian',
      'ik' => 'Inupiaq',
      'it' => 'Italian',
      'jv' => 'Javanese',
      'ja' => 'Japanese',
      'kl' => 'Kalaallisut; Greenlandic',
      'kn' => 'Kannada',
      'ks' => 'Kashmiri',
      'kr' => 'Kanuri',
      'kk' => 'Kazakh',
      'km' => 'Central Khmer',
      'ki' => 'Kikuyu; Gikuyu',
      'rw' => 'Kinyarwanda',
      'ky' => 'Kirghiz; Kyrgyz',
      'kv' => 'Komi',
      'kg' => 'Kongo',
      'ko' => 'Korean',
      'kj' => 'Kuanyama; Kwanyama',
      'ku' => 'Kurdish',
      'lo' => 'Lao',
      'la' => 'Latin',
      'lv' => 'Latvian',
      'li' => 'Limburgan; Limburger; Limburgish',
      'ln' => 'Lingala',
      'lt' => 'Lithuanian',
      'lb' => 'Luxembourgish; Letzeburgesch',
      'lu' => 'Luba-Katanga',
      'lg' => 'Ganda',
      'mk' => 'Macedonian',
      'mh' => 'Marshallese',
      'ml' => 'Malayalam',
      'mi' => 'Maori',
      'mr' => 'Marathi',
      'ms' => 'Malay',
      'mg' => 'Malagasy',
      'mt' => 'Maltese',
      'mo' => 'Moldavian',
      'mn' => 'Mongolian',
      'na' => 'Nauru',
      'nv' => 'Navajo; Navaho',
      'nr' => 'Ndebele, South; South Ndebele',
      'nd' => 'Ndebele, North; North Ndebele',
      'ng' => 'Ndonga',
      'ne' => 'Nepali',
      'nn' => 'Norwegian Nynorsk; Nynorsk, Norwegian',
      'nb' => 'Bokmål, Norwegian, Norwegian Bokmål',
      'no' => 'Norwegian',
      'ny' => 'Chichewa; Chewa; Nyanja',
      'oc' => 'Occitan, Provençal',
      'oj' => 'Ojibwa',
      'or' => 'Oriya',
      'om' => 'Oromo',
      'os' => 'Ossetian; Ossetic',
      'pa' => 'Panjabi; Punjabi',
      'fa' => 'Persian',
      'pi' => 'Pali',
      'pl' => 'Polish',
      'pt' => 'Portuguese',
      'ps' => 'Pushto',
      'qu' => 'Quechua',
      'rm' => 'Romansh',
      'ro' => 'Romanian',
      'rn' => 'Rundi',
      'ru' => 'Russian',
      'sg' => 'Sango',
      'sa' => 'Sanskrit',
      'sr' => 'Serbian',
      'hr' => 'Croatian',
      'si' => 'Sinhala; Sinhalese',
      'sk' => 'Slovak',
      'sl' => 'Slovenian',
      'se' => 'Northern Sami',
      'sm' => 'Samoan',
      'sn' => 'Shona',
      'sd' => 'Sindhi',
      'so' => 'Somali',
      'st' => 'Sotho, Southern',
      'es' => 'Spanish; Castilian',
      'sc' => 'Sardinian',
      'ss' => 'Swati',
      'su' => 'Sundanese',
      'sw' => 'Swahili',
      'sv' => 'Swedish',
      'ty' => 'Tahitian',
      'ta' => 'Tamil',
      'tt' => 'Tatar',
      'te' => 'Telugu',
      'tg' => 'Tajik',
      'tl' => 'Tagalog',
      'th' => 'Thai',
      'bo' => 'Tibetan',
      'ti' => 'Tigrinya',
      'to' => 'Tonga (Tonga Islands)',
      'tn' => 'Tswana',
      'ts' => 'Tsonga',
      'tk' => 'Turkmen',
      'tr' => 'Turkish',
      'tw' => 'Twi',
      'ug' => 'Uighur; Uyghur',
      'uk' => 'Ukrainian',
      'ur' => 'Urdu',
      'uz' => 'Uzbek',
      've' => 'Venda',
      'vi' => 'Vietnamese',
      'vo' => 'Volapük',
      'cy' => 'Welsh',
      'wa' => 'Walloon',
      'wo' => 'Wolof',
      'xh' => 'Xhosa',
      'yi' => 'Yiddish',
      'yo' => 'Yoruba',
      'za' => 'Zhuang; Chuang',
      'zu' => 'Zulu',
    );

    $lang_codes = apply_filters('lang_codes', $lang_codes, $code);
    return strtr($code, $lang_codes);
  }



 /*
  * Stores a translated string into the cache.
  * This is called trough AJAX whenver the translation input field changes.
  * Cache is deleted after 30 days with no activity or when the Po is saved.
  *
  * @since 0.9
  */
  public function updateStringCache(){
    atom()->interface->checkAjaxNonce();

    // string to translate
    $key = html_entity_decode(stripslashes($_GET['key']), ENT_QUOTES);

    // translated version of the string
    $translation = trim(html_entity_decode(stripslashes($_GET['translation']), ENT_QUOTES));

    // string type: singular (0) or a plural form (1, 2, 3, etc)
    $type_index = (int)$_GET['index'];

    // update cache if we have a translation
    if(!empty($translation)){

      if(($cache = get_transient('atom_translation_cache')) === false) $cache = array();
      delete_transient('atom_translation_cache');

      $cache[$key][$type_index] = $translation;

      set_transient('atom_translation_cache', $cache, 60*60*24*30); // 30 days (also gets removed on successful save)
    }

    // send the translation string back
    echo htmlspecialchars($translation);
    exit;
  }



 /*
  * Tab content entry in the theme settings (the translation edit panel)
  *
  * @since 0.9
  */
  public function form($settings){

    $current_translation = $this->getPo('current');
    $original_strings = $this->getPo('original');

    if(!$current_translation || !$original_strings){
      atom()->te('Failed to load pot template');
      return;
    }

    // relevant header entries
    $headers = array(
      'project_ver'  => 'Project-Id-Version',
      'translator'   => 'Last-Translator',
      'team'         => 'Language-Team',
      'language'     => 'X-Poedit-Language',
      'country'      => 'X-Poedit-Country',
      'plural_forms' => 'Plural-Forms',
    );

    foreach($headers as &$attribute)
      $attribute = empty($current_translation->headers[$attribute]) ? '' : $current_translation->headers[$attribute];

    extract($headers);

    // calculate "complete" percentage
    $completed = 0;
    foreach($original_strings->entries as $key => $entry)
      if(!empty($current_translation->entries[$key]->translations)) $completed++;

    $completed = @round(($completed / count($original_strings->entries)) * 100);
    $index = 0;

    // check permissions
    $editing_allowed = is_child_theme() && current_user_can('update_core');
    ?>

    <!-- tab: translate -->
    <div class="clear-block" id="atom-translate">

      <style type="text/css">
        #atom-translate .original{
          color: #999;
        }

        #atom-translate .atom-edit{
          cursor: pointer;
        }

        #atom-translate table, #atom-translate table td, #atom-translate table th{
          border: 1px solid #fff !important;
        }

        #atom-translate #strings p{
          margin:0;
          padding:3px 0;
        }

        #atom-translate .translation input, #atom-translate .translation textarea{
          border:0;
          padding: 0;
          margin: 0;
          width: 100%;
          color: #0066CC;
          background:none;
        }


        #atom-translate table td.processing input{
          color: #CC0000;
        }

        #atom-translate .index{
          width: 25px;
          text-align:right;
        }

        #atom-translate .index p{
          color: #999;
        }

        #atom-translate .plurals-1 .section{width: 97%;}
        #atom-translate .plurals-2 .section{width: 48%;}
        #atom-translate .plurals-3 .section{width: 32%;}
        #atom-translate .plurals-4 .section{width: 24%;}
        #atom-translate .plurals-5 .section{width: 19%;}
        #atom-translate .plurals-6 .section{width: 16%;}

      </style>


      <?php if(!$editing_allowed): ?>
      <div class="notice e">
        <?php atom()->te('To enable editing, activate a child theme and give WordPress write permissions to the child theme folder.'); ?>
      </div>
      <?php endif; ?>

     <?php if(!$this->has_translation): ?>
      <div class="notice">
        <?php atom()->te('There is no translation for your current site language (%s). The default english template was loaded.', $this->getLanguageByCode()); ?>
      </div>
      <?php endif; ?>

      <?php if($editing_allowed): ?>
      <h3>
        <?php atom()->te('Editing %1$s (%2$s), %3$s translated', basename(STYLESHEETPATH)."/lang/{$this->locale}.po", $this->getLanguageByCode(), "{$completed}%"); ?>
        <?php if(!is_readable(STYLESHEETPATH.'/lang/'.$this->locale.'.po') || ($this->cache)): ?>
        <span style="color:#FF0000">(<?php atom()->te('unsaved'); ?>)</span>
        <?php endif; ?>
     </h3>
      <?php endif; ?>

      <div class="clear-block">
        <p>
         <label for="translator" <?php if(!$editing_allowed) echo 'class="disabled"'; ?>><?php atom()->te('Translator(s):'); ?></label>
         <br />
         <input type="text" <?php disabled(!$editing_allowed); ?> size="140" id="translator" name="translator" value="<?php echo htmlspecialchars($translator); ?>" />
        </p>

        <p>
         <label for="team" <?php if(!$editing_allowed) echo 'class="disabled"'; ?>><?php atom()->te('Team:'); ?></label>
         <br />
         <input type="text" <?php disabled(!$editing_allowed); ?> size="70" id="team" name="team" value="<?php echo htmlspecialchars($team); ?>" />
        </p>
      </div>


      <?php if($editing_allowed): ?>
      <div class="notice w">
        <?php atom()->te('Click on the black text below to edit the strings. Modified strings will temporarily be auto-saved. Press the "%s" button to export strings to .mo / .po files in your child theme directory.', atom()->t('Save Changes')); ?>
      </div>
      <?php endif; ?>


      <table class="widefat list plurals-<?php echo $this->plural_count; ?>">
       <thead>
         <th class="index">#</th>
         <th class="section"><?php atom()->te('Singular form'); ?></th>
         <th class="section" <?php if(empty($entry->is_plural)) echo 'colspan="'.($this->plural_count - 1).'"'; ?>><?php atom()->te('Plural form(s)'); ?></th>
       </thead>

       <tbody id="strings">
       <?php foreach($original_strings->entries as $key => $entry): ?>
       <tr class="<?php echo ($index % 2) ? 'even': 'odd'; ?>">
         <td class="index">
           <p><?php echo ++$index; ?></p>
         </td>

         <td class="atom-edit section <?php if($this->hasTranslation($key)) echo 'processed'; ?>" <?php if(empty($entry->is_plural)) echo 'colspan="'.($this->plural_count).'"'; ?>>
           <p class="original" <?php $this->references($key); ?>><?php echo htmlspecialchars($entry->singular); ?></p>
           <p class="translation" data-string="<?php echo htmlspecialchars($key); ?>"><?php echo $this->getTranslation($key); ?></p>
         </td>

         <?php if($entry->is_plural): ?>
         <?php for($plural_index = 1; $plural_index < $this->plural_count; $plural_index++): ?>
         <td class="atom-edit section <?php if($this->hasTranslation($key, $plural_index)) echo 'processed'; ?>">
           <p class="original"><?php echo htmlspecialchars($entry->plural); ?></p>
           <p class="translation plural" data-index="<?php echo $plural_index; ?>" data-string="<?php echo htmlspecialchars($key); ?>"><?php echo $this->getTranslation($key, $plural_index); ?></p>
         </td>
         <?php endfor; ?>
         <?php endif; ?>
       </tr>
       <?php endforeach; ?>
       </tbody>

      </table>

      <script type="text/javascript">

        jQuery(document).ready(function($){


          $('#strings').delegate('.atom-edit:not(.active)', 'click', function(edit_event){

            edit_event.preventDefault();

            var edit           = $(this).addClass('active'),
                field          = $('.translation', this),
                editable_value = field.html().replace(/'/g, '&#39;').replace(/"/g, '&#34;'),
                input          = ($('input', field).length > 0) ? $('input', field) : $('<input type="text" value="' + editable_value + '" />'),
                key            = field.data('string') || '';

            key = $('<div/>').text(key).html().replace(/'/g, '&#39;').replace(/"/g, '&#34;');

            if(!key) return;
            field.data('editable_value', editable_value).empty().append(input);

            input.focus().bind('blur keypress', function(event){

              // esc key pressed? return...
              if(event.type == 'keypress'){
                var keycode = event.which || event.keyCode || event.charCode;
                if(keycode == 27){
                  field.html(editable_value);
                  edit.removeClass('active');
                  return false;
                };

                // only intercept the enter key
                if(keycode != 13){
                  edit.removeClass('active');
                  return true;
                }
              }

              event.preventDefault();

              // get the value and escape html
              var translation = $('<div/>').text($(this).val()).html().replace(/'/g, '&#39;').replace(/"/g, '&#34;'),
                  index = field.data('index') || 0;

              // check if changes were made, so we don't fire useless ajax requests
              if(translation == editable_value){
                field.html(editable_value);
                edit.removeClass('active');
                return false;
              }

              // save string in cache
              $.ajax({
                type: 'GET',
                url: ajaxurl,
                data: {
                  action: 'cache_translation_string',
                  key: key,
                  index: index,
                  translation: translation,
                  _ajax_nonce: '<?php echo atom()->interface->getNonce(); ?>'
                },
                context: this,
                beforeSend: function(){
                  field.parents('td').removeClass('processed').addClass('processing');
                },
                success: function(data){
                  if(data){
                    field.html(data).parents('td').removeClass('processing').addClass('processed');

                    if($('input:hidden', field.parent()).length > 0)
                      $('input:hidden', field.parent()).val(data);
                    else
                      $('<input type="hidden" name="translation[' + key + '][' + index + ']" />').appendTo(field.parent()).val(data);

                  }else{
                    field.html(editable_value);
                  }

                  edit.removeClass('active');
                }
              });
            });

          });

        });


      </script>

    </div>
    <!-- /tab: translate -->
    <?php
  }



 /*
  * Output the translation references as a title attribute
  *
  * @since   0.9
  * @param   string $key
  */
  protected function references($key){

    $original_strings = $this->getPo('original');

    // make sure the entry exists, if not load it from the default .po
    $entry = $original_strings->entries[$key];

    $references = array();

    if(!empty($entry->references))
      foreach($entry->references as $reference)
        $references[] = basename($reference);

    if($references)
      echo 'title="'.implode('; ', $references).'"';
  }



 /*
  * Check if a string has a translation available.
  *
  * @since    0.9
  * @param    string $key           Original string
  * @param    int $plural_index     Plural form. 0 = none (singular); 1, 2, 3 etc = plural forms
  * @return   bool
  */
  protected function hasTranslation($key, $plural_index = 0){

    $current_translation = $this->getPo('current');
    $original_strings = $this->getPo('original');

    // make sure the entry exists, if not load it from the default .po
    $entry = empty($current_translation->entries[$key]) ? $original_strings->entries[$key] : $current_translation->entries[$key];

    // type is stored as a numerical key
    $type = ($plural_index !== 0) ? 'plural' : 'singular';

    return isset($this->cache[$key][$plural_index]) || !empty($entry->translations[$plural_index]);
  }



 /*
  * Get the translation of a string.
  * If not translation is found in the current po or in the cache, then the original string is returned.
  *
  * @since     0.9
  * @param     string $key          Original string
  * @param     int $plural_index    Plural form. 0 = none (singular); 1, 2, 3 etc = plural forms
  * @return    string
  */
  protected function getTranslation($key, $plural_index = 0){

    $current_translation = $this->getPo('current');
    $original_strings = $this->getPo('original');

    // make sure the entry exists, if not load it from the default .po
    $entry = empty($current_translation->entries[$key]) ? $original_strings->entries[$key] : $current_translation->entries[$key];

    // type is stored as a numerical key
    $type = ($plural_index !== 0) ? 'plural' : 'singular';

    $prev_translated_string = empty($entry->translations[$plural_index]) ? $entry->$type : $entry->translations[$plural_index];
    $translated_string = isset($this->cache[$key][$plural_index]) ? $this->cache[$key][$plural_index] : $prev_translated_string;

    return htmlspecialchars($translated_string);
  }



 /*
  * The theme setttings save handler.
  * It will attempt to write the .po / .mo files on the server.
  *
  * @since 0.9
  */
  public function save($status){

    // check if it's our turn, and if the user role allows editing
    if(!isset($_POST['translator']) || !isset($_POST['team'])) return;

    $current_translation = $this->getPo('current');
    $original_strings = $this->getPo('original');

    // get cached strings
    if(($cache = get_transient('atom_translation_cache')) === false) $cache = array();

    $new_headers = array(
      'Project-Id-Version'    => atom()->getThemeName().' '.atom()->getThemeVersion(),
      'POT-Creation-Date'     => current_time('mysql'),                                // @todo
      'PO-Revision-Date'      => current_time('mysql'),
      'Last-Translator'       => stripslashes($_POST['translator']),
      'Language-Team'         => stripslashes($_POST['team']),
      'X-Poedit-Language'     => $this->getLanguageByCode(),
      'X-Generator'           => 'ATOM '.Atom::VERSION.' (Translate Module)',
      'X-Poedit-KeywordsList' => '_a;_ae;_an:1,2;nt:1,2;nte:1,2;t;te\n',               // Atom functions
      'X-Poedit-SearchPath-0' => str_replace(array('/', '\\'), '\\', TEMPLATEPATH),    // local paths to the theme files for PoEdit
      'X-Poedit-SearchPath-1' => '.\n"',
      'X-Poedit-SearchPath-2' => '..\n"',
    );

    // merge default headers from WP's language file with the new ones above
    $new_headers = array_merge($GLOBALS['l10n']['default']->headers, $new_headers);
    $original_strings->headers = array_merge($original_strings->headers, $new_headers);
    ksort($original_strings->headers);

    // process each string
    foreach($original_strings->entries as $key => &$entry){

      // lowest priority - old .po file
      if(!empty($current_translation->entries[$key]))
        $entry->translations = $current_translation->entries[$key]->translations + $entry->translations;

      // higher priority - string cache
      if(isset($cache[$key]))
        $entry->translations = $cache[$key] + $entry->translations;

      // highest priority - $_POST
      if(isset($_POST['translation'][$key]))
        $entry->translations = array_map('html_entity_decode', $_POST['translation'][$key]) + $entry->translations;

      // still empty? remove the entry then
      if(empty($entry->translations))
        unset($original_strings->entries[$key]);

    }

    $path  = STYLESHEETPATH.'/lang/';
    $po    = $path.$this->locale.'.po';
    $error = false;

    // attempt to create the 'lang' directory if it doesn't exist
    if(WP_Filesystem())
      if(!$GLOBALS['wp_filesystem']->is_dir($path))
        if(!$GLOBALS['wp_filesystem']->mkdir($path, FS_CHMOD_DIR)) $error = true;

    // export to .po, and convert the .po to .mo, return the error code from above if fails
    if($error || !$original_strings->export_to_file($po, true) || !$this->writeMo($po)) return 30;

    // remove string cache, we no longer need it because we just saved the strings in the file
    delete_transient('atom_translation_cache');

    return $status;
  }



 /*
  * Clean up data
  * from: php.mo 0.1 by Joss Crowcroft (http://www.josscrowcroft.com)
  *
  * @link    http://www.gnu.org/software/gettext/manual/gettext.html#PO-Files
  * @since   0.9
  */
  protected function MoCleanHelper($x){
    if(is_array($x)){
      foreach($x as $k => $v) $x[$k] = $this->MoCleanHelper($v);

    }else{
      if($x[0] == '"') $x = substr($x, 1, -1);
      $x = str_replace("\"\n\"", '', $x);
      $x = str_replace('$', '\\$', $x);
      $x = @eval("return \"$x\";");
    }

    return $x;
  }



 /*
  * Parse gettext .po files
  * from: php.mo 0.1 by Joss Crowcroft (http://www.josscrowcroft.com)
  *
  * @link    http://www.gnu.org/software/gettext/manual/gettext.html#PO-Files
  * @since   0.9
  */
  protected function parsePo($in){

    // read .po file
    $fc = file_get_contents($in);

    // normalize newlines
    $fc = str_replace(array("\r\n", "\r"), array("\n", "\n"), $fc);

    // results array
    $hash = array();

    // temporary array
    $temp = array();

    // state
    $state = null;
    $fuzzy = false;

    // iterate over lines
    foreach(explode("\n", $fc) as $line){
      $line = trim($line);
      if($line === '') continue;

      list($key, $data) = explode(' ', $line, 2);

      switch($key){
        case '#,': // flag...
          $fuzzy = in_array('fuzzy', preg_split('/,\s*/', $data));

        case '#':  // translator-comments
        case '#.': // extracted-comments
        case '#:': // reference...
        case '#|': // msgid previous-untranslated-string
          // start a new entry
          if(sizeof($temp) && array_key_exists('msgid', $temp) && array_key_exists('msgstr', $temp)){
            if(!$fuzzy) $hash[] = $temp;
            $temp = array ();
            $state = null;
            $fuzzy = false;
          }
          break;

        case 'msgctxt':      // context
        case 'msgid':        // untranslated-string
        case 'msgid_plural': // untranslated-string-plural
          $state = $key;
          $temp[$state] = $data;
          break;

        case 'msgstr':       // translated-string
          $state = 'msgstr';
          $temp[$state][] = $data;
          break;

        default:
          if(strpos($key, 'msgstr[') !== false){ // translated-string-case-n
            $state = 'msgstr';
            $temp[$state][] = $data;
          }else{
            // continued lines
            switch($state){
              case 'msgctxt':
              case 'msgid':

              case 'msgid_plural':
                $temp[$state] .= "\n".$line;
                break;

              case 'msgstr':
                $temp[$state][sizeof($temp[$state]) - 1] .= "\n".$line;
                break;

              default: // parse error
              return false;
            }
        }
        break;
      }
    }

    // add final entry
    if($state == 'msgstr') $hash[] = $temp;

    // Cleanup data, merge multiline entries, reindex hash for ksort
    $temp = $hash;
    $hash = array();
    foreach($temp as $entry){
      foreach($entry as & $v){
        $v = $this->MoCleanHelper($v);
        if($v === false) return false; // parse error
      }
      $hash[$entry['msgid']] = $entry;
    }

    return $hash;
  }



 /*
  * Write a GNU gettext style machine object.
  * from: php.mo 0.1 by Joss Crowcroft (http://www.josscrowcroft.com)
  *
  * @link   http://www.gnu.org/software/gettext/manual/gettext.html#MO-Files
  * @since   0.9
  */
  protected function writeMo($input, $output = false){

    if(!$output)
      $output = str_replace('.po', '.mo', $input);

    $hash = $this->parsePo($input);

    if($hash === false) return false;

    // sort by msgid
    ksort($hash, SORT_STRING);

    // our mo file data
    $mo = '';

    // header data
    $offsets = array ();
    $ids = $strings = '';

    foreach($hash as $entry){

      $id = $entry['msgid'];

      if(isset($entry['msgid_plural']))
        $id .= "\x00".$entry['msgid_plural'];

      // context is merged into id, separated by EOT (\x04)
      if(array_key_exists('msgctxt', $entry))
        $id = $entry['msgctxt']."\x04".$id;

      // plural msgstrs are NULL-separated
      $str = implode("\x00", $entry['msgstr']);

      // keep track of offsets
      $offsets[] = array(strlen($ids), strlen($id), strlen($strings), strlen($str));

      // plural msgids are not stored (?)
      $ids .= $id."\x00";
      $strings .= $str."\x00";
    }

    // keys start after the header (7 words) + index tables ($#hash * 4 words)
    $key_start = 7 * 4 + sizeof($hash) * 4 * 4;

    // values start right after the keys
    $value_start = $key_start + strlen($ids);

    // first all key offsets, then all value offsets
    $key_offsets = $value_offsets = array();

    // calculate
    foreach($offsets as $v){
      list($o1, $l1, $o2, $l2) = $v;
      $key_offsets[]           = $l1;
      $key_offsets[]           = $o1 + $key_start;
      $value_offsets[]         = $l2;
      $value_offsets[]         = $o2 + $value_start;
    }
    $offsets = array_merge($key_offsets, $value_offsets);

    // write header
    $mo .= pack(
      'Iiiiiii',
      0x950412de,                // magic number
      0,                         // version
      sizeof($hash),             // number of entries in the catalog
      7 * 4,                     // key index offset
      7 * 4 + sizeof($hash) * 8, // value index offset,
      0,                         // hashtable size (unused, thus 0)
      $key_start                 // hashtable offset
    );

    // offsets
    foreach($offsets as $offset)
      $mo .= pack('i', $offset);

    // ids
    $mo .= $ids;

    // strings
    $mo .= $strings;

    return (file_put_contents($output, $mo) !== false);
  }


}