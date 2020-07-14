/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/form/element/text',
    'Magestore_FulfilSuccess/js/full-screen-loader',
    'mage/translate',
    'uiRegistry',
    'uiLayout',
    'mageUtils'
], function ($, ko, _, Text, fullScreenLoader, __, registry, layout, utils) {
    'use strict';

    return Text.extend({
        /**
         * Input value
         */
        data: ko.observable(),
        /**
         * Place holder text
         */
        placeHolder: ko.observable(),
        /**
         * Warning message
         */
        warningMessage: ko.observable(),
        /**
         * Success message
         */
        successMessage: ko.observable(),
        /**
         * Constructor
         */
        initialize: function () {
            this._super();
            var self = this;
            self.showWarning = ko.pureComputed(function(){
                return (self.warningMessage())?true:false;
            });
            self.showSuccess = ko.pureComputed(function(){
                return (self.successMessage())?true:false;
            });
            this.initData();
            this.initEvents();
        },
        /**
         * Inittialize data
         */
        initData: function(){
        },
        /**
         * Inittialize event
         */
        initEvents: function(){
        },
        /**
         * Reset input value
         */
        resetValue: function(){
            this.data("");
        },
        /**
         * Event when input value change
         * @param data
         * @param event
         */
        change: function(data, event){
            this.data(event.target.value);
        },
        /**
         * Process when an ajax request finish
         * @param response
         */
        requestDone: function(response){

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
            if(response.message){
                if(response.error){
                    self.warningMessage(response.message);
                }
                if(response.success){
                    self.successMessage(response.message);
                }
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
                $.ajax({
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
         * Get data of this component, normally send from data provider
         * @returns {Array}
         */
        getData: function(){
            var data = [];
            if(this.source && this.source.data){
                data = (this.source.data[this.index])?this.source.data[this.index]:[];
            }
            return data;
        },
        /**
         * Remove all messages
         */
        resetMessages: function(){
            this.warningMessage('');
            this.successMessage('');
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
        }
    });
});
