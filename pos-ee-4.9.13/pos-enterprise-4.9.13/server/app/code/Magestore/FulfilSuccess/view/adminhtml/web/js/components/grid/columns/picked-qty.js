/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'jquery',
    'Magestore_FulfilSuccess/js/components/grid/columns/number',
    'Magestore_FulfilSuccess/js/service/pick',
    'Magestore_FulfilSuccess/js/model/pick/item',
    'Magestore_FulfilSuccess/js/model/repository/pickRequestItem',
    'Magestore_FulfilSuccess/js/model/event-manager'
], function (ko, $, ColumnNumber, PickService, PickRequestItem, PickRequestItemRepository, Event) {
    'use strict';

    return ColumnNumber.extend({
        /**
         * Constructor
         */
        initialize: function () {
            this._super();
            this.input_id_prefix = 'os_fulfilsuccess_input_picking';
            this.rowdata_primary_key = PickRequestItem.PICK_REQUEST_ITEM_ID;
            this.ROWDATA_PRIMARY_KEY = PickRequestItem.PICK_REQUEST_ITEM_ID;
        },
        /**
         * Get row data
         * @param record
         */
        afterRender: function(input){
            this._super();
            if(input && input.getAttribute('rowdata')){
                var self = this;
                var rowdata = JSON.parse(input.getAttribute('rowdata'));
                input.item_barcode = rowdata[PickRequestItem.ITEM_BARCODE];
                input[self.ROWDATA_PRIMARY_KEY] = rowdata[self.ROWDATA_PRIMARY_KEY];
            }
        },
        /**
         * Process message after actions
         * @param response
         */
        processMessage: function(response){
            if(response){
                if(this.showMessage()){
                    Event.dispatch('os_fulfilsuccess.picking_form.process_message', response);
                }
                Event.dispatch('os_fulfilsuccess.picking_form.pick_item_after', {
                    action: 'pick_item',
                    data: {
                        response: response
                    }
                });
            }
        },
        /**
         * Validate amount when change value
         */
        validateValue: function(dataObject, eventName){
            var self = this;
            var rowData = dataObject.rowData;
            var element = dataObject.element;
            var value = dataObject.value;
            var itemId = false;
            if(rowData){
                itemId = rowData[self.ROWDATA_PRIMARY_KEY];
            }
            if(element){
                itemId = element[self.ROWDATA_PRIMARY_KEY];
            }
            var valid = true;
            switch (eventName){
                case 'change':
                case 'increase':
                    if(itemId){
                        var response = PickService.canPickItem(self.ROWDATA_PRIMARY_KEY, itemId, value);
                        if(response && response.success){
                            response = PickService.pickItem(self.ROWDATA_PRIMARY_KEY, itemId, value);
                            self.processMessage(response);
                        }else{
                            valid = false;
                            self.processMessage({
                                error:true,
                                message:response.message,
                            });
                        }
                    }
                    break;
                case 'decrease':
                    if(itemId){
                        var response = PickService.canCancelItem(self.ROWDATA_PRIMARY_KEY, itemId, value);
                        if(response && response.success){
                            response = PickService.cancelItem(self.ROWDATA_PRIMARY_KEY, itemId, value);
                            self.processMessage(response);
                        }else{
                            valid = false;
                            self.processMessage({
                                error:true,
                                message:response.message,
                            });
                        }
                    }
                    break;
            }
            return valid;
        },
        /**
         * Event click button -
         * @param data
         * @param event
         */
        decrease: function(rowData){
            this._super();
            var self = this;
            var input_id = self.getInputId(rowData);
            var input = self.getJsInputElement(rowData);
            if(input && input.value < 0){
                self.byPassChange(true);
                self.setValue(input_id,0);
            }
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
            var input = event.target;
            if(input && input.getAttribute('rowdata')){
                if($.isNumeric(value)){
                    var rowdata = JSON.parse(input.getAttribute('rowdata'));
                    var requestQty = PickRequestItemRepository.getItemData(self.ROWDATA_PRIMARY_KEY, rowdata[self.ROWDATA_PRIMARY_KEY], PickRequestItem.REQUEST_QTY);
                    var backupvalue = event.target.backupvalue;
                    value = parseFloat(value);
                    if(!self.validateValue({value:value,element:event.target}, 'change')){
                        self.showMessage(false);
                        if(input.value > requestQty){
                            self.setValue(input_id, requestQty);
                        }else{
                            if(backupvalue){
                                self.setValue(input_id, backupvalue);
                            }
                        }
                    }else{
                        self.showMessage(true);
                        event.target.backupvalue = value;
                    }
                }else{
                    self.showMessage(true);
                    event.target.backupvalue = 0;
                    self.setValue(input_id, 0);
                }
            }
        }
    });
});
