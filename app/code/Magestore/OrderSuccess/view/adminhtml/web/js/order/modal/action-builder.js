/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/confirm',    
    'Magento_Ui/js/modal/alert',    
    'mage/translate',
    'uiRegistry',    
], function($, Confirm, Alert, $t, registry){
    'use strict';
    return {
        initAction: function(button) {
            if(!button.url) {
                return;
            }
            var actionBuilder = this;
            button.click = function(){
                var self = this;
                var orderId = $('#current_order_id').val();
                var requestUrl = self.url + 'order_id/' + orderId;
                if(this.confirm) {
                    Confirm({
                        content: $t('Are you sure you want to' + ' ' + this.text +'?'),
                        actions: {
                            confirm: function () {
                                actionBuilder.sendRequest(requestUrl);
                            },
                            cancel: function () {
                                return false;
                            },
                            always: function () {
                                return false;
                            }
                        }
                    });                     
                } else {
                    actionBuilder.sendRequest(requestUrl);
                }
            }.bind(button);
        },
        
        /**
         * Use to send an ajax request
         * @param url
         * @param params
         */
        sendRequest: function(url, params){
            //this.resetMessages();
            if(url){
                var self = this;
                //fullScreenLoader.startLoader();
                $.ajax({
                    url: url,
                    showLoader: true,
                    method: "GET",
                    success: function(result){
                        //fullScreenLoader.stopLoader();
                        self.processResponse(result);
                    },
                    error: function(error){
                        //fullScreenLoader.stopLoader();
                    }
                });
            }
        },   
        
        /**
         * Prepare ajax response, show message or something
         * @param response
         */
        processResponse: function(response){
            var self = this;
            
            if(typeof response == 'string'){
                response = $.parseJSON(response);
            }
            
            if(response.error){
                self.processError(response);
                return;
            }else if(response.message){
                self.processMessage(response);
            }
            
            self.requestDone(response);
        },    
        
        /**
         * Done request
         * @param response
         */
        requestDone: function(response){
            var orderListing = 'os_needverify_listing.os_needverify_listing_data_source';
            this.reloadUiObject(orderListing);
            
            //console.log($.modal());
            
            $('#verify_order_detail_holder').modal('closeModal');
        },       
        
        /**
         * Process request message
         * @param response
         */
        processError: function(response){
            Alert({title: $t('Error'), content: response.message});
        },       
        
        /**
         * Process request message
         * @param response
         */
        processMessage: function(response){

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
    }
});