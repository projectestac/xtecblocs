
/**
 * jQuery extensions used by Atom.
 * The ones that are minified are original (unmodified) versions.
 * Check their author's websites for the uncompressed versions.
 *
 * @link http://digitalnature.eu
 *
 * @package ATOM
 * @subpackage Template
 */



/* jQuery Mouse Wheel Plugin
 * https://github.com/brandonaaron/jquery-mousewheel
 *
 * Copyright (c) 2011 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.0.6
 *
 * Requires: 1.2.2+
 */
(function(a){function d(b){var c=b||window.event,d=[].slice.call(arguments,1),e=0,f=!0,g=0,h=0;return b=a.event.fix(c),b.type="mousewheel",c.wheelDelta&&(e=c.wheelDelta/120),c.detail&&(e=-c.detail/3),h=e,c.axis!==undefined&&c.axis===c.HORIZONTAL_AXIS&&(h=0,g=-1*e),c.wheelDeltaY!==undefined&&(h=c.wheelDeltaY/120),c.wheelDeltaX!==undefined&&(g=-1*c.wheelDeltaX/120),d.unshift(b,e,g,h),(a.event.dispatch||a.event.handle).apply(this,d)}var b=["DOMMouseScroll","mousewheel"];if(a.event.fixHooks)for(var c=b.length;c;)a.event.fixHooks[b[--c]]=a.event.mouseHooks;a.event.special.mousewheel={setup:function(){if(this.addEventListener)for(var a=b.length;a;)this.addEventListener(b[--a],d,!1);else this.onmousewheel=d},teardown:function(){if(this.removeEventListener)for(var a=b.length;a;)this.removeEventListener(b[--a],d,!1);else this.onmousewheel=null}},a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})})(jQuery);



/*
 * jQuery Cookie plugin
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */
(function(g){g.cookie=function(h,b,a){if(1<arguments.length&&(!/Object/.test(Object.prototype.toString.call(b))||null===b||void 0===b)){a=g.extend({},a);if(null===b||void 0===b)a.expires=-1;if("number"===typeof a.expires){var d=a.expires,c=a.expires=new Date;c.setDate(c.getDate()+d)}b=""+b;return document.cookie=[encodeURIComponent(h),"=",a.raw?b:encodeURIComponent(b),a.expires?"; expires="+a.expires.toUTCString():"",a.path?"; path="+a.path:"",a.domain?"; domain="+a.domain:"",a.secure?"; secure":
""].join("")}for(var a=b||{},d=a.raw?function(a){return a}:decodeURIComponent,c=document.cookie.split("; "),e=0,f;f=c[e]&&c[e].split("=");e++)if(d(f[0])===h)return d(f[1]||"");return null}})(jQuery);



/*
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 *
 * Uses the built in easing capabilities added In jQuery 1.1
 * to offer multiple easing options
 *
 * TERMS OF USE - jQuery Easing
 *
 * Open source under the BSD License.
 *
 * Copyright © 2008 George McGinley Smith
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 *
 * Neither the name of the author nor the names of contributors may be used to endorse
 * or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */
jQuery.easing.jswing=jQuery.easing.swing;
jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(e,a,c,b,d){return jQuery.easing[jQuery.easing.def](e,a,c,b,d)},easeInQuad:function(e,a,c,b,d){return b*(a/=d)*a+c},easeOutQuad:function(e,a,c,b,d){return-b*(a/=d)*(a-2)+c},easeInOutQuad:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a+c:-b/2*(--a*(a-2)-1)+c},easeInCubic:function(e,a,c,b,d){return b*(a/=d)*a*a+c},easeOutCubic:function(e,a,c,b,d){return b*((a=a/d-1)*a*a+1)+c},easeInOutCubic:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a*a+c:
b/2*((a-=2)*a*a+2)+c},easeInQuart:function(e,a,c,b,d){return b*(a/=d)*a*a*a+c},easeOutQuart:function(e,a,c,b,d){return-b*((a=a/d-1)*a*a*a-1)+c},easeInOutQuart:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a*a*a+c:-b/2*((a-=2)*a*a*a-2)+c},easeInQuint:function(e,a,c,b,d){return b*(a/=d)*a*a*a*a+c},easeOutQuint:function(e,a,c,b,d){return b*((a=a/d-1)*a*a*a*a+1)+c},easeInOutQuint:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a*a*a*a+c:b/2*((a-=2)*a*a*a*a+2)+c},easeInSine:function(e,a,c,b,d){return-b*Math.cos(a/
d*(Math.PI/2))+b+c},easeOutSine:function(e,a,c,b,d){return b*Math.sin(a/d*(Math.PI/2))+c},easeInOutSine:function(e,a,c,b,d){return-b/2*(Math.cos(Math.PI*a/d)-1)+c},easeInExpo:function(e,a,c,b,d){return 0==a?c:b*Math.pow(2,10*(a/d-1))+c},easeOutExpo:function(e,a,c,b,d){return a==d?c+b:b*(-Math.pow(2,-10*a/d)+1)+c},easeInOutExpo:function(e,a,c,b,d){return 0==a?c:a==d?c+b:1>(a/=d/2)?b/2*Math.pow(2,10*(a-1))+c:b/2*(-Math.pow(2,-10*--a)+2)+c},easeInCirc:function(e,a,c,b,d){return-b*(Math.sqrt(1-(a/=d)*
a)-1)+c},easeOutCirc:function(e,a,c,b,d){return b*Math.sqrt(1-(a=a/d-1)*a)+c},easeInOutCirc:function(e,a,c,b,d){return 1>(a/=d/2)?-b/2*(Math.sqrt(1-a*a)-1)+c:b/2*(Math.sqrt(1-(a-=2)*a)+1)+c},easeInElastic:function(e,a,c,b,d){var e=1.70158,f=0,g=b;if(0==a)return c;if(1==(a/=d))return c+b;f||(f=0.3*d);g<Math.abs(b)?(g=b,e=f/4):e=f/(2*Math.PI)*Math.asin(b/g);return-(g*Math.pow(2,10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f))+c},easeOutElastic:function(e,a,c,b,d){var e=1.70158,f=0,g=b;if(0==a)return c;if(1==
(a/=d))return c+b;f||(f=0.3*d);g<Math.abs(b)?(g=b,e=f/4):e=f/(2*Math.PI)*Math.asin(b/g);return g*Math.pow(2,-10*a)*Math.sin((a*d-e)*2*Math.PI/f)+b+c},easeInOutElastic:function(e,a,c,b,d){var e=1.70158,f=0,g=b;if(0==a)return c;if(2==(a/=d/2))return c+b;f||(f=d*0.3*1.5);g<Math.abs(b)?(g=b,e=f/4):e=f/(2*Math.PI)*Math.asin(b/g);return 1>a?-0.5*g*Math.pow(2,10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f)+c:0.5*g*Math.pow(2,-10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f)+b+c},easeInBack:function(e,a,c,b,d,f){void 0==
f&&(f=1.70158);return b*(a/=d)*a*((f+1)*a-f)+c},easeOutBack:function(e,a,c,b,d,f){void 0==f&&(f=1.70158);return b*((a=a/d-1)*a*((f+1)*a+f)+1)+c},easeInOutBack:function(e,a,c,b,d,f){void 0==f&&(f=1.70158);return 1>(a/=d/2)?b/2*a*a*(((f*=1.525)+1)*a-f)+c:b/2*((a-=2)*a*(((f*=1.525)+1)*a+f)+2)+c},easeInBounce:function(e,a,c,b,d){return b-jQuery.easing.easeOutBounce(e,d-a,0,b,d)+c},easeOutBounce:function(e,a,c,b,d){return(a/=d)<1/2.75?b*7.5625*a*a+c:a<2/2.75?b*(7.5625*(a-=1.5/2.75)*a+0.75)+c:a<2.5/2.75?
b*(7.5625*(a-=2.25/2.75)*a+0.9375)+c:b*(7.5625*(a-=2.625/2.75)*a+0.984375)+c},easeInOutBounce:function(e,a,c,b,d){return a<d/2?0.5*jQuery.easing.easeInBounce(e,2*a,0,b,d)+c:0.5*jQuery.easing.easeOutBounce(e,2*a-d,0,b,d)+0.5*b+c}});



/*
 * Atom jQuery plugin.
 * This is actually a collection of plugins; a few of them are heavily modified versions of public plugins...

 * We are wrapping all of them as methods inside a single function called atom()
 * The main reason we're doing this is to avoid naming conflicts with javascript added by WP plugins;
 *
 * So instead of calling:
 *   $('.el').tabs();
 * we're doing it like:
 *   $('.e').atom('tabs');
 */
