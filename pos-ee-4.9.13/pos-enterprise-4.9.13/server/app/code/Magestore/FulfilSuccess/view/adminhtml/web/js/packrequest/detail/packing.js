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
    'Magestore_FulfilSuccess/js/packrequest/detail/scan-item',
    'Magestore_FulfilSuccess/js/model/pack/item',
    'uiRegistry',
    'uiLayout',
    'mageUtils',
    'prototype'
], function ($t, Alert, Confirm, $D, fullScreenLoader, ScanForm, PackRequestItem, registry, layout, utils) {

    window.Packing = Class.create();
    Packing.prototype = {
        /**
         * Initialize object
         */
        initialize: function (params) {
            this.params = params;
            this.packRequestId = params.packRequestId ? params.packRequestId : null;

            this.movetoPickUrl = params.movetoPickUrl ? params.movetoPickUrl : null;
            this.movetoPickButton = params.movetoPickButton ? params.movetoPickButton : null;

            this.printItemsUrl = params.printItemsUrl ? params.printItemsUrl : null;
            this.printItemsButton = params.printItemsButton ? params.printItemsButton : null;

            this.markAskPackedAllUrl = params.markAskPackedAllUrl ? params.markAskPackedAllUrl : null;
            this.markAskPackedAllButton = params.markAskPackedAllButton ? params.markAskPackedAllButton : null;

            this.reloadViewDetailUrl = params.reloadViewDetailUrl ? params.reloadViewDetailUrl : null;

            this.modalId = params.modalId ? params.modalId : null;
            this.pack_request_listing = params.pack_request_listing ? params.pack_request_listing : null;
            this.recent_packed_listing = params.recent_packed_listing ? params.recent_packed_listing : null;

            this.initActions();
        },


        initActions: function() {
            this.moveToPick();
            this.printPackedItems();
        },

        printPackedItems: function() {
            var self = this;
            if(!self.packRequestId || !self.printItemsButton) {
                return;
            }

            Event.observe($(self.printItemsButton), 'click', function(){
                console.log('printItemsButton');
                //window.open(self.printItemsUrl + 'id/' + self.packRequestId, "printItemsPopup", "scrollbars=no, menubar=no, height=600,width=1024, resizable=no,toolbar=no,status=no");
                window.location.assign(self.printItemsUrl + 'id/' + self.packRequestId);
            });
        },

        moveToPick: function() {
            var self = this;
            if(!self.packRequestId || !self.movetoPickButton) {
                return;
            }
            Event.observe($(self.movetoPickButton), 'click', function(){
                Confirm({
                    content: $t('Are you sure to move remaining items above back to Pick?'),
                    actions: {
                        confirm: function () {
                            location.href= self.movetoPickUrl + 'id/' + self.packRequestId;
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
         * Get packed qty
         * @returns {Array}
         */
        getPackedItems: function(){
            var items = [];
            var els = $D('.os_fulfilsuccess_input_packing_on_detail');
            if(els.length > 0){
                els.each(function(){
                    var item = {};
                    item[PackRequestItem.PACK_REQUEST_ITEM_ID] = $D(this).data('itemid');
                    item[PackRequestItem.PACKED_QTY] = $D(this).val();
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
                params[PackRequestItem.PACK_REQUEST_ID] = self.packRequestId;
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
         * Reload list recent packed
         */
        reloadRecentListing: function(){
            var self = this;
            var targetName = self.recent_packed_listing;
            if(targetName){
                self.reloadUiObject(targetName);
            }
        },
        /**
         * Reload list pack request
         */
        reloadPackRequestListing: function(){
            var self = this;
            var targetName = self.pack_request_listing;
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
                    case 'mark_as_packed':
                        if(response.success){
                            self.reloadDetailPopup();
                            self.reloadRecentListing();
                            self.reloadPackRequestListing();
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
        }
    };

});