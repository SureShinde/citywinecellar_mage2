/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'prototype',
], function ($t, Alert) {

    window.Bundle = Class.create();
    Bundle.prototype = {
        /**
         * Initialize object
         */
        initialize: function (params) {
            this.params = params;
            this.packQtyClass = params.packQtyClass ? params.packQtyClass : null;
            this.packItemClass = params.packItemClass ? params.packItemClass : null;
            this.initObservers();
        },
        
        initObservers: function() {
            this.innitPackQtyChangeObserver();
        },

        innitPackQtyChangeObserver: function() {
            if(!this.packQtyClass) {
                return;
            }
            var self = this;
            var packQtyInputs = $$('.'+this.packQtyClass);
            
            for(var i in packQtyInputs) {
                if(typeof packQtyInputs[i] == 'function') {
                    continue;
                }
                Event.observe(packQtyInputs[i], 'change', function(event){
                    self.changeBundleItemQty(event.target);
                });
            }            
        },
        
        changeBundleItemQty: function(packQtyInput) {
            var packItemInput = packQtyInput.up('.data-row').down('.' + this.packItemClass);
            if(!packItemInput) {
                return;
            }
            var itemId = packItemInput.value;
            var childItemQtyInputs = $$('.child-qty-' + itemId);
            for(var i in childItemQtyInputs) {
                if(typeof childItemQtyInputs[i] == 'function') {
                    continue;
                }
                var childItemInput = childItemQtyInputs[i];
                var bundleQtyInput = childItemInput.up().down('.bundle-qty');
                var bundleQty = parseFloat(bundleQtyInput.value);
                childItemInput.value = bundleQty * pickQtyInput.value;
                var event = new Event('change');
                childItemInput.dispatchEvent(event);                
            }             
        }
    }
});