(function($){
  $.fn.atom = function(method, cfg){

    var methods = {

      // simple tabbed interface
      // @todo add option to load section content trough ajax (for theme settings)
      tabs: function(){

        return this.each(function(){

          // -- markup should follow this pattern:
          //
          //   <div class="tabs" id="my-tab-instance">
          //     <ul class="navi">
          //        <li class="active"><a href="#tab-first">first</a></li>
          //        <li><a href="#tab-second"second/a></li>
          //     </ul>
          //     <div class="section" id="tab-first">
          //        first
          //     </div>
          //     <div class="section" id="tab-second">
          //        second
          //     </div>
          //   </div>

          var tabs = $(this),
              instance = this.id;

          $('.section', tabs).css({'position' : 'absolute', 'width': '100%'});
          $('.sections', tabs).css('height', $('#' + $('.navi li.active a', tabs).attr('href'), tabs).outerHeight(true));

          $('.navi > li > a[href]', tabs).bind(tabs.data('event') || 'click', function(event){
            event.preventDefault();

            // only proceed if the current item is not selected and if there are no animations with the current block or block <li>'s
            if($('.sections, .sections li', tabs).is(':animated') || $(this).parent('li').hasClass('active')) return false;

            var current = $('#' + $('.navi li.active', tabs).removeClass('active').find('a').attr('href'), tabs),
                next = $('#' + $(this).parent('li').addClass('active').find('a').attr('href'), tabs);

            $.cookie(instance , next.attr('id'), { path: '/' });

            switch(tabs.data('fx')){
              case 'height':
                $('.sections', tabs).animate({ 'height': 0 }, { duration: 333, easing: 'easeInBack', complete: function(){
                  current.hide();
                  next.show();
                  $(this).animate({ 'height': next.outerHeight(true) }, { duration: 333, easing: 'easeOutCirc'});
                }});

              break;
              case 'fade':
                current.fadeOut(333);
                next.fadeIn(333);
                $('.sections', tabs).animate({ 'height': next.outerHeight(true) }, {
                   duration: 300,
                   specialEasing: { opacity: 'easeOutQuad',  height: 'easeOutExpo' }
                });
              break;
              default:
                current.hide();
                next.show();
                $('.sections', tabs).css('height', next.outerHeight(true));
              break;
            }

          });

          // override if tab is present in hash (URL)
          $('.navi li a', tabs).each(function(){
            if(window.location.hash == this.href)
              $(this).trigger(tabs.data('event') || 'click');
          });

        });

      },

      // creates a single tooltip with the current element's title, and appends it to the body
      // the tooltop is hidden/visible depending on the current mouse position...
      bubble: function(){
        var tip = $('<div class="tip"></div>').appendTo('body'),
            original_content = '',
            title = '';

        return this.mouseover(function(){
          var content = $(this).attr('title');
          var start_pos = content.indexOf('|') + 1;
          var end_pos = content.indexOf('|', start_pos);

          original_content = content;
          title = content.substring(start_pos,end_pos);
          content = content.substr(end_pos + 1, content.length);

          //title = content.match(/\|(.*?)\|/);
          //content = content.replace(/\|(.*?)\|/, "");

          tip.html(content);

          if(title)
            tip.prepend('<p><strong>' + title + '</strong></p>');

          $(this).attr('title', '');
          tip.show();
        }).mousemove(function(e){
          tip.css({
            top: e.pageY + 22,
            left: e.pageX + 10
          });
        }).mouseout(function(){
          tip.hide();
          $(this).attr('title', original_content);
        }).click(function(){
          tip.hide();
          $(this).attr('title', original_content);
        });

      },

      // form input field helpers
      // based on clearField 1.1 by Stijn Van Minnebruggen - http://www.donotfold.be
      clearField: function(){

        cfg = $.extend({
          blurClass:   'clearFieldBlurred',
          activeClass: 'clearFieldActive',
          attribute:   'data-default',
          value:       ''
        }, cfg);

        return this.each(function(){

          var field = $(this),
              placeholder = field.attr(cfg.attribute),
              has_pwd_label = false;

          // @todo: use the <placeholder> tag if the browser supports it;
          // currently very few browsers are able to style it, so we're sticking to clearField...
          //var placeholderSupported = !!('placeholder' in document.createElement($(this).get(0).tagName));

          cfg.value = field.val();

          if(placeholder == undefined)
            field.attr(cfg.attribute, field.val()).addClass(cfg.blurClass);
          else
            cfg.value = placeholder;

          if(field.is(':password')){
            var pwd_label = $('<input type="text" />').attr('class', field.attr('class')).val(placeholder).addClass('pass-label').hide();
            field.after(pwd_label);
            has_pwd_label = true;
            pwd_label.focus(function(){
              pwd_label.hide();
              field.show().focus();
            });

          }

          field.focus(function(){

            if(field.val() == placeholder && !field.is(':password'))
              field.val('').removeClass(cfg.blurClass).addClass(cfg.activeClass);

            if(field.attr('name') == 'url' && field.val() == '')
              field.val('http://').removeClass(cfg.blurClass).addClass(cfg.activeClass);
          });

          field.blur(function(){
            if((field.val() == '') || (field.attr('name') == 'url' && field.val() == 'http://')){
              if(field.is(':password') && has_pwd_label){
                field.hide();
                pwd_label.show();
              }else{
                field.val(placeholder).removeClass(cfg.activeClass).addClass(cfg.blurClass);
              }
            }else{
              field.removeClass(cfg.blurClass).addClass(cfg.activeClass);
            }
          });

          field.blur();

        });
      },

      // check if a field has been filled with other than default values (clears the default values)
      clearFieldCheck: function(){
        return this.each(function(){

           var check = function(field){

             field = $(field);

             if(field.val() == (field.data('default') || 'http://')) field.val('');

             if(field.is(':password')){
               field.next('.pass-label').hide();
               field.show();
             }
           }

          if($(this).is('form'))
            $('.clearField', this).each(function(){
              check(this);
            });

          else
            check(this);

        });
      },

      // show/hide a element
      // the link's target data attribute should contain the target element ID to toggle
      toggleVisibility: function(){
        return this.live('click', function(event){
          event.preventDefault();
          var target = $('#' + $(this).data('target'));

          if($('body').hasClass('no-fx')) return $(target).toggle(0);
          return $.support.opacity ? $(target).animate({opacity: 'toggle', height: 'toggle'}, 333,  'easeOutQuart') : $(target).animate({height: 'toggle'}, 333, 'easeOutQuart');
        });

      },


      // display the website screenshot on when mouse over the link
      // @todo: handle "no-fx"
      webShots: function(){
        return this.each(function(i){
          var title = $(this).attr('title'),
              url = 'http://s.wordpress.com/mshots/v1/' + encodeURIComponent(this.href) + '?w=400',
              // defaultimg = 'http://s.wordpress.com/wp-content/plugins/mshots/default.gif',
              webshot = $('<div class="webshot" id="webshot-' + i + '"><img src="' + url + '" width="400" alt="' + title + '" /></div>').hide().appendTo(document.body),
              fadeAmt = $('body').hasClass('no-fx') ? 0 : 333;

          /*/
          setTimeout(function(){
            webshot.find('img').attr('src', function(i, oldSrc){
              return oldSrc + '&_=reload';
          });
          }, 10000);
          //*/

          return $(this).mouseover(function(){
            webshot.show();       // trick to avoid problems when using fade effect:  webshot.css({ opacity: 1, display: 'none'}).fadeIn(fadeAmt);
          }).mousemove(function(kmouse){
            webshot.css({
              left: kmouse.pageX + 15,
              top: kmouse.pageY + 15
            });
          }).mouseout(function(){
            webshot.hide();       // or fadeOut(fademt)
          });

        });

      },

      // set up a "go to top" scroll on this element
      goTopControl: function(){
        return this.each(function(i){
          var link = $(this);

          $(window).scroll(function(){ // on window scroll
            // stupid IE hack
            if(!$.support.hrefNormalized)
              link.css({'position': 'absolute', 'top': $(window).scrollTop() + $(window).height() - 50});

            if($(window).scrollTop() >= 500) link.fadeIn(200); else link.fadeOut(200);
          });

          // on go-to-top click
          link.click(function(){
            var selector = ($.browser.safari || $.browser.chrome) ? 'body' : 'html,body';

            $(selector).animate({ scrollTop: 0 }, 'slow');
            return false;
          });

        });

      },


      // image slider -- this is a improved version of http://webdevkit.net/ubillboard/
      // @todo: make delay directly proportional with the square/stripe size
      imageSlider: function(){

        cfg = $.extend({
          width: false,
          height: false,
          squareSize: 80,  // = 12 x 12 on 960x320
          stripeWidth: 80, // = 4 columns on 960x320
          showPager: true,
          showArrows: true,
          autoplay: true,
          transition: 'random',
          delay: 10        // in seconds
        }, cfg);

        return this.each(function(){

          // set w/h from css if not defined
          cfg.width = cfg.width || $(this).width();
          cfg.height = cfg.height || $(this).height();

          // minimum 47px, max 188px
          cfg.squareSize = Math.min(Math.max(cfg.squareSize, 47), 188);
          cfg.stripeWidth = Math.min(Math.max(cfg.stripeWidth, 30), 188);
          cfg.delay = (parseInt($(this).data('delay'), 10) * 1000) || (cfg.delay * 1000);

          var loader = $('<div class="loading"></div>').css({
                left: cfg.width / 2 - 80,
                top: cfg.height / 2 - 10
              }),
              controls = $('<div class="i-controls"></div>'),
              nextSlide = $('<div class="next-slide"></div>'),
              link = $('<a href="" class="link"></a>').hide().appendTo(controls),
              block = $(this).prepend(nextSlide).append(controls).prepend(loader),

              slides = [],                                           // global slides array
              currentSlideIndex = 0,                                 // slide currently in nextSlide
              prevSlideIndex = 0,                                    // slide in background
              totalImages = $('.slide', block).length,               // image count
              totalImagesLoaded = 0,
              timeout = null,
              playing = (typeof block.data('autoplay') !== 'undefined') ? block.data('autoplay') : cfg.autoplay, // holds current play/pause status
              playState = false,

              squareRows = Math.ceil(cfg.height / cfg.squareSize),   // number of square rows
              squareCols = Math.ceil(cfg.width / cfg.squareSize),    // number of square columns
              stripeCols = Math.ceil(cfg.width / cfg.stripeWidth),   // number of stripe columns

              // animation functions
              animations = {

                animationFade: function(currentIndex, destinationIndex, callback){
                  nextSlide.css({
                    backgroundImage: 'url(' + slides[destinationIndex].image + ')',
                    opacity: 0
                  }).animate({
                    opacity: 1
                  }, {
                    duration: 1333,
                    complete: callback,
                    easing: 'easeOutSine'
                  });
                },

                animationSlideH: function(currentIndex, destinationIndex, callback, reverse){
                  nextSlide.css({
                    backgroundImage: 'url(' + slides[destinationIndex].image + ')',
                    left: reverse ? cfg.width : - cfg.width
                  }).animate({
                    left: 0
                  }, {
                    duration: 999,
                    complete: callback,
                    easing: 'easeOutExpo'
                  });
                },

                animationSlideV: function(currentIndex, destinationIndex, callback, reverse){
                  nextSlide.css({
                    backgroundImage: 'url(' + slides[destinationIndex].image + ')',
                    top: reverse ? cfg.height : (- cfg.height)
                  }).animate({
                    top: 0
                  }, {
                    duration: 999,
                    complete: callback,
                    easing: 'easeOutExpo'
                  });
                },

                animationRandomTiles: function(currentIndex, destinationIndex, callback){

                  // create a array containing delay values for each element
                  for(var delayTable = [], i = 0; i < (squareRows * squareCols - 1); ++i) delayTable[i] =  20 * i;

                  // shuffle it
                  for(var j, x, i = delayTable.length; i; j = parseInt(Math.random() * i), x = delayTable[--i], delayTable[i] = delayTable[j], delayTable[j] = x){};

                  $('.square', nextSlide).css({
                    backgroundImage: 'url(' + slides[destinationIndex].image + ')',
                    opacity: 0
                  }).each(function(i){
                    $(this).stop().delay(delayTable[i]).animate({
                      opacity: 1
                    }, {
                      duration: 999,
                      easing: 'easeOutQuad',
                      complete: (Math.max.apply(Math, delayTable) !== delayTable[i]) || callback
                    });
                  });

                },

                animationHorizontalTiles: function(currentIndex, destinationIndex, callback, reverse){
                  var end = reverse ? 0 : (squareRows * squareCols - 1);

                  // delay: 4x10 > 30  20x8 > 5
                  $('.square', nextSlide).css({
                    backgroundImage: 'url(' + slides[destinationIndex].image + ')',
                    opacity: 0
                  }).each(function(i){
                    $(this).stop().delay(30 * (reverse ? (squareRows * squareCols - i) : i)).animate({
                      opacity: 1
                    }, {
                      duration: 666,
                      easing: 'easeOutSine',
                      complete: (i !== end) || callback
                    });
                  });
                },

                animationDiagonalTiles: function(currentIndex, destinationIndex, callback, reverse){
                  var n = 0,
                      end = reverse ? 0 : ((squareRows * squareCols) - 1);

                  $('.square', nextSlide).css({
                    backgroundImage: 'url(' + slides[reverse ? destinationIndex : currentIndex].image + ')',
                    opacity: reverse ? 0 : 1
                  });

                  if(reverse) _resetSquares(-20, -20); else nextSlide.css('background-image', 'url(' + slides[destinationIndex].image + ')');

                  block.removeClass('no-overflow');

                  for(var y = 0; y < squareRows; y++)
                    for(var x = 0; x < squareCols; x++){
                      $('.square-' + n, nextSlide).stop().delay(100 * (reverse ? (squareRows - y + squareCols - x) : (x + y))).animate({
                        opacity: reverse ? 1 : 0,
                        left: reverse ? (x * cfg.squareSize) : (x * cfg.squareSize - 20),
                        top: reverse ? (y * cfg.squareSize) : (y * cfg.squareSize - 20)
                      }, {
                        duration: 333,
                        complete: (n !== end) || callback
                      });
                      ++n;
                    }

                },

                animationSpiral: function(currentIndex, destinationIndex, callback, reverse){
                  var order = [], rowsHalf = Math.ceil(squareRows/2), x, y, z, n = 0;

                  // create a spiral matrix
                  for(z = 0; z < rowsHalf; z++){
                    y = z;
                    for(x = z; x < squareCols - z - 1; x++) order[n++] = y * squareCols + x;

                    x = squareCols - z - 1;
                    for(y = z; y < squareRows - z - 1; y++) order[n++] = y * squareCols + x;

                    y = squareRows - z - 1;
                    for(x = squareCols - z - 1; x > z; x--) order[n++] = y * squareCols + x;

                    x = z;
                    for(y = squareRows - z - 1; y > z; y--) order[n++] = y * squareCols + x;
                  };

                  if(reverse) order.reverse();

                  for(var m = 0; m < n; m++)
                    $('.square-' + order[m], nextSlide).css({
                      backgroundImage: 'url(' + slides[destinationIndex].image + ')',
                      opacity: 0
                    }).each(function(i){
                      $(this).stop().delay(30*m).animate({
                        opacity: 1
                      }, {
                        duration: 666,
                        easing: 'easeOutSine',
                        complete: (m != n - 1) || callback
                      });
                    });

                },

                animationRandomStripes: function(currentIndex, destinationIndex, callback){
                  // create a array containing delay values for each element
                  for(var delayTable = [], i = 0; i < (stripeCols - 1); ++i) delayTable[i] =  60 * i;

                  // shuffle it
                  for(var j, x, i = delayTable.length; i; j = parseInt(Math.random() * i), x = delayTable[--i], delayTable[i] = delayTable[j], delayTable[j] = x){};

                  $('.stripe', nextSlide).css({
                    backgroundImage: 'url(' + slides[destinationIndex].image + ')',
                    opacity: 0,
                    top: -40
                  });

                  for(var i = 0; i < stripeCols; i++){
                    $('.stripe-' + i, nextSlide).stop().delay(delayTable[i]).animate({
                      opacity: 1,
                      top: 0
                    }, {
                      duration: 999,
                      specialEasing: {
                        opacity: 'easeOutQuad',
                        top: 'easeOutElastic'
                      },
                      complete: (Math.max.apply(Math, delayTable) !== delayTable[i]) || callback
                    });
                  }
                },

                animationStripeWave: function(currentIndex, destinationIndex, callback, reverse){
                  var end = reverse ? 0 : (stripeCols - 1);
                  $('.stripe', nextSlide).css({
                    backgroundImage: 'url(' + slides[destinationIndex].image + ')',
                    opacity: 0,
                    top: - 30
                  });

                  block.removeClass('no-overflow');

                  for(var i = 0; i < stripeCols; i++){
                    $('.stripe-' + i, nextSlide).stop().delay(30 * (reverse ? (stripeCols - i) : i)).animate({
                      opacity: 1,
                      top: 0
                    }, {
                      duration: 999,
                      specialEasing: {
                        opacity: 'easeOutSine',
                        top: 'easeOutElastic'
                      },
                      complete: (i !== end) || callback
                    });
                  }
                },

                animationCurtain: function(currentIndex, destinationIndex, callback, reverse){
                  var end = reverse ? 0 : (stripeCols - 1);

                  $('.stripe', nextSlide).css({
                    backgroundImage: 'url(' + slides[destinationIndex].image + ')',
                    opacity: 0,
                    height: 0,
                    top: -(cfg.height / 2)
                  });

                  block.removeClass('no-overflow');

                  for(var i = 0; i < stripeCols; i++){
                    $('.stripe-' + i, nextSlide).stop().delay(70 * (reverse ? (stripeCols - i) : i)).animate({
                      opacity: 1,
                      height: cfg.height,
                      top: 0
                    }, {
                      duration: 333,
                      specialEasing: {
                        opacity: 'easeInSine',
                        height: 'easeOutQuad',
                        top: 'easeOutQuad'
                      },
                      complete: (i !== end) || callback
                    });
                  }

                },

                animationInterweave: function(currentIndex, destinationIndex, callback, reverse){
                  var end = reverse ? 0 : (stripeCols - 1);

                  $('.stripe', nextSlide).css({
                    backgroundImage: 'url(' + slides[destinationIndex].image + ')',
                    opacity: 0
                  });

                  $('.stripe:even', nextSlide).css('top', - cfg.height);
                  $('.stripe:odd', nextSlide).css('top', cfg.height);

                  block.removeClass('no-overflow');

                  for(var i = 0; i < stripeCols; i++){
                    $('.stripe-' + i, nextSlide).stop().delay(50 * (reverse ? (stripeCols - i) : i)).animate({
                      opacity: 1,
                      top: 0
                    }, {
                      duration: 666,
                      specialEasing: {
                        opacity: 'easeOutSine',
                        top: 'easeOutExpo'
                      },
                      complete: (i !== end) || callback
                    });
                  }

                }

              },

              // check if anything is being animated
              isAnimating = function(){
                return nextSlide.is(':animated') || $('div', nextSlide).is(':animated') || $('.caption', controls).is(':animated');
              },

              // switches the current slide with the next slide
              _showSlide = function(index, reverse){
                clearTimeout(timeout);
                prevSlideIndex = currentSlideIndex;
                currentSlideIndex = index;
                block.css('background-image', 'url(' + slides[prevSlideIndex].image + ')');
                link.hide();

                $('.pager a', controls).removeClass('current');
                $('.pager a:eq(' + currentSlideIndex + ')', controls).addClass('current');

                _hideDescription();

                var getRandomTransition = function(){
                      var count = 0,
                          result;
                      for(var prop in animations) if (Math.random() < 1 / ++count) result = prop;
                      return result;
                    },

                    transition = (slides[currentSlideIndex].transition != 'random') ? animations['animation' + slides[currentSlideIndex].transition] : animations[getRandomTransition()];

                transition(prevSlideIndex, currentSlideIndex, function(){
                    block.removeClass('no-overflow').addClass('no-overflow').css('background-image', 'url(' + slides[currentSlideIndex].image + ')');
                    $('div', nextSlide).add(nextSlide).css('background-image', '');
                    _resetSquares(0, 0);
                    _resetStripes();
                    _showDescription(currentSlideIndex);

                    // change link's href and triggeer a custom event to let the lightbox know about this
                    if(slides[currentSlideIndex].link)
                      link.attr('href', slides[currentSlideIndex].link).show().trigger('href_updated');

                    if(!playing) return;
                    _setupTimeout();
                  },
                  reverse
                );
              },

              _setupTimeout = function(){
                timeout = setTimeout(function(){
                  var index = typeof slides[currentSlideIndex + 1] == 'undefined' ? 0 : currentSlideIndex + 1;
                  _showSlide(index);
                }, slides[currentSlideIndex].delay);
              },

              // shows description, create it if necessary
              _showDescription = function(current){

                // normally the 2nd condition should never be checked, but I encountered a flicker on certain ocassions, so we leave it until I find the issue...
                var caption = ($('.caption', controls).length === 0) ? $(slides[current].caption).appendTo(controls) : $('.caption', controls).replaceAll($(slides[current].caption));

                if(caption.hasClass('push-left')){
                  caption.css('width', (0.3 * cfg.width)).css({
                    opacity: 0,
                    left: - (0.3 * cfg.width)
                  }).stop().show().animate({
                    opacity: 1,
                    left: 0
                  }, {
                    duration: 333,
                    easing: 'easeOutExpo'
                  });

                }else if(caption.hasClass('push-right')){
                  caption.css('width', (0.3 * cfg.width)).css({
                    opacity: 0,
                    right: - (0.3 * cfg.width)
                  }).stop().show().animate({
                    opacity: 1,
                    right: 0
                  }, {
                    duration: 333,
                    easing: 'easeOutExpo'
                  });

                }else{
                  caption.css({
                    opacity: 0,
                    bottom: - caption.outerHeight()
                  }).stop().show().animate({
                    opacity: 1,
                    bottom: 0
                  }, {
                    duration: 333,
                    easing: 'easeOutExpo'
                  });

                }
              },

              // hide description
              _hideDescription = function(){

                var caption = $('.caption', controls);

                if(caption.hasClass('push-left')){
                  caption.stop().animate({
                    opacity: 0,
                    left: - (0.3 * cfg.width)
                  }, {
                    duration: 333,
                    easing: 'easeOutExpo',
                    complete: function(){ caption.remove(); }
                  });
                }else if(caption.hasClass('push-right')){
                  caption.stop().animate({
                    right: - (0.3 * cfg.width),
                    opacity: 0
                  }, {

                    duration: 333,
                    easing: 'easeOutExpo',
                    complete: function(){ caption.remove(); }
                  });
                }else{
                  caption.stop().animate({
                    opacity: 0,
                    bottom: - caption.outerHeight()
                  }, {
                    duration: 333,
                    easing: 'easeOutExpo',
                    complete: function(){ caption.remove(); }
                  });
                }
              },

              _resetSquares = function(offsetX, offsetY){

                for(var y = 0; y < squareRows; y++)
                  for(var x = 0; x < squareCols; x++)
                    $('.square-' + (squareCols * y + x), nextSlide).css({
                      backgroundPosition: - x * cfg.squareSize + 'px ' + (- y * cfg.squareSize) + 'px',
                      backgroundRepeat: 'no-repeat',
                      position: 'absolute',
                      left: x * cfg.squareSize + offsetX,
                      top: y * cfg.squareSize + offsetY,
                      opacity: 0,
                      width: cfg.squareSize,
                      height: cfg.squareSize
                    });
              },

              _resetStripes = function(){
                for(var i = 0; i < stripeCols; i++){
                  $('.stripe-' + i, nextSlide).css({
                    backgroundPosition: - i * cfg.stripeWidth + 'px 0',
                    backgroundRepeat: 'no-repeat',
                    position: 'absolute',
                    left: i * cfg.stripeWidth,
                    top: 0,
                    opacity: 0,
                    width: cfg.stripeWidth,
                    height: cfg.height
                  });
                }
              };


          // process each slide
          $('.slide', block).each(function(i, el){
            var slide = {
                  delay: (parseInt($(this).data('delay')) * 1000) || cfg.delay,
                  transition: $(this).data('fx') || cfg.transition,
                  link: $('img:first', this).parent('a').attr('href') || '',
                  image: $('img:first', this).attr('src'),
                  caption: $('.caption', this).clone().wrap('<div></div>').parent().html()
                };

            slides.push(slide);

            // preloader
            $('<img>', this).load(function(){

              ++totalImagesLoaded;

              if(totalImages === totalImagesLoaded){
                block.css('background-image', 'url(' + slides[0].image + ')').addClass('no-overflow');

                if(slides[0].link)
                  link.attr('href', slides[0].link).show().trigger('href_updated');

                $('.slide', block).remove();
                loader.remove();

                // button controls
                if(block.data('controls')){ // html5 data-controls attribute overrides defaults
                  var items = block.data('controls').replace(/ /g, '').split(',');
                  cfg.showPager = !!~$.inArray('pager', items);
                  cfg.showArrows = !!~$.inArray('arrows', items);
                };

                // previous/next arrow buttons
                if(cfg.showArrows){

                  $('<a>', {
                    'class': 'prev',
                    'css': { top: cfg.height/2 - 48, opacity: 0 },
                    'click': function(){
                      if(isAnimating()) return;
                      var index = currentSlideIndex - 1;
                      if(typeof slides[index] == 'undefined') index = totalImages - 1;
                      _showSlide(index, true);
                      return false;
                    }
                  }).appendTo(controls);

                  $('<a>', {
                    'class': 'next',
                    'css': { top: cfg.height/2 - 48, opacity: 0 },
                    'click': function(){
                      if(isAnimating()) return;
                      var index = currentSlideIndex + 1;
                      if(typeof slides[index] == 'undefined') index = 0;
                      _showSlide(index);
                      return false;
                    }
                  }).appendTo(controls);

                };

                // pagination
                if(cfg.showPager){

                  var current = 0,
                      pager = $('<div>', {
                        'class': 'pager',
                        'css': { opacity: 0 }
                      }).appendTo(controls);

                  for(var i = 0; i < totalImages; i++)
                    $('.pager', controls).append($('<a'+ (i == current ? ' class="current"' : '') + '>&nbsp;</a>'));

                  $('.pager a', controls).click(function(){
                    if($(this).index() == currentSlideIndex || isAnimating()) return false;
                    clearInterval(timeout);
                    _showSlide($(this).index(), (currentSlideIndex > $(this).index()));
                    return false;
                  });

                };

                controls.hover(function(){
                  $('.pager, a.next, a.prev', controls).stop().animate({opacity: 1}, 333);
                  // pause on mouseover / unpause on mouseout
                  clearInterval(timeout);
                  if(playing){
                    playState = true;
                    playing = false;
                  }
                }, function(){
                  $('.pager, a.next, a.prev', controls).stop().animate({opacity: 0}, 666);
                  if(playState){
                    _setupTimeout();
                    playing = true;
                  }
                });

                _showDescription(0);

                // set up squares
                for(var i = 0; i < (squareRows * squareCols); i++) nextSlide.append($('<div />').addClass('square square-' + i));
                _resetSquares(0, 0);

                // set up stripes
                for(var i = 0; i < stripeCols; i++) nextSlide.append($('<div />').addClass('stripe stripe-' + i));
                _resetStripes();

                controls.fadeIn(333);

                if(totalImages < 2 || !playing) return;
                _setupTimeout();

              }
            }).attr('src', slide.image);
          });

        });

      },

      // expand-collapse list - ideas from jQuery Menu by Marco van Hylckama Vlieg - http://www.i-marco.nl/weblog/
      // @todo finish accordion style menu
      collapsibleMenu: function(){

        return this.each(function(){

          $('li.extends', this).addClass('collapsed');
          $('li.extends.active, li.extends.active-parent', this).removeClass('collapsed').addClass('expanded');

          var classes = $(this).attr('class'),
              event = /event-([^\s]+)/.exec(classes)[1];

          $('li.extends span.expand, li.extends a[href="#"]', this).bind(event, function(event){
            event.stopImmediatePropagation();

            var trigger = $(this),
                theElement = trigger.nextAll('ul:first'),
                parent = trigger.parent(),
                menu = trigger.closest('.collapsible, .accordion'),
                slide = function(e, fx){
                  $(e).animate({ // slide[Direction] equivalent -- we use this instead of slideDown/slideUp/slideToggle for the step property (to fix the tab height glitch)
                    'opacity': fx,
                    'height': fx,
                    'marginTop': fx,
                    'marginBottom': fx,
                    'paddingTop': fx,
                    'paddingBottom': fx
                  },
                  { duration: 200,
                    step: function(now, fx){ menu.parents('li.block .sections').height(menu.height()); },
                    complete: function(){
                      parent.removeClass('expanded collapsed');
                      if(theElement.is('ul:visible')) parent.addClass('expanded'); else parent.addClass('collapsed');
                    }
                  });
                };

            if(menu.hasClass('collapsible')){
              if(theElement[0] === undefined) window.location.href = this.href;
              slide(theElement, 'toggle');
              return false;
            }else{
              context_menu = this.parentNode.parentNode;
              if(theElement.is('ul:visible')){
                if(menu.hasClass('collapsible')){
                  slide($('ul:visible', context_menu).first(), 'hide'); // slide up
                  return false;
                }
                return false;
              };
              if(!theElement.is('ul:visible')){
                slide($('ul:visible', context_menu).first(), 'hide'); // slide up
                slide(theElement, 'show'); // slide down
                return false;
              }
            }

            return false;
          });

        });
      },

      // highlights search queries -- not used currenty
      highlightText: function(text, insensitive, highlightClass){
        var regex = new RegExp('(<[^>]*>)|(\\b' + text.replace(/([-.*+?^${}()|[\]\/\\])/g, '\\$1') + ')', insensitive ? 'ig' : 'g');

        if(this.length > 0) return this.html(this.html().replace(regex, function(a, b, c){
          return (a.charAt(0) == '<') ? a : '<strong class="' + highlightClass + '">' + c + '</strong>';
        }));

      },

      // wrapper for the "Show more" link inside various widgets that use ajax
      showMoreControl: function(){
        return this.click(function(event){

          var block = $(this).closest('.block'),
              link = $(this),
              oldTitle = $(this).html();

          event.preventDefault();
          $.ajax({
            type: 'GET',
            url: atom_config.blog_url, // this variable needs to be set first
            data: {
              instance: $(this).data('instance'),
              atom: $(this).data('cmd'),
              offset: parseInt($(this).data('offset'))
            },
            dataType: 'json',
            context: this,
            beforeSend: function(){
              $(this).addClass('loading').html('&nbsp;');
            },
            complete: function(){
              $(this).removeClass('loading').html(oldTitle);
            },

            success: function(response){
              // append to list & update tab container height (if we're inside a tabbed widget)
              if(response.output != '')

                if($('body').hasClass('no-fx')){ // no effects?
                  $(response.output).appendTo(block.find('ul'));
                  link.parents('.sections').height(block.height());  // +40 to compensate for tab navi

                }else{
                  $(response.output).appendTo(block.find('ul')).hide().each(function(i){
                    $(this).delay(333*i).animate({
                      'opacity': 'show',
                      'height': 'show',
                      'marginTop': 'show',
                      'marginBottom': 'show',
                      'paddingTop': 'show',
                      'paddingBottom': 'show'
                    },
                    { duration: 333,
                      step: function(now, fx){ link.parents('.sections').height(block.height()); }
                    });
                  });

                };

              link.data('offset', response.offset);

              // no more data?
              if(!response.more)
                link.hide();

            }
          });

        });

      },

      // simple nudge effect for links inside a element
      // @todo make this live
      nudgeLinks: function(){
        return this.each(function(i){

          var direction = $(this).data('dir'),
              property = 'margin' + direction.charAt(0).toUpperCase() + direction.slice(1),
              amount = -(parseInt($(this).data('amt'))),
              duration = 166;

          $('a', this).each(function(){
            var link = $(this),
                initialValue = link.css(property),
                go = {},
                bk = {};

            go[property] = amount + parseInt(initialValue);
            bk[property] = initialValue;

            link.hover(function(){
              link.stop().animate(go, duration);
            },
            function(){
              link.stop().animate(bk, duration);
            });

          });

        });

      },

      // set up quote/reply/voting on a comments area
      commentControls: function(){
        return this.each(function(){

          var comments = $(this);

          // reply link
          comments.delegate('.comment-reply', 'click', function(event){

            event.preventDefault();

            var data = this.id,
                pos = data.lastIndexOf('-'),
                targetID = data.substr(++pos);

            comments.find('.comment.new').hide();
            comments.find('#comment_parent').attr('value', targetID);

            comments.find('#cancel-reply').show();
            comments.find('.comment.new').appendTo('#comment-body-' + targetID).show(0, function(){

              // move cursor in textarea, at the end of the text
              comments.find('#comment').each(function(){
                if(this.createTextRange){
                  var r = this.createTextRange();
                  r.collapse(false);
                  r.select();
                }
                $(this).focus();
              });

            });
          });

          // cancel reply link
          comments.delegate('#cancel-reply', 'click', function(event){
            event.preventDefault();

            comments.find('.comment.new').hide();
            comments.find('#comment_parent').attr('value', '0');

            $(this).hide();
            comments.find('.comment.new').appendTo(comments.find('li.new')).show(0, function(){

              // move cursor in textarea, at the end of the text
              comments.find('#comment').each(function(){
                if(this.createTextRange) {
                  var r = this.createTextRange();
                  r.collapse(false);
                  r.select();
                }
                $(this).focus();
              });

            });
          });

          // quote link
          comments.delegate('.comment-quote', 'click', function(event){
            var textarea = comments.find('#comment'),
                comment = $(this).parents('.comment');

            event.preventDefault();
            textarea.atom('clearFieldCheck');

            // http://phpjs.org/functions/strip_tags:535
            var strip_tags = function(input, allowed){
                  allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
                  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
                      commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;

                  return input.replace(commentsAndPhpTags, '').replace(tags, function($0, $1){
                    return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
                  });
                },

                // http://phpjs.org/functions/trim:566
                trim = function(str, charlist){
                  var whitespace, l = 0, i = 0;
                      str += '';

                  if(!charlist){
                    // default list
                    whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
                  }else{
                    // preg_quote custom list
                    charlist += '';
                    whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
                  }

                  l = str.length;
                  for(i = 0; i < l; i++)
                    if(whitespace.indexOf(str.charAt(i)) === -1){
                      str = str.substring(i);
                      break;
                    }

                  l = str.length;
                  for(i = l - 1; i >= 0; i--)
                    if(whitespace.indexOf(str.charAt(i)) === -1){
                      str = str.substring(0, i + 1);
                      break;
                    }

                  return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
                };

            // append value to a input field
            jQuery.fn.appendVal = function(txt){ return this.each(function(){ this.value += txt; }); };

            textarea.appendVal(
              '<blockquote>\n<a href="'
              + $('.comment-id', comment).attr('href') + '">\n<strong><em>'
              + $('.comment-author', comment).html()
              + ':</em></strong>\n</a>\n '
              + trim(strip_tags($('.comment-text', comment).html(), '<em><strong><i><b><a><code>')) + '\n</blockquote>\n'
            ).trigger('blur').focus();
          });

          // karma vote
          comments.delegate('a.vote', 'click', function(event){
            event.preventDefault();

            $.ajax({
              url: atom_config.blog_url,
              type: 'GET',
              context: this,
              data:({
                atom: 'comment_karma',
                karma: $(this).data('vote')
              }),
              success: function(data){
                var status = $(this).parent().find('.karma');
                status.html(data);
                if(parseInt(data) > 0){
                  status.removeClass('negative');
                  status.addClass('positive');
                }
                else if(parseInt(data) < 0){
                  status.removeClass('positive');
                  status.addClass('negative');
                }
                $(this).parent().find('a').remove();
              }
            });
          });

          // show buried comment
          comments.delegate('a.show', 'click', function(event){
            event.preventDefault();

            $.ajax({
              url: atom_config.blog_url,
              type: 'GET',
              context: this,
              data:({
                atom: 'get_comment',
                comment_id: $(this).data('comment')
              }),
              success: function(data){
                var comment = $('<ul />').html(data),
                    commentbody = comment.find(".comment-body").hide(),
                    commentbodyOuterHTML = commentbody[0].outerHTML || commentbody.clone().appendTo('<div>').parent().html();

                $(this).parents('.comment-head').after(commentbodyOuterHTML);

                if($('body').hasClass('no-fx')){
                  $(this).parents('li').find('.comment-body').show();
                  $(this).parents('.sections').height(comments.height() + 15);

                }else{
                  $(this).parents('li').find('.comment-body').animate({
                    'opacity': 'show',
                    'height': 'show',
                    'marginTop': 'show',
                    'marginBottom': 'show',
                    'paddingTop': 'show',
                    'paddingBottom': 'show'
                  }, { duration: 333,

                    /*/ cool, but too slow...
                    step: function(now, fx){
                      $(this).parents('.sections').height(comments.height() + 15);
                    }
                    //*/

                    complete: function(){
                      $(this).parents('.sections').height(comments.height() + 15);
                    }
                  });
                };
                $(this).remove();
              }
            });
          });


        });

      },


     /*
      * FancyBox - jQuery Plugin
      * Simple and fancy lightbox alternative
      *
      * Examples and documentation at: http://fancybox.net
      *
      * Copyright (c) 2008 - 2010 Janis Skarnelis
      * That said, it is hardly a one-person project. Many people have submitted bugs, code, and offered their advice freely. Their support is greatly appreciated.
      *
      * Version: 1.3.4 (11/11/2010)
      * Requires: jQuery v1.3+
      *
      * Dual licensed under the MIT and GPL licenses:
      *   http://www.opensource.org/licenses/mit-license.php
      *   http://www.gnu.org/licenses/gpl.html
      */
      fancybox: function(){

        var tmp, loading, overlay, wrap, outer, content, close, title, nav_left, nav_right,
            selectedIndex = 0, cfg = {}, selectedArray = [], currentIndex = 0, currentOpts = {}, currentArray = [],
            ajaxLoader = null, imgPreloader = new Image(), imgRegExp = /\.(jpg|gif|png|bmp|jpeg)(.*)?$/i,
            titleHeight = 0, titleStr = '', padding, start_pos, final_pos, busy = false, fx = $.extend($('<div/>')[0], { prop: 0 }),


        _abort = function(){
          loading.hide();
          imgPreloader.onerror = imgPreloader.onload = null;

          if(ajaxLoader)
            ajaxLoader.abort();

          tmp.empty();
        },

        _error = function(){

          if(false === cfg.onError(selectedArray, selectedIndex, cfg)){
            loading.hide();
            busy = false;
            return;
          }

          cfg.titleShow = false;
          cfg.width = 'auto';
          cfg.height = 'auto';

          tmp.html('<p id="fb-error">The requested content cannot be loaded.<br />Please try again later.</p>');
          _process_inline();
        },

        _start = function(){

          var obj = selectedArray[selectedIndex], href, type, title, str, emb, ret;

          _abort();

          cfg = $.extend({
            padding              : 10,
            margin               : 40,
            opacity              : false,
            cyclic               : false,
            scrolling            : 'auto',	// 'auto', 'yes' or 'no'
            width                : 560,
            height               : 340,
            centerOnScroll       : true,
            ajax                 : {},
            hideOnOverlayClick   : true,
            hideOnContentClick   : false,
            overlayShow          : true,
            overlayOpacity       : 0.5,
            titleShow            : true,
            titleFormat          : null,
            titleFromAlt         : false,
            transitionIn         : 'elastic', // 'elastic', 'fade' or 'none'
            transitionOut        : 'elastic', // 'elastic', 'fade' or 'none'
            speedIn              : 300,
            speedOut             : 300,
            changeSpeed          : 300,
            changeFade           : 'fast',
            easingIn             : 'easeOutBack',
            easingOut            : 'easeInBack',
            showNavArrows        : true,
            enableEscapeButton   : true,
            enableKeyboardNav    : true,
            onStart              : function(){},
            onCancel             : function(){},
            onComplete           : function(){},
            onCleanup            : function(){},
            onClosed             : function(){},
            onError              : function(){}

          }, (typeof $(obj).data('atom.fancybox') == 'undefined' ? cfg : $(obj).data('atom.fancybox')));

          ret = cfg.onStart(selectedArray, selectedIndex, cfg);

          if (ret === false){
            busy = false;
            return;

          }else if(typeof ret == 'object'){
            cfg = $.extend(cfg, ret);
          }

          title = cfg.title || (obj.nodeName ? $(obj).attr('title') : obj.title) || '';

          if(obj.nodeName && !cfg.orig)
            cfg.orig = $(obj).children("img:first").length ? $(obj).children("img:first") : $(obj);

          if(title === '' && cfg.orig && cfg.titleFromAlt)
            title = cfg.orig.attr('alt');

          href = cfg.href || obj.href || null;

          if((/^(?:javascript)/i).test(href) || href == '#')
            href = null;


          if(href)
            type = href.match(imgRegExp) ? 'image' : 'ajax';

          if(!type){
            _error();
            return;
          }

          cfg.href = href;
          cfg.title = title;

          cfg.padding = parseInt(cfg.padding, 10);
          cfg.margin = parseInt(cfg.margin, 10);

          tmp.css('padding', (cfg.padding + cfg.margin));

          $('.fb-inline-tmp').unbind('fb-cancel').bind('fb-change', function(){
            $(this).replaceWith(content.children());
          });

          // images
          if (type !== 'ajax'){
            busy = false;

            loading.show();
            imgPreloader = new Image();
            imgPreloader.onerror = _error;

            imgPreloader.onload = function(){
              busy = true;
              imgPreloader.onerror = imgPreloader.onload = null;
              _process_image();
            };

            imgPreloader.src = href;

          // ajax
          }else{
            busy = false;
            loading.show();
            cfg.ajax.win = cfg.ajax.success;
            ajaxLoader = $.ajax($.extend({}, cfg.ajax, {
              url: href,
              data: cfg.ajax.data || {},
              error: function(XMLHttpRequest, textStatus, errorThrown) {
                if(XMLHttpRequest.status > 0)
                  _error();
              },
              success: function(data, textStatus, XMLHttpRequest){
                var o = typeof XMLHttpRequest == 'object' ? XMLHttpRequest : ajaxLoader;
                if(o.status == 200){
                  if(typeof cfg.ajax.win == 'function'){
                    ret = cfg.ajax.win(href, data, textStatus, XMLHttpRequest);

                    if(ret === false){
                      loading.hide();
                      return;
                    }else if (typeof ret == 'string' || typeof ret == 'object'){
                      data = ret;
                    }
                  }

                  tmp.html( data );
                  _process_inline();
                }
              }
            }));

          }
        },

        _process_inline = function(){

          var w = cfg.width, h = cfg.height;

          if(w.toString().indexOf('%') > -1)
            w = parseInt( ($(window).width() - (cfg.margin * 2)) * parseFloat(w) / 100, 10) + 'px';

          else
            w = w == 'auto' ? 'auto' : w + 'px';

          if(h.toString().indexOf('%') > -1)
            h = parseInt( ($(window).height() - (cfg.margin * 2)) * parseFloat(h) / 100, 10) + 'px';

          else
            h = h == 'auto' ? 'auto' : h + 'px';

          tmp.wrapInner('<div style="width:' + w + ';height:' + h + ';overflow: ' + (cfg.scrolling == 'auto' ? 'auto' : (cfg.scrolling == 'yes' ? 'scroll' : 'hidden')) + ';position:relative;"></div>');

          cfg.width = tmp.width();
          cfg.height = tmp.height();
          _show();
        },

        _process_image = function(){
          cfg.width = imgPreloader.width;
          cfg.height = imgPreloader.height;

          $("<img />").attr({
            'id' : 'fb-img',
            'src' : imgPreloader.src,
            'alt' : cfg.title
          }).appendTo(tmp);

          _show();
        },

        _show = function(){

          var pos, equal;

          loading.hide();

          if(wrap.is(':visible') && (currentOpts.onCleanup(currentArray, currentIndex, currentOpts) === false)){
            $.event.trigger('fb-cancel');
            busy = false;
            return;
          }

          busy = true;

          $(content.add(overlay)).unbind();
          $(window).unbind('resize.fb scroll.fb');
          $(document).unbind('keydown.fb');

          if(wrap.is(':visible'))
            wrap.css('height', wrap.height());

          currentArray = selectedArray;
          currentIndex = selectedIndex;
          currentOpts = cfg;

          if(currentOpts.overlayShow){

            overlay.css({
              'opacity': currentOpts.overlayOpacity,
              'cursor': currentOpts.hideOnOverlayClick ? 'pointer' : 'auto',
              'height': $(document).height()
            });

            if(!overlay.is(':visible'))
              overlay.show();

          }else{
            overlay.hide();
          }

          final_pos = _get_zoom_to();
          _process_title();

          if(wrap.is(':visible')){
            $(nav_left.add(nav_right)).hide();

            pos = wrap.position(),

            start_pos = {
              top    : pos.top,
              left   : pos.left,
              width  : wrap.width(),
              height : wrap.height()
            };

            equal = (start_pos.width == final_pos.width && start_pos.height == final_pos.height);

            content.fadeTo(currentOpts.changeFade, 0.3, function(){

              var finish_resizing = function(){
                content.html(tmp.contents()).fadeTo(currentOpts.changeFade, 1, _finish);
              };

              $.event.trigger('fb-change');

              content
                .empty()
                .css({
                  'border-width' : currentOpts.padding,
                  'width': final_pos.width - currentOpts.padding * 2,
                  'height': (cfg.type == 'ajax') ? 'auto' : final_pos.height - titleHeight - currentOpts.padding * 2
                });

              if(equal){
                finish_resizing();

              }else{
                fx.prop = 0;

                $(fx).animate({prop: 1}, {
                  duration: currentOpts.changeSpeed,
                  easing: currentOpts.easingChange,
                  step: _draw,
                  complete: finish_resizing
                });
              }
            });

            return;
          }

          wrap.removeAttr('style');
          content.css('border-width', currentOpts.padding);

          if(currentOpts.transitionIn == 'elastic'){

            start_pos = _get_zoom_from();
            content.html(tmp.contents());
            wrap.show();

            if(currentOpts.opacity)
              final_pos.opacity = 0;

            fx.prop = 0;

            $(fx).animate({prop: 1}, {
              duration: currentOpts.speedIn,
              easing: currentOpts.easingIn,
              step: _draw,
              complete: _finish
            });

            return;
          }

          if(titleHeight > 0)
            title.show();

          content.css({
            'width': final_pos.width - currentOpts.padding * 2,
            'height': (cfg.type == 'ajax') ? 'auto' : final_pos.height - titleHeight - currentOpts.padding * 2
          }).html(tmp.contents());

          wrap.css(final_pos).fadeIn(currentOpts.transitionIn == 'none' ? 0 : currentOpts.speedIn, _finish);

        },

        _format_title = function(title){

          if(title && title.length)
            return title;


          return false;
        },

        _process_title = function(){
          titleStr = currentOpts.title || '';
          titleHeight = 0;

          title
          .empty()
          .removeAttr('style')
          .removeClass();

          if(currentOpts.titleShow === false){
            title.hide();
            return;
          }

          titleStr = $.isFunction(currentOpts.titleFormat) ? currentOpts.titleFormat(titleStr, currentArray, currentIndex, currentOpts) : _format_title(titleStr);

          if (!titleStr || titleStr === ''){
            title.hide();
            return;
          }

          title
            .html( titleStr )
            .appendTo( 'body' )
            .show();

          title
            .css({
              'width' : final_pos.width - (currentOpts.padding * 2),
              'marginLeft' : currentOpts.padding,
              'marginRight' : currentOpts.padding
          });

          titleHeight = title.outerHeight(true);
          title.appendTo(outer);
          final_pos.height += titleHeight;
          title.hide();
        },

        _set_navigation = function(){
          if(currentOpts.enableEscapeButton || currentOpts.enableKeyboardNav){
            $(document).bind('keydown.fb', function(e){
              if(e.keyCode == 27 && currentOpts.enableEscapeButton){
                e.preventDefault();
                _close();

              }else if ((e.keyCode == 37 || e.keyCode == 39) && currentOpts.enableKeyboardNav && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'SELECT'){
                e.preventDefault();
                e.keyCode == 37 ? _prev() : _next();
              }
            });
          }

          if(!currentOpts.showNavArrows){
            nav_left.hide();
            nav_right.hide();
            return;
          }

          if((currentOpts.cyclic && currentArray.length > 1) || currentIndex !== 0){
            $('a', nav_left).css('top', (wrap.height() / 2 - ($('a', nav_left).height() / 2)));
            nav_left.show();
          }

          if((currentOpts.cyclic && currentArray.length > 1) || currentIndex != (currentArray.length -1)){
            $('a', nav_right).css('top', (wrap.height() / 2 - ($('a', nav_left).height() / 2)));
            nav_right.show();
          }
        },

        _finish = function(){

          if((cfg.type == 'ajax'))
            content.css('height', 'auto');

          wrap.css('height', 'auto');

          if(titleStr && titleStr.length)
            title.show();

          _set_navigation();

          if(currentOpts.hideOnContentClick)
            content.bind('click', _close);

          if(currentOpts.hideOnOverlayClick)
            overlay.bind('click', _close);

          $(window).bind('resize.fb', _resize);

          if(currentOpts.centerOnScroll)
            $(window).bind('scroll.fb', _center);

          wrap.show();
          busy = false;
          _center();
          currentOpts.onComplete(currentArray, currentIndex, currentOpts);
          _preload_images();
        },

        _preload_images = function(){
          var href, objNext;

          if((currentArray.length -1) > currentIndex){
            href = currentArray[currentIndex + 1].href;

            if(typeof href !== 'undefined' && href.match(imgRegExp)){
              objNext = new Image();
              objNext.src = href;
            }
          }

          if(currentIndex > 0){
            href = currentArray[currentIndex - 1].href;

            if(typeof href !== 'undefined' && href.match(imgRegExp)){
              objNext = new Image();
              objNext.src = href;
            }
          }
        },

        _draw = function(pos){
          var dim = {
            width: parseInt(start_pos.width + (final_pos.width - start_pos.width) * pos, 10),
            height: parseInt(start_pos.height + (final_pos.height - start_pos.height) * pos, 10),
            top: parseInt(start_pos.top + (final_pos.top - start_pos.top) * pos, 10),
            left: parseInt(start_pos.left + (final_pos.left - start_pos.left) * pos, 10)
          };

          if(typeof final_pos.opacity !== 'undefined')
            dim.opacity = pos < 0.5 ? 0.5 : pos;

          wrap.css(dim);

          content.css({
            'width': dim.width - currentOpts.padding * 2,
            'height': dim.height - (titleHeight * pos) - currentOpts.padding * 2
          });
        },

        _get_viewport = function(){
          return [
            $(window).width() - (currentOpts.margin * 2),
            $(window).height() - (currentOpts.margin * 2),
            $(document).scrollLeft() + currentOpts.margin,
            $(document).scrollTop() + currentOpts.margin
          ];
        },

        _get_zoom_to = function(){
          var view = _get_viewport(), to = {}, resize = true, double_padding = currentOpts.padding * 2, ratio;

          to.width = (currentOpts.width.toString().indexOf('%') > -1) ? parseInt((view[0] * parseFloat(currentOpts.width)) / 100, 10) : currentOpts.width + double_padding;
          to.height = (currentOpts.height.toString().indexOf('%') > -1) ? parseInt((view[1] * parseFloat(currentOpts.height)) / 100, 10) : currentOpts.height + double_padding;

          if(resize && (to.width > view[0] || to.height > view[1])){
            if(cfg.type == 'image'){
              ratio = (currentOpts.width ) / (currentOpts.height );

              if((to.width ) > view[0]){
                to.width = view[0];
                to.height = parseInt(((to.width - double_padding) / ratio) + double_padding, 10);
              }

              if((to.height) > view[1]){
                to.height = view[1];
                to.width = parseInt(((to.height - double_padding) * ratio) + double_padding, 10);
              }

            }else{
              to.width = Math.min(to.width, view[0]);
              to.height = Math.min(to.height, view[1]);
            }
          }

          to.top = parseInt(Math.max(view[3] - 20, view[3] + ((view[1] - to.height - 40) * 0.5)), 10);
          to.left = parseInt(Math.max(view[2] - 20, view[2] + ((view[0] - to.width - 40) * 0.5)), 10);

          return to;
        },

        _get_obj_pos = function(obj){
          var pos = obj.offset();

          pos.width = obj.width();
          pos.height = obj.height();

          return pos;
        },

        _get_zoom_from = function(){
          var orig = cfg.orig ? $(cfg.orig) : false, from = {}, pos, view;

          if (orig && orig.length){
            pos = _get_obj_pos(orig);

            from = {
              width: pos.width + (currentOpts.padding * 2),
              height: pos.height + (currentOpts.padding * 2),
              top: pos.top - currentOpts.padding - 20,
              left: pos.left - currentOpts.padding - 20
            };

          }else{
            view = _get_viewport();

            from = {
              width: currentOpts.padding * 2,
              height: currentOpts.padding * 2,
              top: parseInt(view[3] + view[1] * 0.5, 10),
              left: parseInt(view[2] + view[0] * 0.5, 10)
            };
          }

          return from;
        },

        _next = function(){
          return _pos(currentIndex + 1);
        },

        _prev = function() {
          return _pos(currentIndex - 1);
        },

        _pos = function(pos){

          if(busy) return;

          pos = parseInt(pos);
          selectedArray = currentArray;

          if(pos > -1 && pos < currentArray.length){
            selectedIndex = pos;
            _start();

          }else if (currentOpts.cyclic && currentArray.length > 1){
            selectedIndex = pos >= currentArray.length ? 0 : currentArray.length - 1;
            _start();
          }

        },

        _cancel = function(){

          if(busy) return;

          busy = true;
          $.event.trigger('fb-cancel');
          _abort();
          cfg.onCancel(selectedArray, selectedIndex, cfg);
          busy = false;

        },

        _close = function(){

          if(busy || wrap.is(':hidden')) return;

          busy = true;

          if(currentOpts && false === currentOpts.onCleanup(currentArray, currentIndex, currentOpts)){
            busy = false;
            return;
          }

          _abort();

          $(nav_left.add(nav_right)).hide();
          $(content.add(overlay)).unbind();
          $(window).unbind('resize.fb scroll.fb');
          $(document).unbind('keydown.fb');

          content.find('iframe').attr('src', 'about:blank');
          wrap.stop();

          function _cleanup(){
            overlay.fadeOut('fast');
            title.empty().hide();
            wrap.hide();
            $.event.trigger('fb-cleanup');
            content.empty();
            currentOpts.onClosed(currentArray, currentIndex, currentOpts);
            currentArray = cfg	= [];
            currentIndex = selectedIndex = 0;
            currentOpts = cfg = {};
            busy = false;
          }

          if(currentOpts.transitionOut == 'elastic'){

            start_pos = _get_zoom_from();

            var pos = wrap.position();

            final_pos = {
              top: pos.top,
              left: pos.left,
              width: wrap.width(),
              height: wrap.height()
            };

            if(currentOpts.opacity)
              final_pos.opacity = 1;

            title.empty().hide();

            fx.prop = 1;

            $(fx).animate({ prop: 0 }, {
              duration: currentOpts.speedOut,
              easing: currentOpts.easingOut,
              step: _draw,
              complete: _cleanup
            });

          }else{
            wrap.fadeOut(currentOpts.transitionOut == 'none' ? 0 : currentOpts.speedOut, _cleanup);

          }
        },

        _resize = function(){
          if(overlay.is(':visible'))
            overlay.css('height', $(document).height());

          _center(true);
        },

        _center = function(){
          var view, align;

          if(busy) return;

          align = arguments[0] === true ? 1 : 0;
          view = _get_viewport();

          if(!align && (wrap.width() > view[0] || wrap.height() > view[1])) return;


          wrap
            .stop()
            .animate({
              'top' : parseInt(Math.max(view[3] - 20, view[3] + ((view[1] - content.height() - 40) * 0.5) - currentOpts.padding)),
              'left' : parseInt(Math.max(view[2] - 20, view[2] + ((view[0] - content.width() - 40) * 0.5) - currentOpts.padding))
            }, typeof arguments[0] == 'number' ? arguments[0] : 200);
        };


        if($('#fb-wrap').length || !$(this).length) return this;

        $('body').append(
          tmp = $('<div id="fb-tmp"></div>'),
          loading = $('<div id="fb-loading"></div>'),
          overlay = $('<div id="fb-overlay"></div>'),
          wrap = $('<div id="fb-wrap"></div>')
        );

        outer = $('<div id="fb-outer"></div>').appendTo(wrap);

        outer.append(
          content = $('<div id="fb-content"></div>'),
          title = $('<div id="fb-title"></div>'),
          nav_left = $('<div id="fb-left"><a>&nbsp;</a></<div'),
          nav_right = $('<div id="fb-right"><a>&nbsp;</a></<div')
        );

        loading.click(_cancel);

        $('a', nav_left).click(function(e){
          e.preventDefault();
          _prev();
        });

        $('a', nav_right).click(function(e){
          e.preventDefault();
          _next();
        });

        if($.fn.mousewheel){
          wrap.bind('mousewheel.fb', function(e, delta) {
            if(busy){
              e.preventDefault();

            }else if ($(e.target).get(0).clientHeight == 0 || $(e.target).get(0).scrollHeight === $(e.target).get(0).clientHeight){
              e.preventDefault();
              delta > 0 ? _prev() : _next();
            }
          });
        }

        return this.data('atom.fancybox', $.extend(cfg, ($.metadata ? $(this).metadata() : {}))).unbind('click.fb').bind('click.fb', function(e){
          e.preventDefault();

          if(busy) return;

          busy = true;
          $(this).blur();
          selectedArray = [];
          selectedIndex = 0;
          var group = $(this).data('group') || '';

          if(!group || group == ''){
            selectedArray.push(this);

          }else{
            selectedArray = $('a[data-group="' + group + '"], area[data-group="' + group + '"]');
            selectedIndex = selectedArray.index(this);
          }

          _start();
        });

      },


     /*
      * Superfish v1.4.8 - jQuery menu widget
      * Copyright (c) 2008 Joel Birch
      *
      * Dual licensed under the MIT and GPL licenses:
      * 	http://www.opensource.org/licenses/mit-license.php
      * 	http://www.gnu.org/licenses/gpl.html
      */
      superfish: function(){

       cfg = $.extend({
          hoverClass    : 'open',
          delay         : 500,
          dir           : 'marginTop',
          fx            : true,
          autoArrows    : true,
          menuClass     : 'superfish',
          anchorClass   : 'has-submenus',
          arrowClass    : 'arrow'
        }, cfg);

        var arrow = $(['<span class="', cfg.arrowClass, '">&nbsp;</span>'].join('')),

            over = function(){
              var menu = getMenu($(this));
              clearTimeout(menu.timer);
              openMenu(this);
              closeMenu($(this).siblings());
            },

            out = function(){
              var menu = getMenu($(this)),
                  that = $(this);

              clearTimeout(menu.timer);
              menu.timer = setTimeout(function(){ closeMenu(that); }, cfg.delay);
            },

            getMenu = function(menu){
              return menu.parents(['ul.', cfg.menuClass, ':first'].join(''))[0];
            },

            addArrow = function(wrapper){
              wrapper.addClass(cfg.anchorClass).append(arrow.clone());
            },

            closeMenu = function(elements){
              $(elements).each(function(){
                var menu = $('li.' + cfg.hoverClass, this).add(this).removeClass(cfg.hoverClass).find('>ul');

                if(cfg.fx){
                  var css = {};

                  css[cfg.dir] = 20;
                  if($.support.opacity)
                    css['opacity'] = 0;

                  menu.stop().animate(css, 150, 'swing', function(){$(this).css({display: 'none'})});

                }else{
                  menu.css({display: 'none'});
                }

              });
            },

            openMenu = function(elements){

              $(elements).each(function(){

                if(cfg.fx){
                  var css1 = {}, css2 = {};
                  css1['display'] = 'block';
                  css1[cfg.dir] = 20;
                  css2[cfg.dir] = 0;
                  if($.support.opacity){
                    css1['opacity'] = 0;
                    css2['opacity'] = 1;
                  };
                  $(this).addClass(cfg.hoverClass).find('>ul:hidden').css(css1).stop().animate(css2, 150, 'swing');

                }else{
                  $(this).addClass(cfg.hoverClass).find('>ul:hidden').css({display: 'block'});
                }

              });
            };

        return this.each(function(){

          if($('body').hasClass('no-fx')) cfg.fx = false;

          if(cfg.fx){
            if($(this).hasClass('slide-up')) cfg.dir = 'marginBottom';
            if($(this).hasClass('slide-left')) cfg.dir = 'marginLeft';
            if($(this).hasClass('slide-right')) cfg.dir = 'marginRight';
          };

          var toHide = $('li:has(ul)', this)['hover'](over, out).each(function(){
            if(cfg.autoArrows)
              addArrow($('>a:first-child', this));
          });

          closeMenu(toHide);

          $('a', this).each(function(i){
            var link_li = $(this).eq(i).parents('li');
            $(this).eq(i).focus(function(){ over.call(link_li); }).blur(function(){ out.call(link_li); });
          });
        }).each(function(){
          $(this).addClass(cfg.menuClass);
        });

      }

    };

    if(methods[method]){
      //cfg = $.extend(this.data(), cfg);
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    }

  }
})(jQuery);





