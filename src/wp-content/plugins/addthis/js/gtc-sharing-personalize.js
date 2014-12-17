(function($, window, document, undefined) {

	var aboveshareNamespace = window.addthisnamespaces && window.addthisnamespaces['aboveshare'] ? addthisnamespaces['aboveshare']: 'addthis-share-above';
	var belowshareNamespace = window.addthisnamespaces && window.addthisnamespaces['belowshare'] ? addthisnamespaces['belowshare']: 'addthis-share-below';

  /* Event Tracking */
  function trackPageView(p) {
    if (typeof gaPageTracker != "undefined") gaPageTracker._trackPageview(p);
  }

  // jQuery ready event
  $(function() {
	  
	  $('.above-smart-sharing-container .restore-default-options').hide();
	  $('.below-smart-sharing-container .restore-default-options').hide();
	  $('#below').tooltip({ position: { my: "left+15 center", at: "right center" } });
	  $('.sortable .disabled').tooltip({ position: { my: "left+15 center", at: "right center" } });
	  $('.sortable .close').tooltip({ position: { my: "left+10 center", at: "right center" } });
    setTimeout(function() {

      window.customServicesAPI.events().fetchServices();

    }, 0);

  });

  // API for the custom service UI
  window.customServicesAPI = {

    'loadDeferred': $.Deferred(),

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
        def = $.Deferred();

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
          def = $.Deferred(),
          defaults,
          services,
          currentType,
          addthisMappedDefaults,
          thirdPartyMappedDefaults,
          addthisServices,
          thirdPartyServices,
          abovesharingSortable = $('.above-smart-sharing-container .sharing-buttons .sortable'),
          aboveselectedSortable = $('.above-smart-sharing-container .selected-services .sortable');

        self.fetchServices().done(function(services) {

          if(!services.length) {

            def.resolve();

          }

          else {

            self.getaboveSavedOrder(function(obj) {
          	  
              defaults = restoreDefaults ? self.defaults: obj.rememberedDefaults;

              abovecurrentType = $('input[name="addthis_settings[above]"]:checked');

              if(!abovecurrentType.length) {
              	abovecurrentType = $('input[name="addthis_settings[above]"]:visible').first();
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
                
                $('body').trigger('populatedList');
                def.resolve();

              }

              else {
                $('body').trigger('populatedList');
                def.resolve();
              }
            });

          }

        });

        return def;

      },
      
      'belowpopulateSharingServices': function(restoreDefaults, pageload) {

          var self = this,
            def = $.Deferred(),
            defaults,
            services,
            currentType,
            addthisMappedDefaults,
            thirdPartyMappedDefaults,
            addthisServices,
            thirdPartyServices,
            belowsharingSortable = $('.below-smart-sharing-container .sharing-buttons .sortable'),
            belowselectedSortable = $('.below-smart-sharing-container .selected-services .sortable');

          self.fetchServices().done(function(services) {

            if(!services.length) {

              def.resolve();

            }

            else {
   
              self.getbelowSavedOrder(function(obj) {
            	  
                defaults = restoreDefaults ? self.defaults: obj.rememberedDefaults;
    
               
                belowcurrentType = $('input[name="addthis_settings[below]"]:checked');

                if(!belowcurrentType.length) {
                	belowcurrentType = $('input[name="addthis_settings[above]"]:visible').first();
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
                    $('body').trigger('populatedList');
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
        services = $.merge([], obj.services);

      // Sorts the addthis button list in the correct order
      $.each(services, function(iterator, value) {
        currentService = value.service;
        whereInArray = $.inArray(currentService, defaults);
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
              return $.inArray(value, defaults) === -1;
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
        disabledServices = (buttonType === 'addthisButtons' || type === 'selected-services') ? []: $.merge($.merge([], self.disabledServices), thirdPartyDisabled),
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
            isDuplicate = $.inArray(service, duplicates) !== -1,
            isDefault = $.inArray(service, defaults) !== -1,
            isExcluded = $.inArray(service, excludeList) !== -1;
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
          $.each(disabledServices, function(iterator, disabledService) {
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
        currentService = $(this).attr('data-service');
        defaults.push(currentService);
        if(currentService === updatedItem) {
          removed = false;
        }
        if($(this).hasClass('enabled')) {
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
        currentService = $(this).attr('data-service');
        defaults.push(currentService);
        if(currentService === updatedItem) {
          removed = false;
        }
        if($(this).hasClass('enabled')) {
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
    		$('.above-smart-sharing-container .selected-services .ui-sortable').each(function(){
    	        $(this).find('li').each(function(){
    	        	if($(this).hasClass('enabled')) {
    	        		buttons += '<span class="at300bs at15nc at15t_'+$(this).attr('data-service')+' at16t_'+$(this).attr('data-service')+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
    	        		if($(this).attr('data-service') == 'compact') {
    	        			buttons += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block;float: left;" href="#" tabindex="0"></a>';
    	        		}
    	        	}
    	        });
    		});
    		buttons += '</div>';
    	}
    	else if (size == "small") {
    		$('.above-smart-sharing-container .selected-services .ui-sortable').each(function(){
    	        $(this).find('li').each(function(){
    	        	if($(this).hasClass('enabled')) {
    	        		buttons += '<span class="at300bs at15nc at15t_'+$(this).attr('data-service')+' at16t_'+$(this).attr('data-service')+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
    	        		if($(this).attr('data-service') == 'compact') {
    	        			buttons += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block;float: left;" href="#" tabindex="0"></a>';
    	        		}
    	        	}
    	        });
    		});
    	}
    	else {
    		$('.above-smart-sharing-container .selected-services .ui-sortable').each(function(){
    	        $(this).find('li').each(function(){
    	        	if($(this).hasClass('enabled')) {
	    	        	if($(this).attr('data-service') == 'compact') {
		        			buttons += '<img src="'+addthis_params.img_base+'addthis_pill_style.png">';
		        		}
		        		else {
		        			buttons += '<img src="'+addthis_params.img_base+$(this).attr('data-service')+'.png">';
		        		}
    	        	}
    	        });
    		});
    	}

        $('#above_previewContainer').html(buttons);
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
    		$('.below-smart-sharing-container .selected-services .ui-sortable').each(function(){
    	        $(this).find('li').each(function(){
    	        	if($(this).hasClass('enabled')) {
    	        		buttons += '<span class="at300bs at15nc at15t_'+$(this).attr('data-service')+' at16t_'+$(this).attr('data-service')+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
    	        		if($(this).attr('data-service') == 'compact') {
    	        			buttons += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block; float: left;" href="#" tabindex="0"></a>';
    	        		}
    	        	}
    	        });
    		});
    		buttons += '</div>';
    	}
    	else if (size == "small") {
    		$('.below-smart-sharing-container .selected-services .ui-sortable').each(function(){
    	        $(this).find('li').each(function(){
    	        	if($(this).hasClass('enabled')) {
    	        		buttons += '<span class="at300bs at15nc at15t_'+$(this).attr('data-service')+' at16t_'+$(this).attr('data-service')+'" style="display:inline-block;padding-right:4px;vertical-align:middle;"></span>';
    	        		if($(this).attr('data-service') == 'compact') {
    	        			buttons += '<a class="addthis_counter addthis_bubble_style" style="display: inline-block; float: left;" href="#" tabindex="0"></a>';
    	        		}
    	        	}
    	        });
    		});
    	}
    	else {
    		$('.below-smart-sharing-container .selected-services .ui-sortable').each(function(){
    	        $(this).find('li').each(function(){
    	        	if($(this).hasClass('enabled')) {
	    	        	if($(this).attr('data-service') == 'compact') {
		        			buttons += '<img src="'+addthis_params.img_base+'addthis_pill_style.png">';
		        		}
		        		else {
		        			buttons += '<img src="'+addthis_params.img_base+$(this).attr('data-service')+'.png">';
		        		}
    	        	}
    	        });
    		});
    	}
        $('#below_previewContainer').html(buttons);
    },

    'events': function() {

      var self = this,
        aboveEnableSmartSharing = $('#above-enable-smart-sharing'),
        aboveDisableSmartSharing = $('#above-disable-smart-sharing'),
        belowEnableSmartSharing = $('#below-enable-smart-sharing'),
        belowDisableSmartSharing = $('#below-disable-smart-sharing'),
        
        sortableContainer,
        aboveradioInputs = $('input[name="addthis_settings[above]"]'),
        belowradioInputs = $('input[name="addthis_settings[below]"]'),
        abovecurrentRadioInput,
        belowcurrentRadioInput,
        abovecurrentType,
        belowcurrentType,
        abovecurrentStyle,
        belowcurrentStyle,
        excludeList,
        whereInputs = $('input[name=where]'),
        aboveSmartSharingContainer = $('.above-smart-sharing-container'),
        aboveSmartSharingInnerContainer = $('.above-smart-sharing-container .smart-sharing-inner-container'),
        aboveCustomizeButtons = $('.above-smart-sharing-container .customize-buttons'),
        aboveButtons = $('.above-smart-sharing-container .customize-buttons'),
        belowSmartSharingContainer = $('.below-smart-sharing-container'),
        belowSmartSharingInnerContainer = $('.below-smart-sharing-container .smart-sharing-inner-container'),
        belowCustomizeButtons = $('.below-smart-sharing-container .customize-buttons'),
        belowButtons = $('.below-smart-sharing-container .customize-buttons'),
        defaults,
        buttontype,
        buttonsize,
        buttonstyle,
        sortableLists = $('.sortable'),
        sortableListItems = sortableLists.find('li'),
        sortableSelectedListItems = $('.selected-services').find('li'),
        sortableListItemsCloseIcons = sortableSelectedListItems.find('.close'),
        aboveRestoreDefaultOptions = $('.above-smart-sharing-container .restore-default-options'),
        belowRestoreDefaultOptions = $('.below-smart-sharing-container .restore-default-options'),
        previewBox = $('#previewBox'),
        aboverestoreToDefault = _.debounce(function() {
          // Updates the personalization UI
        	self.abovepopulateSharingServices(true);
            $('.above-smart-sharing-container .selected-services .sortable:visible').trigger('sortupdate');
          commonMethods.localStorageSettings({ method: "remove", namespace: aboveshareNamespace });
        }, 1000, true),
        belowrestoreToDefault = _.debounce(function() {
            // Updates the personalization UI
        	self.belowpopulateSharingServices(true);
            $('.below-smart-sharing-container .selected-services .sortable:visible').trigger('sortupdate');
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

      //to show options upon save
      if($('#above-chosen-list').val() != "") {
    	  $('.above-smart-sharing-container #customizedMessage').show();
    	  $('.above-smart-sharing-container #personalizedMessage').hide();
//    	  $('.above-smart-sharing-container .customize-your-buttons').html('Your buttons are currently customized. <a href="#" class="above-customize-sharing-link customize-your-buttons">Show customization</a>.');
      }
      
      if($('#below-chosen-list').val() != "") {
    	  $('.below-smart-sharing-container #customizedMessage').show();
    	  $('.below-smart-sharing-container #personalizedMessage').hide();
//    	  $('.below-smart-sharing-container .customize-your-buttons').html('Your buttons are currently customized. <a href="#" class="below-customize-sharing-link customize-your-buttons">Show customization</a>.');
      }

      aboveDisableSmartSharing.add(aboveradioInputs).not('#button_above').bind('click', function() {

    	  abovecurrentRadioInput = $('input[name="addthis_settings[above]"]:checked');
        if(!abovecurrentRadioInput.length) {
        	abovecurrentRadioInput = $('input[name="addthis_settings[above]"]').first();
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

            $('.above-smart-sharing-container .selected-services .sortable:visible').trigger('sortupdate');

          }, 0);

          aboveRestoreDefaultOptions.show();

          $('.sharing-buttons-search').val('');

        }
        
        if(abovecurrentStyle === 'horizontal' && $('#above_previewContainer').width() < 380) {
          $('#above_previewContainer').css({ 'width': '380px' });
        }

        aboveSmartSharingInnerContainer.show();
        $('.above-customize-sharing-link, .customize-sharing-checkbox').show();

      });
      
      belowDisableSmartSharing.add(belowradioInputs).not('#button_below').bind('click', function() {
    	  
    	  belowcurrentRadioInput = $('input[name="addthis_settings[below]"]:checked');
          if(!belowcurrentRadioInput.length) {
        	  belowcurrentRadioInput = $('input[name="addthis_settings[below]"]').first();
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

              $('.below-smart-sharing-container .selected-services .sortable:visible').trigger('sortupdate');

            }, 0);

            belowRestoreDefaultOptions.show();

            $('.sharing-buttons-search').val('');

          }
          
          if(belowcurrentStyle === 'horizontal' && $('#below_previewContainer').width() < 380) {
            $('#below_previewContainer').css({ 'width': '380px' });
          }

          belowSmartSharingInnerContainer.show();
          $('.below-customize-sharing-link, .customize-sharing-checkbox').show();

        });

      $('#button_above').click(function() {
        var self = $(this);
        $('.previewbox').removeClass('previewboxbg');
        aboveSmartSharingInnerContainer.hide();
        $('.above-customize-sharing-link, .customize-sharing-checkbox').hide();
        $('#above_previewContainer').css({ 'width': '100%', 'margin-right': 'auto' });
      });
      
      $('#button_below').click(function() {
          var self = $(this);
          $('.previewbox').removeClass('previewboxbg');
          belowSmartSharingInnerContainer.hide();
          $('.below-customize-sharing-link, .customize-sharing-checkbox').hide();
          $('#below_previewContainer').css({ 'width': '100%', 'margin-right': 'auto' });
        });

      aboveEnableSmartSharing.bind('click', function() {
    	if($('#above-chosen-list').val() != "") {
    		$('.above-smart-sharing-container #customizedMessage').hide();
    		$('#above-chosen-list').val('');
        }
    	else {
    		$('.above-smart-sharing-container #customizedMessage').hide();
    	}

        currentRadioInput = $('input[name="addthis_settings[above]"]:checked');

        disableCustomization();

        aboveCustomizeButtons.hide();

        aboveRestoreDefaultOptions.hide();

        aboveradioInputs.removeClass('disabled-smart-sharing');

        currentRadioInput.click();

      });
      
      belowEnableSmartSharing.bind('click', function() {

    	  if($('#below-chosen-list').val() != "") {
      		$('.below-smart-sharing-container #customizedMessage').hide();
      		$('#below-chosen-list').val('');
          } 
    	  else {
    		  $('.below-smart-sharing-container #customizedMessage').hide();
    	  }
    	  
          currentRadioInput = $('input[name="addthis_settings[below]"]:checked');

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
          $('.above-smart-sharing-container .sortable').sortable({

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
            $('.below-smart-sharing-container .sortable').sortable({

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

      $('.above_button_set .selected-services .sortable').bind('sortupdate', function(ev, item) {

        if($.isPlainObject(item)) {
          item = item.item.attr('data-service');
        }

        if(!$(this).find('li').length) {
          $(this).html('<p class="add-buttons-msg">Add buttons by dragging them in this box.</p>');
          $(this).css('border-style', 'dashed');
          $('.add-buttons-msg').show();
        }

        else {
          $(this).css('border-style', 'solid');
        }

        var abovesortableList = $('.above-smart-sharing-container .selected-services .sortable:visible');
        
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
      
      $('.below_button_set .selected-services .sortable').bind('sortupdate', function(ev, item) {

          if($.isPlainObject(item)) {
            item = item.item.attr('data-service');
          }

          if(!$(this).find('li').length) {
            $(this).html('<p class="add-buttons-msg">Add buttons by dragging them in this box.</p>');
            $(this).css('border-style', 'dashed');
            $('.add-buttons-msg').show();
          }

          else {
            $(this).css('border-style', 'solid');
          }

          var belowsortableList = $('.below-smart-sharing-container .selected-services .sortable:visible');
          
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

          $('.above-smart-sharing-container .sharing-buttons-search').val('');

          aboverestoreToDefault();

        }, 0);

      });
      
      belowRestoreDefaultOptions.bind('click', function(ev) {

          ev.preventDefault();

          setTimeout(function() {

            $('.below-smart-sharing-container .sharing-buttons-search').val('');

            belowrestoreToDefault();

          }, 0);

        });

      sortableSelectedListItems.live({

        'mouseenter': function() {

          $(this).find('.close').css('display', 'inline-block');

        },

        'mouseleave': function() {

          $(this).find('.close').hide();

        },

        'mouseup': function() {

          $(this).find('.close').hide();

        }

      });

      sortableListItems.live({

        'mouseup': function() {

          if(!$('.selected-services li:visible').length) {


            $('.add-buttons-msg').show();

          }

        },

        'mousedown': function() {

          $('.add-buttons-msg').hide();

          $('.below-smart-sharing-container .horizontal-drag').hide();
          
          $('.above-smart-sharing-container .horizontal-drag').hide();

        }

      });

      sortableListItemsCloseIcons.live('click', function() {

        var parent = $(this).parent(),
          isDisabled = parent.hasClass('disabled');
        parent.fadeOut().promise().done(function() {

          $('.sharing-buttons .sortable:visible').prepend(parent);
          parent.find('.close').hide().tooltip().tooltip('close');
          parent.fadeIn();
          $('.selected-services .sortable:visible').trigger('sortupdate', parent.attr('data-service'));

        });

      });

      $('.above-smart-sharing-container .sharing-buttons-search').bind('keyup', function(e) {

        var currentVal = $(this).val();

        $('.above-smart-sharing-container .sharing-buttons .sortable').find('li').each(function() {
          if($(this).text().toLowerCase().search(currentVal.toLowerCase()) === -1) {
            $(this).hide().attr('data-hidden', 'true');
          }
          else {
            $(this).show().removeAttr('data-hidden');
          }
        });

      });
      
      $('.below-smart-sharing-container .sharing-buttons-search').bind('keyup', function(e) {

          var currentVal = $(this).val();

          $('.below-smart-sharing-container .sharing-buttons .sortable').find('li').each(function() {
            if($(this).text().toLowerCase().search(currentVal.toLowerCase()) === -1) {
              $(this).hide().attr('data-hidden', 'true');
            }
            else {
              $(this).show().removeAttr('data-hidden');
            }
          });

        });

      $('.sharing-buttons-search').bind('click', function() {
        trackPageView('/tracker/gtc/' + (window.page || '') + '/event/search_clicked');
      });

      $('.above-smart-sharing-container .sortable').bind('mousedown', function() {
        if($('.above-smart-sharing-container .sharing-buttons-search').is(':focus')) {
          $('.above-smart-sharing-container .sharing-buttons-search').blur();
        }
      });
      $('.below-smart-sharing-container .sortable').bind('mousedown', function() {
          if($('.below-smart-sharing-container .sharing-buttons-search').is(':focus')) {
            $('.below-smart-sharing-container .sharing-buttons-search').blur();
          }
        });

      $('.above-smart-sharing-container .selected-services .sortable').bind({

        'mouseover': function() {
          if($(this).find('li.enabled:visible').length > 1) {
            $('.above-smart-sharing-container .horizontal-drag').hide();
            $('.above-smart-sharing-container .vertical-drag').show();
          }
        },
        'mouseout': function() {
          $('.above-smart-sharing-container .vertical-drag').hide();
        }

      });
      
      $('.below-smart-sharing-container .selected-services .sortable').bind({

          'mouseover': function() {
            if($(this).find('li.enabled:visible').length > 1) {
              $('.below-smart-sharing-container .horizontal-drag').hide();
              $('.below-smart-sharing-container .vertical-drag').show();
            }
          },
          'mouseout': function() {
            $('.below-smart-sharing-container .vertical-drag').hide();
          }

        });

      $('.above-smart-sharing-container .sharing-buttons .sortable').bind({

        'mouseover': function() {
          if($(this).find('li.enabled:visible').length) {
            $('.above-smart-sharing-container .vertical-drag').hide();
            $('.above-smart-sharing-container .horizontal-drag').show();
          }
        },
        'mouseout': function() {
          $('.above-smart-sharing-container .horizontal-drag').hide();
        }

      });
      
      $('.below-smart-sharing-container .sharing-buttons .sortable').bind({

          'mouseover': function() {
            if($(this).find('li.enabled:visible').length) {
              $('.below-smart-sharing-container .vertical-drag').hide();
              $('.below-smart-sharing-container .horizontal-drag').show();
            }
          },
          'mouseout': function() {
            $('.below-smart-sharing-container .horizontal-drag').hide();
          }

        });

      $('.above-customize-sharing-link').bind('click', function(ev) { 

        var aboveSmartSharingLink = $('.above-smart-sharing-container .smart-sharing-link'),
          customizeButtonLink = $('.above-smart-sharing-container .customize-your-buttons');

        ev.preventDefault();

        if($(this).is(customizeButtonLink)) {
          customizeButtonLink.hide();
          aboveSmartSharingLink.show();
          if(!aboveDisableSmartSharing.is(':checked')) {
        	  aboveDisableSmartSharing.prop('checked', true).trigger('click');
          }
        }

        else if($(this).is(aboveSmartSharingLink)) {
        	aboveSmartSharingLink.hide();
          customizeButtonLink.show();
          if(!aboveEnableSmartSharing.is(':checked')) {
            aboveEnableSmartSharing.prop('checked', true).trigger('click');
          }
        }

      });
      
      $('.below-customize-sharing-link').bind('click', function(ev) { 

          var belowSmartSharingLink = $('.below-smart-sharing-container .smart-sharing-link'),
            customizeButtonLink = $('.below-smart-sharing-container .customize-your-buttons');

          ev.preventDefault();

          if($(this).is(customizeButtonLink)) {
            customizeButtonLink.hide();
            belowSmartSharingLink.show();
            if(!belowDisableSmartSharing.is(':checked')) {
          	  belowDisableSmartSharing.prop('checked', true).trigger('click');
            }
          }

          else if($(this).is(belowSmartSharingLink)) {
        	  belowSmartSharingLink.hide();
            customizeButtonLink.show();
            if(!belowEnableSmartSharing.is(':checked')) {
              belowEnableSmartSharing.prop('checked', true).trigger('click');
            }
          }

        });

      $('body').bind({

      'populatedList': function() {
          setTimeout(function() {
            $('.sortable .disabled, .sortable .close').tooltip({
              position: {
            	my: 'left+15 top',
                at: 'right top',
                collision: 'none',
                tooltipClass: 'custom-tooltip-styling'
              }
            });
            $('.above-smart-sharing-container .sortable .disabled').bind('mouseover', function() {
              $('.above-smart-sharing-container .horizontal-drag, .above-smart-sharing-container .vertical-drag').hide();
            });
            $('.above-smart-sharing-container .sharing-buttons .enabled').bind('mouseenter', function() {
              if($(this).parent().parent().hasClass('sharing-buttons')) {
                $('.above-smart-sharing-container .horizontal-drag').show();
              }
            });
            $('.above-smart-sharing-container .selected-services .enabled').bind('mouseenter', function() {
              $('.above-smart-sharing-container .vertical-drag').show();
            });
            $('.below-smart-sharing-container .sortable .disabled').bind('mouseover', function() {
                $('.below-smart-sharing-container .horizontal-drag, .below-smart-sharing-container .vertical-drag').hide();
              });
              $('.below-smart-sharing-container .sharing-buttons .enabled').bind('mouseenter', function() {
                if($(this).parent().parent().hasClass('sharing-buttons')) {
                  $('.below-smart-sharing-container .horizontal-drag').show();
                }
              });
              $('.below-smart-sharing-container .selected-services .enabled').bind('mouseenter', function() {
                $('.below-smart-sharing-container .vertical-drag').show();
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

    $(function() {

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

      customServicesAPI.totalServices = $.merge($.merge([],serviceList), customServicesAPI.thirdPartyButtons.services());

      customServicesAPI.disabledServices = _.filter(serviceList, function(service) {
        return !_.where(customServicesAPI.thirdPartyButtons.services(), { 'linkedService': service.service }).length;
      });

      customServicesAPI.loadDeferred.resolve(serviceList);

    });

  };

}(window.jQuery, window, document));

