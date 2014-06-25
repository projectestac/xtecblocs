<?php
/**
 * Module Name: User Country Flags
 * Description: Display visitor country flags in comments & enable the <code>{FLAG}</code> variable in comment-related widgets. Relies on the <a href="http://firestats.cc/wiki/ip2c">IP2C database</a>
 * Version: 1.0
 * Author: digitalnature
 * Author URI: http://digitalnature.eu
 * Auto Enable: no 
 */



// class name must follow this pattern (AtomMod + directory name)
class AtomModCountryFlags extends AtomMod{

  const IP2C_MAX_INT = 0x7fffffff;

  private
    $resource,
    $m_firstTableOffset,
    $m_numRangesFirstTable,
    $m_secondTableOffset,
    $m_numRangesSecondTable,
    $m_countriesOffset,
    $m_numCountries;

  // available public variables from parent class:
  //
  // $this->url  - this module's url path
  // $this->dir  - this module's directory

  // required method
  public function onInit(){

    // hooks
    atom()->add('comment_author',                   array($this, 'commentAuthor'), 10, 3);
    atom()->add('widget_comments_keywords',         array($this, 'widgetTemplateVars'), 10, 3);
    atom()->add('widget_top_commenters_keywords',   array($this, 'widgetTemplateVars'), 10, 3);

    add_action('wp_enqueue_scripts',                array($this, 'assets'));
    add_filter('comment_class',                     array($this, 'commentClass'));
  }



  // gets the country info based on a IP address
  public function getCountry($ip, $mode = '') {

    // load bin file if not already loaded
    if(!$this->resource){
      $this->resource = fopen($this->dir.'/ip-to-country.bin', 'rb');

      if(!$this->resource)
        throw new Exception('Country Flags: The IP database could not be read!');

      if(fread($this->resource, 4) != 'ip2c')
        throw new Exception('Country Flags: Incorrect signature in country database!');

      $version = $this->_readInt();

      if($version != 2)
        throw new Exception("Country Flags: Incorrect country database format version ({$version})");

      $this->m_firstTableOffset      = $this->_readInt();
      $this->m_numRangesFirstTable   = $this->_readInt();
      $this->m_secondTableOffset     = $this->_readInt();
      $this->m_numRangesSecondTable  = $this->_readInt();
      $this->m_countriesOffset       = $this->_readInt();
      $this->m_numCountries          = $this->_readInt();
    }

    $int_ip = ip2long($ip);

    // happens on 64bit systems
    if($int_ip > self::IP2C_MAX_INT){
      // shift to signed int32 value
      $int_ip -= self::IP2C_MAX_INT;
      $int_ip -= self::IP2C_MAX_INT;
      $int_ip -= 2;
    }

    if($int_ip >= 0){
      $key = $this->findCountryCode($int_ip, 0, $this->m_numRangesFirstTable, true);
    }
    else{
      $nip = (int)($int_ip + self::IP2C_MAX_INT + 2); // the + 2 is a bit wierd, but required.
      $key = $this->findCountryCode($nip, 0, $this->m_numRangesSecondTable, false);
    }

    $country = ($key == false || $key == 0) ? false : $this->findCountryKey($key, 0, $this->m_numCountries);

    if($country){
      $data = array('code' => strtolower($country['id2']), 'code3' => strtolower($country['id3']), 'name' => ucwords(strtolower($country['name'])));

      if($mode === 'html')
        return '<abbr title="'.$data['name'].'" class="flag '.$data['code'].'">'.$country['id3'].'</abbr>';

      return $mode ? $data[$mode] : $data;

    }else{
      return $mode ? '' : false;

    }
  }



  // country flags css
  public function assets(){
    wp_enqueue_style(ATOM.'-flags', $this->url.'/flags.css', array(ATOM.'-core'), atom()->getThemeVersion());
  }



  // add country info to comment class
  public function commentClass($classes){
    global $comment;

    if(!isset($comment->country))
      $comment->country = $this->getCountry($comment->comment_author_IP);

    if(!empty($comment->country)) $classes[] = "country-{$comment->country['code']}";
    return $classes;
  }



