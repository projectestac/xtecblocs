// theme preview

// 960gs overlay -- used in theme preview mode only
(function($){
  $.fn.gridOverlay = function(cfg){
    cfg = $.extend({
      columns: 12,
      columnWidth: 60,
      color: '#ccc',
      opacity: 40
    }, cfg);

    var frame = $('<div style="display:none;overflow:hidden;position:absolute;left:0;top:0;width:100%;height:'+(this.height()-40)+'px;z-index: 999;">'),
        grid = $('<div style="position:relative;width:960px;height:100%;margin:0 auto;overflow:hidden;">'),
        trigger = $('<a style="position:fixed;top:140px;left:20px;cursor:pointer;padding:5px;background:red;color:#fff;font-weight:bold;z-index:9999;">GRID</a>');

    trigger.appendTo('#page').click(function(){
      frame.toggle();
    });

    for(var i = 0; i < cfg.columns; i++){ // columns
      grid.append('<div style="width:'+((i != 0) ? 20 : 10)+'px;float:left;height:100%;"></div>');
      grid.append('<div style="width:'+cfg.columnWidth+'px;height:100%;float:left;background:'+cfg.color+';opacity:'+(cfg.opacity/100)+';"></div>');
    }

    return this.each(function(){
      $(this).prepend(frame.prepend(grid));
    });

  };
})(jQuery);



