/**
* Cookie plugin
*
* Copyright (c) 2006 Klaus Hartl (stilbuero.de)
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/
jQuery.cookie = function(name, value, options){
  if(typeof value != 'undefined'){ // name and value given, set cookie
    options = options || {};
    if(value === null){
      value = '';
      options.expires = -1;
    }
    var expires = '';
    if(options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)){
      var date;
      if(typeof options.expires == 'number'){
        date = new Date();
        date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
      }else{
        date = options.expires;
      }
      expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
    }

    // CAUTION: Needed to parenthesize options.path and options.domain
    // in the following expressions, otherwise they evaluate to undefined
    // in the packed version for some reason...
    var path = options.path ? '; path=' + (options.path) : '',
        domain = options.domain ? '; domain=' + (options.domain) : '',
        secure = options.secure ? '; secure' : '';
    document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');

  }else{ // only name given, get cookie
    var cookieValue = null;
    if(document.cookie && document.cookie != ''){
      var cookies = document.cookie.split(';');
      for(var i = 0; i < cookies.length; i++){
        var cookie = jQuery.trim(cookies[i]);
        // Does this cookie string begin with the name we want?
        if(cookie.substring(0, name.length + 1) == (name + '=')){
          cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
          break;
        }
      }
    }
    return cookieValue;
  }
};




/*

 jQuery Form Dependencies v2.0
   by digitalnature

     http://digitalnature.eu
     http://github.com/digitalnature/Form-Dependencies

     Demo: http://dev.digitalnature.eu/jquery/form-dependencies/


 This was originally based on the Form Manager script by Twey (http://www.dynamicdrive.com/dynamicindex16/formdependency.htm),
 but the code got completely refactored from 2.0...


  Usage:

    $('form').FormDependencies();
    $('#some_div').FormDependencies({attribute:'data-rules', hide_inactive:true, clear_inactive:true});


  Example:

    <input type="checkbox" id="first" />
    <input type="checkbox" id="second" />
    <input type="submit" id="third" data-rules="first + !second" />

    will keep "third" input disabled until "first" checkbox is checked and "second" checkbox is unchecked.



  Change log:

    31 mar. 2012, v2.0  - rewritten and changed syntax completely (removed keyword options)
                        - changed "disable_only" option name to "hide_inactive"
                        - added "selectors" option (implies support for any type of element)

    13 feb. 2012, v1.3  - slightly improved performance (rules are parsed once, and only dependencies are now iterated during checks)
                        - changed target selector to match the container of the input fields, instead of the input fields directly
                        - added repo to github (first public release)

    23 jun. 2011, v1.2  - Fixed a problem in the value matching function

    21 jun. 2011, v1.1  - Added disable_only / clear_inactive options

    20 jun. 2011, v1.0  - First release

*/


(function($){
$.fn.FormDependencies = function(opts){

  var defaults = {

        // the attribute which contains the rules (use 'data-rules' for w3c-valid HTML5 code)
        attribute             : 'rules',

        // selectors to parse rules from
        selectors             : 'input, select, textarea, button',

        // if true it will disable fields + label, otherwise it will also hide them
        hide_inactive         : false,

        // clears input values from hidden/disabled fields
        clear_inactive        : false,

        // attribute used to identify dependencies, must be unique if other than "name"
        identify_by           : 'name'
      },

      opts = $.extend(defaults, opts),

      // disable (and maybe hide) the hooked element
      hide = function(e){

        if(!$(e).is(':input') && !$(e).hasClass('disabled'))
          $(e).addClass('disabled');

        if(!e.disabled){
          e.disabled = true;
          $('label[for="' + e.id + '"]').addClass('disabled');

          if(opts.hide_inactive){
            $(e).hide();
            $('label[for="' + e.id + '"]').hide();
          }

          // we don't want to "clear" submit buttons
          if(opts.clear_inactive && !$(e).is(':submit'))
            if($(e).is(':checkbox, :radio')) e.checked = false; else if(!$(e).is('select')) $(e).val('');

        }
      },

      // enable (and show?) the hooked element
      show = function(e){

        if(!$(e).is(':input') && $(e).hasClass('disabled'))
          $(e).removeClass('disabled');

        if(e.disabled || !$(e).is(':visible')){
          e.disabled = false;
          $('label[for="' + e.id + '"]').removeClass('disabled');

          if(opts.hide_inactive){
            $(e).show();
            $('label[for="' + e.id + '"]').show();
          }

          //if(opts.clear_inactive && $(e).is(':checkbox, :radio')) e.checked = true;
        }

      },

      // verifies if a conditions is met
      matches = function(key, values, block){

        var i, v, invert = false, e = $('[' + opts.identify_by + '="' + key + '"]', block);

        // @note: using filter() is 4-5 times faster than using multiple selectors
        e = e.is(':radio') ? e.filter(':checked') : e.filter('[type!="hidden"]')

        for(i = 0; i < values.length; i++){

          v = values[i];
          invert = false;

          if(v[0] === '!'){
            invert = true;
            v = v.substr(1);
          }

          if((!v && e.is(':checked')) || (e.val() == v) || ((e.is(':submit') || e.is(':button')) && !e.is(':disabled')))
            return !invert;
        }

        return invert;
      };



  return this.each(function(){

    var block = this, rules = [], keys = [];

    // parse rules
    $(opts.selectors, this).each(function(){

      var deps = $(this).attr(opts.attribute), dep, parsed_deps = {}, i, invert;

      if(!deps)
        return this;

      deps = deps.split('+');

      for(i = 0; i < deps.length; i++){

        dep = $.trim(deps[i]);
        invert = false;

        // reverse conditional check if the name starts with '!'
        // the rules should have any values specified in this case
        if(dep[0] === '!'){
          dep = dep.substr(1);
          invert = true;
        }

        dep = dep.split(':');
        values = dep[1] || '';

        if(!values && invert)
          values = '!';

        parsed_deps[dep[0]] = values.split('|');

        // store key inputs in a separate array
        $('[' + opts.identify_by + '="' + dep[0] + '"]', block).filter('[type!="hidden"]').each(function(){
          ($.inArray(this, keys) !== -1) || keys.push(this);
        });

      }

      rules.push({target: this, deps: parsed_deps});
    });

    // attach our state checking function on keys (ie. inputs on which other inputs depend on)
    if(keys.length){
      $(keys).on('change keyup', function(){

        // iterate trough all rules
        $.each(rules, function(input, input_rules){

          var hide_it = false;

          $.each(input_rules.deps, function(key, values){

            // we check only if a condition fails,
            // in which case we know we need to hide the hooked element
            if(!matches(key, values, block)){
              hide_it = true;
              return false;
            }
          });

          hide_it ? hide(input_rules.target) : show(input_rules.target);

        });

      }).change();
    }

    return this;

  });

};

})(jQuery);




/**
 * AJAX Upload ( http://valums.com/ajax-upload/ )
 * Copyright (c) Andris Valums
 * Licensed under the MIT license ( http://valums.com/mit-license/ )
 * Thanks to Gary Haran, David Mark, Corey Burns and others for contributions
 */
