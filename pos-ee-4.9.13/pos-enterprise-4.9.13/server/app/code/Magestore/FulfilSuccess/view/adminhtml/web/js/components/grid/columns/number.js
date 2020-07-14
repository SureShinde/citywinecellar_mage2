/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column',
    'ko',
    'jquery',
    'Magestore_FulfilSuccess/js/full-screen-loader',
    'Magestore_FulfilSuccess/js/model/event-manager'
], function (Column, ko, $, fullScreenLoader, Event) {
    'use strict';

    ko.bindingHandlers.initRowData = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var rowData = bindingContext.$row();
            $(element).attr('rowdata',JSON.stringify(rowData));
            $(element).attr('backupvalue',$(element).val());
        },
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {

        }
    };

    return Column.extend({
        defaults: {
            bodyTmpl: 'Magestore_FulfilSuccess/grid/cells/number',
            sortable: false,
            draggable: false,
            additionalClasses: '',
            mainClass: 'os_fulfilsuccess_input',
            placeHolder: '',
            hasControl: true,
            increment: 1,
            input_id_prefix:'os_fulfilsuccess_input',
            rowdata_primary_key:''
        },
        /**
         * Warning message
         */
        warningMessage: ko.observable(),
        /**
         * Success message
         */
        successMessage: ko.observable(),
        /**
         * Flag to show message or not
         */
        showMessage: ko.observable(true),
        /**
         * Not trigger change event
         */
        byPassChange: ko.observable(false),
        /**
         * Constructor
         */
        initialize: function () {
            this._super();
            this.initEvents();
        },
        /**
         * Initialize events
         */
        initEvents: function(){
            var self = this;
            Event.observer('update_column_value', function(event, data){
                if(data && data.primaryValue){
                    var input_id = self.getInputIdByKey(data.primaryValue);
                    if(data.columnIndex && (data.columnIndex == self.index) && input_id){
                        self.setValue(input_id, data.columnValue);
                    }
                }
            });
        },
        /**
         * Get css classes
         * @returns {string}
         */
        getCssClasses: function(){
            return ' '+ this.mainClass +' '+ this.additionalClasses +' ';
        },
        /**
         * Get dynamic id by primary value
         * @param primaryValue
         * @returns {string}
         */
        getInputIdByKey: function(primaryValue){
            var id = "";
            if(primaryValue){
                id = this.input_id_prefix+'_'+primaryValue;
            }
            return id;
        },
        /**
         * Get dynamic id
         * @param rowData
         * @returns {string}
         */
        getInputId: function(rowData){
            var id = "";
            if(rowData && rowData[this.rowdata_primary_key]){
                id = this.input_id_prefix+'_'+rowData[this.rowdata_primary_key];
            }
            return id;
        },
        /**
         * Get input element
         * @param rowData
         * @returns {boolean} || {jQuery element object}
         */
        getJsInputElement: function(rowData){
            var element = false;
            if(rowData){
                var input_id = this.getInputId(rowData);
                if(input_id && $('#'+input_id).length > 0){
                    element = document.getElementById(input_id);
                }
            }
            return element;
        },
        /**
         * Event input focus
         * @param data
         * @param event
         */
        focus: function(el, event){
            var input_id = event.target.id;
            event.target.backupvalue = event.target.value;
            event.target.select();
        },
        /**
         * Event input blur
         * @param data
         * @param event
         */
        blur: function(el, event){
        },
        /**
         * Event input change
         * @param data
         * @param event
         */
        change: function(el, event){
            var self = this;
            var input_id = event.target.id;
            var value = event.target.value;
            var backupvalue = event.target.backupvalue;
            if(self.byPassChange()){
                self.byPassChange(false);
                return false;
            }
            if($.isNumeric(value)){
                value = parseFloat(value)?parseFloat(value):0;
                if(!self.validateValue({value:value,element:event.target}, 'change')){
                    self.showMessage(false);
                    if(backupvalue){
                        self.setValue(input_id, backupvalue);
                    }
                }else{
                    self.showMessage(true);
                    event.target.backupvalue = value;
                }
            }else{
                self.showMessage(true);
                self.setValue(input_id, 0);
                event.target.backupvalue = 0;
            }

        },
        /**
         * Event click button -
         * @param data
         * @param event
         */
        decrease: function(rowData){
            var self = this;
            var input_id = self.getInputId(rowData);
            var value = self.getValue(input_id);
            var input = self.getJsInputElement(input_id);
            if(input){
                input.backupvalue = value;
            }
            value -= parseFloat(self.increment);
            if(self.validateValue({value:value,element:input, rowData:rowData}, 'decrease')){
                self.byPassChange(true);
                self.setValue(input_id,value);
            }
        },
        /**
         * Event click button +
         * @param data
         * @param event
         */
        increase: function(rowData){
            var self = this;
            var input_id = self.getInputId(rowData);
            var value = self.getValue(input_id);
            var input = self.getJsInputElement(input_id);
            if(input){
                input.backupvalue = value;
            }
            value += parseFloat(self.increment);
            if(self.validateValue({value:value,element:input, rowData:rowData}, 'increase')){
                self.byPassChange(true);
                self.setValue(input_id, value);
            }
        },
        /**
         * Validate amount when change value
         */
        validateValue: function(dataObject, eventName){
            return true;
        },
        /**
         *
         * @param data
         * @param event
         */
        afterRender: function(input){
            if(input && input.getAttribute('rowdata')){
                var self = this;
                var rowdata = JSON.parse(input.getAttribute('rowdata'));
                input.id = self.input_id_prefix+'_'+rowdata[self.rowdata_primary_key];
                input.name = self.input_id_prefix+'_'+rowdata[self.rowdata_primary_key];
            }
        },
        /**
         * Remove all messages
         */
        resetMessages: function(){
            this.warningMessage('');
            this.successMessage('');
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
         * Set element value
         * @param value
         */
        setValue: function(input_id, value){
            if(input_id && $('#'+input_id).length > 0){
                var inputEl = $('#'+input_id);
                value = (parseFloat(value))?parseFloat(value):0;
                inputEl.val(value);
                inputEl.change();
            }
        },
        /**
         * Get element value
         * @returns {Number}
         */
        getValue: function(input_id){
            var value = 0;
            if(input_id && $('#'+input_id).length > 0){
                var inputEl = $('#'+input_id);
                value = inputEl.val();
            }
            return parseFloat(value);
        }
    });
});