jQuery(document).ready(function($){

  var options = atom_config.options.split('|'),
      atom_init = function(){

        if($.inArray('effects', options) !== -1){
          $('body').removeClass('no-fx');
          $('.nudge').atom('nudgeLinks');
          $('a.go-top').atom('goTopControl');
        }

        $('.block a.more').atom('showMoreControl');

        $('.nav ul.menu, nav > ul').atom('superfish');   // add .tabs > .navi too ?
        $('a.screenshot').atom('webShots');
        $('.accordion, .collapsible').atom('collapsibleMenu');
        $('.toggle').atom('toggleVisibility');

        $('a.tt').atom('bubble');
        $('#comments').atom('commentControls');

        // update avatar on e-mail field change
        $('input.email, input#email').bind('change', function(){
          $.ajax({
            url: atom_config.blog_url,
            type: 'GET',
            context: this,
            data: ({
              atom: 'get_avatar',
              email: $(this).val(),
              size: $(this).data('size') || 0
            }),
            success: function(html){
              if(html != '')
                $('#' + ($(this).data('avatar') || 'user-avatar')).html(html);
            }
          });
        });


        $('.clearField').atom('clearField');

        $('form').submit(function(){
          $('.clearField', this).each(function(){ $(this).atom('clearFieldCheck'); });
          return true;
        });

        $('form').each(function(){
          $('a.submit', this).click(function(){ $(this).parents('form').submit(); });
        });

        $('.tabs').atom('tabs');

        $('.iSlider').atom('imageSlider');


        if($.inArray('lightbox', options) !== -1){

          $('a').filter(function(){ return !!this.href.match(/.+\.(jpg|jpeg|png|gif)$/i); }).each(function(){
            $(this).attr('data-group', 'group' + $(this).parents('div').index()).addClass('lightbox');
          });

          $('.lightbox').atom('fancybox');

          $('a').bind('href_updated', function(){  // for DOM changes made by the image slider
            if(this.href.match(/.+\.(jpg|jpeg|png|gif)$/i)) $(this).atom('fancybox');
          });
        }

        // auto resize thumbs;
        // detecting new content relies on ajax requests (old code was using livequery polling)
        if($.inArray('generate_thumbs', options) !== -1){
          $(document).bind('ajaxComplete doThisNow', function(){
            $('span.no-img.regen').each(function(){

              // we don't want infinite loops
              $(this).removeClass('regen');

              $.ajax({
                url: atom_config.blog_url,
                type: 'GET',
                context: this,
                data: ({
                  atom: 'update_thumb',
                  attachment_size: $(this).data('size'),
                  post_id: $(this).data('post'),
                  thumb_id: $(this).data('thumb')
                }),
                beforeSend: function(){
                   $(this).addClass('loading');
                },
                success: function(data){
                  if(data != '') $(this).replaceWith(data); else $(this).removeClass('loading');
                }
              });
            });
          }).trigger('doThisNow');
        }

        /* -- disabled for now -- @todo, handle inline <script>'s and document.write conflicts...
        if(atom_config.search_query)
          $('.posts').highlightText('<?php echo $search_query; ?>', 1, 'highlight');
        */

        // single nav link
        $('.page-navi.single a').click(function(event){

          event.preventDefault();
          var oldTitle = $(this).html();

          $.ajax({
            url: this.href,
            type: 'POST',
            data: { atom: 'content_only' },
            context: this,
            beforeSend: function(){ $(this).addClass('loading').html(''); },
            success: function(data){
              $(data).find('#primary-content .posts .hentry').hide().appendTo($('.posts')).fadeIn(333);
              var new_page = $(data).find('#primary-content .page-navi.single a').attr('href');
              new_page ? $(this).attr('href', new_page).removeClass('loading').html(oldTitle) : $(this).remove();
            }
          });
        });


        /**
        $('.posts').delegate('.post-content .more-link', 'click', function(event){
          event.preventDefault();

          $.ajax({
            url: blog_url,
            type: 'GET',
            context: this,
            data: ({
              atom: 'read_more',
              post_id: $(this).data('post')
            }),
            beforeSend: function(){
              $(this).text('Loading...');
            },
            success: function(data){
              $(this).closest('.post-content').html(data);
            }
          });
        });
        /**/

        // attempt to fix z-index issues with youtube movies
        $('iframe[src*="youtube.com"]').each(function(){
          this.src += (this.src.indexOf('?') !== -1) ? '&wmode=Opaque' : '?wmode=Opaque';
        });

        // fix for older embed code
        $('[type="application/x-shockwave-flash"]').append('<param name="wMode" value="transparent" />');

      };


  $(document).bind('atom_ready', atom_init).trigger('atom_ready');

});