(function () {
    /* global window */
    /* jslint browser: true, devel: true, undef: true, nomen: true, bitwise: true, regexp: true, newcap: true, immed: true */

    /**
     * Wrapper for FireBug's console.log
     */
    function log(){
        if (typeof(console) != 'undefined' && typeof(console.log) == 'function'){
            Array.prototype.unshift.call(arguments, '[Ajax Upload]');
            console.log( Array.prototype.join.call(arguments, ' '));
        }
    }

    /**
     * Attaches event to a dom element.
     * @param {Element} el
     * @param type event name
     * @param fn callback This refers to the passed element
     */
    function addEvent(el, type, fn){
        if (el.addEventListener) {
            el.addEventListener(type, fn, false);
        } else if (el.attachEvent) {
            el.attachEvent('on' + type, function(){
                fn.call(el);
	        });
	    } else {
            throw new Error('not supported or DOM not loaded');
        }
    }

    /**
     * Attaches resize event to a window, limiting
     * number of event fired. Fires only when encounteres
     * delay of 100 after series of events.
     *
     * Some browsers fire event multiple times when resizing
     * http://www.quirksmode.org/dom/events/resize.html
     *
     * @param fn callback This refers to the passed element
     */
    function addResizeEvent(fn){
        var timeout;

	    addEvent(window, 'resize', function(){
            if (timeout){
                clearTimeout(timeout);
            }
            timeout = setTimeout(fn, 100);
        });
    }

    // Needs more testing, will be rewriten for next version
    // getOffset function copied from jQuery lib (http://jquery.com/)
    if (document.documentElement.getBoundingClientRect){
        // Get Offset using getBoundingClientRect
        // http://ejohn.org/blog/getboundingclientrect-is-awesome/
        var getOffset = function(el){
            var box = el.getBoundingClientRect();
            var doc = el.ownerDocument;
            var body = doc.body;
            var docElem = doc.documentElement; // for ie
            var clientTop = docElem.clientTop || body.clientTop || 0;
            var clientLeft = docElem.clientLeft || body.clientLeft || 0;

            // In Internet Explorer 7 getBoundingClientRect property is treated as physical,
            // while others are logical. Make all logical, like in IE8.
            var zoom = 1;
            if (body.getBoundingClientRect) {
                var bound = body.getBoundingClientRect();
                zoom = (bound.right - bound.left) / body.clientWidth;
            }

            if (zoom > 1) {
                clientTop = 0;
                clientLeft = 0;
            }

            var top = box.top / zoom + (window.pageYOffset || docElem && docElem.scrollTop / zoom || body.scrollTop / zoom) - clientTop, left = box.left / zoom + (window.pageXOffset || docElem && docElem.scrollLeft / zoom || body.scrollLeft / zoom) - clientLeft;

            return {
                top: top,
                left: left
            };
        };
    } else {
        // Get offset adding all offsets
        var getOffset = function(el){
            var top = 0, left = 0;
            do {
                top += el.offsetTop || 0;
                left += el.offsetLeft || 0;
                el = el.offsetParent;
            } while (el);

            return {
                left: left,
                top: top
            };
        };
    }

    /**
     * Returns left, top, right and bottom properties describing the border-box,
     * in pixels, with the top-left relative to the body
     * @param {Element} el
     * @return {Object} Contains left, top, right,bottom
     */
    function getBox(el){
        var left, right, top, bottom;
        var offset = getOffset(el);
        left = offset.left;
        top = offset.top;

        right = left + el.offsetWidth;
        bottom = top + el.offsetHeight;

        return {
            left: left,
            right: right,
            top: top,
            bottom: bottom
        };
    }

    /**
     * Helper that takes object literal
     * and add all properties to element.style
     * @param {Element} el
     * @param {Object} styles
     */
    function addStyles(el, styles){
        for (var name in styles) {
            if (styles.hasOwnProperty(name)) {
                el.style[name] = styles[name];
            }
        }
    }

    /**
     * Function places an absolutely positioned
     * element on top of the specified element
     * copying position and dimentions.
     * @param {Element} from
     * @param {Element} to
     */
    function copyLayout(from, to){
	    var box = getBox(from);

        addStyles(to, {
	        position: 'absolute',
	        left : box.left + 'px',
	        top : box.top + 'px',
	        width : from.offsetWidth + 'px',
	        height : from.offsetHeight + 'px'
	    });
    }

    /**
    * Creates and returns element from html chunk
    * Uses innerHTML to create an element
    */
    var toElement = (function(){
        var div = document.createElement('div');
        return function(html){
            div.innerHTML = html;
            var el = div.firstChild;
            return div.removeChild(el);
        };
    })();

    /**
     * Function generates unique id
     * @return unique id
     */
    var getUID = (function(){
        var id = 0;
        return function(){
            return 'ValumsAjaxUpload' + id++;
        };
    })();

    /**
     * Get file name from path
     * @param {String} file path to file
     * @return filename
     */
    function fileFromPath(file){
        return file.replace(/.*(\/|\\)/, "");
    }

    /**
     * Get file extension lowercase
     * @param {String} file name
     * @return file extenstion
     */
    function getExt(file){
        return (-1 !== file.indexOf('.')) ? file.replace(/.*[.]/, '') : '';
    }

    function hasClass(el, name){
        var re = new RegExp('\\b' + name + '\\b');
        return re.test(el.className);
    }
    function addClass(el, name){
        if ( ! hasClass(el, name)){
            el.className += ' ' + name;
        }
    }
    function removeClass(el, name){
        var re = new RegExp('\\b' + name + '\\b');
        el.className = el.className.replace(re, '');
    }

    function removeNode(el){
        el.parentNode.removeChild(el);
    }

    /**
     * Easy styling and uploading
     * @constructor
     * @param button An element you want convert to
     * upload button. Tested dimentions up to 500x500px
     * @param {Object} options See defaults below.
     */
    window.AjaxUpload = function(button, options){
        this._settings = {
            // Location of the server-side upload script
            action: 'upload.php',
            // File upload name
            name: 'userfile',
            // Additional data to send
            data: {},
            // Submit file as soon as it's selected
            autoSubmit: true,
            // The type of data that you're expecting back from the server.
            // html and xml are detected automatically.
            // Only useful when you are using json data as a response.
            // Set to "json" in that case.
            responseType: false,
            // Class applied to button when mouse is hovered
            hoverClass: 'hover',
            // Class applied to button when AU is disabled
            disabledClass: 'disabled',
            // When user selects a file, useful with autoSubmit disabled
            // You can return false to cancel upload
            onChange: function(file, extension){
            },
            // Callback to fire before file is uploaded
            // You can return false to cancel upload
            onSubmit: function(file, extension){
            },
            // Fired when file upload is completed
            // WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
            onComplete: function(file, response){
            }
        };

        // Merge the users options with our defaults
        for (var i in options) {
            if (options.hasOwnProperty(i)){
                this._settings[i] = options[i];
            }
        }

        // button isn't necessary a dom element
        if (button.jquery){
            // jQuery object was passed
            button = button[0];
        } else if (typeof button == "string") {
            if (/^#.*/.test(button)){
                // If jQuery user passes #elementId don't break it
                button = button.slice(1);
            }

            button = document.getElementById(button);
        }

        if ( ! button || button.nodeType !== 1){
            throw new Error("Please make sure that you're passing a valid element");
        }

        if ( button.nodeName.toUpperCase() == 'A'){
            // disable link
            addEvent(button, 'click', function(e){
                if (e && e.preventDefault){
                    e.preventDefault();
                } else if (window.event){
                    window.event.returnValue = false;
                }
            });
        }

        // DOM element
        this._button = button;
        // DOM element
        this._input = null;
        // If disabled clicking on button won't do anything
        this._disabled = false;

        // if the button was disabled before refresh if will remain
        // disabled in FireFox, let's fix it
        this.enable();

        this._rerouteClicks();
    };

    // assigning methods to our class
    AjaxUpload.prototype = {
        setData: function(data){
            this._settings.data = data;
        },
        disable: function(){
            addClass(this._button, this._settings.disabledClass);
            this._disabled = true;

            var nodeName = this._button.nodeName.toUpperCase();
            if (nodeName == 'INPUT' || nodeName == 'BUTTON'){
                this._button.setAttribute('disabled', 'disabled');
            }

            // hide input
            if (this._input){
                // We use visibility instead of display to fix problem with Safari 4
                // The problem is that the value of input doesn't change if it
                // has display none when user selects a file
                this._input.parentNode.style.visibility = 'hidden';
            }
        },
        enable: function(){
            removeClass(this._button, this._settings.disabledClass);
            this._button.removeAttribute('disabled');
            this._disabled = false;

        },
        /**
         * Creates invisible file input
         * that will hover above the button
         * <div><input type='file' /></div>
         */
        _createInput: function(){
            var self = this;

            var input = document.createElement("input");
            input.setAttribute('type', 'file');
            input.setAttribute('name', this._settings.name);

            addStyles(input, {
                'position' : 'absolute',
                // in Opera only 'browse' button
                // is clickable and it is located at
                // the right side of the input
                'right' : 0,
                'margin' : 0,
                'padding' : 0,
                'fontSize' : '480px',
                'cursor' : 'pointer'
            });

            var div = document.createElement("div");
            addStyles(div, {
                'display' : 'block',
                'position' : 'absolute',
                'overflow' : 'hidden',
                'margin' : 0,
                'padding' : 0,
                'opacity' : 0,
                // Make sure browse button is in the right side
                // in Internet Explorer
                'direction' : 'ltr',
                //Max zIndex supported by Opera 9.0-9.2
                'zIndex': 2147483583
            });

            // Make sure that element opacity exists.
            // Otherwise use IE filter
            if ( div.style.opacity !== "0") {
                if (typeof(div.filters) == 'undefined'){
                    throw new Error('Opacity not supported by the browser');
                }
                div.style.filter = "alpha(opacity=0)";
            }

            addEvent(input, 'change', function(){

                if ( ! input || input.value === ''){
                    return;
                }

                // Get filename from input, required
                // as some browsers have path instead of it
                var file = fileFromPath(input.value);

                if (false === self._settings.onChange.call(self, file, getExt(file))){
                    self._clearInput();
                    return;
                }

                // Submit form when value is changed
                if (self._settings.autoSubmit) {
                    self.submit();
                }
            });

            addEvent(input, 'mouseover', function(){
                addClass(self._button, self._settings.hoverClass);
            });

            addEvent(input, 'mouseout', function(){
                removeClass(self._button, self._settings.hoverClass);

                // We use visibility instead of display to fix problem with Safari 4
                // The problem is that the value of input doesn't change if it
                // has display none when user selects a file
                //input.parentNode.style.visibility = 'hidden';

            });

	        div.appendChild(input);
            document.body.appendChild(div);

            this._input = input;
        },
        _clearInput : function(){
            if (!this._input){
                return;
            }

            // this._input.value = ''; Doesn't work in IE6
            removeNode(this._input.parentNode);
            this._input = null;
            this._createInput();

            removeClass(this._button, this._settings.hoverClass);
        },
        /**
         * Function makes sure that when user clicks upload button,
         * the this._input is clicked instead
         */
        _rerouteClicks: function(){
            var self = this;

            // IE will later display 'access denied' error
            // if you use using self._input.click()
            // other browsers just ignore click()

            addEvent(self._button, 'mouseover', function(){
                if (self._disabled){
                    return;
                }

                if ( ! self._input){
	                self._createInput();
                }

                var div = self._input.parentNode;
                copyLayout(self._button, div);
                div.style.visibility = 'visible';

            });


            // commented because we now hide input on mouseleave
            /**
             * When the window is resized the elements
             * can be misaligned if button position depends
             * on window size
             */
            //addResizeEvent(function(){
            //    if (self._input){
            //        copyLayout(self._button, self._input.parentNode);
            //    }
            //});

        },
        /**
         * Creates iframe with unique name
         * @return {Element} iframe
         */
        _createIframe: function(){
            // We can't use getTime, because it sometimes return
            // same value in safari :(
            var id = getUID();

            // We can't use following code as the name attribute
            // won't be properly registered in IE6, and new window
            // on form submit will open
            // var iframe = document.createElement('iframe');
            // iframe.setAttribute('name', id);

            var iframe = toElement('<iframe src="javascript:false;" name="' + id + '" />');
            // src="javascript:false; was added
            // because it possibly removes ie6 prompt
            // "This page contains both secure and nonsecure items"
            // Anyway, it doesn't do any harm.
            iframe.setAttribute('id', id);

            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            return iframe;
        },
        /**
         * Creates form, that will be submitted to iframe
         * @param {Element} iframe Where to submit
         * @return {Element} form
         */
        _createForm: function(iframe){
            var settings = this._settings;

            // We can't use the following code in IE6
            // var form = document.createElement('form');
            // form.setAttribute('method', 'post');
            // form.setAttribute('enctype', 'multipart/form-data');
            // Because in this case file won't be attached to request
            var form = toElement('<form method="post" enctype="multipart/form-data"></form>');

            form.setAttribute('action', settings.action);
            form.setAttribute('target', iframe.name);
            form.style.display = 'none';
            document.body.appendChild(form);

            // Create hidden input element for each data key
            for (var prop in settings.data) {
                if (settings.data.hasOwnProperty(prop)){
                    var el = document.createElement("input");
                    el.setAttribute('type', 'hidden');
                    el.setAttribute('name', prop);
                    el.setAttribute('value', settings.data[prop]);
                    form.appendChild(el);
                }
            }
            return form;
        },
        /**
         * Gets response from iframe and fires onComplete event when ready
         * @param iframe
         * @param file Filename to use in onComplete callback
         */
        _getResponse : function(iframe, file){
            // getting response
            var toDeleteFlag = false, self = this, settings = this._settings;

            addEvent(iframe, 'load', function(){

                if (// For Safari
                    iframe.src == "javascript:'%3Chtml%3E%3C/html%3E';" ||
                    // For FF, IE
                    iframe.src == "javascript:'<html></html>';"){
                        // First time around, do not delete.
                        // We reload to blank page, so that reloading main page
                        // does not re-submit the post.

                        if (toDeleteFlag) {
                            // Fix busy state in FF3
                            setTimeout(function(){
                                removeNode(iframe);
                            }, 0);
                        }

                        return;
                }

                var doc = iframe.contentDocument ? iframe.contentDocument : window.frames[iframe.id].document;

                // fixing Opera 9.26,10.00
                if (doc.readyState && doc.readyState != 'complete') {
                   // Opera fires load event multiple times
                   // Even when the DOM is not ready yet
                   // this fix should not affect other browsers
                   return;
                }

                // fixing Opera 9.64
                if (doc.body && doc.body.innerHTML == "false") {
                    // In Opera 9.64 event was fired second time
                    // when body.innerHTML changed from false
                    // to server response approx. after 1 sec
                    return;
                }

                var response;

                if (doc.XMLDocument) {
                    // response is a xml document Internet Explorer property
                    response = doc.XMLDocument;
                } else if (doc.body){
                    // response is html document or plain text
                    response = doc.body.innerHTML;

                    if (settings.responseType && settings.responseType.toLowerCase() == 'json') {
                        // If the document was sent as 'application/javascript' or
                        // 'text/javascript', then the browser wraps the text in a <pre>
                        // tag and performs html encoding on the contents.  In this case,
                        // we need to pull the original text content from the text node's
                        // nodeValue property to retrieve the unmangled content.
                        // Note that IE6 only understands text/html
                        if (doc.body.firstChild && doc.body.firstChild.nodeName.toUpperCase() == 'PRE') {
                            response = doc.body.firstChild.firstChild.nodeValue;
                        }

                        if (response) {
                            response = eval("(" + response + ")");
                        } else {
                            response = {};
                        }
                    }
                } else {
                    // response is a xml document
                    response = doc;
                }

                settings.onComplete.call(self, file, response);

                // Reload blank page, so that reloading main page
                // does not re-submit the post. Also, remember to
                // delete the frame
                toDeleteFlag = true;

                // Fix IE mixed content issue
                iframe.src = "javascript:'<html></html>';";
            });
        },
        /**
         * Upload file contained in this._input
         */
        submit: function(){
            var self = this, settings = this._settings;

            if ( ! this._input || this._input.value === ''){
                return;
            }

            var file = fileFromPath(this._input.value);

            // user returned false to cancel upload
            if (false === settings.onSubmit.call(this, file, getExt(file))){
                this._clearInput();
                return;
            }

            // sending request
            var iframe = this._createIframe();
            var form = this._createForm(iframe);

            // assuming following structure
            // div -> input type='file'
            removeNode(this._input.parentNode);
            removeClass(self._button, self._settings.hoverClass);

            form.appendChild(this._input);

            form.submit();

            // request set, clean up
            removeNode(form); form = null;
            removeNode(this._input); this._input = null;

            // Get response from iframe and fire onComplete event when ready
            this._getResponse(iframe, file);

            // get ready for next request
            this._createInput();
        }
    };
})();