/**
  The MIT License

  Copyright (c) 2010 Daniel Park (http://metaweb.com, http://postmessage.freebaseapps.com)

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in
  all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
  THE SOFTWARE.
**/
(function(window, $, undefined){

  $.fn.pm = function(){
    throw("usage: \nto send:    $.pm(options)\nto receive: $.pm.bind(type, fn, [origin])");    
  };

  // send postmessage
  $.pm = window.pm = function(options){
    pm.send(options);
  };

  // bind postmessage handler
  $.pm.bind = window.pm.bind = function(type, fn, origin){
    pm.bind(type, fn, origin);
  };

  // unbind postmessage handler
  $.pm.unbind = window.pm.unbind = function(type, fn){
    pm.unbind(type, fn);
  };

  // default postmessage origin on bind
  $.pm.origin = window.pm.origin = null;

  // default postmessage polling if using location hash to pass postmessages
  $.pm.poll = window.pm.poll = 200;

  var pm = {

    send: function(options){
      var o = $.extend({}, pm.defaults, options),
          target = o.target;

      if (!o.target){
        throw("postmessage target window required");        
      }

      if(!o.type){
        throw("postmessage type required");       
      }

      var msg = {data:o.data, type:o.type};

      if(o.success)
        msg.callback = pm._callback(o.success);

      if(o.error)
        msg.errback = pm._callback(o.error);

      if(("postMessage" in target)){
        pm._bind();
        target.postMessage(JSON.stringify(msg), o.origin || '*');
      }

    },

    bind: function(type, fn, origin){

      if(("postMessage" in window))
        pm._bind();

      var l = pm.data("listeners.postmessage");

      if(!l){
        l = {};
        pm.data("listeners.postmessage", l);
      }

      var fns = l[type];
      if(!fns){
        fns = [];
        l[type] = fns;
      }
      fns.push({fn:fn, origin:origin || $.pm.origin});
    },


    unbind: function(type, fn){
      var l = pm.data("listeners.postmessage");
      if(!l) return;
      if(type){
        if(fn){
          // remove specific listener
          var fns = l[type];
          if(fns){
            var m = [];
            for(var i=0,len=fns.length; i<len; i++){
              var o = fns[i];
              if(o.fn !== fn)
                m.push(o);

            }
            l[type] = m;
          }

        }else{
          // remove all listeners by type
          delete l[type];
        }

      }else{
        // unbind all listeners of all type
        for(var i in l)
          delete l[i];
      }

    },

    data: function(k, v){
      if(v === undefined)
        return pm._data[k];

      pm._data[k] = v;
      return v;
    },

    _data: {},

    _CHARS: '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split(''),

    _random: function(){
      var r = [];
      for(var i=0; i<32; i++)
        r[i] = pm._CHARS[0 | Math.random() * 32];

      return r.join('');
    },

    _callback: function(fn){
      var cbs = pm.data("callbacks.postmessage");
      if(!cbs){
        cbs = {};
        pm.data("callbacks.postmessage", cbs);
       }
       var r = pm._random();
       cbs[r] = fn;
       return r;
    },

    _bind: function(){
      // are we already listening to message events on this w?
      if(!pm.data("listening.postmessage")){
        if(window.addEventListener)
          window.addEventListener("message", pm._dispatch, false);
        else if(window.attachEvent)
          window.attachEvent("onmessage", pm._dispatch);

        pm.data("listening.postmessage", 1);
      }
    },

    _dispatch: function(e){
      //console.log("$.pm.dispatch", e, this);
      try{
        var msg = $.parseJSON(e.data);

      }catch (ex){
        throw("postmessage data invalid json: " + ex);        
      }

      if(!msg.type){
        throw("postmessage message type required");        
      }

      var cbs = pm.data("callbacks.postmessage") || {},
          cb = cbs[msg.type];

      if(cb){
        cb(msg.data);

      }else{
        var l = pm.data("listeners.postmessage") || {},
            fns = l[msg.type] || [];

        for(var i=0,len=fns.length; i<len; i++){
          var o = fns[i];
          if(o.origin && e.origin !== o.origin){            
            if (msg.errback){
               // notify post message errback
               var error = {
                   message: "postmessage origin mismatch",
                   origin: [e.origin, o.origin]
               };
               pm.send({target:e.source, data:error, type:msg.errback});
            }
            throw("postmessage message origin mismatch - " + e.origin + ' ' + o.origin);                        
          }
          try{
            var r = o.fn(msg.data);
            if(msg.callback)
              pm.send({target:e.source, data:r, type:msg.callback});

          }catch(ex){
            if(msg.errback)
              // notify post message errback
              pm.send({target:e.source, data:ex, type:msg.errback});

          }
        };
      }
    }
  };


  $.extend(pm, {
    defaults: {
      target: null,  /* target window (required) */
      url: null,     /* target window url (required if no window.postMessage or hash == true) */
      type: null,    /* message type (required) */
      data: null,    /* message data (required) */
      success: null, /* success callback (optional) */
      error: null,   /* error callback (optional) */
      origin: '*'    /* postmessage origin (optional) */
    }
  });

})(this, jQuery);



