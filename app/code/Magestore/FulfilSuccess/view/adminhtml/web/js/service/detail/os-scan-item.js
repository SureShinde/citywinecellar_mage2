/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'mage/translate',
    'Magestore_FulfilSuccess/js/model/pick/item',
    'Magestore_FulfilSuccess/js/model/repository/pickRequestItem'
], function ($, ko, __, PickRequestItem, PickRequestItemRepository) {
    'use strict';

    return {
        EXTERNAL_SELECTOR:{
            INCREASE_BUTTON:".col-picked-qty .os-increase-button"
        },
        ROLE:{
            CONTAINER: 'os-scan-container',
            MESSAGE:{
                WARNING: 'os-scan-warning-message',
                SUCCESS: 'os-scan-success-message'
            },
            INPUT: 'os-scan-input'
        },
        DATA:{
            URL:'url',
            SOURCE:'data',
            ROLE:'role',
            ID:'id'
        },
        sources: {},
        /**
         * Get container selector
         * @returns {string}
         */
        getMainSelector: function(){
            var self = this;
            var selector = '[data-'+self.DATA.ROLE+'="'+self.ROLE.CONTAINER+'"]';
            return selector;
        },
        /**
         * Get warning message container selector
         * @returns {string}
         */
        getWarningSelector: function(){
            var self = this;
            var selector = '[data-'+self.DATA.ROLE+'="'+self.ROLE.MESSAGE.WARNING+'"]';
            return selector;
        },
        /**
         * Get success message container selector
         * @returns {string}
         */
        getSuccessSelector: function(){
            var self = this;
            var selector = '[data-'+self.DATA.ROLE+'="'+self.ROLE.MESSAGE.SUCCESS+'"]';
            return selector;
        },
        /**
         * Get input selector
         * @returns {string}
         */
        getInputSelector: function(){
            var self = this;
            var selector = '[data-'+self.DATA.ROLE+'="'+self.ROLE.INPUT+'"]';
            return selector;
        },
        /**
         * Generate dinamyc ID
         * @returns {number|*}
         */
        generateId: function(){
            return $.now();
        },
        /**
         * Get elelement and init event
         */
        init: function(source){
            var self = this;
            var selector = self.getMainSelector();
            if($(selector).length > 0){
                $(selector).each(function(el, index){
                    self.initEvent(this);
                    var inputEl = $(this).find(self.getInputSelector());
                    inputEl.focus();
                    if(inputEl.length > 0){
                        var generatedId = self.generateId() + index;
                        var id = inputEl.data(self.DATA.ID);
                        if(!id){
                            inputEl.attr('data-id', generatedId);
                            id = generatedId;
                        }
                        self.sources[id] = JSON.parse(source);
                    }
                });
            }
        },
        /**
         * Init event
         * @param el
         */
        initEvent: function(el){
            var self = this;
            var warningEl = $(el).find(self.getWarningSelector());
            var successEl = $(el).find(self.getSuccessSelector());
            var inputEl = $(el).find(self.getInputSelector());
            if(inputEl.length > 0){
                inputEl.change(function(){
                    var result = self.scan(this);
                    if(result.success){
                        if(result.message){
                            successEl.html(result.message);
                            successEl.show();
                        }else{
                            successEl.hide();
                        }
                        warningEl.hide();
                    }else{
                        if(result.message){
                            warningEl.html(result.message);
                            warningEl.show();
                        }else{
                            warningEl.hide();
                        }
                        successEl.hide();
                    }
                });
            }
        },
        /**
         * Scan item
         * @param el
         */
        scan: function(el){
            var self = this;
            var sourceId = $(el).data(self.DATA.ID);
            var source = self.sources[sourceId];
            var value = $(el).val();
            var result = {
                success: true,
                message: ''
            };
            value = (value)?value.trim():"";
            if(source && source.length > 0){
                var item = ko.utils.arrayFirst(source, function(item) {
                    return (PickRequestItemRepository.isValidItem(item, PickRequestItem.ITEM_BARCODE, value));
                });
                if(item){
                    var itemId = item[PickRequestItem.PICK_REQUEST_ITEM_ID];
                    self.pickItem(itemId);
                }else{
                    result.success = false;
                    result.message = __('This item does not exist on this order');
                }
            }else{
                result.success = false;
                result.message = __('Data not found');
            }
            $(el).val("");
            return result;
        },
        /**
         * Pick item scanned
         * @param itemId
         */
        pickItem: function(itemId){
            var self = this;
            var increaseBtns = $(self.EXTERNAL_SELECTOR.INCREASE_BUTTON+'[data-target="os_picked_items_'+itemId+'"]');
            if(increaseBtns.length > 0){
                increaseBtns.click();
            }
        }
    };
});
