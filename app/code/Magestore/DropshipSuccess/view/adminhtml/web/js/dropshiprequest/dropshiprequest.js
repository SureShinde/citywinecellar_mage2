/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'prototype',
], function ($t, Alert) {

    window.DropshipRequest = Class.create();
    DropshipRequest.prototype = {
        /**
         * Initialize object
         */
        initialize: function (params) {
            this.params = params;
            this.dropshipRequestQtyClass = params.dropshipRequestQtyClass ? params.dropshipRequestQtyClass : null;
            this.dropshipRequestSupplierClass = params.dropshipRequestSupplierClass ? params.dropshipRequestSupplierClass : null;
            this.dropshipRequestProductClass = params.dropshipRequestProductClass ? params.dropshipRequestProductClass : null;
            this.dropshipRequestItemClass = params.dropshipRequestItemClass ? params.dropshipRequestItemClass : null;
            this.availabelSuppliers = params.availabelSuppliers ? params.availabelSuppliers : null;
            this.initObservers();
            this.initValidation();
            this.checkSelectedItems();
        },
        
        initObservers: function() {
            this.initDropshipRequestSupplierChangeObserver();
            this.initDropshipRequestQtyChangeObserver();
        },
        
        initValidation: function() {
            var self = this;
            var qtyValidation = {
                errorMessage: $t('There are Supplier(s) which is not enough available qty to Dropship. Please change qty to dropship or select another Supplier!'),
                validate: function(){
                    var supplierSelectors = $$('.' + self.dropshipRequestSupplierClass);
                    for(var i in supplierSelectors) {
                        if(typeof supplierSelectors[i] == 'function') {
                            continue;
                        }
                        if(!self.validateDropshipRequestQty(supplierSelectors[i])) {
                            return false;
                        }
                    }
                    return true;
                }
            };
            packaging.addCustomValidation(qtyValidation);
        },
        
        initDropshipRequestSupplierChangeObserver: function() {
            if(!this.dropshipRequestSupplierClass) {
                return
            }
            var self = this;
            var supplierSelectors = $$('.'+this.dropshipRequestSupplierClass);
            
            for(var i in supplierSelectors) {
                if(typeof supplierSelectors[i] == 'function') {
                    continue;
                }
                Event.observe(supplierSelectors[i], 'change', function(event){
                    if(!self.validateDropshipRequestQty(event.target)) {
                        Alert({content: $t('The available qty is not enough to dropship. Please change qty to dropship or select another Supplier!')});
                    }
                    self.changeBundleItemSupplier(event.target);
                });
            }
        },
        
        initDropshipRequestQtyChangeObserver: function() {
            if(!this.dropshipRequestQtyClass) {
                return;
            }
            var self = this;
            var dropshipRequestQtyInputs = $$('.'+this.dropshipRequestQtyClass);
            
            for(var i in dropshipRequestQtyInputs) {
                if(typeof dropshipRequestQtyInputs[i] == 'function') {
                    continue;
                }
                Event.observe(dropshipRequestQtyInputs[i], 'change', function(event){
                    var supplierSelector = event.target.up('.data-row').down('.'+self.dropshipRequestSupplierClass);
                    if(!self.validateDropshipRequestQty(supplierSelector)) {
                        Alert({content: $t('The available qty is not enough to request. Please change qty to dropship or select another Supplier!')});
                    }
                    var parentRequest = event.target;
                    var maxRequestQty = parentRequest.up().down('.max-dropship-request-qty');
                    if (parseFloat(event.target.value) < 0) {
                        event.target.value = 0;
                    }
                    if (parseFloat(event.target.value) > parseFloat(maxRequestQty.value)) {
                        event.target.value = maxRequestQty.value;
                    }
                    self.changeBundleItemQty(event.target);
                });
            }            
        },
        
        changeBundleItemSupplier: function(supplierSelector) {
            var dropshipRequestItemInput = supplierSelector.up().down('.' + this.dropshipRequestItemClass);
            if(!dropshipRequestItemInput) {
                return;
            }
            var itemId = dropshipRequestItemInput.value;
            var childWHSelectors = $$('.child-wh-' + itemId);
            for(var i in childWHSelectors) {
                if(typeof childWHSelectors[i] == 'function') {
                    continue;
                }
                var childWHSelector = childWHSelectors[i];
                childWHSelector.value = supplierSelector.value;
                var event = new Event('change');
                childWHSelector.dispatchEvent(event);                
            }             
        },
        
        changeBundleItemQty: function(dropshipRequestQtyInput) {
            var dropshipRequestItemInput = dropshipRequestQtyInput.up('.data-row').down('.' + this.dropshipRequestItemClass);
            if(!dropshipRequestItemInput) {
                return;
            }
            var itemId = dropshipRequestItemInput.value;
            var childItemQtyInputs = $$('.child-qty-' + itemId);
            for(var i in childItemQtyInputs) {
                if(typeof childItemQtyInputs[i] == 'function') {
                    continue;
                }
                var childItemInput = childItemQtyInputs[i];
                var bundleQtyInput = childItemInput.up().down('.bundle-qty');
                var bundleQty = parseFloat(bundleQtyInput.value);
                childItemInput.value = bundleQty * dropshipRequestQtyInput.value;
                var event = new Event('change');
                childItemInput.dispatchEvent(event);                
            }             
        },
        
        validateDropshipRequestQty: function(supplierSelector) {
            return true;
            var dropshipRequestQtyInput = supplierSelector.up('.data-row').down('.' + this.dropshipRequestQtyClass);
            var dropshipRequestItemInput = supplierSelector.up().down('.' + this.dropshipRequestItemClass);
            var dropshipRequestQty = parseFloat(dropshipRequestQtyInput.value);
            var itemId = dropshipRequestItemInput.value;
            var supplierId = supplierSelector.value;
            var availableQty = this.availabelSuppliers[itemId][supplierId]['available_qty'];

            if(dropshipRequestQty > availableQty) {
                supplierSelector.addClassName('_error');
                return false;
            }            
            supplierSelector.removeClassName('_error');
            return true;
        },

        checkSelectedItems: function(){
            if(!this.dropshipRequestSupplierClass) {
                return
            }
            var self = this;
            var supplierSelectors = $$('.'+this.dropshipRequestSupplierClass);

            for(var i in supplierSelectors) {
                if(typeof supplierSelectors[i] == 'function') {
                    continue;
                }
                var selected = false;
                var supplierSelector = supplierSelectors[i];
                var pickItemInput = supplierSelector.up().down('.' + this.dropshipRequestItemClass);
                var itemId = pickItemInput.value;
                var selectedSupplierElement = null;

                var selectedItemDiv = supplierSelector.up('.admin__page-subsection').next('.package_items');
                if(selectedItemDiv) {
                    var selectedItems = selectedItemDiv.select('.' + this.dropshipRequestItemClass);
                    for(var i in selectedItems) {
                        if(selectedItems[i].value == itemId) {
                            selected = true;
                            selectedSupplierElement = selectedItems[i].next('.' + this.dropshipRequestSupplierClass);
                            break;
                        }
                    }
                }
                if(selected) {
                    supplierSelector.hide();
                    var selectedSupplier = selectedSupplierElement.options[selectedSupplierElement.selectedIndex].innerHTML;
                    supplierSelector.insert({after:'<span>'+selectedSupplier+'</span>'});
                }
            }
        }
    }
});