jQuery(document).ready(function($){

  if(atom_config.preview_mode){
    $('#page').gridOverlay();

    pm.bind('background_color', function(data){
      if(data.color){
        $(data.selector).css('background-color', '#' + data.color);
        $('body').addClass('cbgc');
        return;
      }
      $(data.selector).css('background-color', '');
      $('body').removeClass('cbgc');
    });

    pm.bind('logo', function(data){
      $('#logo a img').remove();
      $('#logo a').html((data.url != 'remove') ? '<img src="' + data.url + '" />' : data.title);
    });

    pm.bind('background_image', function(data){
      if(data.url != 'remove'){
        $(data.selector).css('background-image', 'url("' + data.url + '")');
        $('body').addClass('cbgi');
        return;
      }
      $(data.selector).css('background-image', '');
      $('body').removeClass('cbgi');
    });

    pm.bind('color-scheme', function(data){

      var core = $('link#' + atom_config.id + '-core-css'),
          color = $('link#' + atom_config.id + '-style-css');

      // we need this so the style unloads from browser memory
      $(core, color).attr('disabled', 'disabled');

      // @note: we could just change the href, but it appears IE fails to correctly render the styles if we do this multiple times...
      if(data != ''){
        core.replaceWith('<link rel="stylesheet" id="' + atom_config.id + '-core-css" href="' + atom_config.theme_url + '/css/core.css" type="text/css" />');
        color.replaceWith('<link rel="stylesheet" id="' + atom_config.id + '-style-css" href="' + atom_config.theme_url + '/css/style-' + data + '.css" type="text/css" />');
      }
    });

    pm.bind('layout', function(data){
      $('body').removeClass('c1 c2 c2left c2right c3 c3left c3right').addClass(data);
    });

    pm.bind('page_width', function(data){
      $('body').removeClass('fluid fixed').addClass(data);
    });

    pm.bind('page_width_max', function(data){
      data = parseInt(data);
      if(data > 400) $('.page-content').css('max-width', data + 'px');
    });

    pm.bind('dimensions', function(data){
      var s = data.sizes.split(';');
      s[0] = parseInt(s[0]);
      s[1] = parseInt(s[1]);
      var unit = data.unit;
      var gs = data.gs;
      switch(data['layout']){
        case 'c1':
          $('#primary-content').css({'width': gs+unit, 'left': '0'});
          $('#mask-1').css({'right': 0});
          $('#mask-2').css({'right': 0});
          break;
        case 'c2left':
          $('#primary-content').css({'width': gs-s[0]+unit, 'left': gs+unit});
          $('#sidebar').css({'width': s[0]+unit, 'left': '0'});
          $('#mask-1').css({'right': gs-s[0]+unit});
          $('#mask-2').css({'right': 0});
          break;
        case 'c2right':
          $('#primary-content').css({'width': gs-(gs-s[0])+unit, 'left': gs-s[0]+unit});
          $('#sidebar').css({'width': gs-s[0]+unit, 'left': gs-s[0]+unit});
          $('#mask-1').css({'right': gs-s[0]+unit});
          $('#mask-2').css({'right': 0});
          break;
        case 'c3':
          $('#primary-content').css({'width': (gs-s[0]-(gs-s[1]))+unit, 'left': gs+unit});
          $('#sidebar').css({'width': s[0]+unit, 'left': gs-s[1]+unit});
          $('#sidebar2').css({'width': gs-s[1]+unit, 'left': (gs-s[0])+unit});
          $('#mask-2').css({'right': gs-s[1]+unit});
          $('#mask-1').css({'right': ((gs-s[0])-(gs-s[1]))+unit});
          break;
        case 'c3left':
          $('#primary-content').css({'width': (gs-s[1])+unit, 'left': (gs+(s[1]-s[0]))+unit});
          $('#sidebar').css({'width': s[0]+unit, 'left': (s[1]-s[0])+unit});
          $('#sidebar2').css({'width': (s[1]-s[0])+unit, 'left': (s[1]-s[0])+unit});
          $('#mask-2').css({'right': (gs-s[1])+unit});
          $('#mask-1').css({'right': (s[1]-s[0])+unit});
          break;
        case 'c3right':
          $('#primary-content').css({'width': s[0]+unit, 'left': ((gs-s[0]-(gs-s[1]))+(gs-s[1]))+unit});
          $('#sidebar').css({'width': (gs-s[0]-(gs-s[1]))+unit, 'left': (gs-s[0])+unit});
          $('#sidebar2').css({'width': (gs-s[1])+unit, 'left': (gs-s[0])+unit});
          $('#mask-2').css({'right': (gs-s[1])+unit});
          $('#mask-1').css({'right': (s[1]-s[0])+unit});
          break;
      }
    });

    // remove all links to other pages,
    // because we loose our live styles if we change the page
    $('body a').each(function(){
      var href = $(this).attr('href'); // this.href returns the full URL, we don't want that

      if(href && href.indexOf('#') !== 0)
        $(this).attr('href', '#');
    });

    pm({ // notify the parent document that the current document has loaded
      target: parent,
      type: 'themepreview-load',
      data: true
    });

    $(document).trigger('theme_preview');

  }

});