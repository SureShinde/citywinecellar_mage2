/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'prototype',
], function ($t, Alert) {

    window.PickRequest = Class.create();
    PickRequest.prototype = {
        /**
         * Initialize object
         */
        initialize: function (params) {
            this.params = params;
            this.pickQtyClass = params.pickQtyClass ? params.pickQtyClass : null;
            this.pickWarehouseClass = params.pickWarehouseClass ? params.pickWarehouseClass : null;
            this.pickProductClass = params.pickProductClass ? params.pickProductClass : null;
            this.pickItemClass = params.pickItemClass ? params.pickItemClass : null;
            this.availabelWarehouses = params.availabelWarehouses ? params.availabelWarehouses : null;
            this.initObservers();
            this.initValidation();
            this.checkSelectedItems();
        },

        initObservers: function () {
            this.initPickWarehouseChangeObserver();
            this.innitPickQtyChangeObserver();
        },

        initValidation: function () {
            var self = this;
            var qtyValidation = {
                errorMessage: $t('There are Warehouse(s) which is not enough available qty to pick. Please change qty to pick or select another Warehouse!'),
                validate: function () {
                    var warehouseSelectors = $$('.' + self.pickWarehouseClass);
                    for (var i in warehouseSelectors) {
                        if (typeof warehouseSelectors[i] == 'function') {
                            continue;
                        }
                        if (!self.validatePickQty(warehouseSelectors[i])) {
                            return false;
                        }
                    }
                    return true;
                }
            };
            packaging.addCustomValidation(qtyValidation);
        },

        initPickWarehouseChangeObserver: function () {
            if (!this.pickWarehouseClass) {
                return
            }
            var self = this;
            var warehouseSelectors = $$('.' + this.pickWarehouseClass);

            for (var i in warehouseSelectors) {
                if (typeof warehouseSelectors[i] == 'function') {
                    continue;
                }
                Event.observe(warehouseSelectors[i], 'change', function (event) {
                    if (!self.validatePickQty(event.target)) {
                        Alert({content: $t('The available qty is not enough to pick. Please change qty to pick or select another Warehouse!')});
                    }
                    self.changeBundleItemWarehouse(event.target);
                });
            }
        },

        innitPickQtyChangeObserver: function () {
            if (!this.pickQtyClass) {
                return;
            }
            var self = this;
            var pickQtyInputs = $$('.' + this.pickQtyClass);

            for (var i in pickQtyInputs) {
                if (typeof pickQtyInputs[i] == 'function') {
                    continue;
                }
                Event.observe(pickQtyInputs[i], 'change', function (event) {
                    var warehouseSelector = event.target.up('.data-row').down('.' + self.pickWarehouseClass);
                    if (!self.validatePickQty(warehouseSelector)) {
                        Alert({content: $t('The available qty is not enough to pick. Please change qty to pick or select another Warehouse!')});
                    }
                    self.changeBundleItemQty(event.target);
                });
            }
        },

        changeBundleItemWarehouse: function (warehouseSelector) {
            var pickItemInput = warehouseSelector.up().down('.' + this.pickItemClass);
            if (!pickItemInput) {
                return;
            }
            var itemId = pickItemInput.value;
            var childWHSelectors = $$('.child-wh-' + itemId);
            for (var i in childWHSelectors) {
                if (typeof childWHSelectors[i] == 'function') {
                    continue;
                }
                var childWHSelector = childWHSelectors[i];
                childWHSelector.value = warehouseSelector.value;
                var event = new Event('change');
                childWHSelector.dispatchEvent(event);
            }
        },

        changeBundleItemQty: function (pickQtyInput) {
            var pickItemInput = pickQtyInput.up('.data-row').down('.' + this.pickItemClass);
            if (!pickItemInput) {
                return;
            }
            var itemId = pickItemInput.value;
            var childItemQtyInputs = $$('.child-qty-' + itemId);
            for (var i in childItemQtyInputs) {
                if (typeof childItemQtyInputs[i] == 'function') {
                    continue;
                }
                var childItemInput = childItemQtyInputs[i];
                var bundleQtyInput = childItemInput.up().down('.bundle-qty');
                var bundleQty = parseFloat(bundleQtyInput.value);
                childItemInput.value = bundleQty * pickQtyInput.value;
                var event = new Event('change');
                childItemInput.dispatchEvent(event);
            }
        },

        validatePickQty: function (warehouseSelector) {
            if(!warehouseSelector.up('.data-row').down('input[type=checkbox]').checked)
                return true;
            var pickQtyInput = warehouseSelector.up('.data-row').down('.' + this.pickQtyClass);
            var pickItemInput = warehouseSelector.up().down('.' + this.pickItemClass);
            var pickQty = parseFloat(pickQtyInput.value);
            var itemId = pickItemInput.value;

            var warehouseId = warehouseSelector.value;
            if (!warehouseId)
                return false;
            var availableQty = this.availabelWarehouses[itemId][warehouseId]['available_qty'];

            pickQty = this.collectTotalPickQty(itemId, warehouseId);

            if (pickQty > availableQty) {
                warehouseSelector.addClassName('_error');
                return false;
            }
            warehouseSelector.removeClassName('_error');
            return true;
        },

        collectTotalPickQty: function (itemId, warehouseId) {
            var pickQty = 0;
            var pickWarehouseClass = this.pickWarehouseClass + '-' + itemId;
            var warehouseSelectors = $$('.' + pickWarehouseClass);

            for (var i in warehouseSelectors) {
                if (typeof warehouseSelectors[i] == 'function') {
                    continue;
                }
                var warehouseSelector = warehouseSelectors[i];
                if (warehouseSelector.value == warehouseId) {
                    var pickQtyInput = warehouseSelector.up('.data-row').down('.' + this.pickQtyClass);
                    pickQty += parseFloat(pickQtyInput.value);
                }
            }
            return pickQty;
        },

        checkSelectedItems: function () {
            if (!this.pickWarehouseClass) {
                return
            }
            var self = this;
            var warehouseSelectors = $$('.' + this.pickWarehouseClass);

            for (var i in warehouseSelectors) {
                if (typeof warehouseSelectors[i] == 'function') {
                    continue;
                }
                var selected = false;
                var warehouseSelector = warehouseSelectors[i];
                var pickItemInput = warehouseSelector.up().down('.' + this.pickItemClass);
                var itemId = pickItemInput.value;
                var selectedWarehouseElement = null;

                var selectedItemDiv = warehouseSelector.up('.admin__page-subsection').next('.package_items');
                if (selectedItemDiv) {
                    var selectedItems = selectedItemDiv.select('.' + this.pickItemClass);
                    for (var i in selectedItems) {
                        if (selectedItems[i].value == itemId) {
                            selected = true;
                            selectedWarehouseElement = selectedItems[i].next('.' + this.pickWarehouseClass);
                            break;
                        }
                    }
                }
                if (selected) {
                    warehouseSelector.hide();
                    warehouseSelector.value = selectedWarehouseElement.value;
                    var selectedWarehouse = selectedWarehouseElement.options[selectedWarehouseElement.selectedIndex].innerHTML;
                    warehouseSelector.insert({after: '<span>' + selectedWarehouse + '</span>'});
                }
            }
        }
    }
});