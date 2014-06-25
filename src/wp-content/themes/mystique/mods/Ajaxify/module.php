<?php
/**
 * Module Name: Ajaxify my Atom (EXPERIMENTAL)
 * Description: Get the target of internal links trough AJAX. Limited to browsers that support the HTML5 History API (FF 4+, Chrome, Opera 11.5+, Safari 5+)
 * Version: 2.0
 * Author: digitalnature
 * Author URI: http://digitalnature.eu
 * Auto Enable: no
 */


// class name must follow this pattern (AtomMod + directory name)
class AtomModAjaxify extends AtomMod{

  // available public variables from parent class:
  //
  // $this->url  - this module's url path
  // $this->dir  - this module's directory

  public function onInit(){
    if(!is_admin()){
      add_action('wp_enqueue_scripts', array($this, 'enqueueJS'));
      add_action('wp_footer',          array($this, 'inlineJS'));
    }
  }


  public function enqueueJS(){
    wp_enqueue_script('atom-ajaxify', $this->url.'/ajaxify.js', array('jquery', ATOM), '2.0', true);
  }

  public function inlineJS(){ ?>
    <script>

    jQuery(document).ready(function($){
      $('#page').ajaxify({links: '#logo a, .nav a, h2.title a, #footer a, .post-std a, a.comments, .post-tags a, .page-navi a, .box ul a, .menu a, .post-links a, .tagcloud a, .block .title small a'});
    });
    </script> <?php
  }


}