  // country flag next to comment author link
  public function commentAuthor($output, $comment){
    if(!isset($comment->country))
      $comment->country = $this->getCountry($comment->comment_author_IP);

    if(!empty($comment->country))
      $output .= "<abbr title=\"{$comment->country['name']}\" class=\"flag {$comment->country['code']}\">&nbsp;</abbr>";

    return $output;
  }

  // add {FLAG} template variable to recent comments and top commenters widgets
  public function widgetTemplateVars($vars, $comm, $args){
    $vars['FLAG'] = ($country = $this->getCountry($comm->comment_author_IP)) ? '<abbr title="'.$country['name'].'" class="flag '.$country['code'].'">&nbsp;</abbr>' : '';
    return $vars;
  }


  private function findCountryCode($ip, $startIndex, $endIndex, $firstTable, $d = 0){

    while(1){
      $middle = (int)(($startIndex + $endIndex) / 2);
      $mp = $this->getPair($middle, $firstTable);
      $mip = $mp['ip'];

      if($ip < $mip){
        if($startIndex + 1 == $endIndex)
          return false; // not found

        $endIndex = $middle;
        continue;

      }elseif($ip > $mip){
        $np = $this->getPair($middle + 1, $firstTable);

        if($ip < $np['ip']){
          return $mp['key'];

        }else{
          if($startIndex + 1 == $endIndex)
            return false; // not found

          $startIndex = $middle;
          continue;
        }

      }else{ // ip == mip
        return $mp['key'];

      }
    }
  }


  private function findCountryKey($code, $startIndex, $endIndex){

    $i = 0;
    while(1){

      if($i > 20)
        throw new Exception("Country Flags:  Internal error - endless loop detected, code = {$code}!");

      $i++;
      $middle = (int)(($startIndex + $endIndex) / 2);
      $mc = $this->getCountryCode($middle);

      if($mc == $code){ // found.
        return $this->loadCountry($middle);

      }elseif($code > $mc){
        if($middle + 1 == $endIndex)
          return ($this->getCountryCode($middle) == $code) ? $this->loadCountry($middle) : false;

        $startIndex = $middle;
        continue;

      }else{  // $code < $mc
        if($startIndex + 1 == $middle)
          return ($this->getCountryCode($startIndex) == $code) ? $this->loadCountry($startIndex) : false;

        $endIndex = $middle;
        continue;
      }
    }
  }


  private function loadCountry($index){

    $offset = $this->m_countriesOffset + $index * 10;
    fseek($this->resource, $offset);

    $id2c = fread($this->resource, 2);

    fread($this->resource, 1);
    $d = fread($this->resource, 3);
    $id3c = ($d != '   ') ? $d : '';
    $nameOffset = $this->_readInt();

    fseek($this->resource, $nameOffset);

    $len = unpack('n', fread($this->resource, 2));
    $name = ($len[1] != 0) ? fread($this->resource, $len[1]) : '';

    return array('id2' => $id2c, 'id3' => $id3c, 'name' => $name);
  }



  private function getCountryCode($index){
    $offset = $this->m_countriesOffset + $index * 10;

    fseek($this->resource, $offset);
    $a = unpack('n', fread($this->resource, 2));

    return $a[1];
  }



  private function getPair($index, $firstTable){

    $offset = 0;

    if($firstTable){
      if($index > $this->m_numRangesFirstTable)
        return array('key'=>false,'ip'=>0);

      $offset = $this->m_firstTableOffset + $index * 6;

    }else{
      if($index > $this->m_numRangesSecondTable)
        return array('key' => false, 'ip'=> 0);

      $offset = $this->m_secondTableOffset + $index * 6;
    }

    fseek($this->resource, $offset);

    return unpack('Nip/nkey', fread($this->resource, 6));
  }


  private function _readInt(){
    $a = unpack('N', fread($this->resource, 4));
    return $a[1];
  }

}
