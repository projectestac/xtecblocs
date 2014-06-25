

// dynamically injected scripts are not visible in the DOM,
// so this is the only way to keep track of loaded scripts appended to the body
var _loaded_scripts  = new Array();

// form input field helpers
// based on clearField 1.1 by Stijn Van Minnebruggen - http://www.donotfold.be
(function($){
  $.fn.ajaxify = function(cfg){

  cfg = $.extend({
      links: 'a',

    }, cfg);


  return $(this).delegate(cfg.links, 'click', function(event){

    var target = $(this).attr('href');

    // make sure it's local, no within wp-admin, and the browser supports HTML5 history spec...
    if((target.indexOf(atom_config.blog_url) !== -1) && (target.indexOf('wp-admin') === -1) && (!!(window.history && history.pushState))){

      event.preventDefault();

      // current page requested, do nothing
      if((window.location == target)) return false;

      // prevent other link clicks until our request is complete
      $(this).click(function(event){ return false; });

      var loader = $('<div class="loader black"></div>');

      loader.css({
        zIndex:1000,
        borderRadius: 10,
        position: 'absolute',
        top: ($(window).scrollTop() + $(window).height() / 2 - 40) + 'px',
        left: $(window).width() / 2 - 76,
        margin:'-' + (loader.height() / 2) + 'px 0 0 -'+(loader.width() / 2) + 'px'
      });

      $.ajax({
        url: target,
        type: 'GET',
        context: this,
        data: ({
          atom: 'ajaxify'
        }),
        dataType: 'html',
        beforeSend: function(){
          $('body').append(loader);
        },
         success: function(data){

          // extracting the the head and body tags
          var dataHead = data.match(/<\s*head.*>[\s\S]*<\s*\/head\s*>/ig).join(''),
              dataBody = data.match(/<\s*body.*>[\s\S]*<\s*\/body\s*>/ig).join(''),
              dataTitle = data.match(/<\s*title.*>[\s\S]*<\s*\/title\s*>/ig).join('');


          dataHead  = dataHead.replace(/<\s*head/gi, '<div');
          dataHead  = dataHead.replace(/<\s*\/head/gi, '</div');

          dataBody  = dataBody.replace(/<\s*body/gi, '<div');
          dataBody  = dataBody.replace(/<\s*\/body/gi, '</div');

          dataTitle = dataTitle.replace(/<\s*title/gi, '<div');
          dataTitle = dataTitle.replace(/<\s*\/title/gi, '</div');

          var newTitle        = $(dataTitle).text(),
              newBodyClass    = $(dataBody).attr('class'),
              inline_styles   = '',
              inline_scripts  = '';



          // change title
          document.title = newTitle;


          // disable link tags that are not found in the response
          $('link').each(function(){
            var href = $(this).attr('href');
            if(!href || $(dataHead).find('link[href="' + href + '"]').length == 0) $(this).remove();
          });

          // add new link tags
          $(dataHead).filter('link').each(function(){
            var href = $(this).attr('href');
            if(href && ($('link[href="' + href + '"]').length == 0)) $('head').append(this);
          });

          // disable scripts that are not found in the response
          // unfortunatelly we can't unload dynamically injected scripts, so they will keep running on any page
          $('script').each(function(){

            var src = $(this).attr('src');

            if(src && $(data).filter('script[src="' + src + '"]').length == 0){

              $(this).remove();

              if(jQuery.inArray(src, _loaded_scripts) > -1)
                _loaded_scripts = jQuery.grep(_loaded_scripts, function(value){
                  return value != src;
                });

            }

          });

          // add new script tags
          $(data).filter('script').each(function(){

            var src = $(this).attr('src');

            if(src && ($('script[src="' + src + '"]').length == 0) && jQuery.inArray(src, _loaded_scripts) < 0){
              $('head').append('<scr' + 'ipt src="' + src +'"></scr' + 'ipt>');
              _loaded_scripts.push(src);
            }

            if(!src){
              var code = $(this).html();
              // we don't want document.write ...
              if(code.indexOf('document.write') < 0)
                inline_scripts = inline_scripts + '\n' + code;

            }

          });

          // find inline css blocks
          $(dataHead).find('style').each(function(index, content){
            inline_styles = inline_styles + "\n" + $(content).html();
            $(this).remove();
          });

          // removing existing inline css
          $('head').find('style').remove();

          // inject new inline css
          if(inline_styles)
            $('head').append('<style>' + inline_styles + '</style>');


          // change body class
          $('body').attr('class', newBodyClass).removeClass('no-js'); // obviously we have js if we reached this point


          var new_content = $(dataBody);
          new_content.find('script').remove();

          // replace page content
          $('#page').replaceWith(new_content.find('#page'));


          // inject inline js
          if(inline_scripts)
            $('#page').append('<scr' + 'ipt>' + inline_scripts + '</scr' + 'ipt>')


          // re-initialize javascript
          $(document).trigger('atom_ready');

          loader.remove();

          var history_data = { url: target };

          history.pushState(history_data, newTitle, target);
          //window.scrollTo(0, $('h1').position().top());


          $(window).bind('popstate', function(event){
             // @todo: use ajax
             if(event.originalEvent.state.url)
               document.location.href == event.originalEvent.state.url;
          });

         }
       });
     };
   });


  };
})(jQuery);