/*
 * jQuery miniColors: A small color selector
 * Copyright 2011 Cory LaViska for A Beautiful Site, LLC. (http://abeautifulsite.net/)
 * Dual licensed under the MIT or GPL Version 2 licenses
 *
 * Heavily modified for Atom! Careful if you're attempting to update the plugin,
 * which you shouldn't because I'm planning to maintain this fork in the future....
 *
 */
(function($){

  $.extend($.fn, {

    miniColors: function(o, data){

      var lastColors = ['FFFFFF', 'CCCCCC', '888888', '444444', '000000'], // last picked colors are stored here (we're filling this with defaults on start-up)

          pickerSize = 150, // other sizes not yet supported, need to adjust the math grrr..

          create = function(input, o, data){

            // determine initial color (defaults to white)
            var color = expandHex(input.val());

            // Create trigger
            var trigger = $('<a class="trigger" href="#"></a>');

            if(color)
              trigger.css('background-color', '#' + color);

            if(!color)
              trigger.addClass('alpha');

            trigger.insertAfter(input);

            // Set input data and update attributes
            input
            .addClass('miniColors')
            .data('cp.original-maxlength', input.attr('maxlength') || null)
            .data('cp.original-autocomplete', input.attr('autocomplete') || null)
            .data('cp.letterCase', 'uppercase')
            .data('cp.trigger', trigger)
            .data('cp.hsb', hex2hsb(color))
            .data('cp.change', o.change ? o.change : null)
            .attr('maxlength', 7)
            .attr('autocomplete', 'off')
            .val(convertCase(color, o.letterCase));

            // Handle options
            if(o.readonly)
              input.prop('readonly', true);

            if(o.disabled)
              disable(input);

            // Show selector when trigger is clicked
            trigger.bind('click.miniColors', function(event){
              event.preventDefault();
              show(input);
            });

            input
              .bind('focus.miniColors', function(event){
                show(input);
              })
              .bind('blur.miniColors', function(event){
                var hex = expandHex(input.val());
                input.val(hex ? convertCase(hex, input.data('cp.letterCase')) : '');
              })
              .bind('keydown.miniColors', function(event){
                if(event.keyCode === 9) hide(input);
              })
              .bind('keyup.miniColors', function(event){
                setColorFromInput(input);
              })
              .bind('paste.miniColors', function(event){
                // Short pause to wait for paste to complete
                setTimeout(function(){ setColorFromInput(input); }, 5);
              });

          },

         // Destroys an active instance of the miniColors selector
          destroy = function(input){

            hide();
            input = $(input);

            // Restore to original state
            input.data('cp.trigger').remove();
            input
              .attr('autocomplete', input.data('cp.original-autocomplete'))
              .attr('maxlength', input.data('cp.original-maxlength'))
              .removeData()
              .removeClass('miniColors')
              .unbind('.miniColors');

            $(document).unbind('.miniColors');
          },

          // Enables the input control and the selector
          enable = function(input){
            input.prop('disabled', false).data('cp.trigger').css('opacity', 1);
          },

         // Disables the input control and the selector
          disable = function(input){
            hide(input);
            input.prop('disabled', true).data('cp.trigger').css('opacity', 0.5);
          },

          // Shows the miniColors selector
          show = function(input){

            if(input.prop('disabled')) return false;

            // Hide all other instances
            hide();

            // Generate the selector
            var selector = $('<div class="selector"></div>'),
                shortcuts = $('<div class="shortcuts"></div>');

            $('<a class="alpha"></a>').appendTo(shortcuts).click(function(){
              input.val('').data('cp.trigger').addClass('alpha');
              hide();

              // fire change callback, send "false" to denote that no color was selected
              if(input.data('cp.change'))
                input.data('cp.change').call(input.get(0), false, false);
            });

            for(var i = 1; i <= 5; i++){
              $('<a class="quick-set-' + i + '"></a>').appendTo(shortcuts).data('índex', i).css('background-color', '#' + lastColors[i -1]).click(function(){
                var color = lastColors[$(this).data('índex') - 1];

                $(this).css('background-color', '#' + color);
                input.val(color);
                setColorFromInput(input);
              });
            }

            // we will attempt to bring up the selector within the current user viewport
			var inputPos = input.is(':visible') ? input.offset() : input.data('cp.trigger').offset(),
                inputHeight = (input.is(':visible') ? input.outerHeight() : input.data('cp.trigger').outerHeight()),
                viewPort = {
                  l : window.pageXOffset || document.documentElement.scrollLeft,
                  t : window.pageYOffset || document.documentElement.scrollTop,
                  w : window.innerWidth || document.documentElement.clientWidth,
                  h : window.innerHeight || document.documentElement.clientHeight
                };

            selector
              .append('<div class="wheel" style="background-color: #fff;"><div class="wheel-picker"></div></div>')
              .append('<div class="hue"><div class="hue-picker"></div></div>')
              .append(shortcuts)
              .css({
                top: (inputPos.top + inputHeight + pickerSize > viewPort.t + viewPort.h) ? inputPos.top - (pickerSize + 10) : inputPos.top + inputHeight,
                left: (inputPos.left + (pickerSize + 60) > viewPort.l + viewPort.w) ? inputPos.left - (pickerSize + 60) : inputPos.left,
                display: 'none'
              })
              .addClass(input.attr('class'));



            // Set background for colors
            var hsb = input.data('cp.hsb');

            selector.find('.wheel').css('backgroundColor', '#' + hsb2hex({ h: hsb.h, s: 100, b: 100 }));

            // Set colorPicker position
            var colorPosition = input.data('cp.colorPosition');

            if(!colorPosition)
              colorPosition = getColorPositionFromHSB(hsb);

            selector.find('.wheel-picker').css({top: colorPosition.y + 'px', left: colorPosition.x + 'px'});

            // Set huePicker position
            var huePosition = input.data('cp.huePosition');

            if(!huePosition)
              huePosition = getHuePositionFromHSB(hsb);

            selector.find('.hue-picker').css('top', huePosition.y + 'px');

            // Set input data
            input
              .data('cp.selector', selector)
              .data('cp.huePicker', selector.find('.hue-picker'))
              .data('cp.colorPicker', selector.find('.wheel-picker'))
              .data('cp.mousebutton', 0);

            $('body').append(selector);

            selector.fadeIn(100);

            // Prevent text selection in IE
            selector.bind('selectstart', function(){ return false; });

            $(document).bind('mousedown.miniColors touchstart.miniColors', function(event){
              input.data('cp.mousebutton', 1);

              if($(event.target).parents().andSelf().hasClass('wheel')){
                event.preventDefault();
                input.data('cp.moving', 'colors');
                moveColor(input, event);
              }

              if($(event.target).parents().andSelf().hasClass('hue')){
                event.preventDefault();
                input.data('cp.moving', 'hues');
                moveHue(input, event);
              }

              if($(event.target).parents().andSelf().hasClass('selector')){
                event.preventDefault();
                return;
              }

              if($(event.target).parents().andSelf().hasClass('miniColors')) return;

              hide(input);
            });

            $(document)
              .bind('mouseup.miniColors touchend.miniColors', function(event){
                event.preventDefault();
                input.data('cp.mousebutton', 0).removeData('moving');
              })
              .bind('mousemove.miniColors touchmove.miniColors', function(event){
                event.preventDefault();
                if(input.data('cp.mousebutton') === 1){

                  if(input.data('cp.moving') === 'colors')
                    moveColor(input, event);

                  if(input.data('cp.moving') === 'hues')
                    moveHue(input, event);
                }
              });

          },

          // Hides one or more miniColors selectors
          hide = function(input){

            var color = input ? $(input).val() : false;

            if(color && $.inArray(color, lastColors) === -1){
              lastColors.unshift(color);
              lastColors = lastColors.slice(0, -1);
            }

            // Hide all other instances if input isn't specified
            if(!input)
              input = '.miniColors';

            $(input).each(function(){
              var selector = $(this).data('cp.selector');
              $(this).removeData('selector');
              $(selector).fadeOut(100, function(){ $(this).remove(); });
            });

            $(document).unbind('.miniColors');

          },

          moveColor = function(input, event){

            var colorPicker = input.data('cp.colorPicker');

            colorPicker.hide();

            var position = {x: event.pageX, y: event.pageY};

            // Touch support
            if(event.originalEvent.changedTouches){
              position.x = event.originalEvent.changedTouches[0].pageX;
              position.y = event.originalEvent.changedTouches[0].pageY;
            }

            position.x = position.x - input.data('cp.selector').find('.wheel').offset().left - 5;
            position.y = position.y - input.data('cp.selector').find('.wheel').offset().top - 5;

            if(position.x <= -5)
              position.x = -5;

            if(position.x >= pickerSize - 6)
              position.x = pickerSize - 6;

            if(position.y <= -5)
              position.y = -5;

            if(position.y >= pickerSize - 6)
              position.y = pickerSize - 6;

            input.data('cp.colorPosition', position);
            colorPicker.css('left', position.x).css('top', position.y).show();

            // Calculate saturation
            var s = Math.round((position.x + 5) * 0.67);

            s = Math.min(Math.max(s, 0), 100);

            // Calculate brightness
            var b = 100 - Math.round((position.y + 5) * 0.67);

            b = Math.min(Math.max(b, 0), 100);

            // Update HSB values
            var hsb = input.data('cp.hsb');
            hsb.s = s;
            hsb.b = b;

            // Set color
            setColor(input, hsb, true);
          },

          moveHue = function(input, event){

            var huePicker = input.data('cp.huePicker');

            huePicker.hide();

            var position = {y: event.pageY};

            // Touch support
            if(event.originalEvent.changedTouches)
              position.y = event.originalEvent.changedTouches[0].pageY;

            position.y = position.y - input.data('cp.selector').find('.wheel').offset().top - 1;

            position.y = Math.min(Math.max(position.y, -1), pickerSize - 1);

            input.data('cp.huePosition', position);
            huePicker.css('top', position.y).show();

            // Update HSB values
            var hsb = input.data('cp.hsb');

            // Calculate hue
            hsb.h = Math.min(Math.max((Math.round((pickerSize - position.y - 1) * 2.4)), 0), 360);

            // Set color
            setColor(input, hsb, true);
          },

          setColor = function(input, hsb, updateInput){
            input.data('cp.hsb', hsb);
            var hex = hsb2hex(hsb);

            if(updateInput)
              input.val(convertCase(hex, input.data('cp.letterCase')));

            input.data('cp.trigger').removeClass('alpha');
            input.data('cp.trigger').css('backgroundColor', '#' + hex);

            if(input.data('cp.selector'))
              input.data('cp.selector').find('.wheel').css('backgroundColor', '#' + hsb2hex({h: hsb.h, s: 100, b: 100}));

            // Fire change callback
            if(input.data('cp.change')){
              if(hex === input.data('cp.lastChange')) return;
              input.data('cp.change').call(input.get(0), hex, hsb2rgb(hsb));
              input.data('cp.lastChange', hex);
            }

          },

          setColorFromInput = function(input){

            input.val(cleanHex(input.val()));
            var hex = expandHex(input.val());

            if(!hex){
              input.data('cp.trigger').addClass('alpha');
              return false;
            }

            // Get HSB equivalent
            var hsb = hex2hsb(hex);

            // If color is the same, no change required
            var currentHSB = input.data('cp.hsb');

            if(hsb.h === currentHSB.h && hsb.s === currentHSB.s && hsb.b === currentHSB.b)
              return true;

            // Set colorPicker position
            var colorPosition = getColorPositionFromHSB(hsb);
            var colorPicker = $(input.data('cp.colorPicker'));

            colorPicker.css('top', colorPosition.y + 'px').css('left', colorPosition.x + 'px');
            input.data('cp.colorPosition', colorPosition);

            // Set huePosition position
            var huePosition = getHuePositionFromHSB(hsb);
            var huePicker = $(input.data('cp.huePicker'));

            huePicker.css('top', huePosition.y + 'px');
            input.data('cp.huePosition', huePosition);

            setColor(input, hsb);

            return true;
          },

          convertCase = function(string, letterCase){

            if(letterCase === 'lowercase')
              return string.toLowerCase();

            if(letterCase === 'uppercase')
              return string.toUpperCase();

            return string;
          },

          getColorPositionFromHSB = function(hsb){
            var x = Math.min(Math.max(Math.ceil(hsb.s / 0.67), 0), pickerSize),
                y = Math.min(Math.max((pickerSize - Math.ceil(hsb.b / 0.67)), 0), pickerSize);

            return {x: x - 5, y: y - 5};
          },

          getHuePositionFromHSB = function(hsb){
            return {y: Math.min(Math.max((pickerSize - (hsb.h / 2.4)), 0), pickerSize) - 1};
          },

          cleanHex = function(hex){
            return hex.replace(/[^A-F0-9]/ig, '');
          },

          expandHex = function(hex){
            hex = cleanHex(hex);

            if(!hex)
              return null;

            if(hex.length === 3)
              hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];

            return hex.length === 6 ? hex : null;
          },

          hsb2rgb = function(hsb){
            var rgb = {},
                h = Math.round(hsb.h);
                s = Math.round(hsb.s * 255 / 100);
                v = Math.round(hsb.b * 255 / 100);

            if(s === 0){
              rgb.r = rgb.g = rgb.b = v;

            }else{
              var t1 = v;
              var t2 = (255 - s) * v / 255;
              var t3 = (t1 - t2) * (h % 60) / 60;
              if(h === 360) h = 0;
              if(h < 60){ rgb.r = t1; rgb.b = t2; rgb.g = t2 + t3;}
              else if(h < 120){rgb.g = t1; rgb.b = t2; rgb.r = t1 - t3;}
              else if(h < 180){rgb.g = t1; rgb.r = t2; rgb.b = t2 + t3;}
              else if(h < 240){rgb.b = t1; rgb.r = t2; rgb.g = t1 - t3;}
              else if(h < 300){rgb.b = t1; rgb.g = t2; rgb.r = t2 + t3;}
              else if(h < 360){rgb.r = t1; rgb.g = t2; rgb.b = t1 - t3;}
              else{ rgb.r = 0; rgb.g = 0; rgb.b = 0;}
            }

            return {r: Math.round(rgb.r), g: Math.round(rgb.g), b: Math.round(rgb.b)};
          },

          rgb2hex = function(rgb){
            var hex = [parseInt(rgb.r).toString(16), parseInt(rgb.g).toString(16), parseInt(rgb.b).toString(16)];
            $.each(hex, function(nr, val){
              if(val.length === 1)
                hex[nr] = '0' + val;
            });
            return hex.join('');
          },

          hex2rgb = function(hex){
            hex = parseInt(hex, 16);
            return {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: (hex & 0x0000FF)};
          },

          rgb2hsb = function(rgb){
            var hsb = {h: 0, s: 0, b: 0};
            var min = Math.min(rgb.r, rgb.g, rgb.b);
            var max = Math.max(rgb.r, rgb.g, rgb.b);
            var delta = max - min;
            hsb.b = max;
            hsb.s = max !== 0 ? 255 * delta / max : 0;

            if(hsb.s !== 0){
              if(rgb.r === max){
                hsb.h = (rgb.g - rgb.b) / delta;
              }else if(rgb.g === max){
                hsb.h = 2 + (rgb.b - rgb.r) / delta;
              }else{
                hsb.h = 4 + (rgb.r - rgb.g) / delta;
              }

            }else{
              hsb.h = -1;
            }

            hsb.h *= 60;

            if(hsb.h < 0)
              hsb.h += 360;

            hsb.s *= 100 / 255;
            hsb.b *= 100 / 255;

            return hsb;
          },

          hex2hsb = function(hex){
            var hsb = rgb2hsb(hex2rgb(hex));

            // Zero out hue marker for black, white, and grays (saturation === 0)
            if(hsb.s === 0)
              hsb.h = 360;

            return hsb;
          },

          hsb2hex = function(hsb){
            return rgb2hex(hsb2rgb(hsb));
          };


      // Handle calls to $([selector]).miniColors()
      switch(o){
        case 'readonly':
          $(this).each(function(){
            if(!$(this).hasClass('miniColors')) return;
            $(this).prop('readonly', data);
          });
          return $(this);

        case 'disabled':
          $(this).each(function(){
            if(!$(this).hasClass('miniColors')) return;
            data ? disable($(this)) : enable($(this));
          });
          return $(this);

        case 'value':
          // Getter
          if(data === undefined){
            if(!$(this).hasClass('miniColors')) return;
            var input = $(this),
                hex = expandHex(input.val());
            return hex ? convertCase(hex, input.data('cp.letterCase')) : null;
          }

          // Setter
          $(this).each(function(){
            if(!$(this).hasClass('miniColors')) return;
            $(this).val(data);
            setColorFromInput($(this));
          });

          return $(this);

        case 'destroy':
          $(this).each(function(){
            if(!$(this).hasClass('miniColors')) return;
            destroy($(this));
          });

          return $(this);

        default:
          if(!o) o = {};
          $(this).each(function(){

            // Must be called on an input element
            if($(this)[0].tagName.toLowerCase() !== 'input') return;

            // If a trigger is present, the control was already created
            if($(this).data('cp.trigger')) return;

            // Create the control
            create($(this), o, data);
          });

          return $(this);

      }

    }

  });

})(jQuery);





