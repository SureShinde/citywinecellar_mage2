/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $, $H */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedProducts = config.selectedProducts,
            hiddenInputField = config.hiddenInputField,
            //editAbleFields = config.editAbleFields,
            supplierProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $(hiddenInputField).value = Object.toJSON(supplierProducts);

        /**
         * Register Supplier Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerSupplierProduct(grid, element, checked) {
            //if (checked) {
            //    if (element.positionElement) {
            //        element.positionElement.disabled = false;
            //        supplierProducts.set(element.value, element.positionElement.value);
            //    }
            //} else {
            //    if (element.positionElement) {
            //        element.positionElement.disabled = true;
            //    }
            //    supplierProducts.unset(element.value);
            //}
            //$(hiddenInputField).value = Object.toJSON(supplierProducts);
            //grid.reloadParams = {
            //    'selected_products[]': supplierProducts.keys()
            //};

            if (checked) {
                var value = {};
                console.log(editAbleFields)
                //for (var i=0; i < editAbleFields.length; i++) {
                //    console.log(editAbleFields);
                //}
                if (element.costElement) {
                    element.costElement.show();
                    element.costElement.up('div').down('span').hide();
                    element.costElement.disabled = false;
                    value.cost = element.costElement.value;
                    supplierProducts.set(element.value, JSON.stringify(value));
                }
                if (element.taxElement) {
                    element.taxElement.show();
                    element.taxElement.up('div').down('span').hide();
                    element.taxElement.disabled = false;
                    value.tax = element.taxElement.value;
                    supplierProducts.set(element.value, JSON.stringify(value));
                }
            } else {
                if (element.costElement) {
                    element.costElement.hide();
                    element.costElement.up('div').down('span').show();
                    element.costElement.disabled = true;
                }
                if (element.taxElement) {
                    element.taxElement.hide();
                    element.taxElement.up('div').down('span').show();
                    element.taxElement.disabled = true;
                }
                supplierProducts.unset(element.value);
            }
            $(hiddenInputField).value = Object.toJSON(supplierProducts);
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function supplierProductRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         * Change product position
         *
         * @param {String} event
         */
        function positionChange(event) {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                supplierProducts.set(element.checkboxElement.value, element.value);
                $(hiddenInputField).value = Object.toJSON(supplierProducts);
            }
        }

        /**
         * Initialize supplier product row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function supplierProductRowInit(grid, row) {
            //var checkbox = $(row).getElementsByClassName('checkbox')[0],
            //    position = $(row).getElementsByClassName('input-text')[0];
            //
            //if (checkbox && position) {
            //    checkbox.positionElement = position;
            //    position.checkboxElement = checkbox;
            //    position.disabled = !checkbox.checked;
            //    position.tabIndex = tabIndex++;
            //    Event.observe(position, 'keyup', positionChange);
            //}
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                cost = $(row).down('input[name=cost]'),
                tax = $(row).down('input[name=tax]'),
                viewButton = $(row).down('a[class=view_infor]');

            if (checkbox) {
                if (cost || tax) {
                    if (cost) {
                        checkbox.costElement = cost;
                        cost.checkboxElement = checkbox;
                        cost.disabled = !checkbox.checked;
                        cost.tabIndex = tabIndex++;
                        Event.observe(cost, 'keyup', function(event) {
                            changeColumn(event, 'cost');
                        });
                    }
                    if (tax) {
                        checkbox.taxElement = tax;
                        tax.checkboxElement = checkbox;
                        tax.disabled = !checkbox.checked;
                        tax.tabIndex = tabIndex++;
                        Event.observe(tax, 'keyup', function(event) {
                            changeColumn(event, 'tax');
                        });
                    }
                }
                var values = supplierProducts.get(checkbox.value);
                if(values){
                    var values = JSON.parse(values);
                    if(values.cost)
                        checkbox.costElement.value = values.cost;
                    if(values.tax)
                        checkbox.taxElement.value = values.tax;
                    gridJsObject.setCheckboxChecked(checkbox, true);
                };
            }
        }

        /**
         * Change product total qty
         *
         * @param {String} event
         */
        function changeColumn(event, column) {
            var element = Event.element(event);
            if (element && element.checkboxElement && element.checkboxElement.checked) {
                if(element.value == '' || isNaN(element.value) || element.value<0){
                    element.value = 0;
                    element.select();
                }
                var value = JSON.parse(supplierProducts.get(element.checkboxElement.value));
                value = value ? value : {};
                value[column] = element.value;
                supplierProducts.set(element.checkboxElement.value, JSON.stringify(value));
                $(hiddenInputField).value = Object.toJSON(supplierProducts);
            }
        }

        gridJsObject.rowClickCallback = supplierProductRowClick;
        gridJsObject.initRowCallback = supplierProductRowInit;
        gridJsObject.checkboxCheckCallback = registerSupplierProduct;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                supplierProductRowInit(gridJsObject, row);
            });
        }
    };
});
