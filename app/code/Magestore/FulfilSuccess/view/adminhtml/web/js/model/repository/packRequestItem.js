/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magestore_FulfilSuccess/js/model/localStorage',
    'Magestore_FulfilSuccess/js/model/pack/item',
    'ko',
    'jquery'
], function (Storage, PackRequestItem, ko, $) {
    'use strict';

    return {
        /**
         * Scope of service
         */
        SCOPE: 'os_fulfilsuccess_service_pack',
        /**
         * Key child action
         */
        STORAGE_KEY: 'packing_items',
        /**
         * Get all packing items from localStorage
         * @returns {Array}
         */
        getItems: function(){
            var path = Storage.getKeyPath(this.SCOPE, this.STORAGE_KEY);
            var items = Storage.get(path);
            var dataArray = this.convertJsonToArray(JSON.parse(items));
            return (dataArray)?dataArray:[];
        },
        /**
         *
         * @param obj
         * @returns {Array}
         */
        convertJsonToArray: function(obj){
            var array = [];
            if(obj){
                $.each(obj, function(index, item) {
                    array[index] = item;
                });
            }
            return array;
        },
        /**
         *
         * @param array
         * @returns {{}}
         */
        convertArrayToJson: function(array){
            var obj = {};
            ko.utils.arrayForEach(array, function(item, index) {
                obj[index] = item;
            });
            return obj;
        },
        /**
         * Save all packing items to localStorage
         * @param items
         */
        saveItems: function(items){
            items = (items)?items:[];
            var path = Storage.getKeyPath(this.SCOPE, this.STORAGE_KEY);
            var dataString = JSON.stringify(this.convertArrayToJson(items));
            Storage.set(path, dataString);
        },
        /**
         * Get packing item by a key
         * @param key
         * @param value
         * @returns {*}
         */
        getItem: function(key, value){
            var self = this;
            var items = self.getItems();
            if(items.length > 0 && key){
                var item = ko.utils.arrayFirst(items, function(item) {
                    return self.isValidItem(item, key, value);
                });
                return item;
            }
        },
        /**
         * Save packing item data
         * @param key
         * @param value
         * @param json object data
         */
        saveItem: function(key, value, data){
            var self = this;
            var items = self.getItems();
            if(items.length > 0 && key){
                ko.utils.arrayForEach(items, function(item, index) {
                    if(typeof data == 'object' && item[key] == value){
                        items[index] = data;
                    }
                });
                self.saveItems(items);
            }
        },
        /**
         * Set data for specific key
         * @param key
         * @param value
         * @param dataKey
         * @param dataValue
         */
        setItemData: function(key, value, dataKey, dataValue){
            var self = this;
            var items = self.getItems();
            if(items.length > 0 && key && dataKey){
                ko.utils.arrayForEach(items, function(item, index) {
                    if(self.isValidItem(item, key, value)){
                        items[index][dataKey] = dataValue;
                    }
                });
                self.saveItems(items);
            }
        },
        /**
         * Get data for specific key
         * @param key
         * @param value
         * @param dataKey
         * @returns {boolean}
         */
        getItemData: function(key, value, dataKey){
            var self = this;
            var items = self.getItems();
            if(items.length > 0 && key && dataKey){
                var item = ko.utils.arrayFirst(items, function(item) {
                    return self.isValidItem(item, key, value);
                });
                return (item)?item[dataKey]:false;
            }
        },
        /**
         * Remove packing items data from localStorage
         */
        resetItemsData: function(){
            var path = Storage.getKeyPath(this.SCOPE, this.STORAGE_KEY);
            Storage.remove(path);
        },
        /**
         * Pack item
         */
        pack: function(key, value, qty){
            var self = this;
            var items = self.getItems();
            if(items.length > 0 && key){
                ko.utils.arrayForEach(items, function(item, index) {
                    if(self.isValidItem(item, key, value)){
                        var packedQty = parseFloat(items[index][PackRequestItem.PACKED_QTY]) + 1;
                        items[index][PackRequestItem.PACKED_QTY] = (qty)?parseFloat(qty):packedQty;
                    }
                });
                self.saveItems(items);
            }
        },
        /**
         * Cancel item
         */
        cancel: function(key, value, qty){
            var self = this;
            var items = self.getItems();
            if(items.length > 0 && key){
                ko.utils.arrayForEach(items, function(item, index) {
                    if(self.isValidItem(item, key, value)){
                        var packedQty = parseFloat(items[index][PackRequestItem.PACKED_QTY]) - 1;
                        items[index][PackRequestItem.PACKED_QTY] = (qty)?parseFloat(qty):packedQty;
                    }
                });
                self.saveItems(items);
            }
        },
        /**
         * Validate item
         * @param item
         * @param key
         * @param value
         * @returns {boolean}
         */
        isValidItem: function(item, key, value){
            if(!item[key]){
                return false;
            }
            if((key != PackRequestItem.ITEM_BARCODE)||(item[key].indexOf('||') < 0 )){
                return (item[key] == value);
            }
            if(item[key].indexOf('||') >= 0){
                var barcodes = item[key].split('||');
                return (barcodes.indexOf(value) >= 0);
            }
            return false;
        }
    };
});
