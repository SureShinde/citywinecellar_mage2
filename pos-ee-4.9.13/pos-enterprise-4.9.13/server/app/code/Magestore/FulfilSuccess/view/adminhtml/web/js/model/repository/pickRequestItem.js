/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magestore_FulfilSuccess/js/model/localStorage',
    'Magestore_FulfilSuccess/js/model/pick/item',
    'ko',
    'jquery'
], function (Storage, PickRequestItem, ko, $) {
    'use strict';

    return {
        /**
         * Scope of service
         */
        SCOPE: 'os_fulfilsuccess_service_pick',
        /**
         * Key child action
         */
        STORAGE_KEY: 'picking_items',
        /**
         * Get all picking items from localStorage
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
         * Save all picking items to localStorage
         * @param items
         */
        saveItems: function(items){
            items = (items)?items:[];
            var path = Storage.getKeyPath(this.SCOPE, this.STORAGE_KEY);
            var dataString = JSON.stringify(this.convertArrayToJson(items));
            Storage.set(path, dataString);
        },
        /**
         * Get picking item by a key
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
         * Save picking item data
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
         * Remove picking items data from localStorage
         */
        resetItemsData: function(){
            var path = Storage.getKeyPath(this.SCOPE, this.STORAGE_KEY);
            Storage.remove(path);
        },
        /**
         * Pick item
         */
        pick: function(key, value, qty){
            var self = this;
            var items = self.getItems();
            if(items.length > 0 && key){
                ko.utils.arrayForEach(items, function(item, index) {
                    if(self.isValidItem(item, key, value)){
                        var pickedQty = parseFloat(items[index][PickRequestItem.PICKED_QTY]) + 1;
                        items[index][PickRequestItem.PICKED_QTY] = (typeof qty != 'undefined')?parseFloat(qty):pickedQty;
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
                        var pickedQty = parseFloat(items[index][PickRequestItem.PICKED_QTY]) - 1;
                        items[index][PickRequestItem.PICKED_QTY] = (qty)?parseFloat(qty):pickedQty;
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
            if((key != PickRequestItem.ITEM_BARCODE)||(item[key].indexOf('||') < 0 )){
                return (item[key] == value);
            }
            if(item[key].indexOf('||') >= 0){
                var barcodes = item[key].split('||');
                return (barcodes.indexOf(value) >= 0);
            }
            return false;
        },
        /**
         * Check item has child
         * @param item
         * @returns {boolean}
         */
        getChilds: function(parent){
            var self = this;
            var items = self.getItems();
            if(items.length > 0 && parent){
                var childs = [];
                ko.utils.arrayForEach(items, function(item, index) {
                    if(self.isValidItem(item, PickRequestItem.PARENT_ID, parent[PickRequestItem.PICK_REQUEST_ITEM_ID])){
                        childs.push(item);
                    }
                });
                return (childs.length > 0)?childs:false;
            }
            return false;
        },
        /**
         * Check item has parent
         * @param item
         * @returns {boolean}
         */
        getParent: function(child){
            var self = this;
            var items = self.getItems();
            if(items.length > 0 && child && child[PickRequestItem.PARENT_ID]){
                var parent = ko.utils.arrayFirst(items, function(item) {
                    return self.isValidItem(item, PickRequestItem.PICK_REQUEST_ITEM_ID, child[PickRequestItem.PARENT_ID]);
                });
                return (parent)?parent:false;
            }
            return false;
        }
    };
});
