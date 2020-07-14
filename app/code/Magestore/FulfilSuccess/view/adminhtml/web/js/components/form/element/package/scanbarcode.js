/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magestore_FulfilSuccess/js/components/form/element/input',
    'mage/translate',
    'ko',
    'jquery',
    'Magento_Ui/js/modal/alert'
], function (Input, __, ko, $, Alert) {
    'use strict';

    return Input.extend({
        /**
         * Place holder for input
         */
        placeHolder: ko.observable(__('SCAN TRACKING NUMBER HERE')),
        /**
         * Prefix of Order barcode
         */
        PACK_REQUEST_BARCODE_PREFIX: 'PACK',
        /**
         * Modal id
         */
        MODAL_ID: '',
        /**
         * Inittialize data
         */
        initData: function(){
            var self = this;
            var data = self.getData();
            if(data && data.modal_id){
                self.MODAL_ID = data.modal_id;
            }
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
        checkBarcode: function () {
            var self = this;
            var barcode = self.data();
            if (barcode) {
                barcode = barcode.trim();
                self.resetModal();
                self.reloadModal(barcode);
                self.openModal();
            }
        },
        /**
         * Reset modal html
         */
        updateModal: function(html){
            if($(this.MODAL_ID).length > 0){
                $(this.MODAL_ID).html(html);
            }
        },
        /**
         * Reset modal html
         */
        resetModal: function(){
            if($(this.MODAL_ID).length > 0){
                $(this.MODAL_ID).html('');
            }
        },
        /**
         * Reset modal html
         */
        openModal: function(){
            if($(this.MODAL_ID).length > 0){
                $(this.MODAL_ID).modal('openModal');
            }
        },
        /**
         * Reload modal data
         */
        reloadModal: function(id){
            var self = this;
            var data = self.getData();
            if(data.urls && data.urls.package_detail && id){
                var url = data.urls.package_detail;
                var params = {
                    tracking_number: id
                };
                self.sendRequest(url, params);
            }
        },
        /**
         * Prepare ajax response, show message or something
         * @param response
         */
        processResponse: function(response){
            this.updateModal(response);
        }
    });
});