// jQuery Slider Plugin
// Egor Khmelev - http://blog.egorkhmelev.com/ - hmelyoff@gmail.com

(function(){

  // Simple Inheritance
  Function.prototype.inheritFrom = function(BaseClass, oOverride){
  	var Inheritance = function() {};
  	Inheritance.prototype = BaseClass.prototype;
  	this.prototype = new Inheritance();
  	this.prototype.constructor = this;
  	this.prototype.baseConstructor = BaseClass;
  	this.prototype.superClass = BaseClass.prototype;

  	if(oOverride){
  		for(var i in oOverride) {
  			this.prototype[i] = oOverride[i];
  		}
  	}
  };

  // Format numbers
  Number.prototype.jSliderNice=function(iRoundBase){
  	var re=/^(-)?(\d+)([\.,](\d+))?$/;
  	var iNum=Number(this);
  	var sNum=String(iNum);
  	var aMatches;
  	var sDecPart='';
  	var sTSeparator=' ';
  	if((aMatches = sNum.match(re))){
  		var sIntPart=aMatches[2];
  		var iDecPart=(aMatches[4]) ? Number('0.'+aMatches[4]) : 0;
  		if(iDecPart){
  			var iRF=Math.pow(10, (iRoundBase) ? iRoundBase : 2);
  			iDecPart=Math.round(iDecPart*iRF);
  			sNewDecPart=String(iDecPart);
  			sDecPart = sNewDecPart;
  			if(sNewDecPart.length < iRoundBase){
  				var iDiff = iRoundBase-sNewDecPart.length;
  				for (var i=0; i < iDiff; i++) {
  					sDecPart = "0" + sDecPart;
  				};
  			}
  			sDecPart = "," + sDecPart;
  		} else {
  			if(iRoundBase && iRoundBase != 0){
  				for (var i=0; i < iRoundBase; i++) {
  					sDecPart += "0";
  				};
  				sDecPart = "," + sDecPart;
  			}
  		}
  		var sResult;
  		if(Number(sIntPart) < 1000){
  			sResult = sIntPart+sDecPart;
  		}else{
  			var sNewNum='';
  			var i;
  			for(i=1; i*3<sIntPart.length; i++)
  				sNewNum=sTSeparator+sIntPart.substring(sIntPart.length - i*3, sIntPart.length - (i-1)*3)+sNewNum;
  			sResult = sIntPart.substr(0, 3 - i*3 + sIntPart.length)+sNewNum+sDecPart;
  		}
  		if(aMatches[1])
  			return '-'+sResult;
  		else
  			return sResult;
  	}
  	else{
  		return sNum;
  	}
  };

  this.jSliderIsArray = function( value ){
    if( typeof value == "undefined" ) return false;

    if (value instanceof Array ||  // Works quickly in same execution context.
        // If value is from a different execution context then
        // !(value instanceof Object), which lets us early out in the common
        // case when value is from the same context but not an array.
        // The {if (value)} check above means we don't have to worry about
        // undefined behavior of Object.prototype.toString on null/undefined.
        //
        // HACK: In order to use an Object prototype method on the arbitrary
        //   value, the compiler requires the value be cast to type Object,
        //   even though the ECMA spec explicitly allows it.
        (!(value instanceof Object) &&
         (Object.prototype.toString.call(
             /** @type {Object} */ (value)) == '[object Array]') ||

         // In IE all non value types are wrapped as objects across window
         // boundaries (not iframe though) so we have to do object detection
         // for this edge case
         typeof value.length == 'number' &&
         typeof value.splice != 'undefined' &&
         typeof value.propertyIsEnumerable != 'undefined' &&
         !value.propertyIsEnumerable('splice')

        )) {
      return true;
    }

    return false;
  }


})();


// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed

(function(){
  var cache = {};

  this.jSliderTmpl = function jSliderTmpl(str, data){
    // Figure out if we're getting a template, or if we need to
    // load the template - and be sure to cache the result.
    var fn = !(/\W/).test(str) ?
      cache[str] = cache[str] ||
        jSliderTmpl(str) :

      // Generate a reusable function that will serve as a template
      // generator (and which will be cached).
      new Function("obj",
        "var p=[],print=function(){p.push.apply(p,arguments);};" +

        // Introduce the data as local variables using with(){}
        "with(obj){p.push('" +

        // Convert the template into pure JavaScript
        str
          .replace(/[\r\t\n]/g, " ")
          .split("<%").join("\t")
          .replace(/((^|%>)[^\t]*)'/g, "$1\r")
          .replace(/\t=(.*?)%>/g, "',$1,'")
          .split("\t").join("');")
          .split("%>").join("p.push('")
          .split("\r").join("\\'")
      + "');}return p.join('');");

    // Provide some basic currying to the user
    return data ? fn( data ) : fn;
  };
})();


// Draggable Class
// Egor Khmelev - http://blog.egorkhmelev.com/

(function( $ ){

  this.Draggable = function(){
  	this._init.apply( this, arguments );
  };

  Draggable.prototype = {
  	// Methods for re-init in child class
  	oninit: function(){},
  	events: function(){},
  	onmousedown: function(){
  		this.ptr.css({ position: "absolute" });
  	},
  	onmousemove: function( evt, x, y ){
  		this.ptr.css({ left: x, top: y });
  	},
  	onmouseup: function(){},

  	isDefault: {
  		drag: false,
  		clicked: false,
  		toclick: true,
  		mouseup: false
  	},

  	_init: function(){
  		if( arguments.length > 0 ){
  			this.ptr = $(arguments[0]);
  			this.outer = $(".draggable-outer");

  			this.is = {};
  			$.extend( this.is, this.isDefault );

  			var _offset = this.ptr.offset();
  			this.d = {
  				left: _offset.left,
  				top: _offset.top,
  				width: this.ptr.width(),
  				height: this.ptr.height()
  			};

  			this.oninit.apply( this, arguments );

  			this._events();
  		}
  	},
  	_getPageCoords: function( event ){
  	  if( event.targetTouches && event.targetTouches[0] ){
  	    return { x: event.targetTouches[0].pageX, y: event.targetTouches[0].pageY };
  	  } else
  	    return { x: event.pageX, y: event.pageY };
  	},
  	_bindEvent: function( ptr, eventType, handler ){
  	  var self = this;

  	  if( this.supportTouches_ )
        ptr.get(0).addEventListener( this.events_[ eventType ], handler, false );

  	  else
  	    ptr.bind( this.events_[ eventType ], handler );
  	},
  	_events: function(){
  		var self = this;

      this.supportTouches_ = ( $.browser.webkit && navigator.userAgent.indexOf("Mobile") != -1 );
      this.events_ = {
        "click": this.supportTouches_ ? "touchstart" : "click",
        "down": this.supportTouches_ ? "touchstart" : "mousedown",
        "move": this.supportTouches_ ? "touchmove" : "mousemove",
        "up"  : this.supportTouches_ ? "touchend" : "mouseup"
      };

      this._bindEvent( $( document ), "move", function( event ){
				if( self.is.drag ){
          event.stopPropagation();
          event.preventDefault();
					self._mousemove( event );
				}
			});
      this._bindEvent( $( document ), "down", function( event ){
				if( self.is.drag ){
          event.stopPropagation();
          event.preventDefault();
				}
			});
      this._bindEvent( $( document ), "up", function( event ){
				self._mouseup( event );
			});

      this._bindEvent( this.ptr, "down", function( event ){
				self._mousedown( event );
				return false;
			});
      this._bindEvent( this.ptr, "up", function( event ){
				self._mouseup( event );
			});

  		this.ptr.find("a")
  			.click(function(){
  				self.is.clicked = true;

  				if( !self.is.toclick ){
  					self.is.toclick = true;
  					return false;
  				}
  			})
  			.mousedown(function( event ){
  				self._mousedown( event );
  				return false;
  			});

  		this.events();
  	},
  	_mousedown: function( evt ){
  		this.is.drag = true;
  		this.is.clicked = false;
  		this.is.mouseup = false;

  		var _offset = this.ptr.offset();
  		var coords = this._getPageCoords( evt );
  		this.cx = coords.x - _offset.left;
  		this.cy = coords.y - _offset.top;

  		$.extend(this.d, {
  			left: _offset.left,
  			top: _offset.top,
  			width: this.ptr.width(),
  			height: this.ptr.height()
  		});

  		if( this.outer && this.outer.get(0) ){
  			this.outer.css({ height: Math.max(this.outer.height(), $(document.body).height()), overflow: "hidden" });
  		}

  		this.onmousedown( evt );
  	},
  	_mousemove: function( evt ){
  		this.is.toclick = false;
  		var coords = this._getPageCoords( evt );
  		this.onmousemove( evt, coords.x - this.cx, coords.y - this.cy );
  	},
  	_mouseup: function( evt ){
  		var oThis = this;

  		if( this.is.drag ){
  			this.is.drag = false;

  			if( this.outer && this.outer.get(0) ){

  				if( $.browser.mozilla ){
  					this.outer.css({ overflow: "hidden" });
  				} else {
  					this.outer.css({ overflow: "visible" });
  				}

  				if( $.browser.msie && $.browser.version == '6.0' ){
  					this.outer.css({ height: "100%" });
  				} else {
  					this.outer.css({ height: "auto" });
  				}
  			}

  			this.onmouseup( evt );
  		}
  	}

  };

})( jQuery );



// jQuery Slider (Safari)
// Egor Khmelev - http://blog.egorkhmelev.com/

(function( $ ) {

	$.slider = function( node, settings ){
	  var jNode = $(node);
	  if( !jNode.data( "jslider" ) )
	    jNode.data( "jslider", new jSlider( node, settings ) );

	  return jNode.data( "jslider" );
	};

	$.fn.slider = function( action, opt_value ){
	  var returnValue, args = arguments;

	  function isDef( val ){
	    return val !== undefined;
	  };

	  function isDefAndNotNull( val ){
      return val != null;
	  };

		this.each(function(){
		  var self = $.slider( this, action );

		  // do actions
		  if( typeof action == "string" ){
		    switch( action ){
		      case "value":
		        if( isDef( args[ 1 ] ) && isDef( args[ 2 ] ) ){
		          var pointers = self.getPointers();
		          if( isDefAndNotNull( pointers[0] ) && isDefAndNotNull( args[1] ) ){
		            pointers[0].set( args[ 1 ] );
		            pointers[0].setIndexOver();
		          }

		          if( isDefAndNotNull( pointers[1] ) && isDefAndNotNull( args[2] ) ){
		            pointers[1].set( args[ 2 ] );
		            pointers[1].setIndexOver();
		          }
		        }

		        else if( isDef( args[ 1 ] ) ){
		          var pointers = self.getPointers();
		          if( isDefAndNotNull( pointers[0] ) && isDefAndNotNull( args[1] ) ){
		            pointers[0].set( args[ 1 ] );
		            pointers[0].setIndexOver();
		          }
		        }

		        else
  		        returnValue = self.getValue();

		        break;

		      case "prc":
		        if( isDef( args[ 1 ] ) && isDef( args[ 2 ] ) ){
		          var pointers = self.getPointers();
		          if( isDefAndNotNull( pointers[0] ) && isDefAndNotNull( args[1] ) ){
		            pointers[0]._set( args[ 1 ] );
		            pointers[0].setIndexOver();
		          }

		          if( isDefAndNotNull( pointers[1] ) && isDefAndNotNull( args[2] ) ){
		            pointers[1]._set( args[ 2 ] );
		            pointers[1].setIndexOver();
		          }
		        }

		        else if( isDef( args[ 1 ] ) ){
		          var pointers = self.getPointers();
		          if( isDefAndNotNull( pointers[0] ) && isDefAndNotNull( args[1] ) ){
		            pointers[0]._set( args[ 1 ] );
		            pointers[0].setIndexOver();
		          }
		        }

		        else
  		        returnValue = self.getPrcValue();

		        break;

  		    case "calculatedValue":
  		      var value = self.getValue().split(";");
  		      returnValue = "";
  		      for (var i=0; i < value.length; i++) {
  		        returnValue += (i > 0 ? ";" : "") + self.nice( value[i] );
  		      };

  		      break;

  		    case "skin":
		        self.setSkin( args[1] );

  		      break;
		    };

		  }

		  // return actual object
		  else if( !action && !opt_value ){
		    if( !jSliderIsArray( returnValue ) )
		      returnValue = [];

		    returnValue.push( slider );
		  }
		});

		// flatten array just with one slider
		if( jSliderIsArray( returnValue ) && returnValue.length == 1 )
		  returnValue = returnValue[ 0 ];

		return returnValue || this;
	};

  var OPTIONS = {

    settings: {
      from: 1,
      to: 10,
      step: 1,
      smooth: true,
      limits: true,
      round: 0,
      value: "5;7",
      dimension: ""
    },

    className: "jslider",
    selector: ".jslider-",

    template: jSliderTmpl(
      '<span class="<%=className%>">' +
        '<table><tr><td>' +
          '<div class="<%=className%>-bg">' +
            '<i class="l"><i></i></i><i class="r"><i></i></i>' +
            '<i class="v"><i></i></i>' +
          '</div>' +

          '<div class="<%=className%>-pointer"><i></i></div>' +
          '<div class="<%=className%>-pointer <%=className%>-pointer-to"><i></i></div>' +

          '<div class="<%=className%>-label"><span><%=settings.from%></span></div>' +
          '<div class="<%=className%>-label <%=className%>-label-to"><span><%=settings.to%></span><%=settings.dimension%></div>' +

          '<div class="<%=className%>-value"><span></span><%=settings.dimension%></div>' +
          '<div class="<%=className%>-value <%=className%>-value-to"><span></span><%=settings.dimension%></div>' +

          '<div class="<%=className%>-scale"><%=scale%></div>'+

        '</td></tr></table>' +
      '</span>'
    )

  };

  this.jSlider = function(){
  	return this.init.apply( this, arguments );
  };

  jSlider.prototype = {
    init: function( node, settings ){
      this.settings = $.extend(true, {}, OPTIONS.settings, settings ? settings : {});

      // obj.sliderHandler = this;
      this.inputNode = $( node ).hide();

			this.settings.interval = this.settings.to-this.settings.from;
			this.settings.value = this.inputNode.attr("value");

			if( this.settings.calculate && $.isFunction( this.settings.calculate ) )
			  this.nice = this.settings.calculate;

			if( this.settings.onstatechange && $.isFunction( this.settings.onstatechange ) )
			  this.onstatechange = this.settings.onstatechange;

      this.is = {
        init: false
      };
			this.o = {};

      this.create();
    },

    onstatechange: function(){},

    create: function(){
      var $this = this;

      this.domNode = $( OPTIONS.template({
        className: OPTIONS.className,
        settings: {
          from: this.nice( this.settings.from ),
          to: this.nice( this.settings.to ),
          dimension: this.settings.dimension
        },
        scale: this.generateScale()
      }) );

      this.inputNode.after( this.domNode );
      this.drawScale();

      // set skin class
      if( this.settings.skin && this.settings.skin.length > 0 )
        this.setSkin( this.settings.skin );

			this.sizes = {
			  domWidth: this.domNode.width(),
			  domOffset: this.domNode.offset()
			};

      // find some objects
      $.extend(this.o, {
        pointers: {},
        labels: {
          0: {
            o: this.domNode.find(OPTIONS.selector + "value").not(OPTIONS.selector + "value-to")
          },
          1: {
            o: this.domNode.find(OPTIONS.selector + "value").filter(OPTIONS.selector + "value-to")
          }
        },
        limits: {
          0: this.domNode.find(OPTIONS.selector + "label").not(OPTIONS.selector + "label-to"),
          1: this.domNode.find(OPTIONS.selector + "label").filter(OPTIONS.selector + "label-to")
        }
      });

      $.extend(this.o.labels[0], {
        value: this.o.labels[0].o.find("span")
      });

      $.extend(this.o.labels[1], {
        value: this.o.labels[1].o.find("span")
      });


      if( !$this.settings.value.split(";")[1] ){
        this.settings.single = true;
        this.domNode.addDependClass("single");
      }

      if( !$this.settings.limits )
        this.domNode.addDependClass("limitless");

      this.domNode.find(OPTIONS.selector + "pointer").each(function( i ){
        var value = $this.settings.value.split(";")[i];
        if( value ){
          $this.o.pointers[i] = new jSliderPointer( this, i, $this );

          var prev = $this.settings.value.split(";")[i-1];
          if( prev && new Number(value) < new Number(prev) ) value = prev;

          value = value < $this.settings.from ? $this.settings.from : value;
          value = value > $this.settings.to ? $this.settings.to : value;

          $this.o.pointers[i].set( value, true );
        }
      });

      this.o.value = this.domNode.find(".v");
      this.is.init = true;

      $.each(this.o.pointers, function(i){
        $this.redraw(this);
      });

      (function(self){
        $(window).resize(function(){
          self.onresize();
        });
      })(this);

    },

    setSkin: function( skin ){
      if( this.skin_ )
        this.domNode.removeDependClass( this.skin_, "_" );

      this.domNode.addDependClass( this.skin_ = skin, "_" );
    },

    setPointersIndex: function( i ){
      $.each(this.getPointers(), function(i){
        this.index( i );
      });
    },

    getPointers: function(){
      return this.o.pointers;
    },

    generateScale: function(){
      if( this.settings.scale && this.settings.scale.length > 0 ){
        var str = "";
        var s = this.settings.scale;

        //var prc = Math.round((100/(s.length-1))*10)/10;
        var prc = (((100/(s.length-1)))*10)/10; // Math.round() change

        for( var i=0; i < s.length; i++ ){
          str += '<span style="left: ' + i*prc + '%">' + ( s[i] != '|' ? '<ins>' + s[i] + '</ins>' : '' ) + '</span>';
        };
        return str;
      };

      return '';
    },

    drawScale: function(){
      this.domNode.find(OPTIONS.selector + "scale span ins").each(function(){
        $(this).css({ marginLeft: -$(this).outerWidth()/2 });
      });
    },

    onresize: function(){
      var self = this;
			this.sizes = {
			  domWidth: this.domNode.width(),
			  domOffset: this.domNode.offset()
			};

      $.each(this.o.pointers, function(i){
        self.redraw(this);
      });
    },

    limits: function( x, pointer ){
  	  // smooth
  	  if( !this.settings.smooth ){
  	    var step = this.settings.step*100 / ( this.settings.interval );
  	    x = Math.round( x/step ) * step;
  	  }

  	  var another = this.o.pointers[1-pointer.uid];
  	  if( another && pointer.uid && x < another.value.prc ) x = another.value.prc;
  	  if( another && !pointer.uid && x > another.value.prc ) x = another.value.prc;

      // base limit
  	  if( x < 0 ) x = 0;
  	  if( x > 100 ) x = 100;

      return Math.round( x*10 ) / 10;
    },

    redraw: function( pointer ){
      if( !this.is.init ) return false;

      this.setValue();

      // redraw range line
      if( this.o.pointers[0] && this.o.pointers[1] )
        this.o.value.css({ left: this.o.pointers[0].value.prc + "%", width: ( this.o.pointers[1].value.prc - this.o.pointers[0].value.prc ) + "%" });

      this.o.labels[pointer.uid].value.html(
        this.nice(
          pointer.value.origin
        )
      );

      // redraw position of labels
      this.redrawLabels( pointer );

    },

    redrawLabels: function( pointer ){

      function setPosition( label, sizes, prc ){
    	  sizes.margin = -sizes.label/2;

        // left limit
        label_left = sizes.border + sizes.margin;
        if( label_left < 0 )
          sizes.margin -= label_left;

        // right limit
        if( sizes.border+sizes.label / 2 > self.sizes.domWidth ){
          sizes.margin = 0;
          sizes.right = true;
        } else
          sizes.right = false;

        label.o.css({ left: prc + "%", marginLeft: sizes.margin, right: "auto" });
        if( sizes.right ) label.o.css({ left: "auto", right: 0 });
        return sizes;
      }

      var self = this;
  	  var label = this.o.labels[pointer.uid];
  	  var prc = pointer.value.prc;

  	  var sizes = {
  	    label: label.o.outerWidth(),
  	    right: false,
  	    border: ( prc * this.sizes.domWidth ) / 100
  	  };

      //console.log(this.o.pointers[1-pointer.uid])
      if( !this.settings.single ){
        // glue if near;
        var another = this.o.pointers[1-pointer.uid];
      	var another_label = this.o.labels[another.uid];

        switch( pointer.uid ){
          case 0:
            if( sizes.border+sizes.label / 2 > another_label.o.offset().left-this.sizes.domOffset.left ){
              another_label.o.css({ visibility: "hidden" });
          	  another_label.value.html( this.nice( another.value.origin ) );

            	label.o.css({ visibility: "visible" });

            	prc = ( another.value.prc - prc ) / 2 + prc;
            	if( another.value.prc != pointer.value.prc ){
            	  label.value.html( this.nice(pointer.value.origin) + "&nbsp;&ndash;&nbsp;" + this.nice(another.value.origin) );
              	sizes.label = label.o.outerWidth();
              	sizes.border = ( prc * this.sizes.domWidth ) / 100;
              }
            } else {
            	another_label.o.css({ visibility: "visible" });
            }
            break;

          case 1:
            if( sizes.border - sizes.label / 2 < another_label.o.offset().left - this.sizes.domOffset.left + another_label.o.outerWidth() ){
              another_label.o.css({ visibility: "hidden" });
          	  another_label.value.html( this.nice(another.value.origin) );

            	label.o.css({ visibility: "visible" });

            	prc = ( prc - another.value.prc ) / 2 + another.value.prc;
            	if( another.value.prc != pointer.value.prc ){
            	  label.value.html( this.nice(another.value.origin) + "&nbsp;&ndash;&nbsp;" + this.nice(pointer.value.origin) );
              	sizes.label = label.o.outerWidth();
              	sizes.border = ( prc * this.sizes.domWidth ) / 100;
              }
            } else {
              another_label.o.css({ visibility: "visible" });
            }
            break;
        }
      }

      sizes = setPosition( label, sizes, prc );

      /* draw second label */
      if( another_label ){
        var sizes = {
    	    label: another_label.o.outerWidth(),
    	    right: false,
    	    border: ( another.value.prc * this.sizes.domWidth ) / 100
    	  };
        sizes = setPosition( another_label, sizes, another.value.prc );
      }

	    this.redrawLimits();
    },

    redrawLimits: function(){
  	  if( this.settings.limits ){

        var limits = [ true, true ];

        for( key in this.o.pointers ){

          if( !this.settings.single || key == 0 ){

        	  var pointer = this.o.pointers[key];
            var label = this.o.labels[pointer.uid];
            var label_left = label.o.offset().left - this.sizes.domOffset.left;

        	  var limit = this.o.limits[0];
            if( label_left < limit.outerWidth() )
              limits[0] = false;

        	  var limit = this.o.limits[1];
        	  if( label_left + label.o.outerWidth() > this.sizes.domWidth - limit.outerWidth() )
        	    limits[1] = false;
        	}

        };

        for( var i=0; i < limits.length; i++ ){
          if( limits[i] )
            this.o.limits[i].fadeIn("fast");
          else
            this.o.limits[i].fadeOut("fast");
        };

  	  }
    },

    setValue: function(){
      var value = this.getValue();
      this.inputNode.attr( "value", value );
      this.onstatechange.call( this, value );
    },
    getValue: function(){
      if(!this.is.init) return false;
      var $this = this;

      var value = "";
      $.each( this.o.pointers, function(i){
        if( this.value.prc != undefined && !isNaN(this.value.prc) ) value += (i > 0 ? ";" : "") + $this.prcToValue( this.value.prc );
      });
      return value;
    },
    getPrcValue: function(){
      if(!this.is.init) return false;
      var $this = this;

      var value = "";
      $.each( this.o.pointers, function(i){
        if( this.value.prc != undefined && !isNaN(this.value.prc) ) value += (i > 0 ? ";" : "") + this.value.prc;
      });
      return value;
    },
    prcToValue: function( prc ){

  	  if( this.settings.heterogeneity && this.settings.heterogeneity.length > 0 ){
    	  var h = this.settings.heterogeneity;

    	  var _start = 0;
    	  var _from = this.settings.from;

    	  for( var i=0; i <= h.length; i++ ){
    	    if( h[i] ) var v = h[i].split("/");
    	    else       var v = [100, this.settings.to];

    	    v[0] = new Number(v[0]);
    	    v[1] = new Number(v[1]);

    	    if( prc >= _start && prc <= v[0] ) {
    	      var value = _from + ( (prc-_start) * (v[1]-_from) ) / (v[0]-_start);
    	    }

    	    _start = v[0];
    	    _from = v[1];
    	  };

  	  } else {
        var value = this.settings.from + ( prc * this.settings.interval ) / 100;
  	  }

      return this.round( value );
    },

  	valueToPrc: function( value, pointer ){
  	  if( this.settings.heterogeneity && this.settings.heterogeneity.length > 0 ){
    	  var h = this.settings.heterogeneity;

    	  var _start = 0;
    	  var _from = this.settings.from;

    	  for (var i=0; i <= h.length; i++) {
    	    if(h[i]) var v = h[i].split("/");
    	    else     var v = [100, this.settings.to];
    	    v[0] = new Number(v[0]); v[1] = new Number(v[1]);

    	    if(value >= _from && value <= v[1]){
    	      var prc = pointer.limits(_start + (value-_from)*(v[0]-_start)/(v[1]-_from));
    	    }

    	    _start = v[0]; _from = v[1];
    	  };

  	  } else {
    	  var prc = pointer.limits((value-this.settings.from)*100/this.settings.interval);
  	  }

  	  return prc;
  	},


  	round: function( value ){
	    value = Math.round( value / this.settings.step ) * this.settings.step;
  		if( this.settings.round ) value = Math.round( value * Math.pow(10, this.settings.round) ) / Math.pow(10, this.settings.round);
  		else value = Math.round( value );
  		return value;
  	},

  	nice: function( value ){
  		value = value.toString().replace(/,/gi, ".");
  		value = value.toString().replace(/ /gi, "");
  		if( Number.prototype.jSliderNice )
  		  return (new Number(value)).jSliderNice(this.settings.round).replace(/-/gi, "&minus;");
  		else
  		  return new Number(value);
  	}

  };

  function jSliderPointer(){
  	this.baseConstructor.apply(this, arguments);
  }

  jSliderPointer.inheritFrom(Draggable, {
    oninit: function( ptr, id, _constructor ){
      this.uid = id;
      this.parent = _constructor;
      this.value = {};
      this.settings = this.parent.settings;
    },
  	onmousedown: function(evt){
  	  this._parent = {
  	    offset: this.parent.domNode.offset(),
  	    width: this.parent.domNode.width()
  	  };
  	  this.ptr.addDependClass("hover");
  	  this.setIndexOver();
  	},
  	onmousemove: function( evt, x ){
  	  var coords = this._getPageCoords( evt );
  	  this._set( this.calc( coords.x ) );
  	},
  	onmouseup: function( evt ){
      // var coords = this._getPageCoords( evt );
      // this._set( this.calc( coords.x ) );

  	  if( this.parent.settings.callback && $.isFunction(this.parent.settings.callback) )
  	    this.parent.settings.callback.call( this.parent, this.parent.getValue() );

  	  this.ptr.removeDependClass("hover");
  	},

  	setIndexOver: function(){
  	  this.parent.setPointersIndex( 1 );
  	  this.index( 2 );
  	},

  	index: function( i ){
  	  this.ptr.css({ zIndex: i });
  	},

  	limits: function( x ){
  	  return this.parent.limits( x, this );
  	},

  	calc: function(coords){
  	  var x = this.limits(((coords-this._parent.offset.left)*100)/this._parent.width);
  	  return x;
  	},

  	set: function( value, opt_origin ){
  	  this.value.origin = this.parent.round(value);
  	  this._set( this.parent.valueToPrc( value, this ), opt_origin );
  	},
  	_set: function( prc, opt_origin ){
  	  if( !opt_origin )
  	    this.value.origin = this.parent.prcToValue(prc);

  	  this.value.prc = prc;
  		this.ptr.css({ left: prc + "%" });
  	  this.parent.redraw(this);
  	}

  });


})(jQuery);





/* end */







/*
 * Depend Class v0.1b : attach class based on first class in list of current element
 * File: jquery.dependClass.js
 * Copyright (c) 2009 Egor Hmelyoff, hmelyoff@gmail.com
 */


(function($) {
	// Init plugin function
	$.baseClass = function(obj){
	  obj = $(obj);
	  return obj.get(0).className.match(/([^ ]+)/)[1];
	};

	$.fn.addDependClass = function(className, delimiter){
		var options = {
		  delimiter: delimiter ? delimiter : '-'
		}
		return this.each(function(){
		  var baseClass = $.baseClass(this);
		  if(baseClass)
    		$(this).addClass(baseClass + options.delimiter + className);
		});
	};

	$.fn.removeDependClass = function(className, delimiter){
		var options = {
		  delimiter: delimiter ? delimiter : '-'
		}
		return this.each(function(){
		  var baseClass = $.baseClass(this);
		  if(baseClass)
    		$(this).removeClass(baseClass + options.delimiter + className);
		});
	};

	$.fn.toggleDependClass = function(className, delimiter){
		var options = {
		  delimiter: delimiter ? delimiter : '-'
		}
		return this.each(function(){
		  var baseClass = $.baseClass(this);
		  if(baseClass)
		    if($(this).is("." + baseClass + options.delimiter + className))
    		  $(this).removeClass(baseClass + options.delimiter + className);
    		else
    		  $(this).addClass(baseClass + options.delimiter + className);
		});
	};

	// end of closure
})(jQuery);








/*
	jQuery TextAreaResizer plugin
	Created on 17th January 2008 by Ryan O'Dell
	Version 1.0.4

	Converted from Drupal -> textarea.js
	Found source: http://plugins.jquery.com/misc/textarea.js
	$Id: textarea.js,v 1.11.2.1 2007/04/18 02:41:19 drumm Exp $

	1.0.1 Updates to missing global 'var', added extra global variables, fixed multiple instances, improved iFrame support
	1.0.2 Updates according to textarea.focus
	1.0.3 Further updates including removing the textarea.focus and moving private variables to top
	1.0.4 Re-instated the blur/focus events, according to information supplied by dec


*/
(function($) {
	var area, staticOffset;  // added the var declaration for 'staticOffset' thanks to issue logged by dec.
	var iLastMousePos = 0;
	var iMin = 32;
	var grip;

	$.fn.Resizer = function(options) {
        var defaults = {
    		onStartDrag: function () {},
		    onEndDrag: function () {}
        };

        var settings = $.extend({}, defaults, options);

      	/* private functions */
      	var startDrag = function(e) {
      		area = $(e.data.el);
      		area.blur();
      		iLastMousePos = mousePosition(e).y;
      		staticOffset = area.height() - iLastMousePos;
      		area.css('opacity', 0.25);
       	    settings.onStartDrag.call(e, area);
      		$(document).mousemove(performDrag).mouseup(endDrag);
      		return false;
      	};

      	var performDrag = function (e){
      		var iThisMousePos = mousePosition(e).y;
      		var iMousePos = staticOffset + iThisMousePos;
      		if (iLastMousePos >= (iThisMousePos)) {
      			iMousePos -= 5;
      		}
      		iLastMousePos = iThisMousePos;
      		iMousePos = Math.max(iMin, iMousePos);
      		area.height(iMousePos + 'px');
      		if (iMousePos < iMin) endDrag(e);
      		return false;
      	};

      	var endDrag = function (e){
      		$(document).unbind('mousemove', performDrag).unbind('mouseup', endDrag);
      		area.css('opacity', 1);
      		area.focus();
            settings.onEndDrag.call(e, area);
      		area = null;
      		staticOffset = null;
      		iLastMousePos = 0;
      	};

      	var mousePosition = function(e) {
      		return { x: e.clientX + document.documentElement.scrollLeft, y: e.clientY + document.documentElement.scrollTop };
      	};

		return this.each(function() {
		    area = $(this).addClass('processed'), staticOffset = null;

			// 18-01-08 jQuery bind to pass data element rather than direct mousedown - Ryan O'Dell
		    // When wrapping the text area, work around an IE margin bug.  See:
		    // http://jaspan.com/ie-inherited-margin-bug-form-elements-and-haslayout
            if($(this).is("iframe")){
              // don't move iframes around the DOM because some browser will reload them...
              $(this).parents('.resizable-wrapper').append($('<div class="grippie"></div>').bind("mousedown",{el: this} , startDrag));
            }else{

  		      $(this).wrap('<div class="resizable-wrapper"></div>')
		        .parent().append($('<div class="grippie"></div>').bind("mousedown",{el: this} , startDrag));
            }

		    var grippie = $('div.grippie', $(this).parent())[0];
		    grippie.style.marginRight = (grippie.offsetWidth - $(this)[0].offsetWidth) +'px';

		});
	};

})(jQuery);





// define our CodeMirror mode (atom/html)
// highlights Atom {KEYWORDS}
CodeMirror.defineMode('atom/html', function(config, parserConfig){
  var atomOverlay = {
    token: function(stream, state){
      var isAtomVar = false;
      if(stream.match('{')){
        while((ch = stream.next()) != null && ch === ch.toUpperCase())
          if(ch === '}'){
            if(/^{[A-Z,_]+}$/.test(stream.current())) isAtomVar = true;
            break;
          }

        if(isAtomVar) return 'atom-var';
      }
      while(stream.next() != null && !stream.match('{', false)){}
      return null;
    }
  };
  return CodeMirror.overlayParser(CodeMirror.getMode(config, parserConfig.backdrop || 'text/html'), atomOverlay);
});






jQuery(document).ready(function($){


  var editors = new Array(),

      widget_controls = function(widget){

        // template selector (using links, looks nicer than select inputs)
        $('.template-selector a.select', widget).click(function(){
          var button = $(this),
              selector = $(this).parents('.template-selector'),
              selected = $(this).attr('rel');

          $('input.select', selector).val(selected).change();
          $('a', selector).removeClass('active');
          button.addClass('active');
          return false;
        });

        // hidden input field (used for creating the select-links above)
        // not to be confused with input[type=select]
        $('.template-selector input.select', widget).change(function(){
          var data = $(this).val(),
              selector = $(this).parents('.template-selector');

          $('a.select[rel="' + data + '"]', selector).addClass('active');

          if(data != 'template'){
            $('.user-template', widget).animate({
              opacity: 'hide',
              height: 'hide',
              marginTop: 'hide',
              marginBottom: 'hide',
              paddingTop: 'hide',
              paddingBottom: 'hide'
            }, 150);
          }else{
            $('.user-template', widget).animate({
              opacity: 'show',
              height: 'show',
              marginTop: 'show',
              marginBottom: 'show',
              paddingTop: 'show',
              paddingBottom: 'show'
            }, 150);

            // refresh codemirror editors, if any
            for(var i = 0; i < editors.length; i++)
              editors[i].refresh();

          }

        }).change();

        // visibility options
        $('input.button-visibility', widget).click(function(e){

          e.preventDefault();

          var control      = $(this),
              status       = $('input.visibility', widget),
              options      = $('.visibility-options', widget),
              widget_id    = status.data('widget'),
              nonce        = status.data('nonce');

          if(parseInt(status.val()) !== 1){

            status.val(1);

            // only make the ajax request if we didn't do it once already (we're keeping contents of the previous one)
            if($('input', options).length > 0){
              options.show();
              control.val(atom_config.label_visibility_hide);

            }else{
              $.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                  action: 'widget_visibility_options_fields',
                  widget_id: widget_id,
                  _ajax_nonce: nonce
                },
                beforeSend: function(){
                  control.val(atom_config.label_loading).attr('disabled','disabled');
                },
                error: function(err){
                  alert(err);
                },
                success: function(data){
                  options.append(data);
                  control.val(atom_config.label_visibility_hide).removeAttr('disabled');
                }
              });
            }

          }else{
            status.val(0);
            options.hide();
            control.val(atom_config.label_visibility_show);

          }

          return false;
        });

      },


      atom_interface = function(block){

        // update check link
        $('#update-check').click(function(event){
          event.preventDefault();
          $.ajax({
            type: 'GET',
            url: ajaxurl,
            context: this,
            data: {
              action: 'force_update_check',
              _ajax_nonce: $(this).data('nonce')
            },
            beforeSend: function(){
              $(this).text(atom_config.label_checking);
            },
            success: function(data){
              $(this).replaceWith(data);
            }
          });
        });

        $('textarea.resizable:not(.processed)', block).Resizer();

        // code editors (codemirror)
        $('textarea.code.editor', block).each(function(i, el){
          editors.push(CodeMirror.fromTextArea(document.getElementById($(this).attr('id')), {
            lineNumbers: true,
            matchBrackets: true,
            mode: $(this).data('mode'),
            onChange: function(inst){
              inst.save();
            }
         }));
        });

        // form dependencies (disable/enable them based on the rules attribute check)
        block.FormDependencies();

        // fix for widget form toggle glitch
        $('.widget-title-action', block.parents('.widget')).click(function(){
          setTimeout(function(){
            // refresh codemirror editors, if any
            for(var i = 0; i < editors.length; i++)
              editors[i].refresh();

            // twice:(
            for(var i = 0; i < editors.length; i++)
              editors[i].refresh();

          }, 10);

        });

        // (un)select-all
        $('.toggle-select-all', block).toggle(function() {
          $(this).select();
        }, function() {
          $(this).unselect();
        });


        // latest news rss
        $('.rss-meta-box', block).each(function(){
          $.ajax({
            type: 'GET',
            url: ajaxurl,
            data: {
              action: 'rss_meta_box',
              feed: $(this).data('feed'),
              items: $(this).data('items'),
              _ajax_nonce: $(this).data('nonce')
            },
            context: this,
            success: function(data){
              $(this).html(data);
              $('ul', this).animate({ opacity: 'show', height: 'show' }, 200).show();
            }
          });
        });

        // color pickers
        $('.color-selector', block).miniColors({
          change: function(hex, rgb){
            $(this).change();
          }
        });

        // image upload buttons -- old stuff @todo: use wp swf uploader
        $('a.upload', block).each(function(){
          var button = $(this),
              button_id = $(this).attr('id'),
              nonce = $(this).data('nonce'),
              option_name = $(this).parents('div').find('input:hidden').attr('name');

          new AjaxUpload(button_id, {
            action: ajaxurl,
            name: option_name, // file upload name
            data: { // Additional data to send
              action: 'process_upload',
              _ajax_nonce: nonce,
              option: option_name
            },
            autoSubmit: true,
            responseType: 'json',   // @note: ajaxUpload doesn't handle HTML nicely, so we have to pass the image source only, and handle HTML ourselves
            onChange: function(file, extension){},
            onSubmit: function(file, extension){
              button.text(atom_config.label_uploading);
              this.disable(); // If you want to allow uploading only 1 file at time, you can disable upload button
              interval = window.setInterval(function(){
                var text = button.text();
                if(text.length < 13)
                  button.text(text + '.');
                else
                  button.text(atom_config.label_uploading);
              }, 200);
            },
            onComplete: function(file, response){
              window.clearInterval(interval);
              this.enable(); // enable upload button

              // we have a error
              if(response.error != ''){
                $('div.error.upload').remove(); // remove the old error messages, if they exists
                $('#theme-settings-form').prepend('<div class="error upload"><p>' + response.error + '</p></div>');
                button.text(atom_config.label_try_another);
                $('html').animate({scrollTop:0}, 333);

              }else{
                $('div.error.upload').remove();

                $('img', button.parent()).animate({ opacity: 0, top: -100 }, 200, function(){
                  $(this).attr('src', response.url).animate({ opacity: 1, top: 0 }, 200);
                });

                $('a.reset_upload', button.parent()).fadeIn(150);
                button.text(atom_config.label_change_image);
                $('input[name="' + option_name + '"]').val(response.url).trigger('change');

              }
            }
          });
        });

        // remove uploaded image button
        $('a.reset_upload', block).click(function(){
          var button = $(this),
              button_id = $(this).attr('id'),
              nonce = $(this).data('nonce'),
              option_name = $(this).parents('div').find('input:hidden').attr('name');

          $('#image-' + option_name).animate({ opacity: 0, top: -100 }, 200);
          button.fadeOut(150);
          $('a.upload', button.parent()).text(atom_config.label_upload_image);
          $('input[name="' + option_name + '"]').val('').trigger('change');
          $('div.error.upload').remove();

          return false;
        });


      };


  $(document).bind('atom_ready', function(){

    // set up generic atom controls
    atom_interface($('.atom-block'));

    // set up our widget controls on the currently active/inactive widgets
    widget_controls($('#widgets-right .widget, #wp_inactive_widgets .widget'));

    // style splitter widget
    $("div[id*='atom-splitter-']").each(function(){
      $(this).addClass('atom-widget-splitter').find('.widget-control-actions .alignright').remove();
    })

    // cool javascript error handler for the design panel -- need to add this somehow as a debug message for the front-end too ;)
    $('#atom-design-panel-status').each(function(){

      var status = $(this);

      // check after 10 seconds (maybe we should extend this?)
      setTimeout(function(){
        if($('#atom-design-panel').is(':visible')){
          status.remove();

        }else{
          status.addClass('error').html('<p>' + atom_config.label_design_panel_error + '</p>');

        }
      }, 10 * 1000);

    });

    // set up widget controls on widgets that are going to be dropped around
    // this also fixes WP's widget-save bug -- 2nd attempt, without livequery (though the previous one was working too)
    $('#widgets-right').ajaxComplete(function(event, XMLHttpRequest, ajaxOptions){

      // determine which ajax request is this (we're after "save-widget")
      var request = {}, pairs = ajaxOptions.data.split('&'), i, split;

      for(i in pairs){
        split = pairs[i].split('=');
        request[decodeURIComponent(split[0])] = decodeURIComponent(split[1]);
      }

      // only proceed if this was a widget-save request
      if(request.action && (request.action === 'save-widget')){

        // locate the widget block
        var widget = $('input.widget-id[value="' + request['widget-id'] + '"]').parents('.widget');

        // trigger manual save, if this was the save request and if we didn't get the form html response
        if(!XMLHttpRequest.responseText){
          wpWidgets.save(widget, 0, 1, 0);

        // we got an response, so we hook our controls on the new elements
        }else{
          widget_controls(widget);
          atom_interface(widget);

        }

      }

    });

  }).trigger('atom_ready');


  // featured posts handler
  $('a.feature').click(function(event){
    event.preventDefault();

    $.ajax({
      url: ajaxurl,
      type: 'GET',
      context: this,
      data: ({
        action: 'process_featured',
        id: $(this).data('post'),
        what: $(this).data('type'),
        isOn: $(this).hasClass('on') ? 1 : 0
      }),
      beforeSend: function() {
        $(this).removeClass('on off').addClass('loading');
      },

      error: function(request){
        $(this).removeClass('loading').addClass('error');
      },

      success: function(data){
        $(this).removeClass('loading').addClass(data);
      }

    });
  });


  /*/ simple ajax tabbed interface for the theme settings tabs
  $('#theme-settings .atom-tabs .nav a').click(function(event){

    var tabs = $(this).parents('.atom-tabs'),
        target = $(this).attr('href'),
        content = $('.tab-content', tabs);

    event.preventDefault();

    target = decodeURI(RegExp('section=(.+?)(&|$)').exec(target)[1]) || target.split('#')[1];

    $('.nav li', tabs).removeClass('active');

    $(this).parent('li').addClass('active');

    $.ajax({
      type: 'GET',
      url: ajaxurl,
      context: this,

      data: {
        action: 'get_tab',
        section: target,
        _ajax_nonce: tabs.data('nonce')
      },

      beforeSend: function() {
        content.addClass('loading');
      },

      // normal page loading on error
      error: function(){
        window.location.href = $(this).attr('href');
      },

      success: function(data){
        content.removeClass('loading').html(data);
        atom_interface(content);
        $.cookie(atom_config.id + '-settings' , target, { path: '/' });
      }
    });

    return false;
  });

  // override active tab, if a tab ID is present in the hash
  $("#theme-settings .atom-tabs .nav a").each(function(){
    if(window.location.hash == $(this).attr('href'))
      $(this).trigger('click');
  });
  /*/


  // live theme preview
  pm.bind('themepreview-load', function(){

    // make design preview iframe resizable, and remember user-set height
    $('#themepreview').Resizer({
      onEndDrag: function(iframe){
        $.cookie(iframe.attr('id') + '-height' , iframe.height(), { path: '/' });
      }
    });

    // live preview: background color
    $('input[name="background_color"]').change(function(){
      pm({
        target: window.frames['themepreview'],
        type: 'background_color', // $(this).attr('name')
        data: {
          color: $(this).val(),
          selector: $(this).data('selector')
        }
      });
      $('input[name="background_image"]').change();
    });

    // live preview: logo
    $('input[name="logo"]').change(function(){
      pm({
        target: window.frames['themepreview'],
        type: 'logo',
        data: {
          url: $(this).val() || 'remove',
          title: $(this).data('title')
        }
      });
    });

    // live preview: background-image
    $('input[name="background_image"]').change(function(){
      pm({
        target: window.frames['themepreview'],
        type: 'background_image',
        data: {
          url: $(this).val() || 'remove',
          selector: $(this).data('selector')
        }
      });
    });

    // live preview: favicon
    $('input[name="favicon"]').change(function(){
      pm({
        target: window.frames['themepreview'],
        type: 'favicon',
        data: $(this).val() || 'remove'
      });
    });

    // show design panel
    $('#atom-design-panel').animate({
      opacity: 'show',
      height: 'show',
      marginTop: 'show',
      marginBottom: 'show',
      paddingTop: 'show',
      paddingBottom: 'show'
    }, 333, function(){

      $(document).trigger('themepreview-loaded');
    });

    // remove design panel error message,
    // which in some cases might be there because the iframe took more than 10 seconds to load...
    $('#atom-design-panel-status').remove();

  });

});