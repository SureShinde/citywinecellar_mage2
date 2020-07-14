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
            editAbleFields = config.editAbleFields,
            hiddenField = config.hiddenField,
            managerProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $(hiddenField).value = Object.toJSON(managerProducts);
        //for(var i = 0; i < editAbleFields.length; i++) {
        //    console.log(editAbleFields[i]);
        //}

        /**
         * Register Supplier Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerManagerProduct(grid, element, checked) {
            if (checked) {
                var value = {};
                if (element.costElement) {
                    element.costElement.show();
                    element.costElement.up('div').down('span').hide();
                    element.costElement.disabled = false;
                    value.cost = element.costElement.value;
                    managerProducts.set(element.value, JSON.stringify(value));
                }
                if (element.taxElement) {
                    element.taxElement.show();
                    element.taxElement.up('div').down('span').hide();
                    element.taxElement.disabled = false;
                    value.tax = element.taxElement.value;
                    managerProducts.set(element.value, JSON.stringify(value));
                }
                if (element.product_supplier_skuElement) {
                    element.product_supplier_skuElement.show();
                    element.product_supplier_skuElement.up('div').down('span').hide();
                    element.product_supplier_skuElement.disabled = false;
                    value.product_supplier_sku = element.product_supplier_skuElement.value;
                    managerProducts.set(element.value, JSON.stringify(value));
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
                if (element.product_supplier_skuElement) {
                    element.product_supplier_skuElement.hide();
                    element.product_supplier_skuElement.up('div').down('span').show();
                    element.product_supplier_skuElement.disabled = true;
                }
                managerProducts.unset(element.value);
            }
            $(hiddenField).value = Object.toJSON(managerProducts);
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function managerProductRowClick(grid, event) {
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
                managerProducts.set(element.checkboxElement.value, element.value);
                $(hiddenField).value = Object.toJSON(managerProducts);
            }
        }

        /**
         * Initialize manager product row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function managerProductRowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                cost = $(row).down('input[name=cost]'),
                tax = $(row).down('input[name=tax]'),
                product_supplier_sku = $(row).down('input[name=product_supplier_sku]'),
                deleteButton = $(row).down('a[class=delete_item]'),
                viewButton = $(row).down('a[class=view_infor]');

            if (checkbox) {
                if (cost || tax || product_supplier_sku) {
                    if (cost) {
                        checkbox.costElement = cost;
                        cost.checkboxElement = checkbox;
                        cost.disabled = !checkbox.checked;
                        cost.tabIndex = tabIndex++;
                        Event.observe(cost, 'keyup', function(event) {
                            changeColumn(event, 'cost', 'number');
                        });
                    }
                    if (tax) {
                        checkbox.taxElement = tax;
                        tax.checkboxElement = checkbox;
                        tax.disabled = !checkbox.checked;
                        tax.tabIndex = tabIndex++;
                        Event.observe(tax, 'keyup', function(event) {
                            changeColumn(event, 'tax', 'number');
                        });
                    }
                    if (product_supplier_sku) {
                        checkbox.product_supplier_skuElement = product_supplier_sku;
                        product_supplier_sku.checkboxElement = checkbox;
                        product_supplier_sku.disabled = !checkbox.checked;
                        product_supplier_sku.tabIndex = tabIndex++;
                        Event.observe(product_supplier_sku, 'keyup', function(event) {
                            changeColumn(event, 'product_supplier_sku', 'text');
                        });
                    }
                }
                var values = managerProducts.get(checkbox.value);
                if(values){
                    var values = JSON.parse(values);
                    if(values.cost)
                        checkbox.costElement.value = values.cost;
                    if(values.tax)
                        checkbox.taxElement.value = values.tax;
                    if(values.product_supplier_sku)
                        checkbox.product_supplier_skuElement.value = values.product_supplier_sku;
                    gridJsObject.setCheckboxChecked(checkbox, true);
                };
                if (checkbox.checked) {
                    gridJsObject.setCheckboxChecked(checkbox, true);
                }
            }
        }

        function deleteItem(event){
            event.stopPropagation();
            var purchaseItem = event.element().getAttribute('value'),
                productId = event.element().getAttribute('product_id');
            var ajaxSettings = {
                url: deleteUrl + (deleteUrl.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ),
                showLoader: true,
                method: 'post',
                data: {id: purchaseItem, product_id: productId},
                dataType: 'json',
                complete:  function(data, textStatus, transport) {
                    if(data.responseText > 0){
                        selectProductObject.unset(productId);
                        $(hiddenInputField).value = Object.toJSON(selectProductObject);
                    }
                    gridJsObject.doFilter()
                }
            };
            jQuery.ajax(ajaxSettings);
        }

        /**
         * Change product total qty
         *
         * @param {String} event
         */
        function changeColumn(event, column, type) {
            var element = Event.element(event);
            if (element && element.checkboxElement && element.checkboxElement.checked) {
                if (type == 'number' && (element.value == '' || isNaN(element.value) || element.value < 0)) {
                    element.value = 0;
                    element.select();
                }
                var value = JSON.parse(managerProducts.get(element.checkboxElement.value));
                value = value ? value : {};
                value[column] = element.value;
                managerProducts.set(element.checkboxElement.value, JSON.stringify(value));
                $(hiddenField).value = Object.toJSON(managerProducts);
            }
        }
        gridJsObject.rowClickCallback = managerProductRowClick;
        gridJsObject.initRowCallback = managerProductRowInit;
        gridJsObject.checkboxCheckCallback = registerManagerProduct;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                managerProductRowInit(gridJsObject, row);
            });
        }
    };
});
