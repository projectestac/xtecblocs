/**
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2008-2015 AddThis, LLC                                     |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 2 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program; if not, write to the Free Software              |
 * | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
 * +--------------------------------------------------------------------------+
 */

 (function(jQuery, window, document, undefined) {

  var aboveshareNamespace = window.addthisnamespaces && window.addthisnamespaces['aboveshare'] ? addthisnamespaces['aboveshare']: 'addthis-share-above';
  var belowshareNamespace = window.addthisnamespaces && window.addthisnamespaces['belowshare'] ? addthisnamespaces['belowshare']: 'addthis-share-below';

  /* Event Tracking */
  function trackPageView(p) {
    if (typeof gaPageTracker != "undefined") gaPageTracker._trackPageview(p);
  }

  // jQuery ready event
  jQuery(function() {

    jQuery('.above-smart-sharing-container .restore-default-options').hide();
    jQuery('.below-smart-sharing-container .restore-default-options').hide();
    jQuery('#below').tooltip({ position: { my: "left+15 center", at: "right center" } });
    jQuery('.sortable .disabled').tooltip({ position: { my: "left+15 center", at: "right center" } });
    jQuery('.sortable .close').tooltip({ position: { my: "left+10 center", at: "right center" } });
    setTimeout(function() {

      window.customServicesAPI.events().fetchServices();

    }, 0);

  });

  // API for the custom service UI
  window.customServicesAPI = {

    'loadDeferred': jQuery.Deferred(),

    'scriptIncluded': false,

    'sorted': false,

    'servicesEndpoint': 'https://cache.addthiscdn.com/services/v1/sharing.en.ssl.jsonp?jsonpcallback=loadServices',

    'loadJS': function(file) {

        var self = this,
          script = document.createElement('script');

        script.src = file;

        document.body.appendChild(script);

        return self.loadDeferred;

    },

    // defaults button list
    'defaults': [
      'facebook_like',
      'tweet',
      /*'email',*/
      'pinterest_pinit',
      'google_plusone',
      /*'linkedin_counter',*/
      'compact'
    ],

    'totalServices': [],

    'thirdPartyButtons': {

      // Exclude list that will exclude certain services from showing up
      'exclude': {
        'horizontal': [
          'stumbleupon_badge'
        ],
        'vertical': [
          'pinterest_pinit',
          'hyves_respect',
          'stumbleupon_badge'
        ]
      },

      'services': function() {

        var self = this;

        return [

          {
            'service': 'facebook_like',
            'name': 'Facebook',
            'linkedService': 'facebook',
            'icon': 'facebook',
            'attrs': {
              'horizontal': 'fb:like:layout="button_count"',
              'vertical': 'fb:like:layout="box_count"'
            }
          },

          {
            'service': 'tweet',
            'name': 'Twitter',
            'linkedService': 'twitter',
            'icon': 'twitter',
            'attrs': {
              'horizontal': '',
              'vertical': 'tw:count="vertical"'
            }
          },

          {
            'service': 'pinterest_pinit',
            'name': 'Pinterest',
            'linkedService': 'pinterest_share',
            'icon': 'pinterest_share',
            'attrs': {
              'horizontal': '',
              'vertical': ''
            }
          },

          {
            'service': 'google_plusone',
            'name': 'Google +1',
            'linkedService': 'google_plusone_share',
            'icon': 'google_plusone',
            'attrs': {
              'horizontal': 'g:plusone:size="medium"',
              'vertical': 'g:plusone:size="tall"'
            }
          },

          {
            'service': 'hyves_respect',
            'name': 'Hyves',
            'linkedService': 'hyves',
            'icon': 'hyves',
            'attrs': {
              'horizontal': '',
              'vertical': ''
            }
          },

          {
            'service': 'linkedin_counter',
            'name': 'LinkedIn',
            'linkedService': 'linkedin',
            'icon': 'linkedin',
            'attrs': {
              'horizontal': '',
              'vertical': 'li:counter="top"'
            }
          },

          {
            'service': 'stumbleupon_badge',
            'name': 'Stumbleupon',
            'linkedService': 'stumbleupon',
            'icon': 'stumbleupon',
            'attrs': {
              'horizontal': '',
              'vertical': ''
            }
          },

          {
            'service': 'compact',
            'name': 'More',
            'linkedService': 'compact',
            'icon': 'compact',
            'attrs': {
              'horizontal': '',
              'vertical': ''
            }
          }

       ];

      }

    },

    'addthisButtons': {

      // Exclude list that will exclude certain services from showing up
      'exclude': [
        'facebook_like',
        'pinterest'
      ],

      // All AddThis supported services get pulled in dynamically from a jsonp endpoint
      'services': []

    },

    'fetchServices': function() {

      var self = this,
        def = jQuery.Deferred();

      if(self.scriptIncluded) {
        def.resolve(self.addthisButtons.services);
        return def;
      }

      else {

        self.loadJS(self.servicesEndpoint).done(function(services) {

          self.scriptIncluded = true;

          def.resolve(services);

        });

      }

      return def;

    },

    'abovepopulateSharingServices': function(restoreDefaults, pageload) {

        var self = this,
          def = jQuery.Deferred(),
          defaults,
          services,
          currentType,
          addthisMappedDefaults,
          thirdPartyMappedDefaults,
          addthisServices,
          thirdPartyServices,
          abovesharingSortable = jQuery('.above-smart-sharing-container .sharing-buttons .sortable'),
          aboveselectedSortable = jQuery('.above-smart-sharing-container .selected-services .sortable');

        self.fetchServices().done(function(services) {

          if(!services.length) {

            def.resolve();

          }

          else {

            self.getaboveSavedOrder(function(obj) {

              defaults = restoreDefaults ? self.defaults: obj.rememberedDefaults;

              abovecurrentType = jQuery('input[name="addthis_settings[above]"]:checked');

              if(!abovecurrentType.length) {
                abovecurrentType = jQuery('input[name="addthis_settings[above]"]:visible').first();
              }

              if(abovecurrentType.length) {

                if(abovecurrentType.attr('id') == 'large_toolbox_above') {
                  style = "horizontal";
                  abovecurrentType = "addthisButtons";
                }
                else if(abovecurrentType.attr('id') == 'fb_tw_p1_sc_above') {
                  style = "horizontal";
                  abovecurrentType = "thirdPartyButtons";
                }
                else if(abovecurrentType.attr('id') == 'small_toolbox_above') {
                  style = "horizontal";
                  abovecurrentType = "addthisButtons";
                }
                else if(abovecurrentType.attr('id') == 'button_above') {
                  style = "";
                  abovecurrentType = "image";
                }



                addthisMappedDefaults = _.map(defaults, function(value) {
                  var service = _.where(self.thirdPartyButtons.services(), { 'service': value });
                  if(service.length) {
                    return service[0].linkedService;
                  }
                  else {
                    return value;
                  }
                });
                thirdPartyMappedDefaults = _.map(defaults, function(value) {
                  var service = _.where(self.thirdPartyButtons.services(), { 'linkedService': value });
                  if(service.length) {
                    return service[0].service;
                  }
                  else {
                    return value;
                  }
                });

                defaults = abovecurrentType === 'addthisButtons' ? addthisMappedDefaults: thirdPartyMappedDefaults;

                addthisServices = self.sort({ defaults: addthisMappedDefaults, services: self.totalServices });

                thirdPartyServices = self.sort({ defaults: thirdPartyMappedDefaults, services: self.totalServices });

                if(abovecurrentType === 'addthisButtons') {
                  self.populateList({ elem: abovesharingSortable, services: addthisServices, exclude: self.addthisButtons.exclude, defaults: addthisMappedDefaults, type: 'sharing-buttons', buttonType: 'addthisButtons' });

                  self.populateList({ elem: aboveselectedSortable, services: addthisServices, exclude: self.addthisButtons.exclude, defaults: addthisMappedDefaults, type: 'selected-services', buttonType: 'addthisButtons' });

                }

                if(abovecurrentType === 'thirdPartyButtons' && style === 'horizontal') {
                  self.populateList({ elem: abovesharingSortable, services: thirdPartyServices, exclude: self.thirdPartyButtons.exclude['horizontal'], defaults: thirdPartyMappedDefaults, type: 'sharing-buttons', style: 'horizontal', buttonType: 'thirdPartyButtons' });

                  self.populateList({ elem: aboveselectedSortable, services: thirdPartyServices, exclude: self.thirdPartyButtons.exclude['horizontal'], defaults: thirdPartyMappedDefaults, type: 'selected-services', style: 'horizontal', buttonType: 'thirdPartyButtons' });

                }

                jQuery('body').trigger('populatedList');
                def.resolve();

              }

              else {
                jQuery('body').trigger('populatedList');
                def.resolve();
              }
            });

          }

        });

        return def;

      },

      'belowpopulateSharingServices': function(restoreDefaults, pageload) {

          var self = this,
            def = jQuery.Deferred(),
            defaults,
            services,
            currentType,
            addthisMappedDefaults,
            thirdPartyMappedDefaults,
            addthisServices,
            thirdPartyServices,
            belowsharingSortable = jQuery('.below-smart-sharing-container .sharing-buttons .sortable'),
            belowselectedSortable = jQuery('.below-smart-sharing-container .selected-services .sortable');

          self.fetchServices().done(function(services) {

            if(!services.length) {

              def.resolve();

            }

            else {

              self.getbelowSavedOrder(function(obj) {

                defaults = restoreDefaults ? self.defaults: obj.rememberedDefaults;


                belowcurrentType = jQuery('input[name="addthis_settings[below]"]:checked');

                if(!belowcurrentType.length) {
                  belowcurrentType = jQuery('input[name="addthis_settings[above]"]:visible').first();
                }

                if(belowcurrentType.length) {

                  if(belowcurrentType.attr('id') == 'large_toolbox_below') {
                    style = "horizontal";
                    belowcurrentType = "addthisButtons";
                  }
                  else if(belowcurrentType.attr('id') == 'fb_tw_p1_sc_below') {
                    style = "horizontal";
                    belowcurrentType = "thirdPartyButtons";
                  }
                  else if(belowcurrentType.attr('id') == 'small_toolbox_below') {
                    style = "horizontal";
                    belowcurrentType = "addthisButtons";
                  }
                  else if(belowcurrentType.attr('id') == 'button_below') {
                    style = "";
                    belowcurrentType = "image";
                  }

                  addthisMappedDefaults = _.map(defaults, function(value) {
                        var service = _.where(self.thirdPartyButtons.services(), { 'service': value });
                        if(service.length) {
                          return service[0].linkedService;
                        }
                        else {
                          return value;
                        }
                      });
                      thirdPartyMappedDefaults = _.map(defaults, function(value) {
                        var service = _.where(self.thirdPartyButtons.services(), { 'linkedService': value });
                        if(service.length) {
                          return service[0].service;
                        }
                        else {
                          return value;
                        }
                      });

                      defaults = belowcurrentType === 'addthisButtons' ? addthisMappedDefaults: thirdPartyMappedDefaults;

                      addthisServices = self.sort({ defaults: addthisMappedDefaults, services: self.totalServices });

                      thirdPartyServices = self.sort({ defaults: thirdPartyMappedDefaults, services: self.totalServices });

                      if(belowcurrentType === 'addthisButtons') {
                        self.populateList({ elem: belowsharingSortable, services: addthisServices, exclude: self.addthisButtons.exclude, defaults: addthisMappedDefaults, type: 'sharing-buttons', buttonType: 'addthisButtons' });

                          self.populateList({ elem: belowselectedSortable, services: addthisServices, exclude: self.addthisButtons.exclude, defaults: addthisMappedDefaults, type: 'selected-services', buttonType: 'addthisButtons' });
                      }

                      if(belowcurrentType === 'thirdPartyButtons' && style === 'horizontal') {
                          self.populateList({ elem: belowsharingSortable, services: thirdPartyServices, exclude: self.thirdPartyButtons.exclude['horizontal'], defaults: thirdPartyMappedDefaults, type: 'sharing-buttons', style: 'horizontal', buttonType: 'thirdPartyButtons' });

                          self.populateList({ elem: belowselectedSortable, services: thirdPartyServices, exclude: self.thirdPartyButtons.exclude['horizontal'], defaults: thirdPartyMappedDefaults, type: 'selected-services', style: 'horizontal', buttonType: 'thirdPartyButtons' });

                        }

                }
                else {
                    jQuery('body').trigger('populatedList');
                    def.resolve();
                  }

              });

            }

          });

          return def;

        },

    'sort': function(obj) {
      var self = this,
        copiedItem,
        whereInArray,
        currentService,
        defaults = obj.defaults,
        services = jQuery.merge([], obj.services);

      // Sorts the addthis button list in the correct order
      jQuery.each(services, function(iterator, value) {
        currentService = value.service;
        whereInArray = jQuery.inArray(currentService, defaults);
        if(whereInArray !== -1) {
          copiedItem = services[whereInArray];
          services[whereInArray] = value;
          services[iterator] = copiedItem;
        }

      });

      return services;

    },

    'populateList': function(obj) {

      var self = this,
        list = obj.elem,
        listHtml = '',
        service,
        type = obj['type'],
        services = obj.services,
        iconService = '',
        defaults = obj.defaults,
        attrs,
        name,
        style = obj['style'],
        duplicates = [],
        buttonType = obj.buttonType;
        buttonServices = buttonType === 'addthisButtons' ? self.addthisButtons.services: self.thirdPartyButtons.services(),
        excludeList = obj.exclude,
        thirdPartyDisabled = (function() {
          var arr, disabledArr = [], serviceObj;
          if(buttonType === 'addthisButtons' || type === 'selected-services') {
            return disabledArr;
          }
          else {
            arr = _.filter(self.thirdPartyButtons.exclude[style], function(value) {
              return jQuery.inArray(value, defaults) === -1;
            }), disabledArr = [], serviceObj = {};
            _.each(arr, function(value) {
              serviceObj = _.where(self.thirdPartyButtons.services(), { service: value });
              if(serviceObj.length) {
                disabledArr.push(serviceObj[0]);
              }
            });
          }
          return disabledArr;
        }()),
        disabledServices = (buttonType === 'addthisButtons' || type === 'selected-services') ? []: jQuery.merge(jQuery.merge([], self.disabledServices), thirdPartyDisabled),
        selectedDefaults = [],
        isDuplicate = false,
        isDefault = false,
        isExcluded = false,
        containsService = false;

        for(var key in services) {

          if(services.hasOwnProperty(key)) {

            value = services[key];
            service = (value.service) || key;
            name = value.name;
            iconService = value.icon;
            isDuplicate = jQuery.inArray(service, duplicates) !== -1,
            isDefault = jQuery.inArray(service, defaults) !== -1,
            isExcluded = jQuery.inArray(service, excludeList) !== -1;
            containsService = _.where(buttonServices , { 'service': service }).length;

            if(!isDuplicate) {

              if(type === "selected-services") {

                if(defaults && isDefault) {

                  selectedDefaults.push(service);

                  if(!containsService || isExcluded) {
                    listHtml += "<li class='disabled service' data-service='" + service + "' title='The " + name + " button is not supported in this style'><span class='at300bs at15nc at15t_" + iconService + " at16t_" + iconService + "' style='display:inline-block;padding-right:10px;vertical-align:middle;margin-left:10px;'></span><span class='service-name'>" + name + "</span><button type='button' title='Remove' class='close'>x</button></li>";
                  }

                  else {
                    listHtml += "<li class='enabled service' data-service='" + service + "'><span class='at300bs at15nc at15t_" + iconService + " at16t_" + iconService + "' style='display:inline-block;padding-right:10px;vertical-align:middle;margin-left:10px;'></span><span class='service-name'>" + name + "</span><button type='button' title='Remove' class='close'>x</button></li>";
                  }

                }

              }

              else {

                if(defaults && !isDefault && !isExcluded) {

                  if(containsService) {

                    listHtml += "<li class='enabled service' data-service='" + service + "'><span class='at300bs at15nc at15t_" + iconService + " at16t_" + iconService + "' style='display:inline-block;padding-right:10px;vertical-align:middle;margin-left:10px;'></span><span class='service-name'>" + name + "</span><button type='button' title='Remove' class='close'>x</button></li>";
                  }
                }

              }

              duplicates.push(service);

            }

          }

        }

        if(disabledServices.length) {
          jQuery.each(disabledServices, function(iterator, disabledService) {
            var service = disabledService.service,
              iconService = disabledService.icon,
              name = disabledService['name'];
            listHtml += "<li class='disabled service' data-service='" + service + "' title='The " + name + " button is not supported in this style'><span class='at300bs at15nc at15t_" + iconService + " at16t_" + iconService + "' style='display:inline-block;padding-right:10px;vertical-align:middle;margin-left:10px;'></span><span class='service-name'>" + name + "</span><button type='button' title='Remove' class='close'>Ã—</button></li>";
          });
        }

        if(!defaults.length && type === 'selected-services') {
          listHtml = '<p class="add-buttons-msg">Add buttons by dragging them in this box.</p>';
          list.css('border-style', 'dashed');
        }
        else if(defaults.length && type === 'selected-services') {
          list.css('border-style', 'solid');
        }

        list.html(listHtml).data('selectedDefaults', selectedDefaults);

        return self;

    },

    'getaboveSavedOrder': function(callback) {

        var self = this;

        if(window.commonMethods && window.commonMethods.localStorageSettings) {

          commonMethods.localStorageSettings({ namespace: aboveshareNamespace, method: 'get' }, function(obj) {

            callback.call(self, obj || { rememberedDefaults: self.defaults });

          });

        }

        else {

          callback.call(self, {});

        }

        return self;

      },

      'getbelowSavedOrder': function(callback) {

          var self = this;

          if(window.commonMethods && window.commonMethods.localStorageSettings) {

            commonMethods.localStorageSettings({ namespace: belowshareNamespace, method: 'get' }, function(obj) {

              callback.call(self, obj || { rememberedDefaults: self.defaults });

            });

          }

          else {

            callback.call(self, {});

          }

          return self;

        },

    'abovesaveOrder': function(obj) {

      var self = this,
        defaults = [],
        dynamicObj = {},
        size = obj['size'],
        type = obj['type'],
        style = obj['style'],
        updatedItem = obj['item'],

        currentService,
        elem = obj.elem,
        serviceItems = elem.find('li'),
        enabled = elem.find('li.enabled'),
        disabled = elem.find('li.disabled'),
        enabledDefaults = [],
        removed = true;

      serviceItems.each(function(iterator) {
        currentService = jQuery(this).attr('data-service');
        defaults.push(currentService);
        if(currentService === updatedItem) {
          removed = false;
        }
        if(jQuery(this).hasClass('enabled')) {
          enabledDefaults.push(currentService);
        }
      });

      if(updatedItem) {
        if(removed) {
          trackPageView('/tracker/gtc/' + (window.page || '') + '/event/removed_' + updatedItem);
        }
        else {
          trackPageView('/tracker/gtc/' + (window.page || '') + '/event/select_add_' + updatedItem);
        }
      }

      if(window.commonMethods && window.commonMethods.localStorageSettings) {
        dynamicObj['rememberedDefaults'] = defaults;
        commonMethods.localStorageSettings({ namespace: aboveshareNamespace, method: 'set', data: dynamicObj });
        setTimeout(function() {

          self.aboveupdatePreview({ size: size, services: enabledDefaults, type: type, style: style, location: location });

          }, 1000);

      }

      return self;

    },

    'belowsaveOrder': function(obj) {

      var self = this,
        defaults = [],
        dynamicObj = {},
        size = obj['size'],
        type = obj['type'],
        style = obj['style'],
        updatedItem = obj['item'],

        currentService,
        elem = obj.elem,
        serviceItems = elem.find('li'),
        enabled = elem.find('li.enabled'),
        disabled = elem.find('li.disabled'),
        enabledDefaults = [],
        removed = true;

      serviceItems.each(function(iterator) {
        currentService = jQuery(this).attr('data-service');
        defaults.push(currentService);
        if(currentService === updatedItem) {
          removed = false;
        }
        if(jQuery(this).hasClass('enabled')) {
          enabledDefaults.push(currentService);
        }
      });

      if(updatedItem) {
        if(removed) {
          trackPageView('/tracker/gtc/' + (window.page || '') + '/event/removed_' + updatedItem);
        }
        else {
          trackPageView('/tracker/gtc/' + (window.page || '') + '/event/select_add_' + updatedItem);
        }
      }

      if(window.commonMethods && window.commonMethods.localStorageSettings) {
        dynamicObj['rememberedDefaults'] = defaults;
        commonMethods.localStorageSettings({ namespace: belowshareNamespace, method: 'set', data: dynamicObj });
        setTimeout(function() {

          self.belowupdatePreview({ size: size, services: enabledDefaults, type: type, style: style, location: location });

          }, 1000);

      }

      return self;

    },

    'aboveupdatePreview': function(obj) {

      var self = this,
        size = obj['size'],
        style = obj['style'],
        services = obj.services,
        type = obj['type'],
        buttons = '';
      if (size == "large") {
        buttons += '<div class="addthis_toolbox addthis_default_style addthis_32x32_style">';
        jQuery('.above-smart-sharing-container .selected-services .ui-sortable').each(function(){
              jQuery(this).find('li').each(function(){
                if(jQuery(this).hasClass('enabled')) {
                  buttons += '<span class="at300bs at15nc at15t_'+jQuery(this).attr('data-service')+' at16t_'+jQuery(this).attr('data-service')+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
                  if(jQuery(this).attr('data-service') == 'compact') {
                    buttons += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block;float: left;" href="#" tabindex="0"></a>';
                  }
                }
              });
        });
        buttons += '</div>';
      }
      else if (size == "small") {
        jQuery('.above-smart-sharing-container .selected-services .ui-sortable').each(function(){
              jQuery(this).find('li').each(function(){
                if(jQuery(this).hasClass('enabled')) {
                  buttons += '<span class="at300bs at15nc at15t_'+jQuery(this).attr('data-service')+' at16t_'+jQuery(this).attr('data-service')+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
                  if(jQuery(this).attr('data-service') == 'compact') {
                    buttons += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block;float: left;" href="#" tabindex="0"></a>';
                  }
                }
              });
        });
      }
      else {
        jQuery('.above-smart-sharing-container .selected-services .ui-sortable').each(function(){
              jQuery(this).find('li').each(function(){
                if(jQuery(this).hasClass('enabled')) {
                  if(jQuery(this).attr('data-service') == 'compact') {
                  buttons += '<img src="'+addthis_params.img_base+'addthis_pill_style.png">';
                }
                else {
                  buttons += '<img src="'+addthis_params.img_base+jQuery(this).attr('data-service')+'.png">';
                }
                }
              });
        });
      }

    },


    'belowupdatePreview': function(obj) {

      var self = this,
        size = obj['size'],
        style = obj['style'],
        services = obj.services,
        type = obj['type'],
        buttons = '';
      if (size == "large") {
        buttons += '<div class="addthis_toolbox addthis_default_style addthis_32x32_style">';
        jQuery('.below-smart-sharing-container .selected-services .ui-sortable').each(function(){
              jQuery(this).find('li').each(function(){
                if(jQuery(this).hasClass('enabled')) {
                  buttons += '<span class="at300bs at15nc at15t_'+jQuery(this).attr('data-service')+' at16t_'+jQuery(this).attr('data-service')+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
                  if(jQuery(this).attr('data-service') == 'compact') {
                    buttons += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block; float: left;" href="#" tabindex="0"></a>';
                  }
                }
              });
        });
        buttons += '</div>';
      }
      else if (size == "small") {
        jQuery('.below-smart-sharing-container .selected-services .ui-sortable').each(function(){
              jQuery(this).find('li').each(function(){
                if(jQuery(this).hasClass('enabled')) {
                  buttons += '<span class="at300bs at15nc at15t_'+jQuery(this).attr('data-service')+' at16t_'+jQuery(this).attr('data-service')+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
                  if(jQuery(this).attr('data-service') == 'compact') {
                    buttons += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block; float: left;" href="#" tabindex="0"></a>';
                  }
                }
              });
        });
      }
      else {
        jQuery('.below-smart-sharing-container .selected-services .ui-sortable').each(function(){
              jQuery(this).find('li').each(function(){
                if(jQuery(this).hasClass('enabled')) {
                  if(jQuery(this).attr('data-service') == 'compact') {
                  buttons += '<img src="'+addthis_params.img_base+'addthis_pill_style.png">';
                }
                else {
                  buttons += '<img src="'+addthis_params.img_base+jQuery(this).attr('data-service')+'.png">';
                }
                }
              });
        });
      }
    },

    'events': function() {

      var self = this,
        aboveEnableSmartSharing = jQuery('#above-enable-smart-sharing'),
        aboveDisableSmartSharing = jQuery('#above-disable-smart-sharing'),
        belowEnableSmartSharing = jQuery('#below-enable-smart-sharing'),
        belowDisableSmartSharing = jQuery('#below-disable-smart-sharing'),

        sortableContainer,
        aboveradioInputs = jQuery('input[name="addthis_settings[above]"]'),
        belowradioInputs = jQuery('input[name="addthis_settings[below]"]'),
        abovecurrentRadioInput,
        belowcurrentRadioInput,
        abovecurrentType,
        belowcurrentType,
        abovecurrentStyle,
        belowcurrentStyle,
        excludeList,
        whereInputs = jQuery('input[name=where]'),
        aboveSmartSharingContainer = jQuery('.above-smart-sharing-container'),
        aboveSmartSharingInnerContainer = jQuery('.above-smart-sharing-container .smart-sharing-inner-container'),
        aboveCustomizeButtons = jQuery('.above-smart-sharing-container .customize-buttons'),
        aboveButtons = jQuery('.above-smart-sharing-container .customize-buttons'),
        belowSmartSharingContainer = jQuery('.below-smart-sharing-container'),
        belowSmartSharingInnerContainer = jQuery('.below-smart-sharing-container .smart-sharing-inner-container'),
        belowCustomizeButtons = jQuery('.below-smart-sharing-container .customize-buttons'),
        belowButtons = jQuery('.below-smart-sharing-container .customize-buttons'),
        defaults,
        buttontype,
        buttonsize,
        buttonstyle,
        sortableLists = jQuery('.sortable'),
        sortableListItems = sortableLists.find('li'),
        sortableSelectedListItems = jQuery('.selected-services').find('li'),
        sortableListItemsCloseIcons = sortableSelectedListItems.find('.close'),
        aboveRestoreDefaultOptions = jQuery('.above-smart-sharing-container .restore-default-options'),
        belowRestoreDefaultOptions = jQuery('.below-smart-sharing-container .restore-default-options'),
        aboverestoreToDefault = _.debounce(function() {
          // Updates the personalization UI
          self.abovepopulateSharingServices(true);
            jQuery('.above-smart-sharing-container .selected-services .sortable:visible').trigger('sortupdate');
          commonMethods.localStorageSettings({ method: "remove", namespace: aboveshareNamespace });
        }, 1000, true),
        belowrestoreToDefault = _.debounce(function() {
            // Updates the personalization UI
          self.belowpopulateSharingServices(true);
            jQuery('.below-smart-sharing-container .selected-services .sortable:visible').trigger('sortupdate');
            commonMethods.localStorageSettings({ method: "remove", namespace: belowshareNamespace });
          }, 1000, true),
        aboveEnableCustomization = _.debounce(function() {
          trackPageView('/tracker/gtc/' + (window.page || '') + '/event/enable_customization');
        }, 1000, true),
        belowEnableCustomization = _.debounce(function() {
            trackPageView('/tracker/gtc/' + (window.page || '') + '/event/enable_customization');
          }, 1000, true),
        disableCustomization = _.debounce(function() {
          trackPageView('/tracker/gtc/' + (window.page || '') + '/event/disable_customization');
        }, 1000, true);

      aboveDisableSmartSharing.add(aboveradioInputs).not('#button_above').bind('click', function() {

        abovecurrentRadioInput = jQuery('input[name="addthis_settings[above]"]:checked');
        if(!abovecurrentRadioInput.length) {
          abovecurrentRadioInput = jQuery('input[name="addthis_settings[above]"]').first();
        }

        if(abovecurrentRadioInput.attr('id') == 'large_toolbox_above') {
          abovecurrentStyle = "horizontal";
          abovecurrentType = "addthisButtons";
      }
      else if(abovecurrentRadioInput.attr('id') == 'fb_tw_p1_sc_above') {
        abovecurrentStyle = "horizontal";
        abovecurrentType = "thirdPartyButtons";
      }
      else if(abovecurrentRadioInput.attr('id') == 'small_toolbox_above') {
        abovecurrentStyle = "horizontal";
        abovecurrentType = "addthisButtons";
      }
      else if(abovecurrentRadioInput.attr('id') == 'button_above') {
        abovecurrentStyle = "";
        abovecurrentType = "image";
      }

        if(aboveDisableSmartSharing.is(':checked')) {

          if(abovecurrentType === 'addthisButtons' || abovecurrentType === 'thirdPartyButtons') {
            aboveButtons.show();
          }
          else {
            aboveButtons.hide();
          }


          aboveradioInputs.addClass('disabled-smart-sharing');

          setTimeout(function() {

            // Updates the personalization UI
            self.abovepopulateSharingServices();

            jQuery('.above-smart-sharing-container .selected-services .sortable:visible').trigger('sortupdate');

          }, 0);

          aboveRestoreDefaultOptions.show();

          jQuery('.sharing-buttons-search').val('');

        }

        aboveSmartSharingInnerContainer.show();

      });

      belowDisableSmartSharing.add(belowradioInputs).not('#button_below').bind('click', function() {

        belowcurrentRadioInput = jQuery('input[name="addthis_settings[below]"]:checked');
          if(!belowcurrentRadioInput.length) {
            belowcurrentRadioInput = jQuery('input[name="addthis_settings[below]"]').first();
          }

          if(belowcurrentRadioInput.attr('id') == 'large_toolbox_below') {
            belowcurrentStyle = "horizontal";
            belowcurrentType = "addthisButtons";
        }
        else if(belowcurrentRadioInput.attr('id') == 'fb_tw_p1_sc_below') {
          belowcurrentStyle = "horizontal";
          belowcurrentType = "thirdPartyButtons";
        }
        else if(belowcurrentRadioInput.attr('id') == 'small_toolbox_below') {
          belowcurrentStyle = "horizontal";
          belowcurrentType = "addthisButtons";
        }
        else if(belowcurrentRadioInput.attr('id') == 'button_below') {
          belowcurrentStyle = "";
          belowcurrentType = "image";
        }

          if(belowDisableSmartSharing.is(':checked')) {

            if(belowcurrentType === 'addthisButtons' || belowcurrentType === 'thirdPartyButtons') {
              belowButtons.show();
            }
            else {
              belowButtons.hide();
            }


            belowradioInputs.addClass('disabled-smart-sharing');

            setTimeout(function() {

              // Updates the personalization UI
              self.belowpopulateSharingServices();

              jQuery('.below-smart-sharing-container .selected-services .sortable:visible').trigger('sortupdate');

            }, 0);

            belowRestoreDefaultOptions.show();

            jQuery('.sharing-buttons-search').val('');

          }

          belowSmartSharingInnerContainer.show();

        });

      jQuery('#button_above').click(function() {
        var self = jQuery(this);
        aboveSmartSharingInnerContainer.hide();
      });

      jQuery('#button_below').click(function() {
          var self = jQuery(this);
          belowSmartSharingInnerContainer.hide();
        });

      aboveEnableSmartSharing.bind('click', function() {

        currentRadioInput = jQuery('input[name="addthis_settings[above]"]:checked');

        disableCustomization();

        aboveCustomizeButtons.hide();

        aboveRestoreDefaultOptions.hide();

        aboveradioInputs.removeClass('disabled-smart-sharing');

        currentRadioInput.click();

      });

      belowEnableSmartSharing.bind('click', function() {




          currentRadioInput = jQuery('input[name="addthis_settings[below]"]:checked');

          disableCustomization();

          belowCustomizeButtons.hide();

          belowRestoreDefaultOptions.hide();

          belowradioInputs.removeClass('disabled-smart-sharing');

          currentRadioInput.click();

        });

      aboveDisableSmartSharing.bind('click', function() {

        aboveEnableCustomization();

        aboveCustomizeButtons.show();

      });

      belowDisableSmartSharing.bind('click', function() {

          belowEnableCustomization();

          belowCustomizeButtons.show();

        });

      aboveDisableSmartSharing.one('click', function() {

        setTimeout(function() {

          // Makes the new list sortable
          jQuery('.above-smart-sharing-container .sortable').sortable({

            placeholder: "sortable-placeholder",

            revert: true,

            scroll: false,

            cancel: '.add-buttons-msg, .disabled',

            start: function(ev, obj) {
              if(obj && obj.item) {
                if(obj.item.parent().parent().hasClass('sharing-buttons')) {
                  obj.item.data('cancel', true);
                }
                else {
                  obj.item.removeData('cancel');
                }
              }
            },

            stop: function(ev, obj) {
              if(obj && obj.item) {
                if(obj.item.data('cancel') && obj.item.parent().parent().hasClass('sharing-buttons')) {
                  return false;
                }
                else {
                  obj.item.removeData('cancel');
                }
              }
            }
          }).disableSelection().sortable('option', 'connectWith', '.sortable');

        }, 0);

      });

      belowDisableSmartSharing.one('click', function() {

          setTimeout(function() {

            // Makes the new list sortable
            jQuery('.below-smart-sharing-container .sortable').sortable({

              placeholder: "sortable-placeholder",

              revert: true,

              scroll: false,

              cancel: '.add-buttons-msg, .disabled',

              start: function(ev, obj) {
                if(obj && obj.item) {
                  if(obj.item.parent().parent().hasClass('sharing-buttons')) {
                    obj.item.data('cancel', true);
                  }
                  else {
                    obj.item.removeData('cancel');
                  }
                }
              },

              stop: function(ev, obj) {
                if(obj && obj.item) {
                  if(obj.item.data('cancel') && obj.item.parent().parent().hasClass('sharing-buttons')) {
                    return false;
                  }
                  else {
                    obj.item.removeData('cancel');
                  }
                }
              }
            }).disableSelection().sortable('option', 'connectWith', '.sortable');

          }, 0);

        });

      jQuery('.above_button_set .selected-services .sortable').bind('sortupdate', function(ev, item) {

        if(jQuery.isPlainObject(item)) {
          item = item.item.attr('data-service');
        }

        if(!jQuery(this).find('li').length) {
          jQuery(this).html('<p class="add-buttons-msg">Add buttons by dragging them in this box.</p>');
          jQuery(this).css('border-style', 'dashed');
          jQuery('.add-buttons-msg').show();
        }

        else {
          jQuery(this).css('border-style', 'solid');
        }

        var abovesortableList = jQuery('.above-smart-sharing-container .selected-services .sortable:visible');
        abovecurrentRadioInput = jQuery('input[name="addthis_settings[above]"]:checked');
        if(abovecurrentRadioInput.attr('id') == 'large_toolbox_above') {
          buttonstyle = "horizontal";
          buttontype = "addthisButtons";
          buttonsize = "large";
        }
        else if(abovecurrentRadioInput.attr('id') == 'fb_tw_p1_sc_above') {
          buttonstyle = "horizontal";
          buttontype = "thirdPartyButtons";
          buttonsize = "";
        }
        else if(abovecurrentRadioInput.attr('id') == 'small_toolbox_above') {
          buttonstyle = "horizontal";
          buttontype = "addthisButtons";
          buttonsize = "small";
        }
        else if(abovecurrentRadioInput.attr('id') == 'button_above') {
          buttonstyle = "";
          buttontype = "image";
          buttonsize = "";
        }

        self.abovesaveOrder({ tool: 'above', type: buttontype, elem: abovesortableList, size: buttonsize, style: buttonstyle, item: item || "" });

      });

      jQuery('.below_button_set .selected-services .sortable').bind('sortupdate', function(ev, item) {

          if(jQuery.isPlainObject(item)) {
            item = item.item.attr('data-service');
          }

          if(!jQuery(this).find('li').length) {
            jQuery(this).html('<p class="add-buttons-msg">Add buttons by dragging them in this box.</p>');
            jQuery(this).css('border-style', 'dashed');
            jQuery('.add-buttons-msg').show();
          }

          else {
            jQuery(this).css('border-style', 'solid');
          }

          var belowsortableList = jQuery('.below-smart-sharing-container .selected-services .sortable:visible');
          belowcurrentRadioInput = jQuery('input[name="addthis_settings[below]"]:checked');
          if(belowcurrentRadioInput.attr('id') == 'large_toolbox_below') {
            buttonstyle = "horizontal";
            buttontype = "addthisButtons";
            buttonsize = "large";
          }
          else if(belowcurrentRadioInput.attr('id') == 'fb_tw_p1_sc_below') {
            buttonstyle = "horizontal";
            buttontype = "thirdPartyButtons";
            buttonsize = "";
          }
          else if(belowcurrentRadioInput.attr('id') == 'small_toolbox_below') {
            buttonstyle = "horizontal";
            buttontype = "addthisButtons";
            buttonsize = "small";
          }
          else if(belowcurrentRadioInput.attr('id') == 'button_below') {
            buttonstyle = "";
            buttontype = "image";
            buttonsize = "";
          }

          self.belowsaveOrder({ tool: 'below',  type: buttontype, elem: belowsortableList, size: buttonsize, style: buttonstyle, item: item || "" });

        });

      aboveRestoreDefaultOptions.bind('click', function(ev) {

        ev.preventDefault();

        setTimeout(function() {

          jQuery('.above-smart-sharing-container .sharing-buttons-search').val('');

          aboverestoreToDefault();

        }, 0);

      });

      belowRestoreDefaultOptions.bind('click', function(ev) {

          ev.preventDefault();

          setTimeout(function() {

            jQuery('.below-smart-sharing-container .sharing-buttons-search').val('');

            belowrestoreToDefault();

          }, 0);

        });

      sortableSelectedListItems.live({

        'mouseenter': function() {

          jQuery(this).find('.close').css('display', 'inline-block');

        },

        'mouseleave': function() {

          jQuery(this).find('.close').hide();

        },

        'mouseup': function() {

          jQuery(this).find('.close').hide();

        }

      });

      sortableListItems.live({

        'mouseup': function() {

          if(!jQuery('.selected-services li:visible').length) {


            jQuery('.add-buttons-msg').show();

          }

        },

        'mousedown': function() {

          jQuery('.add-buttons-msg').hide();

          jQuery('.below-smart-sharing-container .horizontal-drag').hide();

          jQuery('.above-smart-sharing-container .horizontal-drag').hide();

        }

      });

      sortableListItemsCloseIcons.live('click', function() {

        var parent = jQuery(this).parent(),
          isDisabled = parent.hasClass('disabled');
        parent.fadeOut().promise().done(function() {

          jQuery('.sharing-buttons .sortable:visible').prepend(parent);
          parent.find('.close').hide().tooltip().tooltip('close');
          parent.fadeIn();
          jQuery('.selected-services .sortable:visible').trigger('sortupdate', parent.attr('data-service'));

        });

      });

      jQuery('.above-smart-sharing-container .sharing-buttons-search').bind('keyup', function(e) {

        var currentVal = jQuery(this).val();

        jQuery('.above-smart-sharing-container .sharing-buttons .sortable').find('li').each(function() {
          if(jQuery(this).text().toLowerCase().search(currentVal.toLowerCase()) === -1) {
            jQuery(this).hide().attr('data-hidden', 'true');
          }
          else {
            jQuery(this).show().removeAttr('data-hidden');
          }
        });

      });

      jQuery('.below-smart-sharing-container .sharing-buttons-search').bind('keyup', function(e) {

          var currentVal = jQuery(this).val();

          jQuery('.below-smart-sharing-container .sharing-buttons .sortable').find('li').each(function() {
            if(jQuery(this).text().toLowerCase().search(currentVal.toLowerCase()) === -1) {
              jQuery(this).hide().attr('data-hidden', 'true');
            }
            else {
              jQuery(this).show().removeAttr('data-hidden');
            }
          });

        });

      jQuery('.sharing-buttons-search').bind('click', function() {
        trackPageView('/tracker/gtc/' + (window.page || '') + '/event/search_clicked');
      });

      jQuery('.above-smart-sharing-container .sortable').bind('mousedown', function() {
        if(jQuery('.above-smart-sharing-container .sharing-buttons-search').is(':focus')) {
          jQuery('.above-smart-sharing-container .sharing-buttons-search').blur();
        }
      });
      jQuery('.below-smart-sharing-container .sortable').bind('mousedown', function() {
          if(jQuery('.below-smart-sharing-container .sharing-buttons-search').is(':focus')) {
            jQuery('.below-smart-sharing-container .sharing-buttons-search').blur();
          }
        });

      jQuery('.above-smart-sharing-container .selected-services .sortable').bind({

        'mouseover': function() {
          if(jQuery(this).find('li.enabled:visible').length > 1) {
            jQuery('.above-smart-sharing-container .horizontal-drag').hide();
            jQuery('.above-smart-sharing-container .vertical-drag').show();
          }
        },
        'mouseout': function() {
          jQuery('.above-smart-sharing-container .vertical-drag').hide();
        }

      });

      jQuery('.below-smart-sharing-container .selected-services .sortable').bind({

          'mouseover': function() {
            if(jQuery(this).find('li.enabled:visible').length > 1) {
              jQuery('.below-smart-sharing-container .horizontal-drag').hide();
              jQuery('.below-smart-sharing-container .vertical-drag').show();
            }
          },
          'mouseout': function() {
            jQuery('.below-smart-sharing-container .vertical-drag').hide();
          }

        });

      jQuery('.above-smart-sharing-container .sharing-buttons .sortable').bind({

        'mouseover': function() {
          if(jQuery(this).find('li.enabled:visible').length) {
            jQuery('.above-smart-sharing-container .vertical-drag').hide();
            jQuery('.above-smart-sharing-container .horizontal-drag').show();
          }
        },
        'mouseout': function() {
          jQuery('.above-smart-sharing-container .horizontal-drag').hide();
        }

      });

      jQuery('.below-smart-sharing-container .sharing-buttons .sortable').bind({

          'mouseover': function() {
            if(jQuery(this).find('li.enabled:visible').length) {
              jQuery('.below-smart-sharing-container .vertical-drag').hide();
              jQuery('.below-smart-sharing-container .horizontal-drag').show();
            }
          },
          'mouseout': function() {
            jQuery('.below-smart-sharing-container .horizontal-drag').hide();
          }

        });

      jQuery('body').bind({

      'populatedList': function() {
          setTimeout(function() {
            jQuery('.sortable .disabled, .sortable .close').tooltip({
              position: {
              my: 'left+15 top',
                at: 'right top',
                collision: 'none',
                tooltipClass: 'custom-tooltip-styling'
              }
            });
            jQuery('.above-smart-sharing-container .sortable .disabled').bind('mouseover', function() {
              jQuery('.above-smart-sharing-container .horizontal-drag, .above-smart-sharing-container .vertical-drag').hide();
            });
            jQuery('.above-smart-sharing-container .sharing-buttons .enabled').bind('mouseenter', function() {
              if(jQuery(this).parent().parent().hasClass('sharing-buttons')) {
                jQuery('.above-smart-sharing-container .horizontal-drag').show();
              }
            });
            jQuery('.above-smart-sharing-container .selected-services .enabled').bind('mouseenter', function() {
              jQuery('.above-smart-sharing-container .vertical-drag').show();
            });
            jQuery('.below-smart-sharing-container .sortable .disabled').bind('mouseover', function() {
                jQuery('.below-smart-sharing-container .horizontal-drag, .below-smart-sharing-container .vertical-drag').hide();
              });
              jQuery('.below-smart-sharing-container .sharing-buttons .enabled').bind('mouseenter', function() {
                if(jQuery(this).parent().parent().hasClass('sharing-buttons')) {
                  jQuery('.below-smart-sharing-container .horizontal-drag').show();
                }
              });
              jQuery('.below-smart-sharing-container .selected-services .enabled').bind('mouseenter', function() {
                jQuery('.below-smart-sharing-container .vertical-drag').show();
              });
          },0);
        }
      });

      return self;

    }

  };

  // Helps Fetch all of the service names
  window.loadServices = function(response) {

    var serviceList = [],
      currentService = '',
      itemCopy,
      addthisButtonPath = 'http://cache.addthiscdn.com/icons/v1/thumbs/addthis.gif',
      customServicesAPI = window.customServicesAPI,
      duplicateServices = customServicesAPI.duplicateServices = {},
      checkDuplicateName = {},
      checkDuplicateService = {},
      service,
      thirdPartyButtons = customServicesAPI.thirdPartyButtons.services();

    jQuery(function() {

        customServicesAPI.addthisButtons.services = serviceList;
        if((response||{}).data) {
            for (var i = 0; i < response.data.length; i += 1) {
              currentService = response.data[i].code;
              if(currentService === 'pinterest') {
                service = { service: 'pinterest_share', name: 'Pinterest', icon: 'pinterest_share' };
              }
              else {
                service = { service: currentService, name: response.data[i].name, icon: currentService };
              }
              checkDuplicateName['name'] = response.data[i].name;
              checkDuplicateService['service'] = currentService;
              if(_.where(thirdPartyButtons, checkDuplicateName).length) {
                duplicateServices[currentService] = service;
              }
              if(!_.where(thirdPartyButtons, checkDuplicateService).length) {
                serviceList.push(service);
              }
            }
        }

        try {

          if(!_.where(serviceList, { 'service': 'compact' } ).length) {
            serviceList.push({ service: "compact", name: "More", icon: 'compact' });
          }

        } catch(e) {}

      customServicesAPI.totalServices = jQuery.merge(jQuery.merge([],serviceList), customServicesAPI.thirdPartyButtons.services());

      customServicesAPI.disabledServices = _.filter(serviceList, function(service) {
        return !_.where(customServicesAPI.thirdPartyButtons.services(), { 'linkedService': service.service }).length;
      });

      customServicesAPI.loadDeferred.resolve(serviceList);

    });

  };

  jQuery(document).ready(function(jQuery) {
    if(jQuery('#above-disable-smart-sharing').attr('checked')){
      setTimeout(function() {
        window.customServicesAPI.abovepopulateSharingServices();
      }, 0);
      jQuery('.above-smart-sharing-container .restore-default-options').show();
      jQuery('.above-smart-sharing-container .customize-buttons').show();
      setTimeout(function() {
        // Makes the new list sortable
        jQuery('.above-smart-sharing-container .sortable').sortable().disableSelection().sortable('option', 'connectWith', '.sortable');
      }, 0);
    }

    if(jQuery('#below-disable-smart-sharing').attr('checked')){
      setTimeout(function() {
        window.customServicesAPI.belowpopulateSharingServices();
      }, 0);
      jQuery('.below-smart-sharing-container .restore-default-options').show();
      jQuery('.below-smart-sharing-container .customize-buttons').show();
      setTimeout(function() {
        // Makes the new list sortable
        jQuery('.below-smart-sharing-container .sortable').sortable().disableSelection().sortable('option', 'connectWith', '.sortable');
      }, 0);
    }
  });

}(window.jQuery, window, document));

