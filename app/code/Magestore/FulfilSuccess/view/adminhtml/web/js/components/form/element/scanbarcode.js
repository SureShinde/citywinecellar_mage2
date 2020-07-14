/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magestore_FulfilSuccess/js/components/form/element/input',
    'mage/translate',
    'ko',
    'uiRegistry',
    'Magestore_FulfilSuccess/js/service/pick',
    'Magestore_FulfilSuccess/js/model/repository/pickRequestItem',
    'Magestore_FulfilSuccess/js/model/pick/item',
    'Magestore_FulfilSuccess/js/model/pick/request',
    'Magestore_FulfilSuccess/js/model/event-manager',
    'Magento_Ui/js/modal/confirm'
], function (Input, __, ko, Registry, PickService, PickRequestItemRepository, PickRequestItem, PickRequest, Event, Confirm) {
    'use strict';

    return Input.extend({
        /**
         * Place holder for input
         */
        placeHolder: ko.observable(__('SCAN ORDER NUMBER HERE')),
        /**
         * Link to view detail order
         */
        orderlink: PickService.orderlink,
        /**
         * Order Increment ID label
         */
        orderIncrementId: PickService.orderIncrementId,
        /**
         * Current Pick ID
         */
        currentPickRequestId: PickService.currentPickRequestId,
        /**
         * Scanning order barcode
         */
        reloadListing: ko.observable(true),
        /**
         * This a flag to check if in scan action
         */
        isScanning: ko.observable(false),
        /**
         * Prefix of Sales barcode
         */
        PICK_REQUEST_BARCODE_PREFIX: 'PICK',
        /**
         * Inittialize data
         */
        initData: function(){
            var self = this;
            var data = self.getData();
            if(data && data.picking){
                if(data.picking[PickRequest.ORDER_INCREMENT_ID]){
                    PickService.orderIncrementId('#'+data.picking[PickRequest.ORDER_INCREMENT_ID]);
                    PickService.currentPickRequestId(data.picking[PickRequest.PICK_REQUEST_ID]);
                    PickService.currentOrderBarcode(data.picking[PickRequest.PICK_REQUEST_ID]);
                }
                if(data.picking.items && data.picking.items.length > 0){
                    PickService.start(data.picking.items);
                }
            }else{
                PickService.finish();
            }
        },
        /**
         * Inittialize event
         */
        initEvents: function(){
            var self = this;
            Event.observer('os_fulfilsuccess.picking_form.process_message', function(event, data){
                if(data && (data.success || data.error)){
                    if(data.error && data.message){
                        self.warningMessage(__(data.message));
                    }else{
                        self.warningMessage('');
                    }
                    if(data.success && data.message){
                        self.successMessage(__(data.message));
                    }else{
                        self.successMessage('');
                    }
                }
            });
            Event.observer('os_fulfilsuccess.picking_form.pick_item_after', function(event, data){
                if(data && !self.isScanning()){
                    self.requestDone(data);
                }else{
                    self.isScanning(false);
                }
            });
        },
        /**
         * Event when input value change
         * @param data
         * @param event
         */
        change: function(data, event){
            this._super();
            this.checkBarcode();
            event.target.value = "";
        },
        /**
         * Validate barcode data and send request to check on server
         */
        checkBarcode: function(){
            var self = this;
            var barcode = self.data();
            if(barcode){
                barcode = barcode.trim();
                var isOrderBarcode = (barcode.indexOf(self.PICK_REQUEST_BARCODE_PREFIX) == 0 || barcode.indexOf(self.PICK_REQUEST_BARCODE_PREFIX.toLowerCase()) == 0)?true:false;
                if(!PickService.currentOrderBarcode() && !isOrderBarcode){
                    this.warningMessage(__('Please scan the order first'));
                }else{
                    if(isOrderBarcode){
                        var data = self.getData();
                        if(data.urls){
                            var url = data.urls.pick_order;
                            var params = {
                                barcode: barcode.substr(self.PICK_REQUEST_BARCODE_PREFIX.length)
                            };
                            if(PickService.currentOrderBarcode() && PickService.currentOrderBarcode() != params.barcode){
                                Confirm({
                                    title: __('Attention!'),
                                    content: __("There are some items in the current request not picked. If you scan the next picking request, these items will be moved to Prepare Fulfillment step. Do you still want to do this?"),
                                    actions: {
                                        confirm: function () {
                                            self.finishPickItems();
                                            self.sendRequest(url, params);
                                        },
                                        always: function (event) {
                                            event.stopImmediatePropagation();
                                        }
                                    }
                                });
                            }else{
                                self.sendRequest(url, params);
                            }
                        }
                    }else{
                        self.isScanning(true);
                        var response = PickService.pickItem(PickRequestItem.ITEM_BARCODE, barcode);
                        self.requestDone({
                            action: 'pick_item',
                            data: {
                                response: response,
                                barcode:barcode
                            }
                        });
                    }
                }
            }
        },
        /**
         * Finish picking
         */
        finishPickItems: function(){
            var self = this;
            var data = self.getData();
            if(data.urls){
                var url = data.urls.pick_items;
                var params = PickService.getParamsToFinish();
                self.sendRequest(url, params);
            }
        },
        /**
         * Process ajax request response
         * @param response
         */
        requestDone: function(response){
            var self = this;
            switch (response.action){
                case 'pick_order':
                    self.afterPickOrder(response.data);
                    break;
                case 'pick_item':
                    self.afterPickItem(response.data);
                    break;
                case 'pick_items':
                    self.afterPickItems(response.data);
                    break;
                case 'cancel_picking':
                    self.afterPickItems(response.data);
                    break;
            }
            this.resetValue();
        },
        /**
         * Process data after scan request finish
         * @param data
         */
        afterPickOrder: function(data){
            if(data){
                var self = this;
                if(data[PickRequest.PICK_REQUEST_ID]){
                    PickService.currentPickRequestId(data[PickRequest.PICK_REQUEST_ID]);
                }
                if(data[PickRequest.ORDER_INCREMENT_ID]){
                    PickService.orderIncrementId('#'+data[PickRequest.ORDER_INCREMENT_ID]);
                }
                if(data.barcode){
                    PickService.currentOrderBarcode(data.barcode);
                }
                if(data.orderlink){
                    PickService.orderlink(data.orderlink);
                }
                if(data.items){
                    PickService.start(data.items);
                }
                if(self.reloadListing()){
                    self.reloadListing(false);
                    self.reloadItemsListing();
                }
            }
        },
        /**
         * Process data after scan
         * @param data
         */
        afterPickItem: function(data){
            if(data){
                var self = this;
                var response = data.response;
                if(response && response.error && response.message){
                    self.warningMessage(__(response.message));
                }
                if(response && response.success){
                    Event.dispatch('update_column_value', {
                        primaryValue: PickRequestItemRepository.getItemData(PickRequestItem.ITEM_BARCODE, data.barcode, PickRequestItem.PICK_REQUEST_ITEM_ID),
                        columnIndex: PickRequestItem.PICKED_QTY,
                        columnValue: PickRequestItemRepository.getItemData(PickRequestItem.ITEM_BARCODE, data.barcode, PickRequestItem.PICKED_QTY)
                    });
                }
                if(PickService.isPickedAllItems()){
                    self.finishPickItems();
                }
            }
        },
        /**
         * Reset data after pick all items request done
         * @param data
         */
        afterPickItems: function(data){
            PickService.finish();
            this.reloadListing(true);
            this.reloadItemsListing();
            this.reloadRecentListing();
            this.reloadPickRequestListing();
        },
        /**
         * Reload list picking items
         */
        reloadItemsListing: function(){
            var self = this;
            var formData = self.getData();
            if(formData && formData.listing && formData.listing.items){
                var action = {
                    targetName: formData.listing.items,
                    actionName: 'reload'
                };
                self.applyAction(action);
            }
        },
        /**
         * Reload list recent picked
         */
        reloadRecentListing: function(){
            var self = this;
            var formData = self.getData();
            if(formData && formData.listing && formData.listing.recent_picked){
                var action = {
                    targetName: formData.listing.recent_picked,
                    actionName: 'reload'
                };
                self.applyAction(action);
            }
        },
        /**
         * Reload list pick request
         */
        reloadPickRequestListing: function(){
            var self = this;
            var formData = self.getData();
            if(formData && formData.listing && formData.listing.requests){
                self.reloadUiObject(formData.listing.requests);
            }
        },
        /**
         * Mark as picked
         */
        markAsPicked: function(){
            var self = this;
            Confirm({
                title: __('Attention!'),
                content: __("There are some items in the current request not picked. If you mark this request as picked, these items will be moved to Prepare Fulfillment step. Are you sure you want to do this?"),
                actions: {
                    confirm: function () {
                        self.finishPickItems();
                    },
                    always: function (event) {
                        event.stopImmediatePropagation();
                    }
                }
            });
        },
        /**
         * Cancel picking request
         */
        cancel: function(){
            var self = this;
            var data = self.getData();
            if(data.urls){
                var url = data.urls.cancel_picking;
                var params = {
                    data:''
                };
                self.sendRequest(url, params);
            }
        },
        /**
         * Move to need ship
         */
        moveToNeedShip: function(){

        },
        /**
         * Print order items
         */
        print: function(){
            var self = this;
            var data = self.getData();
            if(data.urls){
                var url = data.urls.print_order_items;
                var print_window = window.open(url, 'print_order_items', 'status=1');
                if(!print_window){
                    self.warningMessage(__('Your browser has blocked the automatic popup, please change your browser settings'));
                }
            }

        }
    });
});
