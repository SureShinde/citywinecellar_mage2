
/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $, $H */

define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'ko',
    'mage/adminhtml/grid'
], function (jQuery, alert, confirm, ko) {
    return function (config) {
        var selectedProducts = config.selectedProducts,
            selectProductObject = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            hiddenInputField = config.hiddenInputField,
            editFields = config.editFields,
            deleteUrl = config.deleteUrl,
            // updateCostUrl = config.updateCostUrl,
            // reloadTotalUrl = config.reloadTotalUrl,
            // priceListJson = config.priceListJson,
            tabIndex = 1000;
        $(hiddenInputField).value = Object.toJSON(selectedProducts);
        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function rowClick(grid, event) {
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
         * Process event check checkbox in a row of grid
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function checkCheckbox(grid, element, checked) {
            if (checked) {
                var value = {};
                editFields.each(function(el){
                    if (element[el]){
                        element[el].show();
                        element[el].up('div').down('span').hide();
                        element[el].disabled = false;
                        value[el] = element[el].value;
                        value[el+'_old'] = element[el+'_old'].value;
                    }
                });
                selectProductObject.set(element.value, value);
            } else {
                editFields.each(function(el){
                    if (element[el]){
                        element[el].hide();
                        element[el].up('div').down('span').show();
                        element[el].disabled = true;
                    }
                });
                selectProductObject.unset(element.value);
            }
            $(hiddenInputField).value = Object.toJSON(selectProductObject);
        }

        /**
         * Process event change text box
         *
         * @param {String} event
         */
        function textBoxChange(event) {
            var element = Event.element(event);
            if (element && element['checkbox'] && element['checkbox'].checked) {
                if(isNaN(element.value) || element.value<0){
                    element.value = '';
                    element.select();
                }
                var value = selectProductObject.get(element['checkbox'].value);
                value = value ? value : {};
                value[element.name] = element.value;
                if (element.name == 'qty_orderred')
                {
                    var checkbox = element['checkbox'];
                    var productId = checkbox.value;
                    var costField = checkbox['cost'];
                    var minCost = 0;
                    // ko.utils.arrayForEach(priceListJson, function(item) {
                    //     if ((item.product_id == productId)
                    //         && parseFloat(item.minimal_qty) <= parseFloat(element.value)
                    //         && parseFloat(item.cost) > 0) {
                    //         if (minCost == 0) {
                    //             minCost = item.cost;
                    //         } else if(parseFloat(item.cost) < parseFloat(minCost)) {
                    //             minCost = item.cost;
                    //         }
                    //     }
                    // });
                    if (parseFloat(minCost) > 0) {
                        costField.value = parseFloat(minCost);
                        value.cost = costField.value;
                    }
                }
                selectProductObject.set(element['checkbox'].value, value);
                $(hiddenInputField).value = Object.toJSON(selectProductObject);
            }
        }

        /**
         * Initialize grid row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function initRow(grid, row){
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                deleteButton = $(row).down('a[class=delete_item]');
            if(checkbox){
                editFields.each(function(el){
                    var element = $(row).down('input[name='+el+']');
                    if(element){
                        checkbox[el] = element;
                        checkbox[el+'_old'] = $(row).down('input[name='+el+'_old]');
                        element.disabled = !checkbox.checked;
                        element.tabIndex = tabIndex++;
                        element['checkbox'] = checkbox;
                        Event.observe(element, 'keyup', textBoxChange);
                    }
                });
                var values = selectProductObject.get(checkbox.value);
                if(values){
                    editFields.each(function(el) {
                        checkbox[el].value = values[el]
                    });
                    gridJsObject.setCheckboxChecked(checkbox, true);
                };
                if(checkbox.checked)
                    gridJsObject.setCheckboxChecked(checkbox, true);
            }
            if(deleteButton){
                Event.observe(deleteButton, 'click', deleteItem);
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
                    // console.log(data.responseText);
                    if(data.responseText > 0){
                        selectProductObject.unset(productId);
                        $(hiddenInputField).value = Object.toJSON(selectProductObject);
                    }
                    // reloadTotal();
                    gridJsObject.doFilter()
                }
            };
            jQuery.ajax(ajaxSettings);
        }

        // function reloadTotal(){
        //     var ajaxSettings = {
        //         url: reloadTotalUrl + (reloadTotalUrl.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ),
        //         showLoader: true,
        //         method: 'post',
        //         data: {form_key: window.FORM_KEY},
        //         dataType: 'json',
        //         complete:  function(data) {
        //             if(data.status==200){
        //                 jQuery('#purchase_sumary_total_block').replaceWith(data.responseText);
        //             }
        //         }
        //     };
        //     jQuery.ajax(ajaxSettings);
        // }

        gridJsObject.rowClickCallback = rowClick;
        gridJsObject.initRowCallback = initRow;
        gridJsObject.checkboxCheckCallback = checkCheckbox;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                initRow(gridJsObject, row);
            });
        }
    };
});
