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

window.commonMethods = {

    localStorageSettings: function(obj, callback) {

        var tempObj = {};

        if(!obj.namespace || !obj.namespace.length || !obj.method || !obj.method.length) return;

        if(window.localStorage && window.JSON) {

            if(obj.method.toLowerCase() === "get") {

                callback.call(this, JSON.parse(window.localStorage.getItem(obj.namespace)));

            }

            else if(obj.method.toLowerCase() === "set" && obj.data != null && jQuery.isPlainObject(obj.data)) {

                tempObj = jQuery.extend({}, JSON.parse(window.localStorage.getItem(obj.namespace)), obj.data);

                return window.localStorage.setItem(obj.namespace, JSON.stringify(tempObj));

            }

            else if(obj.method.toLowerCase() === "set" && obj.data != null && jQuery.isArray(obj.data)) {

                return window.localStorage.setItem(obj.namespace, JSON.stringify(obj.data));


            }

            else if(obj.method.toLowerCase() === "remove") {

                return window.localStorage.removeItem(obj.namespace);

            }

        }

    },

    resetOptions: function(namespace, obj, callback) {

        if(obj) {

            for(var x in obj) {

                if(jQuery(x).is(':checkbox') || jQuery(x).is(':radio')) jQuery(x).prop('checked', obj[x]).change();

                else jQuery(x).val(obj[x]).change().keyup();

            }

            if(window.localStorage && namespace) {

                commonMethods.localStorageSettings({ namespace: namespace, method: "remove" });

            }

            if(callback) callback.call(this);

        }

    },

    loadCode: function (namespace, callback) {

        commonMethods.localStorageSettings({ namespace: namespace, method: "get" }, function(obj) {

            if(obj) {

                for(var x in obj) {

                    if(jQuery(x).is(':checkbox') || jQuery(x).is(':radio')) {

                        jQuery(x).prop('checked', obj[x]).val(obj[x]);

                        if(jQuery(x).is(':checked')) jQuery(x).trigger('auto-dismiss');

                    }

                    else jQuery(x).val(obj[x]).attr("data-updated", "updated");

                }

            }

            if(callback) callback.call(this, obj);

        });

    }

};

window.addthisnamespaces = {
    aboveshare: 'addthis-share-above',
    belowshare: 'addthis-share-below'
};
