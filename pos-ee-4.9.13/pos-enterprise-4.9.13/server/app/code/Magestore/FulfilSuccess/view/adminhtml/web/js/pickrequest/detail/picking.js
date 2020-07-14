/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'jquery',
    'Magestore_FulfilSuccess/js/full-screen-loader',
    'Magestore_FulfilSuccess/js/service/detail/os-scan-item',
    'Magestore_FulfilSuccess/js/model/pick/item',
    'uiRegistry',
    'uiLayout',
    'mageUtils',
    'prototype'
], function ($t, Alert, Confirm, $D, fullScreenLoader, ScanForm, PickRequestItem, registry, layout, utils) {
   
    window.Picking = Class.create();
    Picking.prototype = {
        /**
         * Initialize object
         */
        initialize: function (params) {
            this.params = params;
            this.pickRequestId = params.pickRequestId ? params.pickRequestId : null;            
            
            this.movetoNeedShipUrl = params.movetoNeedShipUrl ? params.movetoNeedShipUrl : null;            
            this.movetoNeedShipButton = params.movetoNeedShipButton ? params.movetoNeedShipButton : null;  
            
            this.printItemsUrl = params.printItemsUrl ? params.printItemsUrl : null;            
            this.printItemsButton = params.printItemsButton ? params.printItemsButton : null;   
            
            this.markAskPickedAllUrl = params.markAskPickedAllUrl ? params.markAskPickedAllUrl : null;            
            this.markAskPickedAllButton = params.markAskPickedAllButton ? params.markAskPickedAllButton : null;

            this.reloadViewDetailUrl = params.reloadViewDetailUrl ? params.reloadViewDetailUrl : null;
            this.markAskPickedUrl = params.markAskPickedUrl ? params.markAskPickedUrl : null;
            this.markAskPickedButton = params.markAskPickedButton ? params.markAskPickedButton : null;

            this.modalId = params.modalId ? params.modalId : null;
            this.pick_request_listing = params.pick_request_listing ? params.pick_request_listing : null;
            this.recent_picked_listing = params.recent_picked_listing ? params.recent_picked_listing : null;

            this.initActions();
        },

        
        initActions: function() {
            this.moveToNeedShip();
            this.printPickedItems();
            this.markAsPickedAll();
            this.markAsPicked();
        },
        
        markAsPicked: function() {
            var self = this;
            if(!self.pickRequestId || !self.markAskPickedButton) {
                return;
            }
            Event.observe($(self.markAskPickedButton), 'click', function(){
                if(!self.validatePickQty()) {
                    Alert({
                        content: $t('Please enter the picked qty of items.')
                    });
                    return;
                }
                
                Confirm({
                    title: $t('Attention!'),
                    content: $t('If you mark this request as picked, the not-picked items will be moved to Prepare Fulfil step. Are you sure you want to do this?'),
                    actions: {
                        confirm: function () {
                            var params = {
                                id:self.pickRequestId,
                                items:self.getPickedItems()
                            };
                            self.sendRequest(self.markAskPickedUrl, params);

                        },
                        cancel: function () {
                            return false;
                        },
                        always: function () {
                            return false;
                        }
                    }
                });                
            });        
        },

        markAsPickedAll: function() {
            var self = this;
            if(!self.pickRequestId || !self.markAskPickedAllButton) {
                return;
            }
            Event.observe($(self.markAskPickedAllButton), 'click', function(){
                Confirm({
                    content: $t('Are you sure to mark this request as Picked All Items?'),
                    actions: {
                        confirm: function () {
                            location.href= self.markAskPickedAllUrl + 'id/' + self.pickRequestId;
                        },
                        cancel: function () {
                            return false;
                        },
                        always: function () {
                            return false;
                        }
                    }
                });
            });
        },
        
        printPickedItems: function() {
            var self = this;
            if(!self.pickRequestId || !self.printItemsButton) {
                return;
            }
            
            Event.observe($(self.printItemsButton), 'click', function(){
                console.log('printItemsButton');
                window.open(self.printItemsUrl + 'id/' + self.pickRequestId, "printItemsPopup", "scrollbars=no, menubar=no, height=600,width=1024, resizable=no,toolbar=no,status=no");               
            });                     
        },
        
        moveToNeedShip: function() {
            var self = this;
            if(!self.pickRequestId || !self.movetoNeedShipButton) {
                return;
            }
            Event.observe($(self.movetoNeedShipButton), 'click', function(){
                Confirm({
                    content: $t('Are you sure to move remaining items above back to Prepare Fulfil?'),
                    actions: {
                        confirm: function () {
                            location.href= self.movetoNeedShipUrl + 'id/' + self.pickRequestId;
                        },
                        cancel: function () {
                            return false;
                        },
                        always: function () {
                            return false;
                        }
                    }
                });                
            });
        },
        /**
         * Get picked qty
         * @returns {Array}
         */
        getPickedItems: function(){
            var items = [];
            var els = $D('.os_fulfilsuccess_input_picking_on_detail');
            if(els.length > 0){
                els.each(function(){
                    var item = {};
                    item[PickRequestItem.PICK_REQUEST_ITEM_ID] = $D(this).data('itemid');
                    item[PickRequestItem.PICKED_QTY] = $D(this).val();
                    items.push(item);
                });
            }
            return items;
        },
        /**
         * Reload detail popup
         */
        reloadDetailPopup: function(){
            var self = this;
            var url = self.reloadViewDetailUrl;
            if(url){
                var params = {};
                params[PickRequestItem.PICK_REQUEST_ID] = self.pickRequestId;
                fullScreenLoader.startLoader();
                $D.ajax({
                    url: url,
                    data: params,
                    success: function(result){
                        fullScreenLoader.stopLoader();
                        var modalHtml = $D('#' + self.modalId);
                        if(modalHtml) {
                            modalHtml.html(result);
                        }
                    },
                    error: function(error){
                        fullScreenLoader.stopLoader();
                    }
                });
            }
        },
        /**
         * Reload list recent picked
         */
        reloadRecentListing: function(){
            var self = this;
            var targetName = self.recent_picked_listing;
            if(targetName){
                self.reloadUiObject(targetName);
            }
        },
        /**
         * Reload list pick request
         */
        reloadPickRequestListing: function(){
            var self = this;
            var targetName = self.pick_request_listing;
            if(targetName){
                self.reloadUiObject(targetName);
            }
        },
        /**
         * Apply action on target component,
         * but previously create this component from template if it is not existed
         *
         * @param {Object} action - action configuration
         */
        applyAction: function (action) {
            var targetName = action.targetName,
                params = action.params || [],
                actionName = action.actionName,
                target;

            if (!registry.has(targetName)) {
                this.getFromTemplate(targetName);
            }
            target = registry.async(targetName);
            if (target && typeof target === 'function' && actionName) {
                params.unshift(actionName);
                target.apply(target, params);
            }
        },
        /**
         * Create target component from template
         *
         * @param {Object} targetName - name of component,
         * that supposed to be a template and need to be initialized
         */
        getFromTemplate: function (targetName) {
            var parentName = targetName.split('.'),
                index = parentName.pop(),
                child;

            parentName = parentName.join('.');
            child = utils.template({
                parent: parentName,
                name: index,
                nodeTemplate: targetName
            });
            layout([child]);
        },
        /**
         * Reload UI
         * @param targetName
         */
        reloadUiObject: function(targetName){
            var target = registry.get(targetName);
            if (target && typeof target === 'object') {
                target.set('params.t ', Date.now());
            }
        },
        /**
         * Process when an ajax request finish
         * @param response
         */
        requestDone: function(response){
            var self = this;
            if(response.action){
                switch (response.action){
                    case 'mark_as_picked':
                        if(response.success){
                            self.reloadDetailPopup();
                            self.reloadRecentListing();
                            self.reloadPickRequestListing();
                        }
                        break;
                }
            }
        },
        /**
         * Prepare ajax response, show message or something
         * @param response
         */
        processResponse: function(response){
            var self = this;
            if(typeof response == 'string'){
                response = $D.parseJSON(response);
            }
            if(response.message){
                self.processMessage(response);
            }
            self.requestDone(response);
        },
        /**
         * Use to send an ajax request
         * @param url
         * @param params
         */
        sendRequest: function(url, params){
            this.resetMessages();
            if(url){
                var self = this;
                fullScreenLoader.startLoader();
                $D.ajax({
                    url: url,
                    data: params,
                    success: function(result){
                        fullScreenLoader.stopLoader();
                        self.processResponse(result);
                    },
                    error: function(error){
                        fullScreenLoader.stopLoader();
                    }
                });
            }
        },
        /**
         * Process request message
         * @param response
         */
        processMessage: function(response){
            var self = this;
            var el = ScanForm.getMainSelector();
            var warningEl = $D(el).find(ScanForm.getWarningSelector());
            var successEl = $D(el).find(ScanForm.getSuccessSelector());
            if(response.error){
                warningEl.html(response.message);
                warningEl.show();
                successEl.hide();
            }
            if(response.success){
                successEl.html(response.message);
                successEl.show();
                warningEl.hide();
            }
        },
        /**
         * Reset scan message
         */
        resetMessages: function(){
            var self = this;
            var el = ScanForm.getMainSelector();
            var warningEl = $D(el).find(ScanForm.getWarningSelector());
            var successEl = $D(el).find(ScanForm.getSuccessSelector());
            successEl.hide();
            warningEl.hide();
        },
        
        validatePickQty: function(){
            var items = this.getPickedItems();
            for(var i in items){
                var item = items[i];
                if(parseFloat(item[PickRequestItem.PICKED_QTY]) > 0) {
                    return true;
                }
            }
            return false;
        }
    };

});