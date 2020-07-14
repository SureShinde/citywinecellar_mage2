
/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $, $H */

define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'mage/adminhtml/grid'
], function (jQuery, alert, Confirm, $t) {
    return function (config) {
        var selectedProducts = config.selectedProducts,
            selectProductObject = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            hiddenInputField = config.hiddenInputField,
            editFields = config.editFields,
            deleteUrl = config.deleteUrl,
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
                    if (element[el[0]]){
                        element[el[0]].show();
                        element[el[0]].up('div').down('span').hide();
                        element[el[0]].disabled = false;
                        value[el[0]] = element[el[0]].value;
                    }
                });
                selectProductObject.set(element.value, value);
            } else {
                editFields.each(function(el){
                    if (element[el[0]]){
                        element[el[0]].hide();
                        element[el[0]].up('div').down('span').show();
                        element[el[0]].disabled = true;
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
        function textBoxChange(event, type) {
            var element = Event.element(event);
            if (element && element['checkbox'] && element['checkbox'].checked) {
                if ((isNaN(element.value) || element.value<0) && type == 'number') {
                    element.value = '';
                    element.select();
                }
                var value = selectProductObject.get(element['checkbox'].value);
                value = value ? value : {};
                value[element.name] = element.value;
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
                    var element = $(row).down('input[name='+el[0]+']');
                    if(element){
                        checkbox[el[0]] = element;
                        element.disabled = !checkbox.checked;
                        element.tabIndex = tabIndex++;
                        element['checkbox'] = checkbox;
                        Event.observe(element, 'keyup', function(event) {
                            textBoxChange(event, el[1]);
                        });
                    }
                });
                var values = selectProductObject.get(checkbox.value);
                if(values){
                    editFields.each(function(el) {
                        if(values.total_qty)
                            checkbox[el[0]].value = values[el[0]]
                    });
                    gridJsObject.setCheckboxChecked(checkbox, true);
                };
                if(checkbox.checked)
                    gridJsObject.setCheckboxChecked(checkbox, true);
            }
            if (deleteButton){
                Event.observe(deleteButton, 'click', deleteItem);
            }
        }

        function deleteItem(event){
            event.stopPropagation();
            Confirm({
                content: $t('Do you want to remove this product from supplier?'),
                actions: {
                    confirm: function () {
                        var item = event.element().getAttribute('value'),
                            productId = event.element().getAttribute('product_id');
                        var ajaxSettings = {
                            url: deleteUrl + (deleteUrl.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ),
                            showLoader: true,
                            method: 'post',
                            data: {id: item, product_id: productId},
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
                    },
                    cancel: function () {
                        //element.selectedIndex = self.previousIndex;
                        return false;
                    },
                    always: function () {
                        return false;
                    }
                }
            });

        }

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
