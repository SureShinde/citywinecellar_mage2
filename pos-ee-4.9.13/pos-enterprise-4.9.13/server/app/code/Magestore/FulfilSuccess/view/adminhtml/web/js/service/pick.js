/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magestore_FulfilSuccess/js/model/repository/pickRequestItem',
    'Magestore_FulfilSuccess/js/model/pick/item',
    'ko',
    'Magestore_FulfilSuccess/js/model/event-manager'
], function (PickRequestItemRepository, PickRequestItem, ko, Event) {
    'use strict';

    return {
        /**
         * Link to view detail order
         */
        orderlink: ko.observable(),
        /**
         * Sales Increment ID label
         */
        orderIncrementId: ko.observable(),
        /**
         * Scanning order barcode
         */
        currentOrderBarcode: ko.observable(),
        /**
         * Scanning order barcode
         */
        currentPickRequestId: ko.observable(),
        /**
         * Finish picking request
         */
        finish: function(){
            this.currentPickRequestId('');
            this.currentOrderBarcode('');
            this.orderIncrementId('');
            this.orderlink('');
            PickRequestItemRepository.resetItemsData();
        },
        /**
         * Start picking request
         */
        start: function(pickingtItems){
            if(pickingtItems){
                PickRequestItemRepository.saveItems(pickingtItems);
            }
        },
        /**
         * Pick item
         * @param key
         * @param value
         */
        pickItem: function(key, value, qty){
            var self = this;
            var response = {};
            var validateItem = this.canPickItem(key, value, qty);
            if(validateItem.success){
                PickRequestItemRepository.pick(key, value, qty);
                var item = PickRequestItemRepository.getItem(key, value);
                self.pickByParent(item);
                self.validateParent(item);
                response.success = true;
            }else{
                response.error = true;
                response.message = validateItem.message;
            }
            return response;
        },
        /**
         * Cancel item
         * @param key
         * @param value
         */
        cancelItem: function(key, value, qty){
            var self = this;
            var response = {};
            var validateItem = this.canCancelItem(key, value, qty);
            if(validateItem.success){
                PickRequestItemRepository.cancel(key, value, qty);
                var item = PickRequestItemRepository.getItem(key, value);
                self.pickByParent(item);
                self.validateParent(item);
                response.success = true;
            }else{
                response.error = true;
                response.message = validateItem.message;
            }
            return response;
        },
        /**
         * Validate picked qty
         * @param key
         * @param value
         * @param qty
         * @returns {boolean}
         */
        canPickItem: function(key, value, qty){
            var self = this;
            var result = {
                success: false,
                message: 'This item does not exist on this Sales'
            }
            var items = PickRequestItemRepository.getItems();
            if(items.length > 0 && key){
                ko.utils.arrayForEach(items, function(item) {
                    if(
                        PickRequestItemRepository.isValidItem(item, key, value)
                    ){
                        var pickedQty = parseFloat(item[PickRequestItem.PICKED_QTY]) + 1;
                        var newQty = (typeof qty != 'undefined')?parseFloat(qty):pickedQty;
                        var requesQty = parseFloat(item[PickRequestItem.REQUEST_QTY]);
                        result.success = (((newQty <= requesQty)&&(newQty >= 0)) || (requesQty == 0 && newQty == 0))?true:false;
                        if(!result.success){
                            result.message = (newQty > requesQty)?'This item has been picked enough':'The quantity cannot be changed to negative';
                        }
                    }
                });
            }
            return result;
        },
        /**
         * Validate picked qty
         * @param key
         * @param value
         * @param qty
         * @returns {boolean}
         */
        canCancelItem: function(key, value, qty){
            var self = this;
            var result = {
                success: false,
                message: 'This item does not exist on this Sales'
            }
            var items = PickRequestItemRepository.getItems();
            if(items.length > 0 && key){
                ko.utils.arrayForEach(items, function(item) {
                    if(
                        PickRequestItemRepository.isValidItem(item, key, value)
                    ){
                        var pickedQty = parseFloat(item[PickRequestItem.PICKED_QTY]) - 1;
                        var newQty = (typeof qty != 'undefined')?parseFloat(qty):pickedQty;
                        result.success = (newQty >= 0)?true:false;
                        if(!result.success){
                            result.message = 'Cannot cancel this item anymore';
                        }
                    }
                });
            }
            return result;
        },
        /**
         * Check picked all items
         * @returns {boolean}
         */
        isPickedAllItems: function(){
            var self = this;
            var result = true;
            var items = PickRequestItemRepository.getItems();
            if(items.length > 0){
                ko.utils.arrayForEach(items, function(item) {
                    var pickedQty = parseFloat(item[PickRequestItem.PICKED_QTY]);
                    var requestQty = parseFloat(item[PickRequestItem.REQUEST_QTY]);
                    if(pickedQty != requestQty){
                        result = false;
                        return result;
                    }
                });
            }
            return result;
        },
        /**
         * Get params for finish request
         * @returns {{order_increment_id: *, pick_request_item_data: *}}
         */
        getParamsToFinish: function(){
            var self = this;
            var params = {
                pick_request_id: self.currentOrderBarcode(),
                pick_request_item_data: PickRequestItemRepository.getItems(),
                picked_all_items: self.isPickedAllItems()
            }
            return params;
        },
        /**
         * Pick child items by parent
         * @param parent
         */
        pickByParent: function(parent){
            var childs = PickRequestItemRepository.getChilds(parent);
            if(childs){
                var parentPickedQty = parseFloat(parent[PickRequestItem.PICKED_QTY]);
                var parentRequestQty = parseFloat(parent[PickRequestItem.REQUEST_QTY]);
                ko.utils.arrayForEach(childs, function(item) {
                    var pickedQty = parseFloat(item[PickRequestItem.PICKED_QTY]);
                    var requestQty = parseFloat(item[PickRequestItem.REQUEST_QTY]);
                    var newPickedQty = (parentPickedQty == parentRequestQty)?requestQty:(parentPickedQty/parentRequestQty*requestQty);
                    if(newPickedQty != pickedQty){
                        item[PickRequestItem.PICKED_QTY] = newPickedQty;
                            PickRequestItemRepository.saveItem(PickRequestItem.PICK_REQUEST_ITEM_ID, item[PickRequestItem.PICK_REQUEST_ITEM_ID], item);
                        Event.dispatch('update_column_value', {
                            primaryValue: item[PickRequestItem.PICK_REQUEST_ITEM_ID],
                            columnIndex: PickRequestItem.PICKED_QTY,
                            columnValue: item[PickRequestItem.PICKED_QTY]
                        });
                    }
                });
            }
        },
        /**
         * Calculate parent qty by childs picked qty
         * @param child
         */
        validateParent: function(child){
            var parent = PickRequestItemRepository.getParent(child);
            if(parent){
                var childs = PickRequestItemRepository.getChilds(parent);
                if(childs){
                    var parentPickedQty = parseFloat(parent[PickRequestItem.PICKED_QTY]);
                    var parentRequestQty = parseFloat(parent[PickRequestItem.REQUEST_QTY]);
                    var parentPickedQtys = [];
                    ko.utils.arrayForEach(childs, function(item) {
                        var pickedQty = parseFloat(item[PickRequestItem.PICKED_QTY]);
                        var requestQty = parseFloat(item[PickRequestItem.REQUEST_QTY]);
                        var qtyPerParent = parseInt(pickedQty / requestQty * parentRequestQty);
                        parentPickedQtys.push(qtyPerParent);
                    });
                    var newPickedQty = Math.min.apply(null, parentPickedQtys);
                    if(parentPickedQty != newPickedQty){
                        parent[PickRequestItem.PICKED_QTY] = parseFloat(newPickedQty);
                        PickRequestItemRepository.saveItem(PickRequestItem.PICK_REQUEST_ITEM_ID, parent[PickRequestItem.PICK_REQUEST_ITEM_ID], parent);
                        Event.dispatch('update_column_value', {
                            primaryValue: parent[PickRequestItem.PICK_REQUEST_ITEM_ID],
                            columnIndex: PickRequestItem.PICKED_QTY,
                            columnValue: parent[PickRequestItem.PICKED_QTY]
                        });
                    }
                }
            }
        }
    };
});
